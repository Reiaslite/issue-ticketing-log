<?php

namespace App\Http\Requests\Tickets;

use App\Http\Requests\ApiFormRequest;
use App\Models\Ticket;
use App\Rules\UuidV7;
use Illuminate\Validation\Rule;

/**
 * Validates progress/status entries added to a ticket tracking history.
 */
class StoreTicketTrackingRequest extends ApiFormRequest
{
    /**
     * Get validation rules for POST /api/tickets/{ticket_id}/trackings.
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
            'handled_by' => ['nullable', new UuidV7, 'exists:users,id'],
        ];
    }
}
