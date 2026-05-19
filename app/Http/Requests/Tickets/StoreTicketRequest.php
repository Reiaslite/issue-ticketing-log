<?php

namespace App\Http\Requests\Tickets;

use App\Http\Requests\ApiFormRequest;
use App\Models\Ticket;
use App\Rules\UuidV7;
use Illuminate\Validation\Rule;

class StoreTicketRequest extends ApiFormRequest
{
    /**
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
