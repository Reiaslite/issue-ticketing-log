import { apiRequest } from '../services/apiClient';
import {
	badgeClass,
	buildQuery,
	escapeHtml,
	formatLabel,
	getFormPayload,
	renderTableEmpty,
	setMessage,
} from '../support/ui';

const ticketFilterFields = ['search', 'status', 'priority_level', 'severity_level'];

export const initTicketIndex = () => {
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
