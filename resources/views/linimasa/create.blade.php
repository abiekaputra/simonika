@extends('layouts.app') <!-- Ganti dengan layout utama proyekmu -->

@section('content')
<div class="container">
    <h1>Tambah Linimasa</h1>
    <form action="{{ route('linimasa.store') }}" method="POST">
        @csrf
        <div class="form-group">
            <label for="tanggal">Tanggal</label>
            <input type="date" id="tanggal" name="tanggal" class="form-control" required>
        </div>
        <div class="form-group">
            <label for="nama_proyek">Nama Proyek</label>
            <input type="text" id="nama_proyek" name="nama_proyek" class="form-control" required>
        </div>
        <div class="form-group">
            <label for="nama_pegawai">Nama Pegawai</label>
            <input type="text" id="nama_pegawai" name="nama_pegawai" class="form-control" required>
        </div>
        <div class="form-group">
            <label for="status_proyek">Status Proyek</label>
            <select id="status_proyek" name="status_proyek" class="form-control">
                <option value="berjalan">Berjalan</option>
                <option value="selesai">Selesai</option>
                <option value="tertunda">Tertunda</option>
            </select>
        </div>
        <div class="form-group">
            <label for="tenggat_waktu">Tenggat Waktu</label>
            <input type="date" id="tenggat_waktu" name="tenggat_waktu" class="form-control" required>
        </div>
        <button type="submit" class="btn btn-primary">Simpan</button>
    </form>
</div>
@endsection
