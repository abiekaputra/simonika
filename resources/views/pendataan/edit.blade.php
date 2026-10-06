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
                        <label for="edit_universitas" class="form-label">Universitas</label>
                        <input type="text" class="form-control" id="edit_universitas" name="universitas" required>
                    </div>
                    <div class="mb-3">
                        <label for="edit_jumlah_orang" class="form-label">Jumlah Orang</label>
                        <input type="number" class="form-control" id="edit_jumlah_orang" name="jumlah_orang" required>
                    </div>
                    <div class="mb-3">
                        <label for="edit_tanggal_masuk" class="form-label">Tanggal Masuk</label>
                        <input type="date" class="form-control" id="edit_tanggal_masuk" name="tanggal_masuk" required>
                    </div>
                    <div class="mb-3">
                        <label for="edit_tanggal_keluar" class="form-label">Tanggal Keluar</label>
                        <input type="date" class="form-control" id="edit_tanggal_keluar" name="tanggal_keluar" required>
                    </div>
                    <button type="submit" class="btn btn-primary">Update Pendataan</button>
                </form>
            </div>
        </div>
    </div>
</div>
