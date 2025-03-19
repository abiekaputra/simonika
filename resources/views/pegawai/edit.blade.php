<!-- Edit Modal -->
<div class="modal fade" id="pegawaiEditModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Edit Pegawai</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <form id="editPegawaiForm" method="POST">
                    @csrf
                    @method('PUT')
                    <div class="mb-3">
                        <label for="edit_nama" class="form-label">Nama Pegawai:</label>
                        <input type="text" id="edit_nama" name="nama" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label for="edit_nomor_telepon" class="form-label">Nomor Telepon:</label>
                        <input type="text" id="edit_nomor_telepon" name="nomor_telepon" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label for="edit_email" class="form-label">Email:</label>
                        <input type="email" id="edit_email" name="email" class="form-control" required>
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
        // Event listener untuk tombol edit
        document.querySelectorAll(".btn-edit").forEach(button => {
            button.addEventListener("click", function () {
                let id = this.getAttribute("data-id");
                let nama = this.getAttribute("data-nama");
                let nomor_telepon = this.getAttribute("data-telepon");
                let email = this.getAttribute("data-email");

                editPegawai(id, nama, nomor_telepon, email);
            });
        });

        // Event listener saat modal ditutup
        let pegawaiEditModal = document.getElementById("pegawaiEditModal");
        pegawaiEditModal.addEventListener("hidden.bs.modal", function () {
            let editForm = document.getElementById("editPegawaiForm");
            if (editForm) {
                editForm.reset(); // Reset form setelah modal tertutup
            }

            // Hapus backdrop yang masih tersisa dan pastikan body tidak dalam state modal-open
            document.querySelectorAll(".modal-backdrop").forEach(el => el.remove());
            document.body.classList.remove("modal-open");
        });
    });

    function editPegawai(id, nama, nomor_telepon, email) {
        document.getElementById("edit_nama").value = nama;
        document.getElementById("edit_nomor_telepon").value = nomor_telepon;
        document.getElementById("edit_email").value = email;

        let form = document.getElementById("editPegawaiForm");
        if (form) {
            form.action = "{{ url('pegawai') }}/" + id;
        }

        new bootstrap.Modal(document.getElementById("pegawaiEditModal")).show();
    }
</script>
