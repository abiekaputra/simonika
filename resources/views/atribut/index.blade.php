@extends('layouts.authenticated')

@section('title', 'Atribut - SiMonika')

@section('content')
    <section id="attributePage" data-base-url="{{ url('/atribut') }}" data-application-url="{{ url('/aplikasi') }}">
        <div class="d-flex flex-wrap justify-content-between align-items-start gap-3 mb-4">
            <div>
                <h1 class="h2 mb-1">Kelola Atribut</h1>
                <p class="text-muted mb-0">Definisi atribut global dan nilainya pada setiap aplikasi.</p>
            </div>
            <button class="btn btn-primary" id="addAttributeButton" type="button">
                <i class="bi bi-plus-lg me-1"></i>Tambah atribut
            </button>
        </div>

        <div class="card mb-4">
            <div class="card-body">
                <label class="form-label" for="attributeSearch">Cari atribut</label>
                <input class="form-control" id="attributeSearch" placeholder="Nama atau tipe data">
            </div>
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead><tr><th>Nama</th><th>Tipe data</th><th>Dipakai oleh</th><th class="text-end">Aksi</th></tr></thead>
                    <tbody id="attributeRows">
                        @foreach ($atributs as $atribut)
                            <tr data-attribute-row data-search="{{ strtolower($atribut->nama_atribut.' '.$atribut->tipe_data) }}">
                                <td class="fw-semibold">{{ $atribut->nama_atribut }}</td>
                                <td><code>{{ $atribut->tipe_data }}</code></td>
                                <td>{{ $atribut->aplikasis->count() }} aplikasi</td>
                                <td class="text-end text-nowrap">
                                    <button class="btn btn-sm btn-outline-primary" data-action="detail"
                                        data-id="{{ $atribut->id_atribut }}" type="button">Detail</button>
                                    <button class="btn btn-sm btn-outline-secondary" data-action="edit"
                                        data-id="{{ $atribut->id_atribut }}" type="button">Edit</button>
                                    <button class="btn btn-sm btn-outline-danger" data-action="delete"
                                        data-id="{{ $atribut->id_atribut }}" data-name="{{ $atribut->nama_atribut }}"
                                        type="button">Hapus</button>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            <div class="card-body border-top">{{ $atributs->links('pagination::bootstrap-5') }}</div>
            <p class="text-center text-muted p-4 mb-0 d-none" id="attributeEmptyState">Tidak ada atribut yang cocok.</p>
        </div>

        <div class="card">
            <div class="card-header d-flex flex-wrap justify-content-between align-items-center gap-2">
                <div>
                    <h2 class="h5 mb-1">Aplikasi terdaftar</h2>
                    <p class="text-muted small mb-0">Lihat nilai atribut yang tersimpan pada tiap aplikasi.</p>
                </div>
                <input class="form-control application-search" id="attributeApplicationSearch" placeholder="Cari aplikasi">
            </div>
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead><tr><th>Nama</th><th>OPD</th><th>Status</th><th class="text-end">Aksi</th></tr></thead>
                    <tbody id="attributeApplicationRows">
                        @foreach ($aplikasis as $aplikasi)
                            <tr data-application-row data-search="{{ strtolower($aplikasi->nama.' '.$aplikasi->opd) }}">
                                <td class="fw-semibold">{{ $aplikasi->nama }}</td>
                                <td>{{ $aplikasi->opd }}</td>
                                <td>{{ $aplikasi->status_pemakaian }}</td>
                                <td class="text-end">
                                    <button class="btn btn-sm btn-outline-primary" data-action="application-detail"
                                        data-id="{{ $aplikasi->id_aplikasi }}" type="button">Lihat atribut</button>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </section>

    <div class="modal fade" id="attributeFormModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h2 class="modal-title fs-5" id="attributeFormTitle">Tambah Atribut</h2>
                    <button class="btn-close" data-bs-dismiss="modal" aria-label="Tutup"></button>
                </div>
                <form id="attributeForm" novalidate>
                    <div class="modal-body">
                        <div class="alert alert-danger d-none" id="attributeFormErrors"></div>
                        <div class="mb-3">
                            <label class="form-label" for="nama_atribut">Nama atribut</label>
                            <input class="form-control" id="nama_atribut" name="nama_atribut" maxlength="100" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label" for="tipe_data">Tipe data</label>
                            <select class="form-select" id="tipe_data" name="tipe_data" required>
                                <option value="varchar">Teks pendek</option>
                                <option value="text">Teks panjang</option>
                                <option value="number">Angka</option>
                                <option value="date">Tanggal</option>
                                <option value="enum">Pilihan</option>
                            </select>
                        </div>
                        <div class="d-none" id="enumOptionsSection">
                            <label class="form-label">Pilihan enum</label>
                            <div id="enumOptions"></div>
                            <button class="btn btn-sm btn-outline-secondary" id="addEnumOption" type="button">
                                Tambah pilihan
                            </button>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button class="btn btn-secondary" data-bs-dismiss="modal" type="button">Batal</button>
                        <button class="btn btn-primary" id="attributeSubmitButton" type="submit">Simpan</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <div class="modal fade" id="attributeDetailModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-scrollable">
            <div class="modal-content">
                <div class="modal-header">
                    <h2 class="modal-title fs-5" id="attributeDetailTitle">Detail Atribut</h2>
                    <button class="btn-close" data-bs-dismiss="modal" aria-label="Tutup"></button>
                </div>
                <div class="modal-body">
                    <div class="table-responsive">
                        <table class="table table-sm"><thead><tr><th>Aplikasi</th><th>Nilai</th></tr></thead>
                            <tbody id="attributeDetailRows"></tbody></table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="applicationAttributeModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-scrollable">
            <div class="modal-content">
                <div class="modal-header">
                    <h2 class="modal-title fs-5">Atribut Aplikasi</h2>
                    <button class="btn-close" data-bs-dismiss="modal" aria-label="Tutup"></button>
                </div>
                <div class="modal-body"><div id="applicationAttributeFields"></div></div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script type="module" src="{{ asset('js/atribut/index.js') }}"></script>
@endpush
