<?php

namespace App\Http\Resources;

use App\Models\TicketTracking;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * Serializes a ticket tracking log for tracking endpoints.
 *
 * Includes handled_by because tracking history endpoints expose handler data.
 *
 * @mixin TicketTracking
 */
class TicketTrackingResource extends JsonResource
{
    /**
     * Convert a tracking log into the tracking response shape.
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
            'handled_by' => $this->handled_by,
            'created_by' => $this->created_by,
            'created_at' => $this->created_at?->format('Y-m-d H:i:s'),
        ];
    }
}
