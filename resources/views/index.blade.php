@extends('layouts.authenticated')

@section('title', 'Ringkasan — SiMonika')

@section('content')
    <header class="page-heading d-flex justify-content-between align-items-end">
        <div>
            <h1 class="mb-0">Ringkasan inventaris</h1>
            <p>Selamat datang, {{ $user->nama }}. Pantau kondisi katalog aplikasi dari satu tempat.</p>
        </div>
        <small class="text-muted" id="lastUpdate">Diperbarui {{ $lastUpdate->diffForHumans() }}</small>
    </header>

    <section class="row g-3 mb-4" aria-label="Statistik aplikasi">
        <div class="col-6 col-lg-3"><div class="metric-card"><div class="metric-label">Total aplikasi</div><div class="metric-value">{{ $jumlahAplikasiAktif + $jumlahAplikasiTidakDigunakan }}</div></div></div>
        <div class="col-6 col-lg-3"><div class="metric-card"><div class="metric-label">Aktif</div><div class="metric-value text-success">{{ $jumlahAplikasiAktif }}</div></div></div>
        <div class="col-6 col-lg-3"><div class="metric-card"><div class="metric-label">Perlu perhatian</div><div class="metric-value text-warning">{{ $jumlahAplikasiTidakDigunakan }}</div></div></div>
        <div class="col-6 col-lg-3"><div class="metric-card"><div class="metric-label">Cakupan aktif</div><div class="metric-value">{{ ($jumlahAplikasiAktif + $jumlahAplikasiTidakDigunakan) ? round($jumlahAplikasiAktif / ($jumlahAplikasiAktif + $jumlahAplikasiTidakDigunakan) * 100) : 0 }}%</div></div></div>
    </section>

    @include('templates.charts')

    <section class="panel">
        <div class="panel-header"><div><h2 class="h5 mb-1">Inventaris terbaru</h2><small class="text-muted">Maksimal 200 entri terbaru</small></div><a href="{{ route('aplikasi.index') }}" class="btn btn-outline-primary btn-sm">Kelola aplikasi</a></div>
        <div class="table-responsive">
            <table class="table table-hover mb-0">
                <thead><tr><th>Nama</th><th>Status</th><th>Jenis</th><th>Platform</th><th>Pengembang</th></tr></thead>
                <tbody>
                    @forelse ($aplikasis as $aplikasi)
                        <tr>
                            <td class="fw-semibold">{{ $aplikasi->nama }}</td>
                            <td><span class="badge {{ $aplikasi->status_pemakaian === 'Aktif' ? 'text-bg-success' : 'text-bg-secondary' }}">{{ $aplikasi->status_pemakaian }}</span></td>
                            <td>{{ $aplikasi->jenis }}</td>
                            <td>{{ $aplikasi->basis_aplikasi }}</td>
                            <td>{{ $aplikasi->pengembang }}</td>
                        </tr>
                    @empty
                        <tr><td colspan="5"><div class="empty-state"><i class="bi bi-inbox"></i>Belum ada aplikasi. Mulai dari menu Aplikasi.</div></td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </section>
@endsection

@push('scripts')
    <script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/3.7.0/chart.min.js"></script>
    <script src="{{ asset('js/index/chart.js') }}"></script>
    <script src="{{ asset('js/index/last-update.js') }}"></script>
@endpush
