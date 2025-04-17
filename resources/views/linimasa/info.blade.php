<div class="modal fade" id="modalInfoLinimasa" tabindex="-1" aria-labelledby="modalInfoLabel" aria-hidden="true">
    <div class="modal-dialog modal-md">
        <div class="modal-content">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title" id="modalInfoLabel">Detail Linimasa</h5>
                <button type="button" class="btn-close text-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>

            <div class="modal-body">
                <div class="row">
                    <div class="col-12">
                        <p><strong>Pegawai:</strong> <span id="infoNamaPegawai"></span></p>
                        <p><strong>Proyek:</strong> <span id="infoNamaProyek"></span></p>
                        <p><strong>Mulai:</strong> <span id="infoMulai"></span></p>
                        <p><strong>Tenggat:</strong> <span id="infoTenggat"></span></p>
                        <p><strong>Status:</strong> <span id="infoStatus"></span></p>
                        <p><strong>Deskripsi:</strong> <span id="infoDeskripsi"></span></p>
                    </div>
                </div>
            </div>
            
            @foreach ($linimasa as $item)
            <div class="modal-footer d-flex justify-content-between">
                <button class="btn btn-warning btn-sm btn-edit"
                    data-id="{{ $item->id }}"
                    data-pegawai="{{ $item->pegawai->id }}"
                    data-proyek="{{ $item->proyek->id }}"
                    data-status="{{ $item->status_proyek }}"
                    data-mulai="{{ $item->mulai }}"
                    data-tenggat="{{ $item->tenggat }}"
                    data-deskripsi="{{ $item->deskripsi ?? '' }}"
                    data-bs-toggle="modal"
                    data-bs-target="#linimasaEditModal">
                    <i class="bi bi-pencil-square"></i>
                </button>

                <button class="btn btn-danger btn-delete" data-id="{{ $item->id }}">
                    <i class="bi bi-trash"></i>
                </button>

                <form id="delete-form-{{ $item->id }}" action="{{ route('linimasa.destroy', $item->id) }}" method="POST" style="display: none;">
                    @csrf
                    @method('DELETE')
                </form>
            </div>
            @endforeach
        </div>
    </div>
</div>