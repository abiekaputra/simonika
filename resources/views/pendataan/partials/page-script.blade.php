<script>
    document.addEventListener("DOMContentLoaded", () => {
        const createForm = document.getElementById("pendataanCreateForm");
        const editForm = document.getElementById("pendataanEditForm");

        function datesAreValid(startInput, endInput) {
            if (!startInput.value || !endInput.value || startInput.value < endInput.value) {
                return true;
            }

            Swal.fire({
                icon: "error",
                title: "Tanggal tidak valid",
                text: "Tanggal keluar harus setelah tanggal masuk.",
            });
            endInput.focus();

            return false;
        }

        createForm.addEventListener("submit", (event) => {
            const start = document.getElementById("tanggal_masuk");
            const end = document.getElementById("tanggal_keluar");
            if (!datesAreValid(start, end)) event.preventDefault();
        });

        editForm.addEventListener("submit", (event) => {
            const start = document.getElementById("edit_tanggal_masuk");
            const end = document.getElementById("edit_tanggal_keluar");
            if (!datesAreValid(start, end)) event.preventDefault();
        });

        document.querySelectorAll(".btn-edit").forEach((button) => {
            button.addEventListener("click", () => {
                document.getElementById("edit_universitas").value = button.dataset.universitas;
                document.getElementById("edit_jumlah_orang").value = button.dataset.jumlah_orang;
                document.getElementById("edit_tanggal_masuk").value = button.dataset.tanggal_masuk;
                document.getElementById("edit_tanggal_keluar").value = button.dataset.tanggal_keluar;
                editForm.action = editForm.dataset.baseAction.replace(":id", button.dataset.id);
            });
        });

        document.querySelectorAll(".btn-delete").forEach((button) => {
            button.addEventListener("click", async () => {
                const result = await Swal.fire({
                    title: "Hapus data magang?",
                    text: "Data yang dihapus tidak dapat dikembalikan.",
                    icon: "warning",
                    showCancelButton: true,
                    confirmButtonColor: "#d33",
                    cancelButtonColor: "#3085d6",
                    confirmButtonText: "Ya, hapus",
                    cancelButtonText: "Batal",
                });

                if (result.isConfirmed) {
                    document.getElementById(`delete-form-${button.dataset.id}`).submit();
                }
            });
        });

        const timelineContainer = document.getElementById("pendataanTimeline");
        if (!timelineContainer) return;

        const items = new vis.DataSet([
            @foreach ($pendataans as $pendataan)
                {
                    id: {{ $pendataan->id }},
                    content: @js($pendataan->universitas ?? 'Tidak Diketahui'),
                    start: @js($pendataan->tanggal_masuk),
                    end: @js($pendataan->tanggal_keluar),
                    group: {{ $loop->index + 1 }},
                    jumlahOrang: {{ $pendataan->jumlah_orang }},
                    style: "background-color: lightblue; color: black;",
                },
            @endforeach
        ]);
        const groups = new vis.DataSet([
            @foreach ($pendataans as $pendataan)
                { id: {{ $loop->index + 1 }}, content: @js($pendataan->universitas) },
            @endforeach
        ]);
        const timeline = new vis.Timeline(timelineContainer, items, groups, {
            groupOrder: "content",
            showCurrentTime: true,
            zoomable: true,
            orientation: { axis: "top" },
            margin: { item: 10, axis: 10 },
        });

        function zoom(factor) {
            const range = timeline.getWindow();
            const start = range.start.valueOf();
            const end = range.end.valueOf();
            const change = (end - start) * factor / 2;
            timeline.setWindow(start + change, end - change);
        }

        document.getElementById("zoomIn").addEventListener("click", () => zoom(0.5));
        document.getElementById("zoomOut").addEventListener("click", () => zoom(-0.5));

        document.getElementById("toggleView").addEventListener("click", (event) => {
            document.getElementById("tableContainer").classList.toggle("d-none");
            timelineContainer.classList.toggle("d-none");
            event.currentTarget.textContent = timelineContainer.classList.contains("d-none")
                ? "Tampilkan Data Magang"
                : "Tampilkan Tabel";
        });

        timeline.on("select", ({ items: selectedItems }) => {
            if (selectedItems.length === 0) return;

            const item = items.get(selectedItems[0]);
            document.getElementById("infoUniversitas").textContent = item.content;
            document.getElementById("infoJumlahOrang").textContent = item.jumlahOrang;
            document.getElementById("infoTanggalMasuk").textContent = item.start;
            document.getElementById("infoTanggalKeluar").textContent = item.end;
            bootstrap.Modal.getOrCreateInstance(document.getElementById("modalInfoPendataan")).show();
        });
    });
</script>
