export async function request(url, options = {}) {
    const csrfToken = document.querySelector('meta[name="csrf-token"]')?.content;
    const response = await fetch(url, {
        ...options,
        headers: {
            Accept: 'application/json',
            'X-CSRF-TOKEN': csrfToken,
            'X-Requested-With': 'XMLHttpRequest',
            ...options.headers,
        },
    });
    const payload = await response.json().catch(() => ({}));

    if (!response.ok) {
        const error = new Error(payload.message || 'Permintaan gagal diproses.');
        error.status = response.status;
        error.errors = payload.errors || {};
        throw error;
    }

    return payload;
}

export function escapeHtml(value) {
    const element = document.createElement('div');
    element.textContent = value ?? '';
    return element.innerHTML;
}

export function showValidationErrors(container, error) {
    const messages = Object.values(error.errors || {}).flat();
    container.replaceChildren();

    if (messages.length === 0) {
        container.textContent = error.message;
    } else {
        const list = document.createElement('ul');
        list.className = 'mb-0';
        messages.forEach((message) => {
            const item = document.createElement('li');
            item.textContent = message;
            list.append(item);
        });
        container.append(list);
    }

    container.classList.remove('d-none');
}

export function notify(type, message) {
    if (!window.Swal) return;

    window.Swal.fire({
        icon: type === 'error' ? 'error' : 'success',
        text: message,
        toast: true,
        position: 'top-end',
        showConfirmButton: false,
        timer: 2800,
        timerProgressBar: true,
    });
}

export function showFlashMessage() {
    const flash = document.querySelector('#flash-data');
    if (!flash) return;
    if (flash.dataset.success) notify('success', flash.dataset.success);
    if (flash.dataset.error) notify('error', flash.dataset.error);
}
