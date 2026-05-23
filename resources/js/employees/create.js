import { apiRequest } from '../services/apiClient';
import {
    clearFormErrors,
    getFormPayload,
    renderFormErrors,
    setButtonLoading,
    setMessage,
} from '../support/ui';

const employeeCreateFields = ['name', 'username', 'email', 'role', 'password'];

export const initEmployeeCreate = () => {
    const page = document.querySelector('[data-page="employee-create"]');

    if (!page) {
        return;
    }

    const form = page.querySelector('[data-employee-create-form]');
    const errorTarget = page.querySelector('[data-employee-form-error]');
    const successTarget = page.querySelector('[data-employee-form-success]');
    const button = page.querySelector('[data-employee-create-submit]');

    form?.addEventListener('submit', async (event) => {
        event.preventDefault();
        setMessage(errorTarget);
        setMessage(successTarget);
        clearFormErrors(form);
        setButtonLoading(button, true, 'Creating...');

        try {
            const response = await apiRequest('/employees', {
                method: 'POST',
                body: getFormPayload(form, employeeCreateFields),
            });

            setMessage(successTarget, response.message ?? 'Employee created successfully', 'success');
            window.location.href = `/employees/${response.data.id}`;
        } catch (errorPayload) {
            renderFormErrors(form, errorPayload.errors);
            setMessage(errorTarget, errorPayload.message ?? 'Unable to create employee');
        } finally {
            setButtonLoading(button, false);
        }
    });
};
