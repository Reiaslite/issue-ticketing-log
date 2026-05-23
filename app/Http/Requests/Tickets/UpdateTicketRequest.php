<?php

namespace App\Http\Requests\Tickets;

use App\Http\Requests\ApiFormRequest;
use App\Models\Ticket;
use App\Rules\UuidV7;
use Illuminate\Validation\Rule;

/**
 * Validates mutable main ticket fields.
 *
 * Status is deliberately not accepted here; status changes must go through
 * tracking/status endpoints so history remains complete.
 */
class UpdateTicketRequest extends ApiFormRequest
{
    /**
     * Get validation rules for PUT /api/tickets/{ticket_id}.
     *
     * Allowed severity_level: low, medium, high, critical.
     * Allowed priority_level: low, medium, high, urgent.
     *
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        return [
            'staff_id' => ['sometimes', 'nullable', new UuidV7, 'exists:users,id'],
            'issues' => ['sometimes', 'required', 'string', 'min:5'],
            'description' => ['sometimes', 'required', 'string'],
            'severity_level' => ['sometimes', 'required', 'string', Rule::in(Ticket::SEVERITY_LEVELS)],
            'priority_level' => ['sometimes', 'required', 'string', Rule::in(Ticket::PRIORITY_LEVELS)],
        ];
    }
}
