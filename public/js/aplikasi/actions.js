import { escapeHtml, notify, request } from "../common/http.js";

const detailFields = [
    ["Nama", "nama"], ["OPD", "opd"], ["Uraian", "uraian"],
    ["Tanggal pembuatan", "tahun_pembuatan"], ["Jenis", "jenis"],
    ["Basis", "basis_aplikasi"], ["Bahasa/framework", "bahasa_framework"],
    ["Database", "database"], ["Pengembang", "pengembang"],
    ["Lokasi server", "lokasi_server"], ["Status", "status_pemakaian"],
];

export function initializeApplicationActions(baseUrl, openEdit) {
    const detailModal = window.bootstrap.Modal.getOrCreateInstance(
        document.querySelector("#applicationDetailModal"),
    );

    const showDetail = async (id) => {
        try {
            const response = await request(`${baseUrl}/${id}/detail`);
            const application = response.data;
            document.querySelector("#applicationDetails").innerHTML = detailFields
                .map(([label, key]) => `<dt class="col-sm-4">${label}</dt>` +
                    `<dd class="col-sm-8">${escapeHtml(application[key] || "-")}</dd>`)
                .join("");
            const attributes = application.atribut_tambahans || [];
            document.querySelector("#applicationAttributeDetails").innerHTML = attributes.length
                ? attributes.map((attribute) => `<tr><td>${escapeHtml(attribute.nama_atribut)}</td>` +
                    `<td>${escapeHtml(attribute.pivot?.nilai_atribut || "-")}</td></tr>`).join("")
                : '<tr><td colspan="2" class="text-muted">Belum ada atribut tambahan.</td></tr>';
            detailModal.show();
        } catch (error) {
            notify("error", error.message);
        }
    };

    const deleteApplication = async (id, name) => {
        const confirmation = await window.Swal.fire({
            title: "Hapus aplikasi?",
            text: `${name} akan dihapus beserta nilai atributnya.`,
            icon: "warning",
            showCancelButton: true,
            confirmButtonText: "Hapus",
            cancelButtonText: "Batal",
            confirmButtonColor: "#dc3545",
        });
        if (!confirmation.isConfirmed) return;

        try {
            const response = await request(`${baseUrl}/${id}`, { method: "DELETE" });
            notify("success", response.message);
            window.setTimeout(() => window.location.reload(), 500);
        } catch (error) {
            notify("error", error.message);
        }
    };

    document.querySelector("#applicationRows").addEventListener("click", (event) => {
        const button = event.target.closest("[data-action]");
        if (!button) return;
        if (button.dataset.action === "detail") showDetail(button.dataset.id);
        if (button.dataset.action === "edit") openEdit(button.dataset.id);
        if (button.dataset.action === "delete") deleteApplication(button.dataset.id, button.dataset.name);
    });
}
