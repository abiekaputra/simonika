<!-- Edit Modal -->
<div class="modal fade" id="proyekEditModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Edit Proyek</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <form id="editProyekForm" method="POST">
                    @csrf
                    @method('PUT')
                    <div class="mb-3">
                        <label for="edit_nama_proyek" class="form-label">Nama Proyek:</label>
                        <input type="text" id="edit_nama_proyek" name="nama_proyek" class="form-control" required>
                    </div>

                    <div class="mb-3">
                        <label for="edit_kategori_id" class="form-label">Kategori:</label>
                        <select id="edit_kategori_id" name="kategori_id" class="form-control" required>
                            <option value="" selected disabled>Pilih Kategori</option>
                            @foreach($kategori as $item)
                                <option value="{{ $item->id }}">{{ $item->nama_kategori }}</option>
                            @endforeach
                        </select>
                    </div>
                    
                    <div class="mb-3">
                        <label for="edit_deskripsi" class="form-label">Deskripsi:</label>
                        <input type="text" id="edit_deskripsi" name="deskripsi" class="form-control" required>
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
                let nama_proyek = this.getAttribute("data-nama-proyek");
                let kategori_id = this.getAttribute("data-kategori-id");
                let deskripsi = this.getAttribute("data-deskripsi");

                editProyek(id, nama_proyek, kategori_id, deskripsi);
            });
        });

        let proyekEditModal = document.getElementById("proyekEditModal");
        proyekEditModal.addEventListener("hidden.bs.modal", function () {
            let editForm = document.getElementById("editProyekForm");
            if (editForm) {
                editForm.reset();
            }
            document.querySelectorAll(".modal-backdrop").forEach(el => el.remove());
            document.body.classList.remove("modal-open");
        });
    });

    function editProyek(id, nama_proyek, kategori_id, deskripsi) {
        document.getElementById("edit_nama_proyek").value = nama_proyek;
        document.getElementById("edit_deskripsi").value = deskripsi;

        let kategoriSelect = document.getElementById("edit_kategori_id");
        if (kategoriSelect) {
            for (let option of kategoriSelect.options) {
                if (option.value == kategori_id) {
                    option.selected = true;
                    break;
                }
            }
        }

        let form = document.getElementById("editProyekForm");
        if (form) {
            form.action = "{{ url('proyek') }}/" + id;
        }

        new bootstrap.Modal(document.getElementById("proyekEditModal")).show();
    }
</script>
