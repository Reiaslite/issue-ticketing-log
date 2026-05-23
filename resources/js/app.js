import 'bootstrap';
import {
    apiRequest,
    getStoredUser,
    getToken,
    redirectToLogin,
    storeSession,
} from './services/apiClient';
import { initEmployeeCreate } from './employees/create';
import { initEmployeeIndex } from './employees/index';
import { initEmployeeDetail } from './employees/show';
import { initTicketCreate } from './tickets/create';
import { initTicketIndex } from './tickets/index';
import { initTicketDetail } from './tickets/show';
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
} from './support/ui';

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

initLayout();
initAuthGuard();
initLogin();
loadDashboard();
initEmployeeIndex();
initEmployeeCreate();
initEmployeeDetail();
initTicketIndex();
initTicketCreate();
initTicketDetail();
