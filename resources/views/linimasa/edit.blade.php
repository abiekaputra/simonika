@extends('layouts.app')

@section('content')
    <div class="container">
        <h1 class="mb-4">Edit Linimasa</h1>

        @if ($errors->any())
            <div class="alert alert-danger">
                <ul>
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form action="{{ route('linimasa.update', $linimasa->id) }}" method="POST">
            @csrf
            @method('PUT')

            <div class="mb-3">
                <label for="nama_pegawai" class="form-label">Nama Pegawai</label>
                <input type="text" id="nama_pegawai" name="nama_pegawai" 
                       value="{{ old('nama_pegawai', $linimasa->nama_pegawai) }}" 
                       class="form-control" readonly>
            </div>

            <div class="mb-3">
                <label for="nama_proyek" class="form-label">Nama Proyek</label>
                <input type="text" id="nama_proyek" name="nama_proyek" 
                       value="{{ old('nama_proyek', $linimasa->nama_proyek) }}" 
                       class="form-control" readonly>
            </div>

            <div class="mb-3">
                <label for="tanggal" class="form-label">Tanggal Mulai</label>
                <input type="date" id="tanggal" name="tanggal" 
                       value="{{ old('tanggal', $linimasa->tanggal) }}" 
                       class="form-control" readonly>
            </div>

            <div class="mb-3">
                <label for="tenggat_waktu" class="form-label">Tenggat Waktu</label>
                <input type="date" id="tenggat_waktu" name="tenggat_waktu" 
                       value="{{ old('tenggat_waktu', $linimasa->tenggat_waktu) }}" 
                       class="form-control" required>
            </div>

            <div class="mb-3">
                <label class="form-label">Status Proyek</label>
                <input type="text" class="form-control" value="{{ $linimasa->status_proyek }}" readonly>
            </div>

            <button type="submit" class="btn btn-primary">Update</button>
            <a href="{{ route('linimasa.index') }}" class="btn btn-secondary">Batal</a>
        </form>

        <!-- Form untuk menandai proyek sebagai selesai -->
        @if (is_null($linimasa->tanggal_selesai))
            <form action="{{ route('linimasa.complete', $linimasa->id) }}" method="POST" class="mt-3">
                @csrf
                <button type="submit" class="btn btn-success">Tandai Selesai</button>
            </form>
        @endif
    </div>
@endsection
