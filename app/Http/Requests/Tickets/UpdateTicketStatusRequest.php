<?php

namespace App\Http\Requests\Tickets;

use App\Http\Requests\ApiFormRequest;
use App\Models\Ticket;
use Illuminate\Validation\Rule;

/**
 * Validates status-only ticket update requests.
 *
 * Business rules such as "done requires solved except superadmin" are enforced
 * by TicketService after these basic shape/value rules pass.
 */
class UpdateTicketStatusRequest extends ApiFormRequest
{
    /**
     * Get validation rules for PATCH /api/tickets/{ticket_id}/status.
     *
     * Allowed status: open, assigned, in_progress, pending, solved, done, cancelled.
     *
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        return [
            'status' => ['required', 'string', Rule::in(Ticket::STATUSES)],
            'note' => ['required', 'string'],
        ];
    }
}
