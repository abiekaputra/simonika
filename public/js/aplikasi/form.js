import { notify, request, showValidationErrors } from "../common/http.js";

const fields = [
    "nama", "opd", "uraian", "tahun_pembuatan", "jenis", "basis_aplikasi",
    "bahasa_framework", "database", "pengembang", "lokasi_server", "status_pemakaian",
];

export function initializeApplicationForm(baseUrl) {
    const modalElement = document.querySelector("#applicationFormModal");
    const modal = window.bootstrap.Modal.getOrCreateInstance(modalElement);
    const form = document.querySelector("#applicationForm");
    const title = document.querySelector("#applicationFormTitle");
    const submitButton = document.querySelector("#applicationSubmitButton");
    const errors = document.querySelector("#applicationFormErrors");

    const reset = () => {
        form.reset();
        form.dataset.applicationId = "";
        errors.classList.add("d-none");
        title.textContent = "Tambah Aplikasi";
    };

    const openCreate = () => {
        reset();
        modal.show();
    };

    const openEdit = async (id) => {
        reset();
        try {
            const response = await request(`${baseUrl}/${id}/edit`);
            fields.forEach((field) => {
                const input = form.elements.namedItem(field);
                if (input) input.value = response.aplikasi[field] ?? "";
            });
            response.aplikasi.atribut_tambahans.forEach((attribute) => {
                const input = form.elements.namedItem(`atribut[${attribute.id_atribut}]`);
                if (input) input.value = attribute.pivot?.nilai_atribut ?? "";
            });
            form.dataset.applicationId = id;
            title.textContent = "Edit Aplikasi";
            modal.show();
        } catch (error) {
            notify("error", error.message);
        }
    };

    form.addEventListener("submit", async (event) => {
        event.preventDefault();
        errors.classList.add("d-none");
        submitButton.disabled = true;

        const id = form.dataset.applicationId;
        const formData = new FormData(form);
        if (id) formData.append("_method", "PUT");

        try {
            const response = await request(id ? `${baseUrl}/${id}` : baseUrl, {
                method: "POST",
                body: formData,
            });
            notify("success", response.message);
            window.setTimeout(() => window.location.reload(), 500);
        } catch (error) {
            showValidationErrors(errors, error);
        } finally {
            submitButton.disabled = false;
        }
    });

    document.querySelector("#addApplicationButton").addEventListener("click", openCreate);

    return { openEdit };
}
