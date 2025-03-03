@extends('layouts.app')

@section('content')
<div class="container">
    <h1>Pendaftaran Proyek</h1>
    <a href="#" class="btn btn-primary mb-3" data-bs-toggle="modal" data-bs-target="#tambahProyekModal">Tambah Proyek</a>

    <table class="table table-striped">
        <thead>
            <tr>
                <th>#</th>
                <th>Nama Proyek</th>
                <th>Deskripsi</th>
                <th>Aksi</th>
            </tr>
        </thead>
        <tbody>
            @foreach($proyek as $p)
            <tr>
                <td>{{ $loop->iteration }}</td>
                <td>{{ $p->nama_proyek }}</td>
                <td>{{ $p->deskripsi }}</td>
                <td>
                    <!-- Tombol Edit -->
                    <button class="btn btn-sm btn-warning" data-bs-toggle="modal" data-bs-target="#editProyekModal{{ $p->id }}">Edit</button>

                    <!-- Tombol Hapus -->
                    <form action="{{ route('proyek.destroy', $p->id) }}" method="POST" style="display: inline;">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-sm btn-danger" onclick="return confirm('Yakin ingin menghapus proyek ini?')">Hapus</button>
                    </form>
                </td>
            </tr>

            <!-- Modal Edit Proyek -->
            <div class="modal fade" id="editProyekModal{{ $p->id }}" tabindex="-1" aria-labelledby="editProyekModalLabel{{ $p->id }}" aria-hidden="true">
                <div class="modal-dialog">
                    <div class="modal-content">
                        <form action="{{ route('proyek.update', $p->id) }}" method="POST">
                            @csrf
                            @method('PUT')
                            <div class="modal-header">
                                <h5 class="modal-title" id="editProyekModalLabel{{ $p->id }}">Edit Proyek</h5>
                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                            </div>
                            <div class="modal-body">
                                <div class="mb-3">
                                    <label for="nama_proyek_{{ $p->id }}" class="form-label">Nama Proyek</label>
                                    <input type="text" class="form-control" id="nama_proyek_{{ $p->id }}" name="nama_proyek" value="{{ $p->nama_proyek }}" required>
                                </div>
                                <div class="mb-3">
                                    <label for="deskripsi_{{ $p->id }}" class="form-label">Deskripsi</label>
                                    <textarea class="form-control" id="deskripsi_{{ $p->id }}" name="deskripsi" rows="4" required>{{ $p->deskripsi }}</textarea>
                                </div>
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
                                <button type="submit" class="btn btn-primary">Simpan Perubahan</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
            @endforeach
        </tbody>
    </table>
</div>

<!-- Modal Tambah Proyek -->
<div class="modal fade" id="tambahProyekModal" tabindex="-1" aria-labelledby="tambahProyekModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <form action="{{ route('proyek.store') }}" method="POST">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title" id="tambahProyekModalLabel">Tambah Proyek</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="nama_proyek" class="form-label">Nama Proyek</label>
                        <input type="text" class="form-control" id="nama_proyek" name="nama_proyek" required>
                    </div>
                    <div class="mb-3">
                        <label for="deskripsi" class="form-label">Deskripsi</label>
                        <textarea class="form-control" id="deskripsi" name="deskripsi" rows="4"></textarea>
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
