@extends ('layouts.authenticated')

@section ('title', 'Program Magang — SiMonika')

@push ('styles')
    <link
        href="https://unpkg.com/vis-timeline@7.4.6/styles/vis-timeline-graph2d.min.css"
        rel="stylesheet"
    />
@endpush

@section ('content')
    <header class="page-heading d-flex justify-content-between align-items-end">
        <div>
            <h1 class="mb-0">Program magang</h1>
            <p>Catat periode dan kapasitas peserta tanpa menyimpan identitas personal.</p>
        </div>
        <button
            class="btn btn-primary"
            data-bs-toggle="collapse"
            data-bs-target="#createInternship"
        >
            <i class="bi bi-plus-lg me-1"></i>Tambah periode
        </button>
    </header>

    <section id="createInternship" class="collapse mb-4">
        <div class="panel panel-body">
            <h2 class="h5 mb-3">Periode magang baru</h2>
            <form action="{{ route('pendataan.store') }}" method="POST" class="row g-3">
                @csrf
                <div class="col-md-5">
                    <label class="form-label">Institusi</label
                    ><input
                        class="form-control"
                        name="universitas"
                        value="{{ old('universitas') }}"
                        required
                        maxlength="255"
                    />
                </div>
                <div class="col-md-2">
                    <label class="form-label">Peserta</label
                    ><input
                        class="form-control"
                        name="jumlah_orang"
                        value="{{ old('jumlah_orang', 1) }}"
                        type="number"
                        min="1"
                        required
                    />
                </div>
                <div class="col-md-2">
                    <label class="form-label">Mulai</label
                    ><input class="form-control" name="tanggal_masuk" type="date" required />
                </div>
                <div class="col-md-3">
                    <label class="form-label">Selesai</label
                    ><input class="form-control" name="tanggal_keluar" type="date" required />
                </div>
                <div><button class="btn btn-primary">Simpan periode</button></div>
            </form>
        </div>
    </section>

    @if ($timelineRecords->isNotEmpty())
        <section class="panel mb-4">
            <div class="panel-header"><h2 class="h5 mb-0">Kalender program</h2></div>
            <div class="panel-body">
                <div id="internship-timeline" class="timeline-board"></div>
            </div>
        </section>
    @endif

    <section class="panel">
        <div class="panel-header">
            <h2 class="h5 mb-0">Riwayat periode</h2>
            <span class="badge text-bg-light">{{ $pendataans->total() }} periode</span>
        </div>
        <div class="table-responsive">
            <table class="table table-hover mb-0">
                <thead>
                    <tr>
                        <th>Institusi</th>
                        <th>Peserta</th>
                        <th>Periode</th>
                        <th class="text-end">Tindakan</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($pendataans as $item)
                        <tr>
                            <td class="fw-semibold">{{ $item->universitas }}</td>
                            <td>{{ $item->jumlah_orang }} orang</td>
                            <td>
                                {{ $item->tanggal_masuk->format('d M Y') }} — {{ $item->tanggal_keluar->format('d M Y') }}
                            </td>
                            <td class="text-end">
                                <button
                                    class="btn btn-outline-secondary btn-sm"
                                    data-bs-toggle="collapse"
                                    data-bs-target="#internship-{{ $item->id }}"
                                >
                                    Edit
                                </button>
                                <form
                                    class="d-inline"
                                    action="{{ route('pendataan.destroy', $item) }}"
                                    method="POST"
                                    data-confirm="Periode magang ini akan dihapus."
                                >
                                    @csrf
                                    @method ('DELETE')
                                    <button class="btn btn-outline-danger btn-sm">Hapus</button>
                                </form>
                            </td>
                        </tr>
                        <tr class="collapse" id="internship-{{ $item->id }}">
                            <td colspan="4">
                                <form
                                    action="{{ route('pendataan.update', $item) }}"
                                    method="POST"
                                    class="row g-2 p-2"
                                >
                                    @csrf
                                    @method ('PUT')
                                    <div class="col-md-5">
                                        <label class="form-label">Institusi</label
                                        ><input
                                            class="form-control"
                                            name="universitas"
                                            value="{{ $item->universitas }}"
                                            required
                                        />
                                    </div>
                                    <div class="col-md-2">
                                        <label class="form-label">Peserta</label
                                        ><input
                                            class="form-control"
                                            name="jumlah_orang"
                                            value="{{ $item->jumlah_orang }}"
                                            type="number"
                                            min="1"
                                            required
                                        />
                                    </div>
                                    <div class="col-md-2">
                                        <label class="form-label">Mulai</label
                                        ><input
                                            class="form-control"
                                            name="tanggal_masuk"
                                            value="{{ $item->tanggal_masuk->format('Y-m-d') }}"
                                            type="date"
                                            required
                                        />
                                    </div>
                                    <div class="col-md-3">
                                        <label class="form-label">Selesai</label
                                        ><input
                                            class="form-control"
                                            name="tanggal_keluar"
                                            value="{{ $item->tanggal_keluar->format('Y-m-d') }}"
                                            type="date"
                                            required
                                        />
                                    </div>
                                    <div>
                                        <button class="btn btn-primary btn-sm">
                                            Simpan perubahan
                                        </button>
                                    </div>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4">
                                <div class="empty-state">
                                    <i class="bi bi-mortarboard"></i>Belum ada periode magang.
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if ($pendataans->hasPages())
            <div class="panel-body border-top">
                {{ $pendataans->links('pagination::bootstrap-5') }}
            </div>
        @endif
    </section>

    <script type="application/json" id="internship-data">
        @json($timelineData)
    </script>
@endsection

@push ('scripts')
    <script src="https://unpkg.com/vis-timeline@7.4.6/standalone/umd/vis-timeline-graph2d.min.js"></script>
    <script type="module" src="{{ asset('js/pendataan/index.js') }}"></script>
@endpush
