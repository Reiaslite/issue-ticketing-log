<?php

namespace App\Http\Requests\Tickets;

use App\Http\Requests\ApiFormRequest;
use App\Models\Ticket;
use App\Rules\UuidV7;
use Illuminate\Validation\Rule;

class StoreTicketTrackingRequest extends ApiFormRequest
{
    /**
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
