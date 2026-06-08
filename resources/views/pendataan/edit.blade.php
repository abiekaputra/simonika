<div class="modal fade" id="pendataanEditModal" tabindex="-1" aria-labelledby="pendataanEditModalLabel"
    aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="pendataanEditModalLabel">Edit Pendataan Magang</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="pendataanEditForm" method="POST" data-base-action="{{ route('pendataan.update', ':id') }}">
                    @csrf
                    @method('PUT')

                    <div class="mb-3">
                        <label for="edit-universitas" class="form-label">Universitas</label>
                        <input type="text" class="form-control" id="edit-universitas" name="universitas" required>
                    </div>
                    <div class="mb-3">
                        <label for="edit-jumlah_orang" class="form-label">Jumlah Orang</label>
                        <input type="number" class="form-control" id="edit-jumlah_orang" name="jumlah_orang" required>
                    </div>
                    <div class="mb-3">
                        <label for="edit-tanggal_masuk" class="form-label">Tanggal Masuk</label>
                        <input type="date" class="form-control" id="edit-tanggal_masuk" name="tanggal_masuk" required>
                    </div>
                    <div class="mb-3">
                        <label for="edit-tanggal_keluar" class="form-label">Tanggal Keluar</label>
                        <input type="date" class="form-control" id="edit-tanggal_keluar" name="tanggal_keluar" required>
                    </div>
                    <button type="submit" class="btn btn-primary">Update Pendataan</button>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
    document.addEventListener("DOMContentLoaded", function () {
        // Menambahkan event listener pada setiap tombol edit
        document.querySelectorAll(".btn-edit").forEach(button => {
            button.addEventListener("click", function () {
                let id = this.getAttribute("data-id");
                let universitas = this.getAttribute("data-universitas");
                let jumlah_orang = this.getAttribute("data-jumlah_orang");
                let tanggal_masuk = this.getAttribute("data-tanggal_masuk");
                let tanggal_keluar = this.getAttribute("data-tanggal_keluar");

                // Memanggil fungsi untuk mengisi modal dengan data
                editPendataan(id, universitas, jumlah_orang, tanggal_masuk, tanggal_keluar);
            });
        });

        // Menambahkan event listener untuk membersihkan form ketika modal ditutup
        let pendataanEditModal = document.getElementById("pendataanEditModal");
        pendataanEditModal.addEventListener("hidden.bs.modal", function () {
            let editForm = document.getElementById("pendataanEditForm");
            if (editForm) {
                editForm.reset(); // Mengatur ulang form
            }
            document.querySelectorAll(".modal-backdrop").forEach(el => el.remove()); // Menghapus backdrop modal
            document.body.classList.remove("modal-open"); // Menghapus class modal-open
        });
    });

    // Fungsi untuk mengisi form edit dengan data yang dipilih
    function editPendataan(id, universitas, jumlah_orang, tanggal_masuk, tanggal_keluar) {
        document.getElementById("edit_pendataan_id").value = id;
        document.getElementById("edit_universitas").value = universitas;
        document.getElementById("edit_jumlah_orang").value = jumlah_orang;
        document.getElementById("edit_tanggal_masuk").value = tanggal_masuk;
        document.getElementById("edit_tanggal_keluar").value = tanggal_keluar;

        let form = document.getElementById("pendataanEditForm");
        if (form) {
            let baseAction = form.getAttribute("data-base-action");
            form.action = baseAction.replace(":id", id);
        }

        new bootstrap.Modal(document.getElementById("pendataanEditModal")).show();
    }
</script>