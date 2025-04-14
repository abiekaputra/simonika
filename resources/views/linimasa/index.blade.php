<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Linimasa Proyek - siMonika</title>

    <!-- Bootstrap CSS -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-icons/1.11.1/font/bootstrap-icons.min.css"
        rel="stylesheet">
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
    <link href="https://cdnjs.cloudflare.com/ajax/libs/vis-timeline/7.4.6/vis-timeline-graph2d.min.css"
        rel="stylesheet">

    <style>
        .zoom-controls {
            position: absolute;
            top: 10px;
            right: 10px;
            z-index: 1000;
            display: flex;
            flex-direction: column;
            gap: 10px;
        }

        .zoom-btn {
            width: 30px;
            height: 30px;
            font-size: 20px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.2);
        }
    </style>
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
            <div class="mb-3 d-flex justify-content-end">
                <button id="toggleSubject" class="btn btn-outline-primary">Tampilkan Berdasarkan Proyek</button>
            </div>

            <div id="timelineContainer" style="position: relative;">
                <div id="timeline"></div>
                <div class="zoom-controls">
                    <button id="zoomIn" class="btn btn-info zoom-btn"><i class="bi bi-plus-lg"></i></button>
                    <button id="zoomOut" class="btn btn-info zoom-btn"><i class="bi bi-dash-lg"></i></button>
                </div>
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

                                            <button class="btn btn-warning btn-sm btn-edit" data-id="{{ $item->id }}"
                                                data-pegawai="{{ $item->pegawai->id }}" data-proyek="{{ $item->proyek->id }}"
                                                data-status="{{ $item->status_proyek }}" data-mulai="{{ $item->mulai }}"
                                                data-tenggat="{{ $item->tenggat }}" data-deskripsi="{{ $item->deskripsi ?? '' }}"
                                                data-bs-toggle="modal" data-bs-target="#linimasaEditModal">
                                                <i class="bi bi-pencil-square"></i>
                                            </button>

                                            <button class="btn btn-danger btn-delete" data-id="{{ $item->id }}">
                                                <i class="bi bi-trash"></i>
                                            </button>

                                            <form id="delete-form-{{ $item->id }}" action="{{ route('linimasa.destroy', $item->id) }}"
                                                method="POST" style="display: none;">
                                                @csrf
                                                @method('DELETE')
                                            </form>
                                        <td style="
                                                                    background-color: {{
                            match ($item->status_proyek) {
                                'Selesai Lebih Cepat' => 'green; color: white;',
                                'Tepat Waktu' => 'lightgreen; color: black;',
                                'Terlambat' => 'red; color: white;',
                                'Revisi' => 'orange; color: black;',
                                'Proses' => 'blue; color: white;',
                                'Todo Next' => 'gray; color: white;',
                                default => 'lightgray; color: black;',
                            }
                                                                    }}">
                                        </td>

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
    @include('linimasa/info')

    <script>
        document.addEventListener("DOMContentLoaded", function () {
            let toggleButton = document.getElementById("toggleView");
            if (toggleButton) {
                toggleButton.addEventListener("click", function () {
                    document.getElementById("tableContainer").classList.toggle("d-none");
                    document.getElementById("timelineContainer").classList.toggle("d-none");
                    this.textContent = this.textContent.includes("Tabel") ? "Tampilkan Linimasa" : "Tampilkan Tabel";
                });
            }

            let container = document.getElementById("timeline");
            let zoomStep = 0.5;

            document.getElementById('zoomIn').addEventListener('click', function () {
                let currentRange = timeline.getWindow();
                let start = currentRange.start.valueOf();
                let end = currentRange.end.valueOf();
                let interval = end - start;
                let newInterval = interval * (1 - zoomStep);
                let newStart = start + (interval - newInterval) / 2;
                let newEnd = end - (interval - newInterval) / 2;
                timeline.setWindow(newStart, newEnd);
            });

            document.getElementById('zoomOut').addEventListener('click', function () {
                let currentRange = timeline.getWindow();
                let start = currentRange.start.valueOf();
                let end = currentRange.end.valueOf();
                let interval = end - start;
                let newInterval = interval * (1 + zoomStep);
                let newStart = start - (newInterval - interval) / 2;
                let newEnd = end + (newInterval - interval) / 2;
                timeline.setWindow(newStart, newEnd);
            });

            function getItems(mode = 'pegawai') {
                return new vis.DataSet([
                    @foreach ($linimasa as $item)
                        {
                            id: {{ $item->id }},
                            content: mode === 'pegawai' ? "{{ $item->proyek->nama_proyek }}" : "{{ $item->pegawai->nama }}",
                            start: "{{ $item->mulai }}",
                            end: "{{ $item->tenggat }}",
                            group: mode === 'pegawai' ? {{ $item->pegawai->id }} : {{ $item->proyek->id }},
                            subgroup: {{ $loop->index + 1 }},
                            status: "{{ $item->status_proyek }}",
                            deskripsi: "{{ $item->deskripsi ?? 'Tidak ada deskripsi' }}",
                            pegawai: "{{ $item->pegawai->nama }}",
                            proyek: "{{ $item->proyek->nama_proyek }}",
                            id_proyek: {{ $item->proyek->id }},
                            id_pegawai: {{ $item->pegawai->id }},
                            style: "background-color: {{
                                match ($item->status_proyek) {
                                    'Selesai Lebih Cepat' => 'green; color: white;',
                                    'Tepat Waktu' => 'lightgreen; color: black;',
                                    'Terlambat' => 'red; color: white;',
                                    'Revisi' => 'orange; color: black;',
                                    'Proses' => 'blue; color: white;',
                                    'Todo Next' => 'gray; color: white;',
                                    default => 'lightgray; color: black;',
                                }
                            }}"
                        },
                    @endforeach
                ]);
            }

        let groupBy = 'pegawai'; // default
        const groupPegawai = new vis.DataSet([
            @foreach ($pegawai as $p)
                { id: {{ $p->id }}, content: "{{ $p->nama }}" },
            @endforeach
        ]);
        const groupProyek = new vis.DataSet([
            @foreach ($proyek as $p)
                { id: {{ $p->id }}, content: "{{ $p->nama_proyek }}" },
            @endforeach
        ]);

        let options = {
            groupOrder: "content",
            stack: false,
            subgroupOrder: "subgroup",
            showCurrentTime: true,
            zoomable: true,
            orientation: { axis: "top" },
            margin: {
                item: 10,
                axis: 10
            }
        };

        let items = getItems(groupBy);
        let timeline = new vis.Timeline(container, items, groupPegawai, options);

        document.getElementById("toggleSubject").addEventListener("click", function () {
            groupBy = groupBy === 'pegawai' ? 'proyek' : 'pegawai';
            this.textContent = groupBy === 'pegawai' ? 'Tampilkan Berdasarkan Proyek' : 'Tampilkan Berdasarkan Pegawai';

            // Update timeline
            timeline.setGroups(groupBy === 'pegawai' ? groupPegawai : groupProyek);
            timeline.setItems(getItems(groupBy));
        });

        // Modal Info
        timeline.on("select", function (props) {
            if (props.items.length > 0) {
                let itemId = props.items[0];
                let item = items.get(itemId);

                $("#infoNamaPegawai").text(item.pegawai);
                $("#infoNamaProyek").text(item.proyek);
                $("#infoMulai").text(item.start);
                $("#infoTenggat").text(item.end);
                $("#infoStatus").text(item.status);
                $("#infoDeskripsi").text(item.deskripsi);

                let btnEdit = document.querySelector("#modalInfoLinimasa .btn-edit");
                btnEdit.setAttribute("data-id", item.id);
                btnEdit.setAttribute("data-pegawai", item.group); 
                btnEdit.setAttribute("data-proyek", item.id_proyek);
                btnEdit.setAttribute("data-status", item.status);
                btnEdit.setAttribute("data-mulai", item.start);
                btnEdit.setAttribute("data-tenggat", item.end);
                btnEdit.setAttribute("data-deskripsi", item.deskripsi || "");

                let btnDelete = document.querySelector("#modalInfoLinimasa .btn-delete");
                btnDelete.setAttribute("data-id", item.id);

                $("#modalInfoLinimasa").modal("show");
            }
        });

        // Validasi Tanggal Mulai dan Tenggat
        let mulaiInput = document.getElementById("mulai");
        let tenggatInput = document.getElementById("tenggat");

        function validateDateInput() {
            let mulai = new Date(mulaiInput.value);
            let tenggat = new Date(tenggatInput.value);

            if (mulai > tenggat) {
                Swal.fire({
                    icon: 'error',
                    title: 'Kesalahan Input',
                    text: 'Tanggal mulai tidak boleh lebih besar dari tenggat!',
                });

                // Reset input yang bermasalah
                mulaiInput.value = "";
                return false;
            }
            return true;
        }

        mulaiInput.addEventListener("change", validateDateInput);
        tenggatInput.addEventListener("change", validateDateInput);

        // Submit Edit Linimasa
        let editForm = document.getElementById("editLinimasaForm");
        if (editForm) {
            editForm.addEventListener("submit", function (event) {
                event.preventDefault();

                if (!validateDateInput()) return;

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
                                location.reload();
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

        // Pop Up Hapus
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
                            method: "POST",
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