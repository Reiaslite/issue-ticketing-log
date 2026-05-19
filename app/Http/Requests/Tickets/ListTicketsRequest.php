<?php

namespace App\Http\Requests\Tickets;

use App\Http\Requests\ApiFormRequest;
use App\Models\Ticket;
use Illuminate\Validation\Rule;

class ListTicketsRequest extends ApiFormRequest
{
    /**
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
            'staff_id' => ['nullable', 'uuid', 'exists:users,id'],
            'user_id' => ['nullable', 'uuid', 'exists:users,id'],
            'search' => ['nullable', 'string'],
            'start_date' => ['nullable', 'date'],
            'end_date' => ['nullable', 'date', 'after_or_equal:start_date'],
        ];
    }
}
