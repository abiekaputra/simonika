<!-- resources/views/linimasa/edit.blade.php -->

@extends('layouts.app')

@section('content')
<div class="container">
    <h1>Edit Linimasa</h1>
    <form action="{{ route('linimasa.update', $linimasa->id) }}" method="POST">
        @csrf
        @method('PUT')

        <div class="mb-3">
            <label for="tanggal" class="form-label">Tanggal</label>
            <input type="date" id="tanggal" name="tanggal" value="{{ $linimasa->tanggal }}" class="form-control" required>
        </div>

        <div class="mb-3">
            <label for="nama_proyek" class="form-label">Nama Proyek</label>
            <input type="text" id="nama_proyek" name="nama_proyek" value="{{ $linimasa->nama_proyek }}" class="form-control" required>
        </div>

        <div class="mb-3">
            <label for="nama_pegawai" class="form-label">Nama Pegawai</label>
            <input type="text" id="nama_pegawai" name="nama_pegawai" value="{{ $linimasa->nama_pegawai }}" class="form-control" required>
        </div>

        <div class="mb-3">
            <label for="status_proyek" class="form-label">Status Proyek</label>
            <select id="status_proyek" name="status_proyek" class="form-control">
                <option value="berjalan" {{ $linimasa->status_proyek == 'berjalan' ? 'selected' : '' }}>Berjalan</option>
                <option value="selesai" {{ $linimasa->status_proyek == 'selesai' ? 'selected' : '' }}>Selesai</option>
                <option value="tertunda" {{ $linimasa->status_proyek == 'tertunda' ? 'selected' : '' }}>Tertunda</option>
            </select>
        </div>

        <div class="mb-3">
            <label for="tenggat_waktu" class="form-label">Tenggat Waktu</label>
            <input type="date" id="tenggat_waktu" name="tenggat_waktu" value="{{ $linimasa->tenggat_waktu }}" class="form-control" required>
        </div>

        <button type="submit" class="btn btn-primary">Update</button>
    </form>
</div>
@endsection
