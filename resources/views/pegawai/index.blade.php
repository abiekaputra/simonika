@extends('layouts.app')

@section('content')
<div class="container">
    <div class="d-flex justify-content-between mb-3">
        <h1>Pendataan Pegawai</h1>
        <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#tambahPegawaiModal">
            Tambah Pegawai
        </button>
    </div>

    <!-- Tampilkan feedback -->
    @if(session('success'))
        <div class="alert alert-success">
            {{ session('success') }}
        </div>
    @endif

    <!-- Tabel Data Pegawai -->
    <table class="table table-striped">
        <thead>
            <tr>
                <th>#</th>
                <th>Nama</th>
                <th>No Telepon</th>
                <th>Email</th>
                <th>Aksi</th>
            </tr>
        </thead>
        <tbody id="pegawaiTable">
            @foreach($pegawai as $item)
                <tr>
                    <td>{{ $loop->iteration }}</td>
                    <td>{{ $item->nama }}</td>
                    <td>{{ $item->no_telepon }}</td>
                    <td>{{ $item->email }}</td>
                    <td>
                        <form action="{{ route('pegawai.destroy', $item->id) }}" method="POST" style="display:inline;">
                            @csrf
                            @method('DELETE')
                            <button class="btn btn-sm btn-danger" onclick="return confirm('Hapus pegawai ini?')">Hapus</button>
                        </form>
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>
</div>

<!-- Modal Tambah Pegawai -->
<div class="modal fade" id="tambahPegawaiModal" tabindex="-1" aria-labelledby="tambahPegawaiModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <form action="{{ route('pegawai.store') }}" method="POST">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title" id="tambahPegawaiModalLabel">Tambah Pegawai</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="namaPegawai" class="form-label">Nama</label>
                        <input type="text" class="form-control" id="namaPegawai" name="nama" required>
                    </div>
                    <div class="mb-3">
                        <label for="noTeleponPegawai" class="form-label">No Telepon</label>
                        <input type="text" class="form-control" id="noTeleponPegawai" name="no_telepon" required>
                    </div>
                    <div class="mb-3">
                        <label for="emailPegawai" class="form-label">Email</label>
                        <input type="email" class="form-control" id="emailPegawai" name="email" required>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
                    <button type="submit" class="btn btn-primary">Simpan</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
