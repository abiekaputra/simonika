@extends ('layouts.authenticated')

@section ('title', 'Aplikasi - SiMonika')

@section ('content')
    <section id="applicationPage" data-base-url="{{ url('/aplikasi') }}">
        <div class="d-flex flex-wrap justify-content-between align-items-start gap-3 mb-4">
            <div>
                <h1 class="h2 mb-1">Kelola Aplikasi</h1>
                <p class="text-muted mb-0">Inventaris aplikasi beserta atribut operasionalnya.</p>
            </div>
            <div class="d-flex gap-2">
                <a href="{{ route('aplikasi.export') }}" class="btn btn-outline-primary">
                    <i class="bi bi-download me-1"></i>Export CSV
                </a>
                <button class="btn btn-primary" id="addApplicationButton" type="button">
                    <i class="bi bi-plus-lg me-1"></i>Tambah aplikasi
                </button>
            </div>
        </div>

        <div class="card mb-4">
            <div class="card-body">
                <div class="row g-3">
                    <div class="col-lg-6">
                        <label class="form-label" for="applicationSearch">Cari</label>
                        <div class="input-group">
                            <span class="input-group-text"><i class="bi bi-search"></i></span>
                            <input
                                class="form-control"
                                id="applicationSearch"
                                placeholder="Nama, OPD, atau teknologi"
                            />
                        </div>
                    </div>
                    <div class="col-md-3">
                        <label class="form-label" for="applicationStatusFilter">Status</label>
                        <select class="form-select" id="applicationStatusFilter">
                            <option value="">Semua status</option>
                            <option value="Aktif">Aktif</option>
                            <option value="Tidak Aktif">Tidak aktif</option>
                        </select>
                    </div>
                    <div class="col-md-3">
                        <label class="form-label" for="applicationBasisFilter">Basis</label>
                        <select class="form-select" id="applicationBasisFilter">
                            <option value="">Semua basis</option>
                            <option value="Website">Website</option>
                            <option value="Desktop">Desktop</option>
                            <option value="Mobile">Mobile</option>
                        </select>
                    </div>
                </div>
            </div>
        </div>

        <div class="card">
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead>
                            <tr>
                                <th>Nama</th>
                                <th>OPD</th>
                                <th>Basis</th>
                                <th>Teknologi</th>
                                <th>Proyek</th>
                                <th>Status</th>
                                <th class="text-end">Aksi</th>
                            </tr>
                        </thead>
                        <tbody id="applicationRows">
                            @foreach ($aplikasis as $aplikasi)
                                <tr
                                    data-application-row
                                    data-search="{{ strtolower($aplikasi->nama.' '.$aplikasi->opd.' '.$aplikasi->bahasa_framework) }}"
                                    data-status="{{ $aplikasi->status_pemakaian }}"
                                    data-basis="{{ $aplikasi->basis_aplikasi }}"
                                >
                                    <td class="fw-semibold">{{ $aplikasi->nama }}</td>
                                    <td>{{ $aplikasi->opd }}</td>
                                    <td>{{ $aplikasi->basis_aplikasi }}</td>
                                    <td>{{ $aplikasi->bahasa_framework }}</td>
                                    <td>{{ $aplikasi->proyeks_count }}</td>
                                    <td>
                                        <span
                                            class="badge {{ $aplikasi->status_pemakaian === 'Aktif' ? 'text-bg-success' : 'text-bg-secondary' }}"
                                        >
                                            {{ $aplikasi->status_pemakaian }}
                                        </span>
                                    </td>
                                    <td class="text-end text-nowrap">
                                        <button
                                            class="btn btn-sm btn-outline-primary"
                                            type="button"
                                            data-action="detail"
                                            data-id="{{ $aplikasi->id_aplikasi }}"
                                        >
                                            Detail
                                        </button>
                                        <button
                                            class="btn btn-sm btn-outline-secondary"
                                            type="button"
                                            data-action="edit"
                                            data-id="{{ $aplikasi->id_aplikasi }}"
                                        >
                                            Edit
                                        </button>
                                        <button
                                            class="btn btn-sm btn-outline-danger"
                                            type="button"
                                            data-action="delete"
                                            data-id="{{ $aplikasi->id_aplikasi }}"
                                            data-name="{{ $aplikasi->nama }}"
                                        >
                                            Hapus
                                        </button>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                <p class="text-center text-muted p-4 mb-0 d-none" id="applicationEmptyState">Tidak ada aplikasi yang cocok dengan filter.</p>
            </div>
        </div>
    </section>

    @include ('aplikasi.partials.form-modal')
    @include ('aplikasi.partials.detail-modal')
@endsection

@push ('scripts')
    <script type="module" src="{{ asset('js/aplikasi/index.js') }}"></script>
@endpush
