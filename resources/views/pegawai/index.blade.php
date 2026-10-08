@extends ('layouts.authenticated')

@section ('title', 'Pegawai — SiMonika')

@section ('content')
    <header class="page-heading d-flex justify-content-between align-items-end">
        <div>
            <h1 class="mb-0">Pegawai</h1>
            <p>Kelola kontak pegawai yang dapat ditugaskan ke linimasa proyek.</p>
        </div>
        <button class="btn btn-primary" data-bs-toggle="collapse" data-bs-target="#createEmployee">
            <i class="bi bi-plus-lg me-1"></i>Tambah pegawai
        </button>
    </header>

    <section id="createEmployee" class="collapse mb-4">
        <div class="panel panel-body">
            <h2 class="h5 mb-3">Pegawai baru</h2>
            <form action="{{ route('pegawai.store') }}" method="POST" class="row g-3">
                @csrf
                <div class="col-md-4">
                    <label class="form-label" for="nama">Nama</label
                    ><input
                        class="form-control"
                        id="nama"
                        name="nama"
                        value="{{ old('nama') }}"
                        required
                        maxlength="255"
                    />
                </div>
                <div class="col-md-4">
                    <label class="form-label" for="nomor_telepon">Nomor telepon</label
                    ><input
                        class="form-control"
                        id="nomor_telepon"
                        name="nomor_telepon"
                        value="{{ old('nomor_telepon') }}"
                        required
                        pattern="\+?[0-9]{9,15}"
                    />
                </div>
                <div class="col-md-4">
                    <label class="form-label" for="email">Email</label
                    ><input
                        class="form-control"
                        id="email"
                        name="email"
                        value="{{ old('email') }}"
                        type="email"
                        required
                    />
                </div>
                <div><button class="btn btn-primary" type="submit">Simpan pegawai</button></div>
            </form>
        </div>
    </section>

    <section class="panel">
        <div class="panel-header">
            <h2 class="h5 mb-0">Daftar pegawai</h2>
            <span class="badge text-bg-light">{{ $pegawai->total() }} orang</span>
        </div>
        <div class="table-responsive">
            <table class="table table-hover mb-0">
                <thead>
                    <tr>
                        <th>Nama</th>
                        <th>Kontak</th>
                        <th>Penugasan</th>
                        <th class="text-end">Tindakan</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($pegawai as $item)
                        <tr>
                            <td class="fw-semibold">{{ $item->nama }}</td>
                            <td>
                                <a href="mailto:{{ $item->email }}">{{ $item->email }}</a
                                ><br /><small class="text-muted">{{ $item->nomor_telepon }}</small>
                            </td>
                            <td>{{ $item->linimasa_count }} linimasa</td>
                            <td class="text-end">
                                <button
                                    class="btn btn-outline-secondary btn-sm"
                                    data-bs-toggle="collapse"
                                    data-bs-target="#employee-{{ $item->id }}"
                                >
                                    Edit
                                </button>
                                <form
                                    action="{{ route('pegawai.destroy', $item) }}"
                                    method="POST"
                                    class="d-inline"
                                    data-confirm="Linimasa terkait juga akan dihapus."
                                >
                                    @csrf
                                    @method ('DELETE')
                                    <button class="btn btn-outline-danger btn-sm" type="submit">
                                        Hapus
                                    </button>
                                </form>
                            </td>
                        </tr>
                        <tr class="collapse" id="employee-{{ $item->id }}">
                            <td colspan="4">
                                <form
                                    action="{{ route('pegawai.update', $item) }}"
                                    method="POST"
                                    class="row g-2 p-2"
                                >
                                    @csrf
                                    @method ('PUT')
                                    <div class="col-md-4">
                                        <label class="form-label">Nama</label
                                        ><input
                                            class="form-control"
                                            name="nama"
                                            value="{{ $item->nama }}"
                                            required
                                        />
                                    </div>
                                    <div class="col-md-4">
                                        <label class="form-label">Telepon</label
                                        ><input
                                            class="form-control"
                                            name="nomor_telepon"
                                            value="{{ $item->nomor_telepon }}"
                                            required
                                            pattern="\+?[0-9]{9,15}"
                                        />
                                    </div>
                                    <div class="col-md-4">
                                        <label class="form-label">Email</label
                                        ><input
                                            class="form-control"
                                            name="email"
                                            value="{{ $item->email }}"
                                            type="email"
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
                                    <i class="bi bi-people"></i>Belum ada pegawai.
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if ($pegawai->hasPages())
            <div class="panel-body border-top">
                {{ $pegawai->links('pagination::bootstrap-5') }}
            </div>
        @endif
    </section>
@endsection
