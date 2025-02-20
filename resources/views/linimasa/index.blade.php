<!-- resources/views/linimasa/index.blade.php -->

@extends('layouts.app') <!-- Pastikan ada layout utama -->

@section('content')
<div class="container">
    <h1>Linimasa Proyek</h1>
    <a href="{{ route('linimasa.create') }}" class="btn btn-primary">Tambah Linimasa</a>

    <table class="table mt-3">
        <thead>
            <tr>
                <th>Tanggal</th>
                <th>Nama Proyek</th>
                <th>Nama Pegawai</th>
                <th>Status</th>
                <th>Tenggat Waktu</th>
                <th>Aksi</th>
            </tr>
        </thead>
        <tbody>
            @foreach($linimasa as $linimasa)
            <tr>
                <td>{{ $linimasa->tanggal }}</td>
                <td>{{ $linimasa->nama_proyek }}</td>
                <td>{{ $linimasa->nama_pegawai }}</td>
                <td>{{ $linimasa->status_proyek }}</td>
                <td>{{ $linimasa->tenggat_waktu }}</td>
                <td>
                    <a href="{{ route('linimasa.edit', $linimasa->id) }}" class="btn btn-warning btn-sm">Edit</a>
                    <form action="{{ route('linimasa.destroy', $linimasa->id) }}" method="POST" class="d-inline">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-danger btn-sm" onclick="return confirm('Yakin ingin menghapus?')">Hapus</button>
                    </form>
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>
</div>
@endsection
