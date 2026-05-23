<?php

namespace App\Http\Requests\Tickets;

use App\Http\Requests\ApiFormRequest;
use App\Models\Ticket;
use App\Rules\UuidV7;
use Illuminate\Validation\Rule;

/**
 * Validates ticket list filters and pagination query parameters.
 *
 * Supported filters include status, priority_level, severity_level, staff_id,
 * user_id, search, and created date boundaries.
 */
class ListTicketsRequest extends ApiFormRequest
{
    /**
     * Get validation rules for GET /api/tickets.
     *
     * Allowed status: open, assigned, in_progress, pending, solved, done, cancelled.
     * Allowed priority_level: low, medium, high, urgent.
     * Allowed severity_level: low, medium, high, critical.
     *
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        return [
            'page' => ['nullable', 'integer', 'min:1'],
            'limit' => ['nullable', 'integer', 'min:1', 'max:100'],
            'status' => ['nullable', 'string', Rule::in(Ticket::STATUSES)],
            'priority_level' => ['nullable', 'string', Rule::in(Ticket::PRIORITY_LEVELS)],
            'severity_level' => ['nullable', 'string', Rule::in(Ticket::SEVERITY_LEVELS)],
            'staff_id' => ['nullable', new UuidV7, 'exists:users,id'],
            'user_id' => ['nullable', new UuidV7, 'exists:users,id'],
            'search' => ['nullable', 'string'],
            'start_date' => ['nullable', 'date'],
            'end_date' => ['nullable', 'date', 'after_or_equal:start_date'],
        ];
    }
}
