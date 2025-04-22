<div class="modal fade" id="modalInfoPendataan" tabindex="-1" aria-labelledby="modalInfoPendataanLabel"
    aria-hidden="true">
    <div class="modal-dialog modal-md">
        <div class="modal-content">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title" id="modalInfoPendataanLabel">Detail Pendataan Magang</h5>
                <button type="button" class="btn-close text-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>

            <div class="modal-body">
                <div class="row">
                    <div class="col-12">
                        <p><strong>Universitas:</strong> <span id="infoUniversitas"></span></p>
                        <p><strong>Jumlah Orang:</strong> <span id="infoJumlahOrang"></span></p>
                        <p><strong>Tanggal Masuk:</strong> <span id="infoTanggalMasuk"></span></p>
                        <p><strong>Tanggal Keluar:</strong> <span id="infoTanggalKeluar"></span></p>
                    </div>
                </div>
            </div>

            <!-- Modal Footer -->
            <div class="modal-footer d-flex justify-content-between">
                <button class="btn btn-warning btn-sm btn-edit" data-id="{{ $pendataan->id }}"
                    data-universitas="{{ $pendataan->universitas }}" data-jumlah_orang="{{ $pendataan->jumlah_orang }}"
                    data-tanggal_masuk="{{ $pendataan->tanggal_masuk }}"
                    data-tanggal_keluar="{{ $pendataan->tanggal_keluar }}" data-bs-toggle="modal"
                    data-bs-target="#pendataanEditModal">
                    <i class="bi bi-pencil-square"></i>
                </button>

                <button class="btn btn-danger btn-sm btn-delete" id="btnDeletePendataan">
                    <i class="bi bi-trash"></i> Hapus
                </button>

                <!-- Form Delete -->
                <form id="delete-form" action="" method="POST" style="display: none;">
                    @csrf
                    @method('DELETE')
                </form>
            </div>
        </div>
    </div>
</div>