<?php

namespace App\Http\Resources;

use App\Models\TicketTracking;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * Serializes the compact latest tracking object in status update responses.
 *
 * @mixin TicketTracking
 */
class TicketTrackingStatusResource extends JsonResource
{
    /**
     * Convert latest tracking into the compact status response shape.
     *
     * @param  Request  $request  Current HTTP request.
     * @return array<string, mixed>|null
     */
    public function toArray(Request $request): ?array
    {
        if (! $this->resource) {
            return null;
        }

        return [
            'id' => $this->id,
            'status' => $this->status,
            'note' => $this->note,
            'created_at' => $this->created_at?->format('Y-m-d H:i:s'),
        ];
    }
}
