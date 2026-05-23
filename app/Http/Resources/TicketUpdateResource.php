<?php

namespace App\Http\Resources;

use App\Models\Ticket;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * Serializes the response for updating main ticket issue fields.
 *
 * @mixin Ticket
 */
class TicketUpdateResource extends JsonResource
{
    /**
     * Convert a ticket into the update response shape.
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
            'description' => $this->description,
            'severity_level' => $this->severity_level,
            'priority_level' => $this->priority_level,
            'status' => $this->status,
            'updated_by' => $this->updated_by,
            'updated_at' => $this->updated_at?->format('Y-m-d H:i:s'),
        ];
    }
}
