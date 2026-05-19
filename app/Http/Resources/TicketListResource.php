<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class TicketListResource extends JsonResource
{
    /**
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
