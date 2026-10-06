import { escapeHtml, notify, request, showFlashMessage, showValidationErrors } from "../common/http.js";

const page = document.querySelector("#attributePage");

if (page) initialize();

function initialize() {
    const baseUrl = page.dataset.baseUrl;
    const applicationUrl = page.dataset.applicationUrl;
    const form = document.querySelector("#attributeForm");
    const formModal = window.bootstrap.Modal.getOrCreateInstance(document.querySelector("#attributeFormModal"));
    const detailModal = window.bootstrap.Modal.getOrCreateInstance(document.querySelector("#attributeDetailModal"));
    const applicationModal = window.bootstrap.Modal.getOrCreateInstance(document.querySelector("#applicationAttributeModal"));
    const errors = document.querySelector("#attributeFormErrors");

    initializeFilters();
    initializeEnumOptions();
    showFlashMessage();

    document.querySelector("#addAttributeButton").addEventListener("click", () => openForm());
    document.querySelector("#attributeRows").addEventListener("click", async (event) => {
        const button = event.target.closest("[data-action]");
        if (!button) return;
        if (button.dataset.action === "detail") showDetail(button.dataset.id);
        if (button.dataset.action === "edit") openForm(button.dataset.id);
        if (button.dataset.action === "delete") deleteAttribute(button.dataset.id, button.dataset.name);
    });
    document.querySelector("#attributeApplicationRows").addEventListener("click", (event) => {
        const button = event.target.closest('[data-action="application-detail"]');
        if (button) showApplicationAttributes(button.dataset.id);
    });

    form.addEventListener("submit", async (event) => {
        event.preventDefault();
        errors.classList.add("d-none");
        const id = form.dataset.attributeId;
        const data = new FormData(form);
        if (id) data.append("_method", "PUT");

        try {
            const response = await request(id ? `${baseUrl}/${id}` : baseUrl, { method: "POST", body: data });
            notify("success", response.message);
            window.setTimeout(() => window.location.reload(), 500);
        } catch (error) {
            showValidationErrors(errors, error);
        }
    });

    async function openForm(id = null) {
        form.reset();
        form.dataset.attributeId = id || "";
        errors.classList.add("d-none");
        setEnumOptions([]);
        document.querySelector("#attributeFormTitle").textContent = id ? "Edit Atribut" : "Tambah Atribut";

        if (id) {
            try {
                const response = await request(`${baseUrl}/${id}/edit`);
                form.elements.nama_atribut.value = response.data.nama_atribut;
                form.elements.tipe_data.value = response.data.tipe_data;
                setEnumOptions(response.data.enum_options || []);
            } catch (error) {
                notify("error", error.message);
                return;
            }
        }
        toggleEnumSection();
        formModal.show();
    }

    async function showDetail(id) {
        try {
            const response = await request(`${baseUrl}/${id}/detail`);
            document.querySelector("#attributeDetailTitle").textContent = response.atribut.nama_atribut;
            document.querySelector("#attributeDetailRows").innerHTML = response.aplikasis.length
                ? response.aplikasis.map((application) => `<tr><td>${escapeHtml(application.nama)}</td>` +
                    `<td>${escapeHtml(application.pivot?.nilai_atribut || "-")}</td></tr>`).join("")
                : '<tr><td colspan="2" class="text-muted">Belum digunakan oleh aplikasi.</td></tr>';
            detailModal.show();
        } catch (error) {
            notify("error", error.message);
        }
    }

    async function showApplicationAttributes(id) {
        try {
            const response = await request(`${applicationUrl}/${id}/atribut`);
            document.querySelector("#applicationAttributeFields").innerHTML = response.atribut_tambahans.length
                ? response.atribut_tambahans.map((attribute) => `<div class="border-bottom py-2 d-flex justify-content-between">` +
                    `<span>${escapeHtml(attribute.nama_atribut)}</span>` +
                    `<strong>${escapeHtml(attribute.pivot?.nilai_atribut || "-")}</strong></div>`).join("")
                : '<p class="text-muted mb-0">Belum ada atribut.</p>';
            applicationModal.show();
        } catch (error) {
            notify("error", error.message);
        }
    }

    async function deleteAttribute(id, name) {
        const result = await window.Swal.fire({
            title: "Hapus atribut?", text: `${name} dan seluruh nilainya akan dihapus.`,
            icon: "warning", showCancelButton: true, confirmButtonText: "Hapus",
            cancelButtonText: "Batal", confirmButtonColor: "#dc3545",
        });
        if (!result.isConfirmed) return;

        const deleteForm = new FormData();
        deleteForm.append("_method", "DELETE");
        try {
            await request(`${baseUrl}/${id}`, { method: "POST", body: deleteForm });
            window.location.reload();
        } catch (error) {
            notify("error", error.message);
        }
    }
}

function initializeFilters() {
    bindFilter("#attributeSearch", "[data-attribute-row]", "#attributeEmptyState");
    bindFilter("#attributeApplicationSearch", "[data-application-row]");
}

function bindFilter(inputSelector, rowSelector, emptySelector = null) {
    const input = document.querySelector(inputSelector);
    input.addEventListener("input", () => {
        const query = input.value.trim().toLowerCase();
        let visible = 0;
        document.querySelectorAll(rowSelector).forEach((row) => {
            const matches = row.dataset.search.includes(query);
            row.classList.toggle("d-none", !matches);
            if (matches) visible += 1;
        });
        if (emptySelector) document.querySelector(emptySelector).classList.toggle("d-none", visible > 0);
    });
}

function initializeEnumOptions() {
    document.querySelector("#tipe_data").addEventListener("change", toggleEnumSection);
    document.querySelector("#addEnumOption").addEventListener("click", () => addEnumOption());
}

function toggleEnumSection() {
    const isEnum = document.querySelector("#tipe_data").value === "enum";
    document.querySelector("#enumOptionsSection").classList.toggle("d-none", !isEnum);
    if (isEnum && document.querySelector("#enumOptions").children.length === 0) addEnumOption();
}

function setEnumOptions(options) {
    document.querySelector("#enumOptions").replaceChildren();
    options.forEach(addEnumOption);
}

function addEnumOption(value = "") {
    const row = document.createElement("div");
    row.className = "input-group mb-2";
    row.innerHTML = `<input class="form-control" name="enum_options[]" maxlength="100" value="${escapeHtml(value)}" required>` +
        '<button class="btn btn-outline-danger" type="button" aria-label="Hapus pilihan">&times;</button>';
    row.querySelector("button").addEventListener("click", () => row.remove());
    document.querySelector("#enumOptions").append(row);
}
