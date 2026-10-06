export function initializeApplicationFilters() {
    const search = document.querySelector("#applicationSearch");
    const status = document.querySelector("#applicationStatusFilter");
    const basis = document.querySelector("#applicationBasisFilter");
    const emptyState = document.querySelector("#applicationEmptyState");

    const applyFilters = () => {
        const query = search.value.trim().toLowerCase();
        let visibleRows = 0;

        document.querySelectorAll("[data-application-row]").forEach((row) => {
            const matches =
                row.dataset.search.includes(query) &&
                (!status.value || row.dataset.status === status.value) &&
                (!basis.value || row.dataset.basis === basis.value);

            row.classList.toggle("d-none", !matches);
            if (matches) visibleRows += 1;
        });

        emptyState.classList.toggle("d-none", visibleRows > 0);
    };

    search.addEventListener("input", applyFilters);
    status.addEventListener("change", applyFilters);
    basis.addEventListener("change", applyFilters);
    applyFilters();
}
