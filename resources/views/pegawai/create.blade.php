<div class="modal fade" id="pegawaiCreateModal" tabindex="-1" aria-labelledby="createPegawaiModalLabel">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="createPegawaiModalLabel">Tambah Pegawai</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <form id="formCreatePegawai" action="{{ route('pegawai.store') }}" method="POST">
                    @csrf
                    <div class="mb-3">
                        <label for="nama" class="form-label">Nama Pegawai:</label>
                        <input type="text" id="nama" name="nama" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label for="nomor_telepon" class="form-label">Nomor Telepon:</label>
                        <input type="text" id="nomor_telepon" name="nomor_telepon" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label for="email" class="form-label">Email:</label>
                        <input type="email" id="email" name="email" class="form-control" required>
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
            var myModal = new bootstrap.Modal(document.getElementById('pegawaiCreateModal'));
            myModal.show();
        });
    });
</script>