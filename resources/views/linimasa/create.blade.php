<!-- Modal -->
<div class="modal fade" id="tambahLinimasaModal" tabindex="-1" aria-labelledby="tambahLinimasaLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <form action="{{ route('linimasa.store') }}" method="POST">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title" id="tambahLinimasaLabel">Tambah Linimasa</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        

                    </div>
                    <div class="mb-3">
                        <label for="pegawai_id" class="form-label">Pegawai</label>
                        <select class="form-select" name="pegawai_id" id="pegawai_id" required>
                            @foreach ($pegawais as $pegawai)
                                <option value="{{ $pegawai->id }}">{{ $pegawai->nama }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="mb-3">
                        <label for="proyek_id" class="form-label">Proyek</label>
                        <select class="form-select" name="proyek_id" id="proyek_id" required>
                            @foreach ($proyeks as $proyek)
                                <option value="{{ $proyek->id }}">{{ $proyek->nama_proyek ?? 'Nama Tidak Tersedia' }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="mb-3">
                        <label for="tanggal_mulai" class="form-label">Tanggal Mulai</label>
                        <input type="date" class="form-control" name="tanggal_mulai" id="tanggal_mulai" required>
                    </div>
                    <div class="mb-3">
                        <label for="tanggal_deadline" class="form-label">Tenggat Waktu</label>
                        <input type="date" class="form-control" name="tanggal_deadline" id="tanggal_deadline" required>
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

<!-- Tabel Vis.js -->
<div id="linimasaTable" style="height: 400px;"></div>

<script>
    const container = document.getElementById('linimasaTable');
    const items = new vis.DataSet(@json($linimasas));

    const options = {
        editable: false,
        orientation: 'top',
    };

    const timeline = new vis.Timeline(container, items, options);
</script>