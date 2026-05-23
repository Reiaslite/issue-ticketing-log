import { apiRequest } from '../services/apiClient';
import {
	clearFormErrors,
	getFormPayload,
	renderFormErrors,
	setButtonLoading,
	setMessage,
} from '../support/ui';

const ticketCreateFields = ['staff_id', 'issues', 'description', 'severity_level', 'priority_level'];

export const initTicketCreate = () => {
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
