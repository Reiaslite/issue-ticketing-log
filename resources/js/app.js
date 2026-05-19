import 'bootstrap';
import {
    apiRequest,
    getStoredUser,
    getToken,
    redirectToLogin,
    storeSession,
} from './services/apiClient';
import {
    badgeClass,
    buildQuery,
    clearFormErrors,
    escapeHtml,
    formatLabel,
    getFormPayload,
    renderFormErrors,
    renderTableEmpty,
    setButtonLoading,
    setMessage,
    setText,
} from './support/ui';

const ticketCreateFields = ['staff_id', 'issues', 'description', 'severity_level', 'priority_level'];
const ticketFilterFields = ['search', 'status', 'priority_level', 'severity_level'];
const ticketStatusFields = ['status', 'note'];
const ticketTrackingFields = ['status', 'note', 'handled_by'];

const layoutShell = document.querySelector('[data-layout-shell]');
const sidebarToggle = document.querySelector('[data-sidebar-toggle]');
const sidebarOverlay = document.querySelector('[data-sidebar-overlay]');
const desktopBreakpoint = window.matchMedia('(min-width: 992px)');

const syncSidebarState = () => {
    if (!layoutShell || !sidebarToggle) {
        return;
    }

    const isExpanded = desktopBreakpoint.matches
        ? !layoutShell.classList.contains('is-sidebar-collapsed')
        : layoutShell.classList.contains('is-sidebar-open');

    sidebarToggle.setAttribute('aria-expanded', String(isExpanded));
};

const closeMobileSidebar = () => {
    if (!layoutShell || desktopBreakpoint.matches) {
        return;
    }

    layoutShell.classList.remove('is-sidebar-open');
    syncSidebarState();
};

const initLayout = () => {
    if (!layoutShell || !sidebarToggle) {
        return;
    }

    sidebarToggle.addEventListener('click', () => {
        if (desktopBreakpoint.matches) {
            layoutShell.classList.toggle('is-sidebar-collapsed');
        } else {
            layoutShell.classList.toggle('is-sidebar-open');
        }

        syncSidebarState();
    });

    sidebarOverlay?.addEventListener('click', closeMobileSidebar);

    desktopBreakpoint.addEventListener('change', () => {
        layoutShell.classList.remove('is-sidebar-open');
        syncSidebarState();
    });

    document.addEventListener('keydown', (event) => {
        if (event.key === 'Escape') {
            closeMobileSidebar();
        }
    });

    syncSidebarState();
};

const syncAuthUi = () => {
    const user = getStoredUser();
    const nameElement = document.querySelector('[data-auth-name]');
    const roleElement = document.querySelector('[data-auth-role]');
    const initialsElement = document.querySelector('[data-auth-initials]');

    if (!user) {
        return;
    }

    if (nameElement) {
        nameElement.textContent = user.name ?? user.username ?? 'Account';
    }

    if (roleElement) {
        roleElement.textContent = formatLabel(user.role);
    }

    if (initialsElement) {
        initialsElement.textContent = String(user.name ?? user.username ?? 'IT')
            .split(' ')
            .map((part) => part.charAt(0))
            .join('')
            .slice(0, 2)
            .toUpperCase();
    }
};

const initAuthGuard = () => {
    if (!document.querySelector('[data-requires-auth]')) {
        return;
    }

    if (!getToken()) {
        redirectToLogin();
        return;
    }

    syncAuthUi();

    document.querySelector('[data-logout-button]')?.addEventListener('click', redirectToLogin);

    if (document.querySelector('[data-page="ticket-create"]')) {
        apiRequest('/tickets?limit=1').catch(() => {});
    }
};

const initLogin = () => {
    const page = document.querySelector('[data-page="login"]');

    if (!page) {
        return;
    }

    const form = page.querySelector('[data-login-form]');
    const error = page.querySelector('[data-login-error]');
    const button = page.querySelector('[data-login-submit]');

    form?.addEventListener('submit', async (event) => {
        event.preventDefault();
        setMessage(error);
        clearFormErrors(form);
        setButtonLoading(button, true, 'Signing in...');

        try {
            const response = await apiRequest('/auth/login', {
                method: 'POST',
                body: getFormPayload(form, ['username', 'password']),
            });

            storeSession({
                accessToken: response.data.access_token,
                user: response.data.user,
            });
            window.location.href = '/';
        } catch (errorPayload) {
            renderFormErrors(form, errorPayload.errors);
            setMessage(error, errorPayload.message ?? 'Login failed');
        } finally {
            setButtonLoading(button, false);
        }
    });
};

const loadDashboard = async () => {
    const page = document.querySelector('[data-page="dashboard"]');

    if (!page) {
        return;
    }

    const summaryCards = page.querySelectorAll('.summary-card .summary-value');
    const recentTarget = page.querySelector('[data-dashboard-recent]');
    const errorTarget = page.querySelector('[data-dashboard-error]');
    const statuses = ['open', 'in_progress', 'solved', 'done'];

    try {
        await Promise.all(statuses.map(async (status, index) => {
            const query = buildQuery({ status, limit: 1 });
            const response = await apiRequest(`/tickets?${query}`);
            summaryCards[index].textContent = response.meta?.total ?? 0;
        }));

        const response = await apiRequest('/tickets?limit=5');
        const tickets = response.data ?? [];

        if (!tickets.length) {
            renderTableEmpty(recentTarget, 5, 'No tickets have been created yet.');
            return;
        }

        recentTarget.innerHTML = tickets.map((ticket) => `
            <tr>
                <td><a class="table-link" href="/tickets/${escapeHtml(ticket.id)}">${escapeHtml(ticket.ticket_code)}</a></td>
                <td>${escapeHtml(ticket.issues)}</td>
                <td><span class="${badgeClass(ticket.status)}">${escapeHtml(formatLabel(ticket.status))}</span></td>
                <td>${escapeHtml(formatLabel(ticket.priority_level))}</td>
                <td>${escapeHtml(ticket.created_at)}</td>
            </tr>
        `).join('');
    } catch (errorPayload) {
        setMessage(errorTarget, errorPayload.message ?? 'Unable to load dashboard data');
        renderTableEmpty(recentTarget, 5, 'Dashboard data is unavailable.');
    }
};

const initTicketIndex = () => {
    const page = document.querySelector('[data-page="tickets-index"]');

    if (!page) {
        return;
    }

    const form = page.querySelector('[data-ticket-filters]');
    const resetButton = page.querySelector('[data-reset-filters]');
    const listTarget = page.querySelector('[data-ticket-list]');
    const metaTarget = page.querySelector('[data-ticket-meta]');
    const errorTarget = page.querySelector('[data-ticket-list-error]');
    const prevButton = page.querySelector('[data-page-prev]');
    const nextButton = page.querySelector('[data-page-next]');
    const pageCopy = page.querySelector('[data-page-copy]');
    let currentPage = 1;
    let totalPage = 1;

    const loadTickets = async () => {
        setMessage(errorTarget);
        renderTableEmpty(listTarget, 7, 'Loading tickets...');

        try {
            const query = buildQuery({
                ...getFormPayload(form, ticketFilterFields),
                page: currentPage,
                limit: 10,
            });
            const response = await apiRequest(`/tickets?${query}`);
            const tickets = response.data ?? [];
            totalPage = response.meta?.total_page ?? 1;

            metaTarget.textContent = `${response.meta?.total ?? 0} tickets found`;
            pageCopy.textContent = `Page ${response.meta?.page ?? currentPage} of ${totalPage}`;
            prevButton.disabled = currentPage <= 1;
            nextButton.disabled = currentPage >= totalPage;

            if (!tickets.length) {
                renderTableEmpty(listTarget, 7, 'No tickets match the current filters.');
                return;
            }

            listTarget.innerHTML = tickets.map((ticket) => `
                <tr>
                    <td><a class="table-link" href="/tickets/${escapeHtml(ticket.id)}">${escapeHtml(ticket.ticket_code)}</a></td>
                    <td>${escapeHtml(ticket.issues)}</td>
                    <td>${escapeHtml(ticket.user?.name ?? '--')}</td>
                    <td><span class="${badgeClass(ticket.status)}">${escapeHtml(formatLabel(ticket.status))}</span></td>
                    <td>${escapeHtml(formatLabel(ticket.priority_level))}</td>
                    <td>${escapeHtml(ticket.created_at)}</td>
                    <td class="text-end"><a class="btn btn-light btn-sm" href="/tickets/${escapeHtml(ticket.id)}">Open</a></td>
                </tr>
            `).join('');
        } catch (errorPayload) {
            setMessage(errorTarget, errorPayload.message ?? 'Unable to load tickets');
            renderTableEmpty(listTarget, 7, 'Ticket list is unavailable.');
        }
    };

    form?.addEventListener('submit', (event) => {
        event.preventDefault();
        currentPage = 1;
        loadTickets();
    });

    resetButton?.addEventListener('click', () => {
        form.reset();
        currentPage = 1;
        loadTickets();
    });

    prevButton?.addEventListener('click', () => {
        if (currentPage > 1) {
            currentPage -= 1;
            loadTickets();
        }
    });

    nextButton?.addEventListener('click', () => {
        if (currentPage < totalPage) {
            currentPage += 1;
            loadTickets();
        }
    });

    loadTickets();
};

const initTicketCreate = () => {
    const page = document.querySelector('[data-page="ticket-create"]');

    if (!page) {
        return;
    }

    const form = page.querySelector('[data-ticket-create-form]');
    const errorTarget = page.querySelector('[data-ticket-form-error]');
    const successTarget = page.querySelector('[data-ticket-form-success]');
    const button = page.querySelector('[data-ticket-create-submit]');

    form?.addEventListener('submit', async (event) => {
        event.preventDefault();
        setMessage(errorTarget);
        setMessage(successTarget);
        clearFormErrors(form);
        setButtonLoading(button, true, 'Creating...');

        try {
            const response = await apiRequest('/tickets', {
                method: 'POST',
                body: getFormPayload(form, ticketCreateFields),
            });

            setMessage(successTarget, response.message ?? 'Ticket created successfully', 'success');
            window.location.href = `/tickets/${response.data.id}`;
        } catch (errorPayload) {
            renderFormErrors(form, errorPayload.errors);
            setMessage(errorTarget, errorPayload.message ?? 'Unable to create ticket');
        } finally {
            setButtonLoading(button, false);
        }
    });
};

const initTicketDetail = () => {
    const page = document.querySelector('[data-page="ticket-detail"]');

    if (!page) {
        return;
    }

    const ticketId = page.dataset.ticketId;
    const errorTarget = page.querySelector('[data-ticket-detail-error]');
    const trackingTarget = page.querySelector('[data-tracking-list]');
    const statusForm = page.querySelector('[data-status-form]');
    const trackingForm = page.querySelector('[data-tracking-form]');

    const renderTracking = (logs) => {
        if (!logs?.length) {
            trackingTarget.innerHTML = '<div class="state-message">No tracking records yet.</div>';
            return;
        }

        trackingTarget.innerHTML = logs.map((log) => `
            <article class="tracking-item">
                <div class="tracking-marker" aria-hidden="true"></div>
                <div>
                    <div class="tracking-row">
                        <span class="${badgeClass(log.status)}">${escapeHtml(formatLabel(log.status))}</span>
                        <span class="tracking-time">${escapeHtml(log.created_at)}</span>
                    </div>
                    <p class="tracking-note">${escapeHtml(log.note)}</p>
                    <p class="tracking-meta mb-0">Created by ${escapeHtml(log.created_by)}${log.handled_by ? ` · Handled by ${escapeHtml(log.handled_by)}` : ''}</p>
                </div>
            </article>
        `).join('');
    };

    const loadTicket = async () => {
        setMessage(errorTarget);

        try {
            const response = await apiRequest(`/tickets/${ticketId}`);
            const ticket = response.data;
            const statusElement = page.querySelector('[data-ticket-status]');

            setText(page, '[data-ticket-heading]', ticket.issues);
            setText(page, '[data-ticket-description]', ticket.description);
            setText(page, '[data-ticket-code]', ticket.ticket_code);
            setText(page, '[data-ticket-severity]', formatLabel(ticket.severity_level));
            setText(page, '[data-ticket-priority]', formatLabel(ticket.priority_level));
            setText(page, '[data-ticket-user]', ticket.user_id);
            setText(page, '[data-ticket-staff]', ticket.staff_id ?? 'Unassigned');
            setText(page, '[data-ticket-created]', ticket.created_at);
            setText(page, '[data-ticket-updated]', ticket.updated_at ?? '--');

            if (statusElement) {
                statusElement.className = badgeClass(ticket.status);
                statusElement.textContent = formatLabel(ticket.status);
            }

            renderTracking(ticket.tracking_logs ?? []);
        } catch (errorPayload) {
            setMessage(errorTarget, errorPayload.message ?? 'Unable to load ticket detail');
            trackingTarget.innerHTML = '<div class="state-message">Tracking history is unavailable.</div>';
        }
    };

    statusForm?.addEventListener('submit', async (event) => {
        event.preventDefault();
        const errorElement = page.querySelector('[data-status-error]');
        const button = page.querySelector('[data-status-submit]');
        setMessage(errorElement);
        clearFormErrors(statusForm);
        setButtonLoading(button, true, 'Updating...');

        try {
            await apiRequest(`/tickets/${ticketId}/status`, {
                method: 'PATCH',
                body: getFormPayload(statusForm, ticketStatusFields),
            });

            statusForm.reset();
            await loadTicket();
        } catch (errorPayload) {
            renderFormErrors(statusForm, errorPayload.errors);
            setMessage(errorElement, errorPayload.message ?? 'Unable to update status');
        } finally {
            setButtonLoading(button, false);
        }
    });

    trackingForm?.addEventListener('submit', async (event) => {
        event.preventDefault();
        const errorElement = page.querySelector('[data-tracking-error]');
        const button = page.querySelector('[data-tracking-submit]');
        setMessage(errorElement);
        clearFormErrors(trackingForm);
        setButtonLoading(button, true, 'Adding...');

        try {
            await apiRequest(`/tickets/${ticketId}/trackings`, {
                method: 'POST',
                body: getFormPayload(trackingForm, ticketTrackingFields),
            });

            trackingForm.reset();
            await loadTicket();
        } catch (errorPayload) {
            renderFormErrors(trackingForm, errorPayload.errors);
            setMessage(errorElement, errorPayload.message ?? 'Unable to add tracking');
        } finally {
            setButtonLoading(button, false);
        }
    });

    loadTicket();
};

initLayout();
initAuthGuard();
initLogin();
loadDashboard();
initTicketIndex();
initTicketCreate();
initTicketDetail();
