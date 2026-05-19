export const setButtonLoading = (button, isLoading, loadingText = 'Loading...') => {
    if (!button) {
        return;
    }

    if (isLoading) {
        button.dataset.originalText = button.textContent.trim();
        button.textContent = loadingText;
        button.disabled = true;
        return;
    }

    button.textContent = button.dataset.originalText ?? button.textContent;
    button.disabled = false;
};

export const setMessage = (element, message = '', type = 'error') => {
    if (!element) {
        return;
    }

    element.textContent = message;
    element.classList.toggle('d-none', !message);
    element.classList.toggle('state-message-error', type === 'error');
    element.classList.toggle('state-message-success', type === 'success');
};

export const clearFormErrors = (form) => {
    form?.querySelectorAll('.field-error').forEach((element) => {
        element.textContent = '';
    });
};

export const renderFormErrors = (form, errors = {}) => {
    clearFormErrors(form);

    Object.entries(errors ?? {}).forEach(([field, messages]) => {
        const target = form?.querySelector(`[data-error-for="${field}"]`);

        if (target) {
            target.textContent = Array.isArray(messages) ? messages.join(' ') : String(messages);
        }
    });
};

export const getFormPayload = (form, allowedFields = null) => {
    const payload = {};
    const data = new FormData(form);

    data.forEach((value, key) => {
        if (allowedFields && !allowedFields.includes(key)) {
            return;
        }

        const normalized = String(value).trim();

        if (normalized !== '') {
            payload[key] = normalized;
        }
    });

    return payload;
};

export const formatLabel = (value) => {
    if (!value) {
        return '--';
    }

    return String(value).replaceAll('_', ' ');
};

export const badgeClass = (value) => {
    const status = String(value ?? '').replaceAll('_', '-');

    return `status-badge status-${status}`;
};

export const buildQuery = (params) => {
    const query = new URLSearchParams();

    Object.entries(params).forEach(([key, value]) => {
        if (value !== undefined && value !== null && value !== '') {
            query.set(key, value);
        }
    });

    return query.toString();
};

export const renderTableEmpty = (target, colspan, message) => {
    target.innerHTML = `
        <tr>
            <td colspan="${colspan}">
                <div class="state-message">${message}</div>
            </td>
        </tr>
    `;
};

export const escapeHtml = (value) => String(value ?? '')
    .replaceAll('&', '&amp;')
    .replaceAll('<', '&lt;')
    .replaceAll('>', '&gt;')
    .replaceAll('"', '&quot;')
    .replaceAll("'", '&#039;');

export const setText = (root, selector, value) => {
    const element = root.querySelector(selector);

    if (element) {
        element.textContent = value ?? '--';
    }
};
