import { apiRequest } from '../services/apiClient';
import {
    buildQuery,
    escapeHtml,
    formatLabel,
    getFormPayload,
    renderTableEmpty,
    setMessage,
} from '../support/ui';

const employeeFilterFields = ['search', 'role'];

export const initEmployeeIndex = () => {
    const page = document.querySelector('[data-page="employees-index"]');

    if (!page) {
        return;
    }

    const form = page.querySelector('[data-employee-filters]');
    const resetButton = page.querySelector('[data-reset-filters]');
    const listTarget = page.querySelector('[data-employee-list]');
    const metaTarget = page.querySelector('[data-employee-meta]');
    const errorTarget = page.querySelector('[data-employee-list-error]');
    const prevButton = page.querySelector('[data-page-prev]');
    const nextButton = page.querySelector('[data-page-next]');
    const pageCopy = page.querySelector('[data-page-copy]');
    let currentPage = 1;
    let totalPage = 1;

    const loadEmployees = async () => {
        setMessage(errorTarget);
        renderTableEmpty(listTarget, 6, 'Loading employees...');

        try {
            const query = buildQuery({
                ...getFormPayload(form, employeeFilterFields),
                page: currentPage,
                limit: 10,
            });
            const response = await apiRequest(`/employees?${query}`);
            const employees = response.data ?? [];
            totalPage = response.meta?.total_page ?? 1;

            metaTarget.textContent = `${response.meta?.total ?? 0} employees found`;
            pageCopy.textContent = `Page ${response.meta?.page ?? currentPage} of ${totalPage}`;
            prevButton.disabled = currentPage <= 1;
            nextButton.disabled = currentPage >= totalPage;

            if (!employees.length) {
                renderTableEmpty(listTarget, 6, 'No employees match the current filters.');
                return;
            }

            listTarget.innerHTML = employees.map((employee) => `
                <tr>
                    <td>${escapeHtml(employee.name)}</td>
                    <td>${escapeHtml(employee.username)}</td>
                    <td>${escapeHtml(employee.email ?? '--')}</td>
                    <td>${escapeHtml(formatLabel(employee.role))}</td>
                    <td>${escapeHtml(employee.created_at)}</td>
                    <td class="text-end"><a class="btn btn-light btn-sm" href="/employees/${escapeHtml(employee.id)}">Open</a></td>
                </tr>
            `).join('');
        } catch (errorPayload) {
            setMessage(errorTarget, errorPayload.message ?? 'Unable to load employees');
            renderTableEmpty(listTarget, 6, 'Employee list is unavailable.');
        }
    };

    form?.addEventListener('submit', (event) => {
        event.preventDefault();
        currentPage = 1;
        loadEmployees();
    });

    resetButton?.addEventListener('click', () => {
        form.reset();
        currentPage = 1;
        loadEmployees();
    });

    prevButton?.addEventListener('click', () => {
        if (currentPage > 1) {
            currentPage -= 1;
            loadEmployees();
        }
    });

    nextButton?.addEventListener('click', () => {
        if (currentPage < totalPage) {
            currentPage += 1;
            loadEmployees();
        }
    });

    loadEmployees();
};
