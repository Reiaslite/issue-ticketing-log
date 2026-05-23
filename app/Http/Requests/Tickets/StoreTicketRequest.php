<?php

namespace App\Http\Requests\Tickets;

use App\Http\Requests\ApiFormRequest;
use App\Models\Ticket;
use App\Rules\UuidV7;
use Illuminate\Validation\Rule;

/**
 * Validates frontend-provided fields for creating a ticket issue.
 *
 * Backend-owned fields such as user_id, ticket_code, status, created_by, and
 * timestamps are intentionally excluded and generated server-side.
 */
class StoreTicketRequest extends ApiFormRequest
{
    /**
     * Get validation rules for POST /api/tickets.
     *
     * Allowed severity_level: low, medium, high, critical.
     * Allowed priority_level: low, medium, high, urgent.
     *
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        return [
            'staff_id' => ['nullable', new UuidV7, 'exists:users,id'],
            'issues' => ['required', 'string', 'min:5'],
            'description' => ['required', 'string'],
            'severity_level' => ['required', 'string', Rule::in(Ticket::SEVERITY_LEVELS)],
            'priority_level' => ['required', 'string', Rule::in(Ticket::PRIORITY_LEVELS)],
        ];
    }
}
