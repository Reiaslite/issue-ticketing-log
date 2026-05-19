<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class TicketTrackingStatusResource extends JsonResource
{
    /**
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
