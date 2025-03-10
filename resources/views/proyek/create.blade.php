<div class="modal fade" id="proyekCreateModal" tabindex="-1" aria-labelledby="createProyekModalLabel">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="createProyekModalLabel">Tambah Proyek</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <form id="formCreateProyek" action="{{ route('proyek.store') }}" method="POST">
                    @csrf
                    <div class="mb-3">
                        <label for="nama_proyek" class="form-label">Nama Proyek:</label>
                        <input type="text" id="nama_proyek" name="nama_proyek" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label for="deskripsi" class="form-label">Deskripsi:</label>
                        <input type="text" id="deskripsi" name="deskripsi" class="form-control" required>
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
        document.getElementById("btnCreate").addEventListener("click", function () {
            var myModal = new bootstrap.Modal(document.getElementById('proyekCreateModal'));
            myModal.show();
        });
    });
</script>