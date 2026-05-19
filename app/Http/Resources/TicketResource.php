<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class TicketResource extends JsonResource
{
    /**
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'user_id' => $this->user_id,
            'staff_id' => $this->staff_id,
            'ticket_code' => $this->ticket_code,
            'issues' => $this->issues,
            'description' => $this->description,
            'severity_level' => $this->severity_level,
            'priority_level' => $this->priority_level,
            'status' => $this->status,
            'created_by' => $this->created_by,
            'created_at' => $this->created_at?->format('Y-m-d H:i:s'),
            'updated_by' => $this->updated_by,
            'updated_at' => $this->updated_at?->format('Y-m-d H:i:s'),
            'deleted_by' => $this->deleted_by,
            'deleted_at' => $this->deleted_at?->format('Y-m-d H:i:s'),
            'tracking_logs' => $this->whenLoaded(
                'trackingLogs',
                fn () => TicketDetailTrackingResource::collection($this->trackingLogs)
            ),
        ];
    }
}
