<?php

namespace App\Http\Resources;

use App\Models\Ticket;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * Serializes a ticket row for the paginated list endpoint.
 *
 * Includes compact requester/staff relationship data when those relations are
 * eager loaded by the controller.
 *
 * @mixin Ticket
 */
class TicketListResource extends JsonResource
{
    /**
     * Convert a ticket into the list response shape.
     *
     * @param  Request  $request  Current HTTP request.
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'ticket_code' => $this->ticket_code,
            'issues' => $this->issues,
            'severity_level' => $this->severity_level,
            'priority_level' => $this->priority_level,
            'status' => $this->status,
            'user' => $this->whenLoaded('user', fn () => [
                'id' => $this->user?->id,
                'name' => $this->user?->name,
            ]),
            'staff' => $this->whenLoaded('staff', fn () => $this->staff ? [
                'id' => $this->staff->id,
                'name' => $this->staff->name,
            ] : null),
            'created_at' => $this->created_at?->format('Y-m-d H:i:s'),
        ];
    }
}
