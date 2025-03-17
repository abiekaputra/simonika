<!-- Modal Tambah Kategori -->
<div class="modal fade" id="kategoriCreateModal" tabindex="-1" aria-labelledby="kategoriCreateModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <form id="kategoriForm" method="POST" action="{{ route('kategori.store') }}">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title" id="kategoriCreateModalLabel">Tambah Kategori</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="nama_kategori" class="form-label">Nama Kategori</label>
                        <input type="text" class="form-control" id="nama_kategori" name="nama_kategori" placeholder="Masukkan nama kategori" required>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary">Simpan</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal View Kategori -->
<div class="modal fade" id="kategoriViewModal" tabindex="-1" aria-labelledby="kategoriViewModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="kategoriViewModalLabel">Daftar Kategori</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <ul class="list-group">
                    @foreach($kategori as $item)
                        <li class="list-group-item d-flex justify-content-between align-items-center">
                            {{ $item->nama_kategori }}
                            @if(isset($item->proyek) && $item->proyek->count() > 0)
                                <span class="badge bg-warning">Tidak bisa dihapus</span>
                            @else
                                <button class="btn btn-danger btn-sm btn-delete-kategori" data-id="{{ $item->id }}">Hapus</button>
                            @endif
                        </li>
                    @endforeach
                </ul>
            </div>
        </div>
    </div>
</div>

<script>
    document.addEventListener("DOMContentLoaded", function () {
        document.querySelectorAll(".btn-delete-kategori").forEach(button => {
            button.addEventListener("click", function () {
                let kategoriId = this.getAttribute("data-id");

                if (confirm("Apakah Anda yakin ingin menghapus kategori ini?")) {
                    fetch("{{ url('kategori') }}/" + kategoriId, {
                        method: "DELETE",
                        headers: {
                            "X-CSRF-TOKEN": "{{ csrf_token() }}"
                        }
                    })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            alert("Kategori berhasil dihapus!");
                            location.reload();
                        } else {
                            alert("Kategori tidak dapat dihapus karena sedang digunakan dalam proyek.");
                        }
                    })
                    .catch(error => console.error("Error:", error));
                }
            });
        });
    });
</script>
