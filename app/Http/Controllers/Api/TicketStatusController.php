<?php

namespace App\Http\Controllers\Api;

use App\Http\Concerns\ApiResponses;
use App\Http\Controllers\Controller;
use App\Http\Requests\Tickets\UpdateTicketStatusRequest;
use App\Http\Resources\TicketStatusResource;
use App\Services\TicketService;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Http\JsonResponse;
use Illuminate\Validation\ValidationException;

/**
 * Handles status-only ticket updates.
 *
 * Updating status creates a tracking log and synchronizes tickets.status with
 * the latest tracking status.
 */
class TicketStatusController extends Controller
{
    use ApiResponses;

    public function __construct(private readonly TicketService $ticketService) {}

    /**
     * Update a ticket status without changing main issue fields.
     *
     * `done` is only allowed after `solved`, except for superadmin. Closed
     * tickets cannot be changed except by superadmin.
     *
     * @param  UpdateTicketStatusRequest  $request  Validated status and note payload.
     * @param  string  $ticketId  UUID v7 ticket identifier from the route.
     *
     * @throws AuthorizationException
     * @throws ValidationException
     */
    public function update(UpdateTicketStatusRequest $request, string $ticketId): JsonResponse
    {
        $ticket = $this->ticketService->findActiveTicket($ticketId);

        if (! $ticket) {
            return $this->errorResponse('Ticket not found', null, 404);
        }

        $ticket = $this->ticketService->updateStatus($ticket, $request->user(), $request->validated());

        return $this->successResponse('Ticket status updated successfully', new TicketStatusResource($ticket));
    }
}
