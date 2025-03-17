<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Linimasa Proyek - siMonika</title>

    <!-- Bootstrap CSS -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-icons/1.11.1/font/bootstrap-icons.min.css" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('css/style.css') }}">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <!-- Toastr & SweetAlert2 -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <!-- Scripts -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/vis-timeline/7.4.6/vis-timeline-graph2d.min.js"></script>

    <!-- Vis.js -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/vis-timeline/7.4.6/vis-timeline-graph2d.min.css" rel="stylesheet">
</head>
<body>
    @include('templates.sidebar')

    <div class="main-content p-4">
        <div class="d-flex justify-content-between align-items-center mb-3">
            <div>
                <h2 class="mb-0">Linimasa Proyek</h2>
                <p class="text-muted">Menampilkan timeline proyek yang dikerjakan oleh pegawai</p>
            </div>
            <div class="button-action">
                @if ($linimasa->isNotEmpty())
                    <button id="toggleView" class="btn btn-secondary">Tampilkan Tabel</button>
                @endif
                <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#linimasaCreateModal">
                    <i class="bi bi-plus-lg"></i> Tambah Linimasa
                </button>
            </div>
        </div>

        @if ($linimasa->isEmpty())
            <div class="alert alert-warning text-center">
                <i class="alert alert-warning text-center"></i> Belum ada linimasa terdaftar.
            </div>
        @else
            <div id="timelineContainer">
                <div id="timeline"></div>
            </div>

            <div id="tableContainer" class="d-none">
                <table class="table table-hover">
                    <thead>
                        <tr>
                            <th>Pegawai</th>
                            <th>Proyek</th>
                            <th>Status</th>
                            <th>Mulai</th>
                            <th>Tenggat</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($linimasa as $item)
                        <tr>
                            <td>{{ $item->pegawai->nama }}</td>
                            <td>{{ $item->proyek->nama_proyek }}</td>
                            <td>{{ $item->status_proyek }}</td>
                            <td>{{ $item->mulai }}</td>
                            <td>{{ $item->tenggat }}</td>
                            <td>
                                <button class="btn btn-warning btn-sm btn-edit"
                                    data-id="{{ $item->id }}"
                                    data-pegawai="{{ $item->pegawai->id }}"
                                    data-proyek="{{ $item->proyek->id }}"
                                    data-status="{{ $item->status_proyek }}"
                                    data-mulai="{{ $item->mulai }}"
                                    data-tenggat="{{ $item->tenggat }}"
                                    data-deskripsi="{{ $item->deskripsi ?? '' }}"
                                    data-bs-toggle="modal"
                                    data-bs-target="#linimasaEditModal">
                                    <i class="bi bi-pencil-square"></i>
                                </button>

                                <button class="btn btn-danger btn-delete" data-id="{{ $item->id }}">
                                    <i class="bi bi-trash"></i>
                                </button>

                                <form id="delete-form-{{ $item->id }}" action="{{ route('linimasa.destroy', $item->id) }}" method="POST" style="display: none;">
                                    @csrf
                                    @method('DELETE')
                                </form>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endif
    </div>

    @include('linimasa/create')
    @include('linimasa/edit')

    <script>
        document.addEventListener("DOMContentLoaded", function () {
            // Toggle tampilan antara Vis.js dan tabel
            let toggleButton = document.getElementById("toggleView");
            if (toggleButton) {
                toggleButton.addEventListener("click", function () {
                    document.getElementById("tableContainer").classList.toggle("d-none");
                    document.getElementById("timelineContainer").classList.toggle("d-none");
                    this.textContent = this.textContent.includes("Tabel") ? "Tampilkan Linimasa" : "Tampilkan Tabel";
                });
            }

            // Inisialisasi Timeline Vis.js
            let container = document.getElementById("timeline");
            let items = new vis.DataSet([
                @foreach ($linimasa as $item)
                {
                    id: {{ $item->id }},
                    content: "{{ $item->proyek->nama_proyek }}",
                    start: "{{ $item->mulai }}",
                    end: "{{ $item->tenggat }}",
                    group: {{ $item->pegawai->id }}
                },
                @endforeach
            ]);

            let groups = new vis.DataSet([
                @foreach ($pegawai as $p)
                {
                    id: {{ $p->id }},
                    content: "{{ $p->nama }}"
                },
                @endforeach
            ]);

            let options = {
                groupOrder: "content",
                stack: false,
                showCurrentTime: true,
                zoomable: true,
                orientation: { axis: "top" }
            };

            new vis.Timeline(container, items, groups, options);

            // Submit Form Edit Linimasa
            let editForm = document.getElementById("editLinimasaForm");
            if (editForm) {
                editForm.addEventListener("submit", function (event) {
                    event.preventDefault(); // Mencegah reload halaman

                    let formData = new FormData(editForm);
                    let id = document.getElementById("edit_linimasa_id").value;

                    fetch("{{ url('linimasa') }}/" + id, {
                        method: "POST",
                        body: formData,
                        headers: {
                            "X-CSRF-TOKEN": document.querySelector('meta[name="csrf-token"]').getAttribute("content")
                        }
                    })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            let modalElement = document.getElementById("linimasaEditModal");
                            let modalInstance = bootstrap.Modal.getInstance(modalElement);
                            if (modalInstance) {
                                modalInstance.hide();
                            }

                            document.querySelectorAll(".modal-backdrop").forEach(el => el.remove());

                            Swal.fire({
                                icon: "success",
                                title: "Berhasil!",
                                text: "Data Linimasa berhasil diperbarui!",
                                showConfirmButton: false,
                                timer: 2000
                            }).then(() => {
                                location.reload(); // Refresh halaman setelah sukses
                            });
                        } else {
                            Swal.fire({
                                icon: "error",
                                title: "Gagal!",
                                text: data.message || "Terjadi kesalahan saat memperbarui data.",
                            });
                        }
                    })
                    .catch(error => {
                        Swal.fire({
                            icon: "error",
                            title: "Oops...",
                            text: "Gagal memperbarui data. Coba lagi!",
                        });
                    });
                });
            }

            // Hapus Data Linimasa dengan SweetAlert2
            document.querySelectorAll(".btn-delete").forEach(button => {
                button.addEventListener("click", function () {
                    let id = this.getAttribute("data-id");

                    Swal.fire({
                        title: "Yakin ingin menghapus?",
                        text: "Data linimasa yang dihapus tidak dapat dikembalikan!",
                        icon: "warning",
                        showCancelButton: true,
                        confirmButtonColor: "#d33",
                        cancelButtonColor: "#3085d6",
                        confirmButtonText: "Ya, Hapus!"
                    }).then((result) => {
                        if (result.isConfirmed) {
                            fetch(`{{ url('linimasa') }}/${id}`, {
                                method: "POST", // Sesuai dengan form submission
                                headers: {
                                    "X-CSRF-TOKEN": document.querySelector('meta[name="csrf-token"]').getAttribute("content"),
                                    "X-HTTP-Method-Override": "DELETE"
                                }
                            })
                            .then(response => response.json())
                            .then(data => {
                                if (data.success) {
                                    Swal.fire({
                                        icon: "success",
                                        title: "Berhasil!",
                                        text: "Data Linimasa berhasil dihapus!",
                                        showConfirmButton: false,
                                        timer: 2000
                                    }).then(() => {
                                        location.reload();
                                    });
                                } else {
                                    Swal.fire({
                                        icon: "error",
                                        title: "Gagal!",
                                        text: "Terjadi kesalahan saat menghapus data.",
                                    });
                                }
                            })
                            .catch(error => {
                                Swal.fire({
                                    icon: "error",
                                    title: "Oops...",
                                    text: "Gagal menghapus data. Coba lagi!",
                                });
                            });
                        }
                    });
                });
            });
        });
    </script>

</body>
</html>