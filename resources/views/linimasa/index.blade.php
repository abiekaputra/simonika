@extends('layouts.app')

@section('content')
<div class="container">
    <h1>Daftar Linimasa</h1>

    @if (session('success'))
        <div class="alert alert-success">
            {{ session('success') }}
        </div>
    @endif

    <table class="table table-striped">
        <thead>
            <tr>
                <th>No</th>
                <th>Nama Proyek</th>
                <th>Nama Pegawai</th>
                <th>Status</th>
                <th>Tanggal</th>
                <th>Tenggat Waktu</th>
                <th>Aksi</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($linimasa as $index => $item)
                <tr>
                    <td>{{ $index + 1 }}</td>
                    <td>{{ $item->nama_proyek }}</td>
                    <td>{{ $item->nama_pegawai }}</td>
                    <td>{{ $item->status_proyek }}</td>
                    <td>{{ $item->tanggal }}</td>
                    <td>{{ $item->tenggat_waktu }}</td>
                    <td>
                        <!-- Tombol Edit -->
                        <a href="{{ route('linimasa.edit', $item->id) }}" class="btn btn-warning btn-sm">Edit</a>
                        
                        <!-- Formulir Hapus -->
                        <form action="{{ route('linimasa.destroy', $item->id) }}" method="POST" style="display: inline-block;" onsubmit="return confirm('Apakah Anda yakin ingin menghapus data ini?');">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-danger btn-sm">Hapus</button>
                        </form>
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>
</div>
@endsection
