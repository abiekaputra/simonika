@extends('layouts.authenticated')

@section('title', 'Linimasa — SiMonika')

@push('styles')
    <link href="https://unpkg.com/vis-timeline@7.4.6/styles/vis-timeline-graph2d.min.css" rel="stylesheet">
@endpush

@section('content')
    <header class="page-heading d-flex justify-content-between align-items-end">
        <div><h1 class="mb-0">Linimasa proyek</h1><p>Hubungkan pegawai, proyek, status, dan target penyelesaian.</p></div>
        <button class="btn btn-primary" data-bs-toggle="collapse" data-bs-target="#createTimeline"><i class="bi bi-plus-lg me-1"></i>Tambah linimasa</button>
    </header>

    <section id="createTimeline" class="collapse mb-4">
        <div class="panel panel-body">
            <h2 class="h5 mb-3">Penugasan baru</h2>
            @if ($pegawai->isEmpty() || $proyek->isEmpty())
                <div class="alert alert-warning mb-0">Tambahkan pegawai dan proyek sebelum membuat linimasa.</div>
            @else
                <form action="{{ route('linimasa.store') }}" method="POST" class="row g-3">
                    @csrf
                    <div class="col-md-6"><label class="form-label">Pegawai</label><select class="form-select" name="pegawai_id" required><option value="">Pilih pegawai</option>@foreach ($pegawai as $item)<option value="{{ $item->id }}">{{ $item->nama }}</option>@endforeach</select></div>
                    <div class="col-md-6"><label class="form-label">Proyek</label><select class="form-select" name="proyek_id" required><option value="">Pilih proyek</option>@foreach ($proyek as $item)<option value="{{ $item->id }}">{{ $item->nama_proyek }}</option>@endforeach</select></div>
                    <div class="col-md-4"><label class="form-label">Status</label><select class="form-select" name="status_proyek" data-completion-toggle="completion-create" required>@foreach ($statuses as $status)<option value="{{ $status }}">{{ $status }}</option>@endforeach</select></div>
                    <div class="col-md-4"><label class="form-label">Mulai</label><input class="form-control" name="mulai" type="date" required></div>
                    <div class="col-md-4"><label class="form-label">Tenggat</label><input class="form-control" name="tenggat" type="date" required></div>
                    <div class="col-md-4"><label class="form-label">Tanggal selesai</label><input class="form-control" id="completion-create" name="tanggal_selesai" type="date"><div class="form-hint">Wajib untuk status selesai.</div></div>
                    <div class="col-md-8"><label class="form-label">Catatan</label><textarea class="form-control" name="deskripsi" rows="2" maxlength="5000"></textarea></div>
                    <div><button class="btn btn-primary">Simpan linimasa</button></div>
                </form>
            @endif
        </div>
    </section>

    @if ($timelineRecords->isNotEmpty())
        <section class="panel mb-4">
            <div class="panel-header"><div><h2 class="h5 mb-1">Peta waktu</h2><small class="text-muted">Geser dan zoom untuk meninjau jadwal</small></div></div>
            <div class="panel-body"><div id="timeline" class="timeline-board"></div></div>
        </section>
    @endif

    <section class="panel">
        <div class="panel-header"><h2 class="h5 mb-0">Daftar linimasa</h2><span class="badge text-bg-light">{{ $linimasa->total() }} penugasan</span></div>
        <div class="table-responsive">
            <table class="table table-hover mb-0">
                <thead><tr><th>Pegawai / proyek</th><th>Status</th><th>Periode</th><th>Selesai</th><th class="text-end">Tindakan</th></tr></thead>
                <tbody>
                    @forelse ($linimasa as $item)
                        <tr>
                            <td><strong>{{ $item->pegawai->nama }}</strong><br><small class="text-muted">{{ $item->proyek->nama_proyek }}</small></td>
                            <td><span class="badge text-bg-{{ in_array($item->status_proyek, ['Selesai Lebih Cepat', 'Tepat Waktu']) ? 'success' : ($item->status_proyek === 'Terlambat' ? 'danger' : 'primary') }}">{{ $item->status_proyek }}</span></td>
                            <td>{{ $item->mulai->format('d M Y') }}<br><small class="text-muted">hingga {{ $item->tenggat->format('d M Y') }}</small></td>
                            <td>{{ $item->tanggal_selesai?->format('d M Y') ?? '—' }}</td>
                            <td class="text-end">
                                <button class="btn btn-outline-secondary btn-sm" data-bs-toggle="collapse" data-bs-target="#timeline-{{ $item->id }}">Edit</button>
                                <form class="d-inline" action="{{ route('linimasa.destroy', $item) }}" method="POST" data-confirm="Entri linimasa ini akan dihapus.">@csrf @method('DELETE')<button class="btn btn-outline-danger btn-sm">Hapus</button></form>
                            </td>
                        </tr>
                        <tr class="collapse" id="timeline-{{ $item->id }}">
                            <td colspan="5">
                                <form action="{{ route('linimasa.update', $item) }}" method="POST" class="row g-2 p-2">
                                    @csrf @method('PUT')
                                    <div class="col-md-4"><label class="form-label">Pegawai</label><select class="form-select" name="pegawai_id" required>@foreach ($pegawai as $employee)<option value="{{ $employee->id }}" @selected($employee->id === $item->pegawai_id)>{{ $employee->nama }}</option>@endforeach</select></div>
                                    <div class="col-md-4"><label class="form-label">Proyek</label><select class="form-select" name="proyek_id" required>@foreach ($proyek as $project)<option value="{{ $project->id }}" @selected($project->id === $item->proyek_id)>{{ $project->nama_proyek }}</option>@endforeach</select></div>
                                    <div class="col-md-4"><label class="form-label">Status</label><select class="form-select" name="status_proyek" data-completion-toggle="completion-{{ $item->id }}">@foreach ($statuses as $status)<option value="{{ $status }}" @selected($status === $item->status_proyek)>{{ $status }}</option>@endforeach</select></div>
                                    <div class="col-md-4"><label class="form-label">Mulai</label><input class="form-control" name="mulai" type="date" value="{{ $item->mulai->format('Y-m-d') }}" required></div>
                                    <div class="col-md-4"><label class="form-label">Tenggat</label><input class="form-control" name="tenggat" type="date" value="{{ $item->tenggat->format('Y-m-d') }}" required></div>
                                    <div class="col-md-4"><label class="form-label">Selesai</label><input class="form-control" id="completion-{{ $item->id }}" name="tanggal_selesai" type="date" value="{{ $item->tanggal_selesai?->format('Y-m-d') }}"></div>
                                    <div class="col-12"><label class="form-label">Catatan</label><textarea class="form-control" name="deskripsi">{{ $item->deskripsi }}</textarea></div>
                                    <div><button class="btn btn-primary btn-sm">Simpan perubahan</button></div>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="5"><div class="empty-state"><i class="bi bi-calendar3"></i>Belum ada linimasa.</div></td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if ($linimasa->hasPages()) <div class="panel-body border-top">{{ $linimasa->links('pagination::bootstrap-5') }}</div> @endif
    </section>

    <script type="application/json" id="timeline-data">@json($timelineData)</script>
@endsection

@push('scripts')
    <script src="https://unpkg.com/vis-timeline@7.4.6/standalone/umd/vis-timeline-graph2d.min.js"></script>
    <script type="module" src="{{ asset('js/linimasa/index.js') }}"></script>
@endpush
