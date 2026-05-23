import { apiRequest } from '../services/apiClient';
import {
    badgeClass,
    formatLabel,
    setMessage,
    setText,
} from '../support/ui';

export const initEmployeeDetail = () => {
    const page = document.querySelector('[data-page="employee-detail"]');

    if (!page) {
        return;
    }

    const employeeId = page.dataset.employeeId;
    const errorTarget = page.querySelector('[data-employee-detail-error]');
    const roleBadge = page.querySelector('[data-employee-role-badge]');

    const loadEmployee = async () => {
        setMessage(errorTarget);

        try {
            const response = await apiRequest(`/employees/${employeeId}`);
            const employee = response.data;

            setText(page, '[data-employee-heading]', employee.name);
            setText(page, '[data-employee-name]', employee.name);
            setText(page, '[data-employee-username]', employee.username);
            setText(page, '[data-employee-email]', employee.email ?? '--');
            setText(page, '[data-employee-role]', formatLabel(employee.role));
            setText(page, '[data-employee-created]', employee.created_at);
            setText(page, '[data-employee-updated]', employee.updated_at ?? '--');

            if (roleBadge) {
                roleBadge.className = badgeClass(employee.role);
                roleBadge.textContent = formatLabel(employee.role);
            }
        } catch (errorPayload) {
            setMessage(errorTarget, errorPayload.message ?? 'Unable to load employee detail');
        }
    };

    if (employeeId) {
        loadEmployee();
    } else {
        setMessage(errorTarget, 'Employee record is unavailable.');
    }
};
