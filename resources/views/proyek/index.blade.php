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
</head>

<body>
    <!-- Sidebar -->
    @include('templates.sidebar')

    <!-- Main Content -->
    <div class="main-content p-4">
        <!-- Header -->
        <div class="d-flex justify-content-between align-items-center mb-3">
            <div>
                <h2 class="mb-0">Kelola Proyek</h2>
                <p class="text-muted">Manajemen data proyek dan informasinya</p>
            </div>
            <div class="button-action">
                <!-- Button Tambah Proyek -->
                <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#proyekCreateModal">
                    <i class="bi bi-plus-lg"></i> Tambah Proyek
                </button>
                <!-- Button Tambah Kategori -->
                <button class="btn btn-secondary" data-bs-toggle="modal" data-bs-target="#kategoriCreateModal">
                    <i class="bi bi-plus-lg"></i> Tambah Kategori
                </button>
                <!-- Button Lihat Kategori -->
                <button class="btn btn-secondary" data-bs-toggle="modal" data-bs-target="#kategoriViewModal">
                    <i class="bi bi-list"></i> Lihat Kategori
                </button>
            </div>
        </div>

        @if ($proyek->isEmpty())
        <div class="alert alert-warning text-center">Belum ada proyek terdaftar.</div>
        @else
            <div class="table-responsive">
                <table class="table table-hover">
                    <thead>
                        <tr>
                            <th>Nama proyek</th>
                            <th>Kategori</th>
                            <th>Deskripsi</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($proyek as $p)
                        <tr>
                            <td>{{ $p->nama_proyek }}</td>
                            <td>{{ $p->kategori ? $p->kategori->nama_kategori : '-' }}</td>
                            <td>{{ $p->deskripsi }}</td>
                            <td>
                                <button class="btn btn-warning btn-edit" 
                                    data-id="{{ $p->id }}" 
                                    data-nama-proyek="{{ $p->nama_proyek }}" 
                                    data-kategori-id="{{ $p->kategori_id }}" 
                                    data-deskripsi="{{ $p->deskripsi }}"
                                    data-bs-toggle="modal" 
                                    data-bs-target="#proyekEditModal">
                                    <i class="bi bi-pencil-square"></i>
                                </button>

                                <button class="btn btn-danger btn-delete" data-id="{{ $p->id }}">
                                    <i class="bi bi-trash"></i>
                                </button>

                                <form id="delete-form-{{ $p->id }}" action="{{ route('proyek.destroy', $p->id) }}" method="POST" style="display: none;">
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

    <!-- Modal Tambah Proyek -->
    @include('proyek/create', ['kategori' => $kategori])

    <!-- Modal Edit Proyek -->
    @include('proyek/edit')

    @include('proyek/kategori')


    <script>
        document.addEventListener("DOMContentLoaded", function () {
            // Menampilkan Pop Up Error jika ada validasi yang gagal
            @if ($errors->any())
            Swal.fire({
                title: "Terjadi Kesalahan!",
                text: "{{ implode('\n', $errors->all()) }}",
                icon: "error",
                confirmButtonText: "Mengerti"
            });
            @endif

            // Menampilkan Pop Up Sukses jika ada session success
            @if (session('success'))
            Swal.fire({
                icon: 'success',
                title: 'Sukses!',
                text: "{{ session('success') }}",
                showConfirmButton: false,
                timer: 3000,
                position: 'center'
            });
            @endif

            // Pop Up Konfirmasi Hapus
            document.querySelectorAll(".btn-delete").forEach(button => {
                button.addEventListener("click", function () {
                    let id = this.getAttribute("data-id");

                    Swal.fire({
                        title: "Apakah Anda yakin?",
                        text: "Data proyek akan dihapus secara permanen!",
                        icon: "warning",
                        showCancelButton: true,
                        confirmButtonColor: "#d33",
                        cancelButtonColor: "#3085d6",
                        confirmButtonText: "Ya, Hapus!",
                        cancelButtonText: "Batal"
                    }).then((result) => {
                        if (result.isConfirmed) {
                            document.getElementById(`delete-form-${id}`).submit();
                        }
                    });
                });
            });
        });
    </script>

</body>

</html>
