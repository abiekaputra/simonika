<!-- Modal Tambah Linimasa -->
<div class="modal fade" id="linimasaCreateModal" tabindex="-1" aria-labelledby="linimasaCreateModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <form id="linimasaForm" method="POST" action="{{ route('linimasa.store') }}">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title" id="linimasaCreateModalLabel">Tambah Linimasa</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <!-- Pilih Pegawai -->
                    <div class="mb-3">
                        <label for="pegawai_id" class="form-label">Nama Pegawai</label>
                        <select id="pegawai_id" name="pegawai_id" class="form-control" required>
                            <option value="" disabled selected>Pilih Pegawai</option>
                            @foreach ($pegawai as $p)
                                <option value="{{ $p->id }}">{{ $p->nama }}</option>
                            @endforeach
                        </select>
                    </div>

                    <!-- Pilih Proyek -->
                    <div class="mb-3">
                        <label for="proyek_id" class="form-label">Proyek</label>
                        <select class="form-select" id="proyek_id" name="proyek_id" required>
                            <option value="" disabled selected>Pilih Proyek</option>
                            @foreach ($proyek as $pr)
                                <option value="{{ $pr->id }}">{{ $pr->nama_proyek }}</option>
                            @endforeach
                        </select>
                    </div>

                    <!-- Status Proyek -->
                    <div class="mb-3">
                        <label for="status_proyek" class="form-label">Status Proyek</label>
                        <select class="form-select" id="status_proyek" name="status_proyek" required>
                            <option value="" disabled selected>Pilih Status</option>
                            <option value="Selesai Lebih Cepat">Selesai Lebih Cepat</option>
                            <option value="Tepat Waktu">Tepat Waktu</option>
                            <option value="Terlambat">Terlambat</option>
                            <option value="Revisi">Revisi</option>
                            <option value="Proses">Proses</option>
                            <option value="To Do Next">To Do Next</option>
                        </select>
                    </div>

                    <!-- Tanggal Mulai -->
                    <div class="mb-3">
                        <label for="mulai" class="form-label">Tanggal Mulai</label>
                        <input type="date" class="form-control" id="mulai" name="mulai" required>
                    </div>

                    <!-- Tanggal Tenggat -->
                    <div class="mb-3">
                        <label for="tenggat" class="form-label">Tanggal Tenggat</label>
                        <input type="date" class="form-control" id="tenggat" name="tenggat" required>
                    </div>

                    <!-- Deskripsi (Opsional) -->
                    <div class="mb-3">
                        <label for="deskripsi" class="form-label">Deskripsi (Opsional)</label>
                        <textarea class="form-control" id="deskripsi" name="deskripsi" rows="3" placeholder="Tambahkan catatan jika diperlukan"></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary">Simpan</button>
                </div>
            </form>
        </div>
    </div>
</div>
