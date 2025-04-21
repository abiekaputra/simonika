<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Pendataan Mahasiswa - siMonika</title>

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

    <!-- Vis.js -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/vis-timeline/7.4.6/vis-timeline-graph2d.min.css" rel="stylesheet">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/vis-timeline/7.4.6/vis-timeline-graph2d.min.js"></script>

    <style>
        .zoom-controls {
            position: absolute;
            bottom: 10px;
            right: 10px;
            z-index: 1000;
        }
        .zoom-btn {
            width: 40px;
            height: 40px;
            font-size: 20px;
            border-radius: 50%;
            margin: 0 5px;
            display: flex;
            align-items: center;
            justify-content: center;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.2);
        }
        .timeline-container {
            width: 100%;
            max-height: 500px;
            overflow-y: auto;
            border: 1px solid #ddd;
            margin-top: 20px;
        }
        #timeline {
            min-height: 500px;
        }
    </style>
</head>
<body>
    @include('templates.sidebar')

    <div class="container">
       
        <div class="main-content p-4">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <div>
                    <h2 class="mb-0">Pendataan Proyek</h2>
                    <p class="text-muted">Menampilkan timeline untuk mahasiswa magang</p>
                </div>
            </div>

            <!-- Form Input Data Pendataan -->
            <form id="pendataanForm" action="{{ route('pendataan.store') }}" method="POST">
                @csrf
                <input type="hidden" id="id" name="id"> <!-- Hidden ID untuk edit -->
                <div class="mb-3">
                    <label class="form-label">Universitas</label>
                    <input type="text" name="universitas" id="universitas" class="form-control" required>
                </div>
                <div class="mb-3">
                    <label class="form-label">Jumlah Orang</label>
                    <input type="number" name="jumlah_orang" id="jumlah_orang" class="form-control" required>
                </div>
                <div class="mb-3">
                    <label class="form-label">Tanggal Masuk</label>
                    <input type="date" name="tanggal_masuk" id="tanggal_masuk" class="form-control" required>
                </div>
                <div class="mb-3">
                    <label class="form-label">Tanggal Keluar</label>
                    <input type="date" name="tanggal_keluar" id="tanggal_keluar" class="form-control" required>
                </div>
                <button type="submit" class="btn btn-primary" id="saveBtn">Simpan</button>
                <button type="button" class="btn btn-warning d-none" id="editBtn">Edit</button>
                <button type="button" class="btn btn-danger d-none" id="deleteBtn">Hapus</button>
            </form>

            <!-- Container untuk Timeline -->
            <h2 class="mt-4">Timeline Data Magang</h2>
            @if ($pendataans->isEmpty())
                <div class="alert alert-warning text-center">
                    Belum ada linimasa terdaftar.
                </div>
            @else
                <div class="mb-3 d-flex justify-content-end">
                    <button id="toggleSubject" class="btn btn-outline-primary">Tampilkan Berdasarkan Proyek</button>
                </div>

                <div id="timelineContainer" style="position: relative;">
                    <div id="timeline"></div>
                    <div class="zoom-controls">
                        <button id="zoomIn" class="btn btn-light zoom-btn"><i class="bi bi-plus-lg"></i></button>
                        <button id="zoomOut" class="btn btn-light zoom-btn"><i class="bi bi-dash-lg"></i></button>
                    </div>
                </div>
            @endif
        </div>
    </div>

    @push('scripts')
    <script>
    document.addEventListener("DOMContentLoaded", function () {
        const container = document.getElementById("timeline");
        const editBtn = document.getElementById("editBtn");
        const deleteBtn = document.getElementById("deleteBtn");
        const saveBtn = document.getElementById("saveBtn");
        const form = document.getElementById("pendataanForm");

        // Ambil data dari Blade
        let items = new vis.DataSet([
            @foreach ($pendataans as $index => $pendataan)
                {
                    id: {{ $pendataan->id }},
                    content: "{{ $pendataan->universitas }} ({{ $pendataan->jumlah_orang }} orang)",
                    start: new Date("{{ $pendataan->tanggal_masuk }}"),
                    end: new Date("{{ $pendataan->tanggal_keluar }}"),
                    type: "range",
                },
            @endforeach
        ]);

        let newHeight = Math.max(500, items.length * 50) + "px";
        container.style.height = newHeight;

        const timeline = new vis.Timeline(container, items, {
            locale: "id",
            height: newHeight,
            stack: false,
            margin: { item: 15 },
            orientation: { axis: "top", item: "top" },
            horizontalScroll: true,
            zoomable: true,
        });

        document.getElementById("zoomIn").addEventListener("click", function () {
            timeline.zoomIn(0.5);
        });
        document.getElementById("zoomOut").addEventListener("click", function () {
            timeline.zoomOut(0.5);
        });

        timeline.on("select", function (properties) {
            if (properties.items.length > 0) {
                let selectedId = properties.items[0];
                let selectedItem = items.get(selectedId);

                document.getElementById("id").value = selectedItem.id;
                document.getElementById("universitas").value = selectedItem.content.split(" (")[0];
                document.getElementById("jumlah_orang").value = selectedItem.content.match(/\d+/)[0];
                document.getElementById("tanggal_masuk").value = selectedItem.start.toISOString().split("T")[0];
                document.getElementById("tanggal_keluar").value = selectedItem.end.toISOString().split("T")[0];

                editBtn.classList.remove("d-none");
                deleteBtn.classList.remove("d-none");
                saveBtn.classList.add("d-none");
            }
        });

        editBtn.addEventListener("click", function () {
            let id = document.getElementById("id").value;
            if (!id) {
                alert("Pilih data yang ingin diedit!");
                return;
            }

            form.action = "/pendataan/update/" + id;
            let methodInput = document.createElement("input");
            methodInput.type = "hidden";
            methodInput.name = "_method";
            methodInput.value = "PUT";
            form.appendChild(methodInput);
            form.submit();
        });

        deleteBtn.addEventListener("click", function () {
            let id = document.getElementById("id").value;
            if (!id) {
                alert("Pilih data yang ingin dihapus!");
                return;
            }

            let confirmDelete = confirm("Apakah Anda yakin ingin menghapus data ini?");
            if (confirmDelete) {
                let deleteForm = document.createElement("form");
                deleteForm.action = "/pendataan/delete/" + id;
                deleteForm.method = "POST";
                deleteForm.innerHTML = `
                    @csrf
                    <input type="hidden" name="_method" value="DELETE">
                `;
                document.body.appendChild(deleteForm);
                deleteForm.submit();
            }
        });
    });
    </script>
    @end
