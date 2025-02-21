@extends('layouts.app')

@section('content')
<div class="container">
    <h1 style="text-align: center; margin: 20px;">Daftar Linimasa</h1>

    <!-- Alert jika ada pesan sukses -->
    @if (session('success'))
        <div class="alert alert-success">
            {{ session('success') }}
        </div>
    @endif

    <!-- Tombol Tambah Data -->
    <div class="d-flex justify-content-end mb-3">
        <a href="{{ route('linimasa.create') }}" class="btn btn-primary">Tambah Data</a>
    </div>

    <!-- Tabel Daftar Linimasa -->
    <table class="table table-striped">
        <thead>
            <tr>
                <th>No</th>
                <th>Nama Pegawai</th>
                <th>Nama Proyek</th>
                <th>Tanggal</th>
                <th>Tenggat Waktu</th>
                <th>Status</th>
                <th>Aksi</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($linimasa as $index => $item)
                <tr>
                    <td>{{ $index + 1 }}</td>
                    <td>{{ $item->nama_pegawai }}</td>
                    <td>{{ $item->nama_proyek }}</td>
                    <td>{{ $item->tanggal }}</td>
                    <td>{{ $item->tenggat_waktu }}</td>
                    <td>{{ $item->status_proyek }}</td>
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

    <!-- Timeline Vis.js -->
    <h2 style="text-align: center; margin: 20px;">Timeline Proyek Pegawai</h2>
    <div id="timeline" style="height: 600px; margin: 20px;"></div>
</div>

<!-- Skrip Vis.js -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/vis/4.21.0/vis.min.js"></script>
<link href="https://cdnjs.cloudflare.com/ajax/libs/vis/4.21.0/vis.min.css" rel="stylesheet">
<script>
    // Data kelompok (pegawai)
    const groups = {!! json_encode($linimasa->map(fn($item) => [
        'id' => $item->id,
        'content' => $item->nama_pegawai
    ])->unique('id')) !!};

    // Data proyek
    const items = {!! json_encode($linimasa->map(fn($item) => [
        'id' => $item->id,
        'group' => $item->id,
        'content' => $item->nama_proyek . ' (' . $item->status_proyek . ')',
        'start' => $item->tanggal,
        'end' => $item->tenggat_waktu,
    ])) !!};

    // Elemen container timeline
    const container = document.getElementById('timeline');

    // Opsi konfigurasi timeline
    const options = {
        stack: false,
        orientation: { axis: 'top' },
        start: new Date().toISOString().slice(0, 10), // Tanggal awal (hari ini)
        end: '2025-12-31', // Rentang waktu akhir (disesuaikan)
        margin: { item: 10 },
        zoomMin: 1000 * 60 * 60 * 24 * 7, // Minimum zoom (1 minggu)
        zoomMax: 1000 * 60 * 60 * 24 * 365, // Maksimum zoom (1 tahun)
    };

    // Render timeline
    const timeline = new vis.Timeline(container);
    timeline.setGroups(new vis.DataSet(groups));
    timeline.setItems(new vis.DataSet(items));
    timeline.setOptions(options);
</script>
@endsection
