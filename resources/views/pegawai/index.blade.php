<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Daftar Pegawai - siMonika</title>
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
                <h2 class="mb-0">Kelola Pegawai</h2>
                <p class="text-muted">Manajemen data pegawai dan informasinya</p>
            </div>
            <div class="button-action">
                <!-- Button Tambah Pegawai -->
                <button id="btnCreate"class="btn btn-primary" onclick="openCreateModal()">
                    <i class="bi bi-plus-lg"></i> Tambah Pegawai
                </button>
            </div>
        </div>

        <!-- Daftar Pegawai -->
        @if ($pegawai->isEmpty())
            <div class="alert alert-warning text-center">Belum ada pegawai terdaftar.</div>
        @else
            <div class="table-responsive">
                <table class="table table-hover">
                    <thead>
                        <tr>
                            <th>Nama</th>
                            <th>Nomor Telepon</th>
                            <th>Email</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($pegawai as $p)
                        <tr>
                            <td>{{ $p->nama }}</td>
                            <td>{{ $p->nomor_telepon }}</td>
                            <td>{{ $p->email }}</td>
                            <td>
                                <button class="btn btn-warning btn-edit" 
                                    data-id="{{ $p->id }}" 
                                    data-nama="{{ $p->nama }}" 
                                    data-telepon="{{ $p->nomor_telepon }}" 
                                    data-email="{{ $p->email }}"
                                    data-bs-toggle="modal" 
                                    data-bs-target="#pegawaiEditModal">
                                    <i class="bi bi-pencil-square"></i>
                                </button>

                                <button class="btn btn-danger btn-delete" data-id="{{ $p->id }}">
                                    <i class="bi bi-trash"></i>
                                </button>

                                <form id="delete-form-{{ $p->id }}" action="{{ route('pegawai.destroy', $p->id) }}" method="POST" style="display: none;">
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

    @include('pegawai/create')
    @include('pegawai/edit')

    <script>
        document.addEventListener("DOMContentLoaded", function () {
            
            // 🔹 Menampilkan Pop Up Error jika ada validasi yang gagal
            @if ($errors->any())
            let errorMessage = "";
            @foreach ($errors->all() as $error)
                errorMessage += "{{ $error }}\n";
            @endforeach

            Swal.fire({
                title: "Terjadi Kesalahan!",
                icon: "error",
                confirmButtonText: "Mengerti"
            });
        @endif
            
            // 🔹 Menampilkan Pop Up Sukses jika ada session success
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

            // 🔹 Pop Up Konfirmasi Hapus
            document.querySelectorAll(".btn-delete").forEach(button => {
                button.addEventListener("click", function () {
                    let id = this.getAttribute("data-id");

                    Swal.fire({
                        title: "Apakah Anda yakin?",
                        text: "Data pegawai akan dihapus secara permanen!",
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
