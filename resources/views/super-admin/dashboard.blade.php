@extends ('layouts.authenticated')

@section ('title', 'Audit Sistem — SiMonika')

@section ('content')
    <header class="page-heading d-flex justify-content-between align-items-end">
        <div>
            <h1 class="mb-0">Audit sistem</h1>
            <p>Pantau akses operator dan perubahan penting dalam produk.</p>
        </div>
        <a href="{{ route('super-admin.log.export') }}" class="btn btn-outline-primary"
            ><i class="bi bi-download me-1"></i>Ekspor CSV</a
        >
    </header>

    <section class="row g-3 mb-4" aria-label="Statistik tata kelola">
        <div class="col-6 col-lg-3">
            <div class="metric-card">
                <div class="metric-label">Admin</div>
                <div class="metric-value">{{ $total_admin }}</div>
            </div>
        </div>
        <div class="col-6 col-lg-3">
            <div class="metric-card">
                <div class="metric-label">Aplikasi</div>
                <div class="metric-value">{{ $total_aplikasi }}</div>
            </div>
        </div>
        <div class="col-6 col-lg-3">
            <div class="metric-card">
                <div class="metric-label">Aktif</div>
                <div class="metric-value text-success">{{ $aplikasi_aktif }}</div>
            </div>
        </div>
        <div class="col-6 col-lg-3">
            <div class="metric-card">
                <div class="metric-label">Perlu perhatian</div>
                <div class="metric-value text-warning">{{ $aplikasi_tidak_aktif }}</div>
            </div>
        </div>
    </section>

    <section class="panel mb-4">
        <div class="panel-header"><h2 class="h5 mb-0">Status operator</h2></div>
        <div class="table-responsive">
            <table class="table table-hover mb-0">
                <thead>
                    <tr>
                        <th>Admin</th>
                        <th>Status</th>
                        <th>Aktivitas terakhir</th>
                        <th>Aksi terakhir</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($admin_aktif as $admin)
                        <tr>
                            <td>
                                <strong>{{ $admin['nama'] }}</strong><br /><small
                                    class="text-muted"
                                    >{{ $admin['email'] }}</small
                                >
                            </td>
                            <td>
                                <span
                                    class="badge {{ $admin['status'] === 'Online' ? 'text-bg-success' : 'text-bg-secondary' }}"
                                    >{{ $admin['status'] }}</span
                                >
                            </td>
                            <td>{{ $admin['last_activity']?->diffForHumans() ?? 'Belum ada' }}</td>
                            <td>{{ $admin['last_action'] }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4">
                                <div class="empty-state">Belum ada akun admin.</div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </section>

    <section class="panel">
        <div class="panel-header">
            <h2 class="h5 mb-0">Log aktivitas</h2>
            <small class="text-muted">Riwayat tetap tersedia setelah akun dihapus</small>
        </div>
        <div class="table-responsive">
            <table class="table table-hover mb-0">
                <thead>
                    <tr>
                        <th>Waktu</th>
                        <th>Pelaku</th>
                        <th>Aktivitas</th>
                        <th>Modul</th>
                        <th>Detail</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($log_aktivitas as $log)
                        <tr>
                            <td class="text-nowrap">
                                {{ $log->created_at->timezone('Asia/Jakarta')->format('d M Y H:i') }}
                            </td>
                            <td>{{ $log->user?->nama ?? 'Akun dihapus' }}</td>
                            <td>
                                <span
                                    class="badge text-bg-{{ $log->tipe_aktivitas === 'create' ? 'success' : ($log->tipe_aktivitas === 'delete' ? 'danger' : 'secondary') }}"
                                    >{{ $log->aktivitas }}</span
                                >
                            </td>
                            <td>{{ $log->modul }}</td>
                            <td>{{ $log->detail }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5">
                                <div class="empty-state">
                                    <i class="bi bi-journal-text"></i>Belum ada aktivitas.
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if ($log_aktivitas->hasPages())
            <div class="panel-body border-top">
                {{ $log_aktivitas->links('pagination::bootstrap-5') }}
            </div>
        @endif
    </section>
@endsection
