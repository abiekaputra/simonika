document.querySelectorAll("form[data-confirm]").forEach((form) => {
    form.addEventListener("submit", async (event) => {
        event.preventDefault();
        const result = await Swal.fire({
            title: form.dataset.confirmTitle || "Hapus data ini?",
            text: form.dataset.confirm,
            icon: "warning",
            showCancelButton: true,
            confirmButtonText: "Ya, hapus",
            cancelButtonText: "Batal",
            confirmButtonColor: "#dc3545",
        });

        if (result.isConfirmed) form.submit();
    });
});

document.querySelectorAll("[data-completion-toggle]").forEach((select) => {
    const target = document.getElementById(select.dataset.completionToggle);
    const completed = ["Selesai Lebih Cepat", "Tepat Waktu", "Terlambat"];
    const sync = () => {
        const enabled = completed.includes(select.value);
        target.disabled = !enabled;
        target.required = enabled;
        if (!enabled) target.value = "";
    };
    select.addEventListener("change", sync);
    sync();
});
