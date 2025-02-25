@extends('layouts.app')

@section('content')
<div class="container">
    <h1 style="text-align: center; margin: 20px;">Linimasa Proyek Pegawai</h1>

    <!-- Tombol Tambah Linimasa -->
    <button id="btnTambahLinimasa" class="btn btn-primary">
        Tambah Linimasa
    </button>

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
                            <button class="btn btn-warning btn-sm edit-btn" 
                                    data-id="{{ $item->id }}" 
                                    data-nama-pegawai="{{ $item->nama_pegawai }}"
                                    data-nama-proyek="{{ $item->nama_proyek }}"
                                    data-tanggal="{{ $item->tanggal }}"
                                    data-tenggat-waktu="{{ $item->tenggat_waktu }}"
                                    data-status-proyek="{{ $item->status_proyek }}"
                                    data-tanggal-selesai="{{ $item->tanggal_selesai }}"
                                    data-bs-toggle="modal" 
                                    data-bs-target="#editModal">
                                Edit
                            </button>
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

<!-- Modal Tambah Linimasa -->
<div class="modal fade" id="createLinimasaModal" tabindex="-1" aria-labelledby="createLinimasaLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="createLinimasaLabel">Tambah Linimasa</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form action="{{ route('linimasa.store') }}" method="POST">
                    @csrf
                    <div class="mb-3">
                        <label for="nama_pegawai" class="form-label">Nama Pegawai</label>
                        <input type="text" class="form-control" id="nama_pegawai" name="nama_pegawai" required>
                    </div>
                    <div class="mb-3">
                        <label for="nama_proyek" class="form-label">Nama Proyek</label>
                        <input type="text" class="form-control" id="nama_proyek" name="nama_proyek" required>
                    </div>
                    <div class="mb-3">
                        <label for="tanggal" class="form-label">Tanggal Mulai</label>
                        <input type="date" class="form-control" id="tanggal" name="tanggal" required>
                    </div>
                    <div class="mb-3">
                        <label for="tenggat_waktu" class="form-label">Tenggat Waktu</label>
                        <input type="date" class="form-control" id="tenggat_waktu" name="tenggat_waktu" required>
                    </div>
                    <button type="submit" class="btn btn-primary">Simpan</button>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
    document.addEventListener("DOMContentLoaded", function () {
        document.getElementById("btnTambahLinimasa").addEventListener("click", function () {
            var myModal = new bootstrap.Modal(document.getElementById('createLinimasaModal'));
            myModal.show();
        });
    });
</script>

<!-- Modal Edit Linimasa -->
<div class="modal fade" id="editModal" tabindex="-1" aria-labelledby="editModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="editModalLabel">Edit Linimasa</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="editLinimasaForm" action="" method="POST">
                    @csrf
                    @method('PUT')

                    <input type="hidden" id="editId" name="id">

                    <div class="mb-3">
                        <label for="editNamaPegawai" class="form-label">Nama Pegawai</label>
                        <input type="text" id="editNamaPegawai" name="nama_pegawai" class="form-control" readonly>
                    </div>

                    <div class="mb-3">
                        <label for="editNamaProyek" class="form-label">Nama Proyek</label>
                        <input type="text" id="editNamaProyek" name="nama_proyek" class="form-control" readonly>
                    </div>

                    <div class="mb-3">
                        <label for="editTanggalMulai" class="form-label">Tanggal Mulai</label>
                        <input type="date" id="editTanggalMulai" name="tanggal" class="form-control" readonly>
                    </div>

                    <div class="mb-3">
                        <label for="editTenggatWaktu" class="form-label">Tenggat Waktu</label>
                        <input type="date" id="editTenggatWaktu" name="tenggat_waktu" class="form-control" required>
                    </div>

                    <div class="mb-3">
                        <label for="editStatusProyek" class="form-label">Status Proyek</label>
                        <input type="text" id="editStatusProyek" name="status_proyek" class="form-control" readonly>
                    </div>

                    <button type="submit" class="btn btn-primary">Update</button>
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                </form>

                <!-- Tombol Tandai Selesai -->
                <form id="completeLinimasaForm" action="" method="POST" class="mt-3 d-none">
                    @csrf
                    <button type="submit" class="btn btn-success">Tandai Selesai</button>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
    document.addEventListener("DOMContentLoaded", function () {
        let editButtons = document.querySelectorAll(".edit-btn");

        editButtons.forEach(button => {
            button.addEventListener("click", function () {
                let id = this.getAttribute("data-id");
                let namaPegawai = this.getAttribute("data-nama-pegawai");
                let namaProyek = this.getAttribute("data-nama-proyek");
                let tanggalMulai = this.getAttribute("data-tanggal");
                let tenggatWaktu = this.getAttribute("data-tenggat-waktu");
                let statusProyek = this.getAttribute("data-status-proyek");
                let tanggalSelesai = this.getAttribute("data-tanggal-selesai");

                document.getElementById("editId").value = id;
                document.getElementById("editNamaPegawai").value = namaPegawai;
                document.getElementById("editNamaProyek").value = namaProyek;
                document.getElementById("editTanggalMulai").value = tanggalMulai;
                document.getElementById("editTenggatWaktu").value = tenggatWaktu;
                document.getElementById("editStatusProyek").value = statusProyek;

                document.getElementById("editLinimasaForm").action = "{{ url('/linimasa') }}/" + id;

                let completeForm = document.getElementById("completeLinimasaForm");
                if (tanggalSelesai === "null" || tanggalSelesai === "") {
                    completeForm.classList.remove("d-none");
                    completeForm.action = `/linimasa/${id}/complete`;
                } else {
                    completeForm.classList.add("d-none");
                }
            });
        });
    });
</script>

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
