<?php

namespace App\Http\Requests\Tickets;

use App\Http\Requests\ApiFormRequest;
use App\Models\Ticket;
use App\Rules\UuidV7;
use Illuminate\Validation\Rule;

class UpdateTicketRequest extends ApiFormRequest
{
    /**
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
