<?php

namespace App\Http\Resources;

use App\Models\Ticket;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * Serializes the full ticket detail/create response.
 *
 * Tracking logs are included only when the trackingLogs relationship has been
 * loaded by the controller.
 *
 * @mixin Ticket
 */
class TicketResource extends JsonResource
{
    /**
     * Convert a ticket into the full contract response shape.
     *
     * @param  Request  $request  Current HTTP request.
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
