@extends('layouts.authenticated')

@section('title', 'Profil — SiMonika')

@section('content')
    <header class="page-heading">
        <h1 class="mb-0">Profil akun</h1>
        <p>Perbarui nama tampilan dan lindungi akses akun Anda.</p>
    </header>

    <div class="row g-4">
        <section class="col-lg-4">
            <div class="panel panel-body text-center h-100">
                <i class="bi bi-person-circle d-block text-primary mb-3" style="font-size:4rem"></i>
                <h2 class="h5">{{ auth()->user()->nama }}</h2>
                <p class="text-muted mb-1">{{ auth()->user()->email }}</p>
                <span class="badge text-bg-light text-capitalize">{{ str_replace('_', ' ', auth()->user()->role) }}</span>
                <hr>
                <small class="text-muted">Akun diperbarui {{ auth()->user()->updated_at->diffForHumans() }}</small>
            </div>
        </section>

        <div class="col-lg-8">
            <section class="panel mb-4">
                <div class="panel-header"><h2 class="h5 mb-0">Informasi dasar</h2></div>
                <div class="panel-body">
                    <form action="{{ route('profile.update') }}" method="POST" class="row g-3">
                        @csrf @method('PUT')
                        <div class="col-md-7"><label class="form-label" for="profile-name">Nama lengkap</label><input class="form-control" id="profile-name" name="nama" value="{{ old('nama', auth()->user()->nama) }}" required maxlength="100"></div>
                        <div class="col-md-5"><label class="form-label">Email</label><input class="form-control" value="{{ auth()->user()->email }}" disabled><div class="form-hint">Email dikelola oleh super admin.</div></div>
                        <div><button class="btn btn-primary">Simpan profil</button></div>
                    </form>
                </div>
            </section>

            <section class="panel">
                <div class="panel-header"><h2 class="h5 mb-0">Ganti kata sandi</h2></div>
                <div class="panel-body">
                    <form action="{{ route('profile.updatePassword') }}" method="POST" class="row g-3">
                        @csrf @method('PUT')
                        <div class="col-12"><label class="form-label" for="current-password">Kata sandi saat ini</label><input class="form-control" id="current-password" name="current_password" type="password" autocomplete="current-password" required></div>
                        <div class="col-md-6"><label class="form-label" for="new-password">Kata sandi baru</label><input class="form-control" id="new-password" name="password" type="password" autocomplete="new-password" minlength="12" required><div class="form-hint">Minimal 12 karakter, mengandung huruf dan angka.</div></div>
                        <div class="col-md-6"><label class="form-label" for="confirm-password">Konfirmasi kata sandi</label><input class="form-control" id="confirm-password" name="password_confirmation" type="password" autocomplete="new-password" required></div>
                        <div><button class="btn btn-outline-primary">Perbarui kata sandi</button></div>
                    </form>
                </div>
            </section>
        </div>
    </div>
@endsection
