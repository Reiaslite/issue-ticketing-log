<?php

namespace App\Http\Resources;

use App\Models\Ticket;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * Serializes the response for status-only ticket updates.
 *
 * Includes latest_tracking when the latestTracking relationship is loaded.
 *
 * @mixin Ticket
 */
class TicketStatusResource extends JsonResource
{
    /**
     * Convert a ticket into the status update response shape.
     *
     * @param  Request  $request  Current HTTP request.
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        $latestTracking = $this->relationLoaded('latestTracking')
            ? $this->latestTracking
            : null;

        return [
            'id' => $this->id,
            'ticket_code' => $this->ticket_code,
            'status' => $this->status,
            'updated_by' => $this->updated_by,
            'updated_at' => $this->updated_at?->format('Y-m-d H:i:s'),
            'latest_tracking' => new TicketTrackingStatusResource($latestTracking),
        ];
    }
}
