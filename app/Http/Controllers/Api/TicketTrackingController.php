<?php

namespace App\Http\Controllers\Api;

use App\Http\Concerns\ApiResponses;
use App\Http\Controllers\Controller;
use App\Http\Requests\Tickets\StoreTicketTrackingRequest;
use App\Http\Resources\TicketTrackingResource;
use App\Services\TicketService;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Http\JsonResponse;
use Illuminate\Validation\ValidationException;

/**
 * Manages ticket tracking history.
 *
 * Tracking entries represent status/progress history. Adding tracking also
 * synchronizes the parent ticket status to the latest tracking status.
 */
class TicketTrackingController extends Controller
{
    use ApiResponses;

    public function __construct(private readonly TicketService $ticketService) {}

    /**
     * Return the tracking history for one active ticket.
     *
     * @param  string  $ticketId  UUID v7 ticket identifier from the route.
     */
    public function index(string $ticketId): JsonResponse
    {
        $ticket = $this->ticketService->findActiveTicket($ticketId);

        if (! $ticket) {
            return $this->ticketNotFound();
        }

        return $this->successResponse(
            'Ticket tracking history retrieved successfully',
            TicketTrackingResource::collection($ticket->trackingLogs)
        );
    }

    /**
     * Add a tracking entry and synchronize the parent ticket status.
     *
     * `done` is only allowed after `solved`, except for superadmin. Closed
     * tickets cannot be changed except by superadmin.
     *
     * @param  StoreTicketTrackingRequest  $request  Validated tracking payload.
     * @param  string  $ticketId  UUID v7 ticket identifier from the route.
     *
     * @throws AuthorizationException
     * @throws ValidationException
     */
    public function store(StoreTicketTrackingRequest $request, string $ticketId): JsonResponse
    {
        $ticket = $this->ticketService->findActiveTicket($ticketId);

        if (! $ticket) {
            return $this->ticketNotFound();
        }

        $tracking = $this->ticketService->addTracking($ticket, $request->user(), $request->validated());

        return $this->successResponse('Ticket tracking added successfully', new TicketTrackingResource($tracking), 201);
    }

    /**
     * Build the contract-specific not-found response for tracking routes.
     */
    private function ticketNotFound(): JsonResponse
    {
        return $this->errorResponse('Ticket not found', null, 404);
    }
}
