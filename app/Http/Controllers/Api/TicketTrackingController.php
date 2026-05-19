<?php

namespace App\Http\Controllers\Api;

use App\Http\Concerns\ApiResponses;
use App\Http\Controllers\Controller;
use App\Http\Requests\Tickets\StoreTicketTrackingRequest;
use App\Http\Resources\TicketTrackingResource;
use App\Models\Ticket;
use App\Services\TicketService;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Str;

class TicketTrackingController extends Controller
{
    use ApiResponses;

    public function __construct(private readonly TicketService $ticketService) {}

    public function index(string $ticketId): JsonResponse
    {
        $ticket = $this->findTicket($ticketId);

        if (! $ticket) {
            return $this->ticketNotFound();
        }

        return $this->successResponse(
            'Ticket tracking history retrieved successfully',
            TicketTrackingResource::collection($ticket->trackingLogs)
        );
    }

    public function store(StoreTicketTrackingRequest $request, string $ticketId): JsonResponse
    {
        $ticket = $this->findTicket($ticketId);

        if (! $ticket) {
            return $this->ticketNotFound();
        }

        $tracking = $this->ticketService->addTracking($ticket, $request->user(), $request->validated());

        return $this->successResponse('Ticket tracking added successfully', new TicketTrackingResource($tracking), 201);
    }

    private function findTicket(string $ticketId): ?Ticket
    {
        if (! Str::isUuid($ticketId)) {
            return null;
        }

        return Ticket::query()->find($ticketId);
    }

    private function ticketNotFound(): JsonResponse
    {
        return $this->errorResponse('Ticket not found', null, 404);
    }
}
