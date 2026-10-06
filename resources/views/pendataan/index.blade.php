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
    <script src="https://unpkg.com/vis-timeline@7.4.6/standalone/umd/vis-timeline-graph2d.min.js"></script>

    <!-- Vis.js -->
    <link href="https://unpkg.com/vis-timeline@7.4.6/styles/vis-timeline-graph2d.min.css"
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
                <div class="mt-3">
                    {{ $pendataans->links('pagination::bootstrap-5') }}
                </div>
            </div>
        @endif
    </div>

    @include('pendataan.create')
    @include('pendataan.edit')
    @include('pendataan.info')
</body>

    @include('pendataan.partials.page-script')

</html>
