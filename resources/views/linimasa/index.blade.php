<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Daftar Proyek - siMonika</title>
    <!-- Bootstrap CSS -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/css/bootstrap.min.css" rel="stylesheet">
    <!-- Bootstrap Icons -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-icons/1.11.1/font/bootstrap-icons.min.css"
        rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('css/style.css') }}">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <!-- Toastr CSS -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.css" rel="stylesheet">
    <!-- SweetAlert2 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css" rel="stylesheet">
    <!-- Toastr & SweetAlert2 -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <!-- Bootstrap Bundle with Popper -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/js/bootstrap.bundle.min.js"></script>
    <!-- Skrip Vis.js -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/vis/4.21.0/vis.min.js"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/vis/4.21.0/vis.min.css" rel="stylesheet">
    <script></script>
</head>

<body>
    <!-- Sidebar -->
    @include('templates.sidebar')

    <!-- Main Content -->
    <div class="main-content p-4">
        <!-- Header -->
        <div class="d-flex justify-content-between align-items-center mb-3">
            <div>
                <h2 class="mb-0">Kelola Linimasa</h2>
                <p class="text-muted">Manajemen data linimasa</p>
            </div>
            <div class="button-action">
                <!-- Button Tambah Linimasa -->
                <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#tambahLinimasaModal">
                    <i class="bi bi-plus-lg"></i> Tambah Linimasa
                </button>
            </div>
        </div>

        <!-- Vis.js Timeline -->
        <div id="linimasaTimeline" style="height: 400px; margin-bottom: 30px;"></div>

        <!-- Script to Initialize Vis.js Timeline -->
        <script>
            const container = document.getElementById('linimasaTimeline');
            const items = new vis.DataSet(@json($linimasas));
            

            const options = {
                editable: false,
                orientation: 'top',
            };

            const timeline = new vis.Timeline(container, items, options);
        </script>

        <!-- Tabel Linimasa (Optional Display) -->
        @if ($linimasas->isEmpty())
        <div class="alert alert-warning text-center">Belum ada data linimasa.</div>
        @else
        <div class="table-responsive">
            <table class="table table-hover">
                <thead>
                    <tr>
                        <th>Nama Proyek</th>
                        <th>Nama Pegawai</th>
                        <th>Kategori</th>
                        <th>Tanggal Mulai</th>
                        <th>Tenggat Waktu</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($linimasas as $linimasa)
                    <tr>
                        <td>{{ $linimasa->proyek ? $linimasa->proyek->nama_proyek : '-' }}</td>
                        <td>{{ $linimasa->pegawai ? $linimasa->pegawai->nama : '-' }}</td>
                        <td>{{ $linimasa->kategori ? $linimasa->kategori->nama_kategori : '-' }}</td>
                        <td>{{ $linimasa->tanggal_mulai }}</td>
                        <td>{{ $linimasa->tanggal_deadline }}</td>
                        <td>
                            <button class="btn btn-warning btn-edit" data-id="{{ $linimasa->id }}" data-bs-toggle="modal" data-bs-target="#editLinimasaModal">
                                <i class="bi bi-pencil-square"></i>
                            </button>
                            <button class="btn btn-danger btn-delete" data-id="{{ $linimasa->id }}">
                                <i class="bi bi-trash"></i>
                            </button>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        @endif
    </div>

    <!-- Include Modal -->
    @include('linimasa/create')

</body>

</html>
