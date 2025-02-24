@extends('layouts.app') 

@section('content')
<div class="container mx-auto p-6">
    <h1 class="text-center text-2xl font-bold mb-6">Tambah Linimasa</h1>
    <form action="{{ route('linimasa.store') }}" method="POST" onsubmit="return validateForm()">
        @csrf

        <div class="mb-4">
            <label for="nama_pegawai" class="block text-sm font-medium">Nama Pegawai</label>
            <input type="text" id="nama_pegawai" name="nama_pegawai" class="w-full p-2 border rounded-lg" required>
        </div>
        
        <div class="mb-4">
            <label for="nama_proyek" class="block text-sm font-medium">Nama Proyek</label>
            <input type="text" id="nama_proyek" name="nama_proyek" class="w-full p-2 border rounded-lg" required>
        </div>
        
        <div class="mb-4">
            <label for="tanggal" class="block text-sm font-medium">Tanggal Mulai</label>
            <input type="date" id="tanggal" name="tanggal" class="w-full p-2 border rounded-lg" required>
        </div>

        <div class="mb-4">
            <label for="tenggat_waktu" class="block text-sm font-medium">Tenggat Waktu</label>
            <input type="date" id="tenggat_waktu" name="tenggat_waktu" class="w-full p-2 border rounded-lg" required>
        </div>  

        <button type="submit" class="btn btn-primary">Simpan</button>
    </form>
</div>

<script>
    function validateForm() {
        let startDate = document.getElementById('tanggal').value;
        let endDate = document.getElementById('tenggat_waktu').value;

        if (new Date(endDate) < new Date(startDate)) {
            alert("Tenggat Waktu tidak boleh lebih kecil dari Tanggal Mulai.");
            return false;
        }
        return true;
    }
</script>
@endsection
