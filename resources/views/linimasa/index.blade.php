@extends('layouts.app')

@section('content')
<div class="container">
    <h1 style="text-align: center; margin: 20px;">Linimasa Proyek Pegawai</h1>

    <!-- Tombol Tambah Data -->
    <div class="d-flex justify-content-end mb-3">
        <a href="{{ route('linimasa.create') }}" class="btn btn-primary">Tambah Data</a>
    </div>

    @if ($linimasa->isNotEmpty()) 
        <!-- Tombol untuk Mengubah Tampilan -->
        <div class="d-flex justify-content-center mb-3">
            <button id="toggleView" class="btn btn-secondary">Tampilkan Tabel Linimasa</button>
        </div>

        <!-- Daftar Linimasa -->
        <div id="daftarLinimasa" style="display: none;">
            <table class="table table-striped">
                <thead>
                    <tr>
                        <th>No</th>
                        <th>Nama Pegawai</th>
                        <th>Nama Proyek</th>
                        <th>Tanggal Mulai</th>
                        <th>Tenggat Waktu</th>
                        <th>Status</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($linimasa as $index => $item)
                    @php
                        $today = now();
                        $tenggat = \Carbon\Carbon::parse($item->tenggat_waktu);
                        $sisaHari = $today->diffInDays($tenggat, false);
                        $keterangan = $sisaHari >= 0 ? "Sisa $sisaHari hari" : "Terlambat " . abs($sisaHari) . " hari";

                        $status = $item->status_proyek;
                        $warna = match ($status) {
                            'Berjalan' => 'primary',
                            'Selesai' => 'success',
                            'Terlambat' => 'danger',
                            'Selesai (Terlambat)' => 'warning',
                            default => 'secondary'
                        };
                    @endphp
                    <tr>
                        <td>{{ $index + 1 }}</td>
                        <td>{{ $item->nama_pegawai }}</td>
                        <td>
                            <span class="badge bg-{{ $warna }}" data-bs-toggle="tooltip" title="{{ $keterangan }}">
                                {{ $item->nama_proyek }}
                            </span>
                        </td>
                        <td>{{ $item->tanggal }}</td>
                        <td>{{ $item->tenggat_waktu }}</td>
                        <td>
                            <span class="badge bg-{{ $warna }}">{{ $status }}</span>
                        </td>
                        <td>
                            <!-- Tombol Edit hanya muncul jika proyek belum selesai -->
                            @if (!in_array($item->status_proyek, ['Selesai', 'Selesai (Terlambat)']))
                                <a href="{{ route('linimasa.edit', $item->id) }}" class="btn btn-warning btn-sm">Edit</a>
                            @endif
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

        <!-- Timeline Vis.js -->
        <div id="tabelLinimasa">
            <div id="timeline" style="height: 600px; margin: 20px;"></div>
        </div>
    @else
        <!-- Jika Tidak Ada Data -->
        <div class="alert alert-info text-center">Belum ada proyek dalam linimasa.</div>
    @endif
</div>

<!-- CSS untuk Vis.js agar warna sesuai dengan tabel -->
<style>
    .blue { background-color: #007bff !important; color: white; }  /* Berjalan */
    .green { background-color: #28a745 !important; color: white; } /* Selesai */
    .red { background-color: #dc3545 !important; color: white; }   /* Terlambat */
    .orange { background-color: #ffc107 !important; color: black; } /* Selesai (Terlambat) */
    .gray { background-color: #6c757d !important; color: white; }  /* Default */
</style>

@if ($linimasa->isNotEmpty())
    <!-- Skrip Bootstrap Tooltip & Toggle View -->
    <script>
        document.addEventListener("DOMContentLoaded", function () {
            let tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
            let tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
                return new bootstrap.Tooltip(tooltipTriggerEl);
            });

            let toggleButton = document.getElementById("toggleView");
            let daftarLinimasa = document.getElementById("daftarLinimasa");
            let tabelLinimasa = document.getElementById("tabelLinimasa");

            toggleButton.addEventListener("click", function () {
                if (daftarLinimasa.style.display === "none") {
                    daftarLinimasa.style.display = "block";
                    tabelLinimasa.style.display = "none";
                    toggleButton.textContent = "Tampilkan Linimasa Proyek";
                } else {
                    daftarLinimasa.style.display = "none";
                    tabelLinimasa.style.display = "block";
                    toggleButton.textContent = "Tampilkan Tabel Linimasa";
                }
            });
        });
    </script>

    <!-- Skrip Vis.js -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/vis/4.21.0/vis.min.js"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/vis/4.21.0/vis.min.css" rel="stylesheet">
    <script>
        // Data kelompok (pegawai)
        const groups = {!! json_encode($linimasa->map(fn($item) => [
            'id' => $item->id,
            'content' => $item->nama_pegawai
        ])->unique('id')) !!};

        // Data proyek dengan warna yang sesuai dengan tabel
        const items = {!! json_encode($linimasa->map(fn($item) => [
            'id' => $item->id,
            'group' => $item->id,
            'content' => $item->nama_proyek,
            'start' => $item->tanggal,
            'end' => $item->tenggat_waktu,
            'className' => match ($item->status_proyek) {
                'Berjalan' => 'blue',
                'Selesai' => 'green',
                'Terlambat' => 'red',
                'Selesai (Terlambat)' => 'orange',
                default => 'gray'
            },
            'style' => match ($item->status_proyek) {
                'Berjalan' => 'background-color: #007bff; color: white;',
                'Selesai' => 'background-color: #28a745; color: white;',
                'Terlambat' => 'background-color: #dc3545; color: white;',
                'Selesai (Terlambat)' => 'background-color: #ffc107; color: black;',
                default => 'background-color: #6c757d; color: white;'
            }
        ])) !!};

        // Elemen container timeline
        const container = document.getElementById('timeline');

        // Opsi konfigurasi timeline
        const options = {
            stack: false,
            orientation: { axis: 'top' },
            start: new Date().toISOString().slice(0, 10),
            end: '2025-12-31',
            margin: { item: 10 },
            zoomMin: 1000 * 60 * 60 * 24 * 7,
            zoomMax: 1000 * 60 * 60 * 24 * 365,
        };

        // Render timeline
        const timeline = new vis.Timeline(container);
        timeline.setGroups(new vis.DataSet(groups));
        timeline.setItems(new vis.DataSet(items));
        timeline.setOptions(options);
    </script>
@endif
@endsection
