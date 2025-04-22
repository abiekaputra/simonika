<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Pendataan - siMonika</title>

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
                <h2 class="mb-0">Pendataan Mahasiswa</h2>
                <p class="text-muted">Menampilkan data magang mahasiswa</p>
            </div>
            <div class="button-action">
                @if ($pendataans->isNotEmpty())
                    <button id="toggleView" class="btn btn-secondary">Tampilkan Tabel</button>
                @endif
                <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#pendataanCreateModal">
                    <i class="bi bi-plus-lg"></i> Tambah Data Magang
                </button>
            </div>
        </div>

        @if ($pendataans->isEmpty())
            <div class="alert alert-warning text-center">
                <i class="alert alert-warning text-center"></i> Belum ada data magang terdaftar.
            </div>
        @else
            <div id="pendataanContainer" style="position: relative;">
                <div id="pendataanTimeline"></div>
                <div class="zoom-controls">
                    <button id="zoomIn" class="btn btn-info zoom-btn"><i class="bi bi-plus-lg"></i></button>
                    <button id="zoomOut" class="btn btn-info zoom-btn"><i class="bi bi-dash-lg"></i></button>
                </div>
            </div>

            <div id="tableContainer" class="d-none">
                <table class="table table-hover">
                    <thead>
                        <tr>
                            <th>Universitas</th>
                            <th>Jumlah Orang</th>
                            <th>Tanggal Masuk</th>
                            <th>Tanggal Keluar</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($pendataans as $pendataan)
                            <tr>
                                <td>{{ $pendataan->universitas }}</td>
                                <td>{{ $pendataan->jumlah_orang }}</td>
                                <td>{{ $pendataan->tanggal_masuk }}</td>
                                <td>{{ $pendataan->tanggal_keluar }}</td>
                                <td>
                                    <button class="btn btn-warning btn-sm btn-edit" data-id="{{ $pendataan->id }}"
                                        data-universitas="{{ $pendataan->universitas }}"
                                        data-jumlah_orang="{{ $pendataan->jumlah_orang }}"
                                        data-tanggal_masuk="{{ $pendataan->tanggal_masuk }}"
                                        data-tanggal_keluar="{{ $pendataan->tanggal_keluar }}" data-bs-toggle="modal"
                                        data-bs-target="#pendataanEditModal">
                                        <i class="bi bi-pencil-square"></i>
                                    </button>

                                    <button class="btn btn-danger btn-sm btn-delete" data-id="{{ $pendataan->id }}">
                                        <i class="bi bi-trash"></i>
                                    </button>

                                    <form id="delete-form-{{ $pendataan->id }}"
                                        action="{{ route('pendataan.destroy', $pendataan->id) }}" method="POST"
                                        style="display: none;">
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

    @include('pendataan.create')
    @include('pendataan.edit')
    @include('pendataan.info')
</body>

<script>
    document.addEventListener("DOMContentLoaded", function () {
        // Tombol Toggle View antara Tabel dan Linimasa
        let toggleButton = document.getElementById("toggleView");
        if (toggleButton) {
            toggleButton.addEventListener("click", function () {
                document.getElementById("tableContainer").classList.toggle("d-none");
                document.getElementById("pendataanTimeline").classList.toggle("d-none");
                this.textContent = this.textContent.includes("Tabel") ? "Tampilkan Data Magang" : "Tampilkan Tabel";
            });
        }

        // Container untuk Timeline Pendataan
        let container = document.getElementById("pendataanTimeline");
        let zoomStep = 0.5;

        // Zoom In
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

        // Zoom Out
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

        // Data untuk Items (Pendataan Magang)
        let items = new vis.DataSet([
            @foreach ($pendataans as $pendataan)
                        {
                    id: {{ $pendataan->id }},
                    content: "{{ $pendataan->universitas ?? 'Tidak Diketahui' }}",
                    start: "{{ $pendataan->tanggal_masuk }}",
                    end: "{{ $pendataan->tanggal_keluar }}",
                    group: {{ $loop->index + 1 }},
                    status: "Magang",
                    deskripsi: "Jumlah Orang: {{ $pendataan->jumlah_orang }}",
                    style: "background-color: lightblue; color: black;",
                },
            @endforeach
    ]);

        // Data untuk Groups (Universitas)
        let groups = new vis.DataSet([
            @foreach ($pendataans as $pendataan)
                        {
                    id: {{ $loop->index + 1 }},
                    content: "{{ $pendataan->universitas }}"
                },
            @endforeach
    ]);

        // Opsi Timeline
        let options = {
            groupOrder: "content",
            stackSubgroups: true,
            showCurrentTime: true,
            zoomable: true,
            orientation: { axis: "top" },
            margin: {
                item: 10,
                axis: 10
            }
        };

        // Membuat Timeline Pendataan Magang
        let timeline = new vis.Timeline(container, items, options);

        // Event Listener untuk Klik pada Timeline (untuk Info Pendataan)
        timeline.on("select", function (props) {
            if (props.items.length > 0) {
                let itemId = props.items[0];
                let item = items.get(itemId);

                // Mengisi modal info dengan data yang dipilih
                $("#infoUniversitas").text(item.content);
                $("#infoJumlahOrang").text(item.deskripsi.split(": ")[1]); // Ambil jumlah orang
                $("#infoTanggalMasuk").text(item.start);
                $("#infoTanggalKeluar").text(item.end);

                // Tampilkan modal info
                $("#modalInfoPendataan").modal("show");
            }
        });
    });


    // Validasi Tanggal Masuk dan Keluar
    let tanggalMasukInput = document.getElementById("tanggal_masuk");
    let tanggalKeluarInput = document.getElementById("tanggal_keluar");

    function validateDateInput() {
        let tanggalMasuk = new Date(tanggalMasukInput.value);
        let tanggalKeluar = new Date(tanggalKeluarInput.value);

        if (tanggalMasuk > tanggalKeluar) {
            Swal.fire({
                icon: 'error',
                title: 'Kesalahan Input',
                text: 'Tanggal masuk tidak boleh lebih besar dari tanggal keluar!',
            });

            // Reset input yang bermasalah
            tanggalMasukInput.value = "";
            return false;
        }
        return true;
    }

    tanggalMasukInput.addEventListener("change", validateDateInput);
    tanggalKeluarInput.addEventListener("change", validateDateInput);

    // Submit Edit Pendataan
    let editForm = document.getElementById("editPendataanForm");
    if (editForm) {
        editForm.addEventListener("submit", function (event) {
            event.preventDefault();

            if (!validateDateInput()) return;

            let formData = new FormData(editForm);
            let id = document.getElementById("edit_pendataan_id").value;

            fetch("{{ url('pendataan') }}/" + id, {
                method: "POST",
                body: formData,
                headers: {
                    "X-CSRF-TOKEN": document.querySelector('meta[name="csrf-token"]').getAttribute("content")
                }
            })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        let modalElement = document.getElementById("pendataanEditModal");
                        let modalInstance = bootstrap.Modal.getInstance(modalElement);
                        if (modalInstance) {
                            modalInstance.hide();
                        }

                        document.querySelectorAll(".modal-backdrop").forEach(el => el.remove());

                        Swal.fire({
                            icon: "success",
                            title: "Berhasil!",
                            text: "Data Pendataan berhasil diperbarui!",
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
                text: "Data pendataan yang dihapus tidak dapat dikembalikan!",
                icon: "warning",
                showCancelButton: true,
                confirmButtonColor: "#d33",
                cancelButtonColor: "#3085d6",
                confirmButtonText: "Ya, Hapus!"
            }).then((result) => {
                if (result.isConfirmed) {
                    fetch(`{{ url('pendataan') }}/${id}`, {
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
                                    text: "Data Pendataan berhasil dihapus!",
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
</script>

</html>