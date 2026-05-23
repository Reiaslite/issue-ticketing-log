<?php

namespace App\Http\Resources;

use App\Models\TicketTracking;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * Serializes tracking rows embedded inside the ticket detail response.
 *
 * This variant matches the detail contract and omits handled_by.
 *
 * @mixin TicketTracking
 */
class TicketDetailTrackingResource extends JsonResource
{
    /**
     * Convert the tracking log into the ticket detail tracking shape.
     *
     * @param  Request  $request  Current HTTP request.
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'ticket_id' => $this->ticket_id,
            'status' => $this->status,
            'note' => $this->note,
            'created_by' => $this->created_by,
            'created_at' => $this->created_at?->format('Y-m-d H:i:s'),
        ];
    }
}
