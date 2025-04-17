@extends('layouts.app')

@section('content')
@include('templates/sidebar')
<div class="container">
    <h1>Halaman Pendataan</h1>

    <!-- Form Input Data Pendataan -->
    <form id="pendataanForm" action="{{ route('pendataan.store') }}" method="POST">
        @csrf
        <input type="hidden" id="id" name="id"> <!-- Hidden ID untuk edit -->
        <div class="mb-3">
            <label class="form-label">Universitas</label>
            <input type="text" name="universitas" id="universitas" class="form-control" required>
        </div>
        <div class="mb-3">
            <label class="form-label">Jumlah Orang</label>
            <input type="number" name="jumlah_orang" id="jumlah_orang" class="form-control" required>
        </div>
        <div class="mb-3">
            <label class="form-label">Tanggal Masuk</label>
            <input type="date" name="tanggal_masuk" id="tanggal_masuk" class="form-control" required>
        </div>
        <div class="mb-3">
            <label class="form-label">Tanggal Keluar</label>
            <input type="date" name="tanggal_keluar" id="tanggal_keluar" class="form-control" required>
        </div>
        <button type="submit" class="btn btn-primary" id="saveBtn">Simpan</button>
        <button type="button" class="btn btn-warning d-none" id="editBtn">Edit</button>
        <button type="button" class="btn btn-danger d-none" id="deleteBtn">Hapus</button>
    </form>

    <!-- Container untuk Timeline -->
    <h2 class="mt-4">Timeline Data Magang</h2>
    <div class="timeline-container">
        <div id="timeline"></div>
    </div>

    <!-- Tombol Zoom -->
    <div class="zoom-controls text-center mt-3">
        <button id="zoomIn" class="btn btn-secondary" type="button">Zoom In</button>
        <button id="zoomOut" class="btn btn-secondary" type="button">Zoom Out</button>
    </div>
</div>

<!-- Tambahkan CSS agar halaman bisa di-scroll -->
<style>
    .timeline-container {
        width: 100%;
        max-height: 500px; /* Tambahkan batas tinggi agar bisa di-scroll */
        overflow-y: auto; /* Tambahkan scroll jika data banyak */
        border: 1px solid #ddd;
        margin-top: 20px;
    }
    #timeline {
        min-height: 500px;
    }
</style>

@endsection

@push('scripts')
<script src="https://unpkg.com/vis-timeline@7.4.5/standalone/umd/vis-timeline-graph2d.min.js"></script>
<script>
document.addEventListener("DOMContentLoaded", function () {
    const container = document.getElementById("timeline");

    // Ambil elemen tombol
    const editBtn = document.getElementById("editBtn");
    const deleteBtn = document.getElementById("deleteBtn");
    const saveBtn = document.getElementById("saveBtn");
    const form = document.getElementById("pendataanForm");

    // Ambil data dari Blade
    let items = new vis.DataSet([
        @foreach ($pendataans as $index => $pendataan)
            {
                id: {{ $pendataan->id }},
                content: "{{ $pendataan->universitas }} ({{ $pendataan->jumlah_orang }} orang)",
                start: new Date("{{ $pendataan->tanggal_masuk }}"),
                end: new Date("{{ $pendataan->tanggal_keluar }}"),
                type: "range",
                subgroup: {{ $index }}
            },
        @endforeach
    ]);

    // Hitung tinggi timeline berdasarkan jumlah item
    let newHeight = Math.max(500, items.length * 50) + "px";
    container.style.height = newHeight;

    // Inisialisasi timeline
    const timeline = new vis.Timeline(container, items, {
        locale: "id",
        height: newHeight,
        stack: false, // Agar tidak bertumpuk
        margin: { item: 15 }, // Jarak antar item diperbesar
        orientation: { axis: "top", item: "top" },
        horizontalScroll: true,
        zoomable: true,
        maxHeight: 900,
    });

    // Event Zoom In/Out
    document.getElementById("zoomIn").addEventListener("click", function () {
        timeline.zoomIn(0.5);
    });
    document.getElementById("zoomOut").addEventListener("click", function () {
        timeline.zoomOut(0.5);
    });

    // Event saat item di timeline diklik
    timeline.on("select", function (properties) {
        if (properties.items.length > 0) {
            let selectedId = properties.items[0];
            let selectedItem = items.get(selectedId);

            // Isi form dengan data yang dipilih
            document.getElementById("id").value = selectedItem.id;
            document.getElementById("universitas").value = selectedItem.content.split(" (")[0];
            document.getElementById("jumlah_orang").value = selectedItem.content.match(/\d+/)[0];
            document.getElementById("tanggal_masuk").value = selectedItem.start.toISOString().split("T")[0];
            document.getElementById("tanggal_keluar").value = selectedItem.end.toISOString().split("T")[0];

            // Tampilkan tombol Edit dan Hapus
            editBtn.classList.remove("d-none");
            deleteBtn.classList.remove("d-none");
            saveBtn.classList.add("d-none"); // Sembunyikan tombol Simpan
        }
    });

    // Fungsi Edit
    editBtn.addEventListener("click", function () {
        let id = document.getElementById("id").value;
        if (!id) {
            alert("Pilih data yang ingin diedit!");
            return;
        }

        form.action = "/pendataan/update/" + id; // Pastikan ID masuk ke URL
        let methodInput = document.createElement("input");
        methodInput.type = "hidden";
        methodInput.name = "_method";
        methodInput.value = "PUT";
        form.appendChild(methodInput);
        form.submit();
    });

    // Fungsi Hapus
    deleteBtn.addEventListener("click", function () {
        let id = document.getElementById("id").value;
        if (!id) {
            alert("Pilih data yang ingin dihapus!");
            return;
        }

        let confirmDelete = confirm("Apakah Anda yakin ingin menghapus data ini?");
        if (confirmDelete) {
            let deleteForm = document.createElement("form");
            deleteForm.action = "/pendataan/delete/" + id;
            deleteForm.method = "POST";
            deleteForm.innerHTML = `
                @csrf
                <input type="hidden" name="_method" value="DELETE">
            `;
            document.body.appendChild(deleteForm);
            deleteForm.submit();
        }
    });
});
</script>
@endpush
