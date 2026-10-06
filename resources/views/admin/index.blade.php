@extends('layouts.authenticated')

@section('title', 'Admin — SiMonika')

@section('content')
    <header class="page-heading d-flex justify-content-between align-items-end">
        <div><h1 class="mb-0">Kelola admin</h1><p>Buat dan rawat akun operator. Kredensial awal dikirim melalui email.</p></div>
        <button class="btn btn-primary" data-bs-toggle="collapse" data-bs-target="#createAdmin"><i class="bi bi-plus-lg me-1"></i>Tambah admin</button>
    </header>

    <section id="createAdmin" class="collapse mb-4">
        <div class="panel panel-body">
            <h2 class="h5 mb-3">Admin baru</h2>
            <form action="{{ route('admin.store') }}" method="POST" class="row g-3">
                @csrf
                <div class="col-md-6"><label class="form-label">Nama</label><input class="form-control" name="nama" value="{{ old('nama') }}" maxlength="100" required></div>
                <div class="col-md-6"><label class="form-label">Email</label><input class="form-control" name="email" value="{{ old('email') }}" type="email" maxlength="100" required></div>
                <div><button class="btn btn-primary">Buat dan kirim kredensial</button></div>
            </form>
        </div>
    </section>

    <section class="panel">
        <div class="panel-header"><h2 class="h5 mb-0">Akun operator</h2><span class="badge text-bg-light">{{ $admins->total() }} admin</span></div>
        <div class="table-responsive">
            <table class="table table-hover mb-0">
                <thead><tr><th>Nama</th><th>Email</th><th>Aktivitas terakhir</th><th class="text-end">Tindakan</th></tr></thead>
                <tbody>
                    @forelse ($admins as $admin)
                        <tr>
                            <td class="fw-semibold">{{ $admin->nama }}</td>
                            <td>{{ $admin->email }}</td>
                            <td>{{ $admin->last_activity?->diffForHumans() ?? 'Belum ada' }}</td>
                            <td class="text-end">
                                <button class="btn btn-outline-secondary btn-sm" data-bs-toggle="collapse" data-bs-target="#admin-{{ $admin->id_user }}">Edit</button>
                                <form action="{{ route('admin.destroy', $admin) }}" method="POST" class="d-inline" data-confirm="Akun admin akan dihapus. Log audit tetap disimpan.">@csrf @method('DELETE')<button class="btn btn-outline-danger btn-sm">Hapus</button></form>
                            </td>
                        </tr>
                        <tr class="collapse" id="admin-{{ $admin->id_user }}">
                            <td colspan="4">
                                <form action="{{ route('admin.update', $admin) }}" method="POST" class="row g-2 p-2">
                                    @csrf @method('PUT')
                                    <div class="col-md-4"><label class="form-label">Nama</label><input class="form-control" name="nama" value="{{ $admin->nama }}" required></div>
                                    <div class="col-md-4"><label class="form-label">Email</label><input class="form-control" name="email" value="{{ $admin->email }}" type="email" required></div>
                                    <div class="col-md-4"><label class="form-label">Kata sandi baru</label><input class="form-control" name="password" type="password" minlength="12"><div class="form-hint">Kosongkan jika tidak diubah.</div></div>
                                    <div><button class="btn btn-primary btn-sm">Simpan perubahan</button></div>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="4"><div class="empty-state"><i class="bi bi-person-gear"></i>Belum ada admin.</div></td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if ($admins->hasPages()) <div class="panel-body border-top">{{ $admins->links('pagination::bootstrap-5') }}</div> @endif
    </section>
@endsection
