<div class="modal fade" id="applicationDetailModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-scrollable">
        <div class="modal-content">
            <div class="modal-header">
                <h2 class="modal-title fs-5">Detail Aplikasi</h2>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Tutup"></button>
            </div>
            <div class="modal-body">
                <dl class="row mb-4" id="applicationDetails"></dl>
                <h3 class="fs-6">Atribut tambahan</h3>
                <div class="table-responsive">
                    <table class="table table-sm">
                        <thead><tr><th>Nama</th><th>Nilai</th></tr></thead>
                        <tbody id="applicationAttributeDetails"></tbody>
                    </table>
                </div>
                <h3 class="fs-6 mt-4">Proyek terkait</h3>
                <div class="table-responsive">
                    <table class="table table-sm">
                        <thead><tr><th>Proyek</th><th>Kategori</th><th>Linimasa</th></tr></thead>
                        <tbody id="applicationProjectDetails"></tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
