<!-- Modal Edit Linimasa -->
<div class="modal fade" id="linimasaEditModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Edit Linimasa</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <form id="editLinimasaForm" method="POST">
                    @csrf
                    @method('PUT')
                    <input type="hidden" id="edit_linimasa_id">
                    
                    <div class="mb-3">
                        <label for="edit_pegawai_id" class="form-label">Nama Pegawai:</label>
                        <select id="edit_pegawai_id" name="pegawai_id" class="form-control" required>
                            <option value="" selected disabled>Pilih Pegawai</option>
                            @foreach($pegawai as $p)
                                <option value="{{ $p->id }}">{{ $p->nama }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="mb-3">
                        <label for="edit_proyek_id" class="form-label">Nama Proyek:</label>
                        <select id="edit_proyek_id" name="proyek_id" class="form-control" required>
                            <option value="" selected disabled>Pilih Proyek</option>
                            @foreach($proyek as $pr)
                                <option value="{{ $pr->id }}">{{ $pr->nama_proyek }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="mb-3">
                        <label for="edit_status_proyek" class="form-label">Status Proyek:</label>
                        <select id="edit_status_proyek" name="status_proyek" class="form-control" required>
                            <option value="Selesai Lebih Cepat">Selesai Lebih Cepat</option>
                            <option value="Tepat Waktu">Tepat Waktu</option>
                            <option value="Terlambat">Terlambat</option>
                            <option value="Revisi">Revisi</option>
                            <option value="Proses">Proses</option>
                            <option value="To Do Next">To Do Next</option>
                        </select>
                    </div>

                    <div class="mb-3">
                        <label for="edit_mulai" class="form-label">Mulai:</label>
                        <input type="date" id="edit_mulai" name="mulai" class="form-control" required>
                    </div>

                    <div class="mb-3">
                        <label for="edit_tenggat" class="form-label">Tenggat:</label>
                        <input type="date" id="edit_tenggat" name="tenggat" class="form-control" required>
                    </div>

                    <div class="mb-3">
                        <label for="edit_deskripsi" class="form-label">Deskripsi:</label>
                        <textarea id="edit_deskripsi" name="deskripsi" class="form-control"></textarea>
                    </div>

                    <div class="text-end">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                        <button type="submit" class="btn btn-primary">Simpan</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
    document.addEventListener("DOMContentLoaded", function () {
        document.querySelectorAll(".btn-edit").forEach(button => {
            button.addEventListener("click", function () {
                let id = this.getAttribute("data-id");
                let pegawai_id = this.getAttribute("data-pegawai");
                let proyek_id = this.getAttribute("data-proyek");
                let status = this.getAttribute("data-status");
                let mulai = this.getAttribute("data-mulai");
                let tenggat = this.getAttribute("data-tenggat");
                let deskripsi = this.getAttribute("data-deskripsi");

                editLinimasa(id, pegawai_id, proyek_id, status, mulai, tenggat, deskripsi);
            });
        });

        let linimasaEditModal = document.getElementById("linimasaEditModal");
        linimasaEditModal.addEventListener("hidden.bs.modal", function () {
            let editForm = document.getElementById("editLinimasaForm");
            if (editForm) {
                editForm.reset();
            }
            document.querySelectorAll(".modal-backdrop").forEach(el => el.remove());
            document.body.classList.remove("modal-open");
        });
    });

    function editLinimasa(id, pegawai_id, proyek_id, status, mulai, tenggat, deskripsi) {
        document.getElementById("edit_linimasa_id").value = id;
        document.getElementById("edit_mulai").value = mulai;
        document.getElementById("edit_tenggat").value = tenggat;
        document.getElementById("edit_deskripsi").value = deskripsi;

        let pegawaiSelect = document.getElementById("edit_pegawai_id");
        let proyekSelect = document.getElementById("edit_proyek_id");
        let statusSelect = document.getElementById("edit_status_proyek");

        if (pegawaiSelect) pegawaiSelect.value = pegawai_id;
        if (proyekSelect) proyekSelect.value = proyek_id;
        if (statusSelect) statusSelect.value = status;

        let form = document.getElementById("editLinimasaForm");
        if (form) {
            form.action = "{{ url('linimasa') }}/" + id;
        }

        new bootstrap.Modal(document.getElementById("linimasaEditModal")).show();
    }
</script>
