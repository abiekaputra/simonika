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
                            @php
                                $statusClass = match ($item->status_proyek) {
                                    'Selesai Lebih Cepat' => 'text-bg-success',
                                    'Tepat Waktu' => 'bg-success-subtle text-success-emphasis',
                                    'Terlambat' => 'text-bg-danger',
                                    'Revisi' => 'text-bg-warning',
                                    'Proses' => 'text-bg-primary',
                                    'To Do Next' => 'text-bg-secondary',
                                    default => 'text-bg-light',
                                };
                            @endphp
                            <tr>
                                <td>{{ $item->pegawai->nama }}</td>
                                <td>{{ $item->proyek->nama_proyek }}</td>
                                <td><span class="badge {{ $statusClass }}">{{ $item->status_proyek }}</span></td>
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
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
                <div class="mt-3">
                    {{ $linimasa->links('pagination::bootstrap-5') }}
                </div>
            </div>
        @endif
    </div>

    @include('linimasa/create')
    @include('linimasa/edit')
    @include('linimasa/info')

    @include('linimasa.partials.page-script')

</body>

</html>
