@extends('layouts.authenticated')

@section('title', 'Proyek — SiMonika')

@section('content')
    <header class="page-heading d-flex justify-content-between align-items-end">
        <div><h1 class="mb-0">Proyek</h1><p>Susun portofolio pekerjaan, kategori, dan kaitannya dengan linimasa.</p></div>
        <div class="page-actions">
            <button class="btn btn-outline-primary" data-bs-toggle="collapse" data-bs-target="#createCategory">Tambah kategori</button>
            <button class="btn btn-primary" data-bs-toggle="collapse" data-bs-target="#createProject">Tambah proyek</button>
        </div>
    </header>

    <div class="row g-3 mb-4">
        <section class="col-lg-5 collapse" id="createCategory">
            <div class="panel panel-body h-100">
                <h2 class="h5">Kategori baru</h2>
                <form action="{{ route('kategori.store') }}" method="POST" class="d-flex gap-2">
                    @csrf
                    <input class="form-control" name="nama_kategori" placeholder="Contoh: Layanan Digital" maxlength="100" required>
                    <button class="btn btn-primary">Simpan</button>
                </form>
            </div>
        </section>
        <section class="col-lg-7 collapse" id="createProject">
            <div class="panel panel-body h-100">
                <h2 class="h5">Proyek baru</h2>
                @if ($kategori->isEmpty())
                    <p class="alert alert-warning mb-0">Tambahkan kategori sebelum membuat proyek.</p>
                @else
                    <form action="{{ route('proyek.store') }}" method="POST" class="row g-2">
                        @csrf
                        <div class="col-md-4"><label class="form-label">Nama proyek</label><input class="form-control" name="nama_proyek" required></div>
                        <div class="col-md-4"><label class="form-label">Kategori</label><select class="form-select" name="kategori_id" required><option value="">Pilih kategori</option>@foreach ($kategori as $item)<option value="{{ $item->id }}">{{ $item->nama_kategori }}</option>@endforeach</select></div>
                        <div class="col-md-4"><label class="form-label">Aplikasi terkait</label><select class="form-select" name="aplikasi_id"><option value="">Tidak terkait aplikasi</option>@foreach ($aplikasi as $app)<option value="{{ $app->id_aplikasi }}">{{ $app->nama }}</option>@endforeach</select></div>
                        <div class="col-12"><label class="form-label">Deskripsi</label><textarea class="form-control" name="deskripsi" rows="2" required maxlength="5000"></textarea></div>
                        <div><button class="btn btn-primary">Simpan proyek</button></div>
                    </form>
                @endif
            </div>
        </section>
    </div>

    <section class="panel mb-4">
        <div class="panel-header"><h2 class="h5 mb-0">Kategori</h2><span class="badge text-bg-light">{{ $kategori->count() }} kategori</span></div>
        <div class="panel-body d-flex gap-2 flex-wrap">
            @forelse ($kategori as $item)
                <div class="border rounded-3 p-2 d-flex align-items-center gap-2">
                    <span>{{ $item->nama_kategori }} <small class="text-muted">({{ $item->proyek_count }})</small></span>
                    <button class="btn btn-link btn-sm p-0" data-bs-toggle="collapse" data-bs-target="#category-{{ $item->id }}" aria-label="Edit {{ $item->nama_kategori }}"><i class="bi bi-pencil"></i></button>
                    @if ($item->proyek_count === 0)
                        <form action="{{ route('kategori.destroy', $item) }}" method="POST" data-confirm="Kategori kosong ini akan dihapus.">
                            @csrf @method('DELETE')
                            <button class="btn btn-link text-danger btn-sm p-0" aria-label="Hapus {{ $item->nama_kategori }}"><i class="bi bi-x-lg"></i></button>
                        </form>
                    @endif
                </div>
                <form id="category-{{ $item->id }}" class="collapse w-100 border rounded-3 p-2" action="{{ route('kategori.update', $item) }}" method="POST">
                    @csrf @method('PUT')
                    <div class="input-group input-group-sm"><input class="form-control" name="nama_kategori" value="{{ $item->nama_kategori }}" required maxlength="100"><button class="btn btn-primary">Simpan</button></div>
                </form>
            @empty
                <span class="text-muted">Belum ada kategori.</span>
            @endforelse
        </div>
    </section>

    <section class="panel">
        <div class="panel-header"><h2 class="h5 mb-0">Daftar proyek</h2><span class="badge text-bg-light">{{ $proyek->total() }} proyek</span></div>
        <div class="table-responsive">
            <table class="table table-hover mb-0">
                <thead><tr><th>Proyek</th><th>Aplikasi</th><th>Kategori</th><th>Deskripsi</th><th>Linimasa</th><th class="text-end">Tindakan</th></tr></thead>
                <tbody>
                    @forelse ($proyek as $item)
                        <tr>
                            <td class="fw-semibold">{{ $item->nama_proyek }}</td>
                            <td>{{ $item->aplikasi?->nama ?? '—' }}</td>
                            <td>{{ $item->kategori?->nama_kategori ?? 'Tanpa kategori' }}</td>
                            <td>{{ str($item->deskripsi)->limit(90) }}</td>
                            <td>{{ $item->linimasa_count }}</td>
                            <td class="text-end">
                                <button class="btn btn-outline-secondary btn-sm" data-bs-toggle="collapse" data-bs-target="#project-{{ $item->id }}">Edit</button>
                                <form action="{{ route('proyek.destroy', $item) }}" method="POST" class="d-inline" data-confirm="Proyek dan seluruh linimasa terkait akan dihapus.">
                                    @csrf @method('DELETE')
                                    <button class="btn btn-outline-danger btn-sm">Hapus</button>
                                </form>
                            </td>
                        </tr>
                        <tr class="collapse" id="project-{{ $item->id }}">
                            <td colspan="6">
                                <form action="{{ route('proyek.update', $item) }}" method="POST" class="row g-2 p-2">
                                    @csrf @method('PUT')
                                    <div class="col-md-3"><label class="form-label">Nama</label><input class="form-control" name="nama_proyek" value="{{ $item->nama_proyek }}" required></div>
                                    <div class="col-md-3"><label class="form-label">Kategori</label><select class="form-select" name="kategori_id" required>@foreach ($kategori as $category)<option value="{{ $category->id }}" @selected($category->id === $item->kategori_id)>{{ $category->nama_kategori }}</option>@endforeach</select></div>
                                    <div class="col-md-3"><label class="form-label">Aplikasi terkait</label><select class="form-select" name="aplikasi_id"><option value="">Tidak terkait aplikasi</option>@foreach ($aplikasi as $app)<option value="{{ $app->id_aplikasi }}" @selected($app->id_aplikasi === $item->aplikasi_id)>{{ $app->nama }}</option>@endforeach</select></div>
                                    <div class="col-md-3"><label class="form-label">Deskripsi</label><textarea class="form-control" name="deskripsi" required>{{ $item->deskripsi }}</textarea></div>
                                    <div><button class="btn btn-primary btn-sm">Simpan perubahan</button></div>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="6"><div class="empty-state"><i class="bi bi-kanban"></i>Belum ada proyek.</div></td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if ($proyek->hasPages()) <div class="panel-body border-top">{{ $proyek->links('pagination::bootstrap-5') }}</div> @endif
    </section>
@endsection
