import { apiRequest } from '../services/apiClient';
import {
	badgeClass,
	clearFormErrors,
	escapeHtml,
	formatLabel,
	getFormPayload,
	renderFormErrors,
	setButtonLoading,
	setMessage,
	setText,
} from '../support/ui';

const ticketStatusFields = ['status', 'note'];
const ticketTrackingFields = ['status', 'note', 'handled_by'];

export const initTicketDetail = () => {
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
