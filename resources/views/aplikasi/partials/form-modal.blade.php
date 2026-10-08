<div class="modal fade" id="applicationFormModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-xl modal-dialog-scrollable">
        <div class="modal-content">
            <div class="modal-header">
                <h2 class="modal-title fs-5" id="applicationFormTitle">Tambah Aplikasi</h2>
                <button
                    type="button"
                    class="btn-close"
                    data-bs-dismiss="modal"
                    aria-label="Tutup"
                ></button>
            </div>
            <form id="applicationForm" novalidate>
                <div class="modal-body">
                    <div
                        id="applicationFormErrors"
                        class="alert alert-danger d-none"
                        role="alert"
                    ></div>
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label" for="nama">Nama aplikasi</label>
                            <input
                                class="form-control"
                                id="nama"
                                name="nama"
                                maxlength="255"
                                required
                            />
                        </div>
                        <div class="col-md-6">
                            <label class="form-label" for="opd">OPD</label>
                            <input
                                class="form-control"
                                id="opd"
                                name="opd"
                                maxlength="255"
                                required
                            />
                        </div>
                        <div class="col-12">
                            <label class="form-label" for="uraian">Uraian</label>
                            <textarea
                                class="form-control"
                                id="uraian"
                                name="uraian"
                                rows="3"
                                maxlength="10000"
                            ></textarea>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label" for="tahun_pembuatan"
                                >Tanggal pembuatan</label
                            >
                            <input
                                class="form-control"
                                id="tahun_pembuatan"
                                name="tahun_pembuatan"
                                type="date"
                                required
                            />
                        </div>
                        <div class="col-md-6">
                            <label class="form-label" for="jenis">Jenis</label>
                            <input
                                class="form-control"
                                id="jenis"
                                name="jenis"
                                maxlength="255"
                                required
                            />
                        </div>
                        <div class="col-md-6">
                            <label class="form-label" for="basis_aplikasi">Basis aplikasi</label>
                            <select
                                class="form-select"
                                id="basis_aplikasi"
                                name="basis_aplikasi"
                                required
                            >
                                <option value="">Pilih basis</option>
                                <option value="Website">Website</option>
                                <option value="Desktop">Desktop</option>
                                <option value="Mobile">Mobile</option>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label" for="bahasa_framework"
                                >Bahasa/framework</label
                            >
                            <input
                                class="form-control"
                                id="bahasa_framework"
                                name="bahasa_framework"
                                required
                            />
                        </div>
                        <div class="col-md-4">
                            <label class="form-label" for="database">Database</label>
                            <input class="form-control" id="database" name="database" required />
                        </div>
                        <div class="col-md-4">
                            <label class="form-label" for="pengembang">Pengembang</label>
                            <input
                                class="form-control"
                                id="pengembang"
                                name="pengembang"
                                required
                            />
                        </div>
                        <div class="col-md-4">
                            <label class="form-label" for="lokasi_server">Lokasi server</label>
                            <input
                                class="form-control"
                                id="lokasi_server"
                                name="lokasi_server"
                                required
                            />
                        </div>
                        <div class="col-md-6">
                            <label class="form-label" for="status_pemakaian"
                                >Status pemakaian</label
                            >
                            <select
                                class="form-select"
                                id="status_pemakaian"
                                name="status_pemakaian"
                                required
                            >
                                <option value="Aktif">Aktif</option>
                                <option value="Tidak Aktif">Tidak Aktif</option>
                            </select>
                        </div>
                    </div>

                    @if ($atributs->isNotEmpty())
                        <hr class="my-4" />
                        <h3 class="fs-6">Atribut tambahan</h3>
                        <div class="row">
                            @foreach ($atributs as $atribut)
                                <div class="col-md-6">
                                    @include ('aplikasi.partials.attribute-field', ['atribut' => $atribut])
                                </div>
                            @endforeach
                        </div>
                    @endif
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                        Batal
                    </button>
                    <button type="submit" class="btn btn-primary" id="applicationSubmitButton">
                        Simpan
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
