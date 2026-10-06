    <script>
        document.addEventListener("DOMContentLoaded", function () {
            let toggleButton = document.getElementById("toggleView");
            if (toggleButton) {
                toggleButton.addEventListener("click", function () {
                    document.getElementById("tableContainer").classList.toggle("d-none");
                    document.getElementById("timelineContainer").classList.toggle("d-none");
                    this.textContent = this.textContent.includes("Tabel") ? "Tampilkan Linimasa" : "Tampilkan Tabel";
                });
            }

            let container = document.getElementById("timeline");
            if (container) {
                let zoomStep = 0.5;

            document.getElementById('zoomIn').addEventListener('click', function () {
                let currentRange = timeline.getWindow();
                let start = currentRange.start.valueOf();
                let end = currentRange.end.valueOf();
                let interval = end - start;
                let newInterval = interval * (1 - zoomStep);
                let newStart = start + (interval - newInterval) / 2;
                let newEnd = end - (interval - newInterval) / 2;
                timeline.setWindow(newStart, newEnd);
            });

            document.getElementById('zoomOut').addEventListener('click', function () {
                let currentRange = timeline.getWindow();
                let start = currentRange.start.valueOf();
                let end = currentRange.end.valueOf();
                let interval = end - start;
                let newInterval = interval * (1 + zoomStep);
                let newStart = start - (newInterval - interval) / 2;
                let newEnd = end + (newInterval - interval) / 2;
                timeline.setWindow(newStart, newEnd);
            });

            let items = new vis.DataSet([
                @foreach ($linimasaAll as $item)
                            {
                                id: {{ $item->id }},
                                content: @js($item->proyek->nama_proyek),
                                start: @js($item->mulai),
                                end: @js($item->tenggat),
                                group: {{ $item->pegawai->id }},
                                subgroup: {{ $loop->index + 1 }},
                                status: @js($item->status_proyek),
                                deskripsi: @js($item->deskripsi ?? 'Tidak ada deskripsi'),
                                pegawai: @js($item->pegawai->nama),
                                proyek: @js($item->proyek->nama_proyek),
                                style: "background-color: {{
                    match ($item->status_proyek) {
                        'Selesai Lebih Cepat' => 'green; color: white;',
                        'Tepat Waktu' => 'lightgreen; color: black;',
                        'Terlambat' => 'red; color: white;',
                        'Revisi' => 'orange; color: black;',
                        'Proses' => 'blue; color: white;',
                        'To Do Next' => 'gray; color: white;',
                        default => 'lightgray; color: black;',
                    }
                                }}"
                    },
                @endforeach
        ]);

        let groups = new vis.DataSet([
            @foreach ($pegawai as $p)
                    {
                    id: {{ $p->id }},
                    content: @js($p->nama)
                },
            @endforeach
        ]);

        let options = {
            groupOrder: "content",
            stack: false,
            subgroupOrder: "subgroup",
            showCurrentTime: true,
            zoomable: true,
            orientation: { axis: "top" },
            margin: {
                item: 10,
                axis: 10
            }
        };

        let timeline = new vis.Timeline(container, items, groups, options);

        // Modal Info
        timeline.on("select", function (props) {
            if (props.items.length > 0) {
                let itemId = props.items[0];
                let item = items.get(itemId);

                $("#infoNamaPegawai").text(item.pegawai);
                $("#infoNamaProyek").text(item.proyek);
                $("#infoMulai").text(item.start);
                $("#infoTenggat").text(item.end);
                $("#infoStatus").text(item.status);
                $("#infoDeskripsi").text(item.deskripsi);

                bootstrap.Modal.getOrCreateInstance(document.getElementById("modalInfoLinimasa")).show();
            }
        });
            }

        // Validasi Tanggal Mulai dan Tenggat
        let mulaiInput = document.getElementById("mulai");
        let tenggatInput = document.getElementById("tenggat");

        function validateDateInput() {
            let mulai = new Date(mulaiInput.value);
            let tenggat = new Date(tenggatInput.value);

            if (mulai > tenggat) {
                Swal.fire({
                    icon: 'error',
                    title: 'Kesalahan Input',
                    text: 'Tanggal mulai tidak boleh lebih besar dari tenggat!',
                });

                // Reset input yang bermasalah
                mulaiInput.value = "";
                return false;
            }
            return true;
        }

        mulaiInput.addEventListener("change", validateDateInput);
        tenggatInput.addEventListener("change", validateDateInput);

        // Submit Edit Linimasa
        let editForm = document.getElementById("editLinimasaForm");
        if (editForm) {
            editForm.addEventListener("submit", function (event) {
                event.preventDefault();

                if (!validateDateInput()) return;

                let formData = new FormData(editForm);
                let id = document.getElementById("edit_linimasa_id").value;

                fetch("{{ url('linimasa') }}/" + id, {
                    method: "POST",
                    body: formData,
                    headers: {
                        "X-CSRF-TOKEN": document.querySelector('meta[name="csrf-token"]').getAttribute("content")
                    }
                })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            let modalElement = document.getElementById("linimasaEditModal");
                            let modalInstance = bootstrap.Modal.getInstance(modalElement);
                            if (modalInstance) {
                                modalInstance.hide();
                            }

                            document.querySelectorAll(".modal-backdrop").forEach(el => el.remove());

                            Swal.fire({
                                icon: "success",
                                title: "Berhasil!",
                                text: "Data Linimasa berhasil diperbarui!",
                                showConfirmButton: false,
                                timer: 2000
                            }).then(() => {
                                location.reload();
                            });
                        } else {
                            Swal.fire({
                                icon: "error",
                                title: "Gagal!",
                                text: data.message || "Terjadi kesalahan saat memperbarui data.",
                            });
                        }
                    })
                    .catch(error => {
                        Swal.fire({
                            icon: "error",
                            title: "Oops...",
                            text: "Gagal memperbarui data. Coba lagi!",
                        });
                    });
            });
        }

        // Pop Up Hapus
        document.querySelectorAll(".btn-delete").forEach(button => {
            button.addEventListener("click", function () {
                let id = this.getAttribute("data-id");

                Swal.fire({
                    title: "Yakin ingin menghapus?",
                    text: "Data linimasa yang dihapus tidak dapat dikembalikan!",
                    icon: "warning",
                    showCancelButton: true,
                    confirmButtonColor: "#d33",
                    cancelButtonColor: "#3085d6",
                    confirmButtonText: "Ya, Hapus!"
                }).then((result) => {
                    if (result.isConfirmed) {
                        fetch(`{{ url('linimasa') }}/${id}`, {
                            method: "POST",
                            headers: {
                                "X-CSRF-TOKEN": document.querySelector('meta[name="csrf-token"]').getAttribute("content"),
                                "X-HTTP-Method-Override": "DELETE"
                            }
                        })
                            .then(response => response.json())
                            .then(data => {
                                if (data.success) {
                                    Swal.fire({
                                        icon: "success",
                                        title: "Berhasil!",
                                        text: "Data Linimasa berhasil dihapus!",
                                        showConfirmButton: false,
                                        timer: 2000
                                    }).then(() => {
                                        location.reload();
                                    });
                                } else {
                                    Swal.fire({
                                        icon: "error",
                                        title: "Gagal!",
                                        text: "Terjadi kesalahan saat menghapus data.",
                                    });
                                }
                            })
                            .catch(error => {
                                Swal.fire({
                                    icon: "error",
                                    title: "Oops...",
                                    text: "Gagal menghapus data. Coba lagi!",
                                });
                            });
                    }
                });
            });
        });
    });
    </script>
