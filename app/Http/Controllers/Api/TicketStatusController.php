<?php

namespace App\Http\Controllers\Api;

use App\Http\Concerns\ApiResponses;
use App\Http\Controllers\Controller;
use App\Http\Requests\Tickets\UpdateTicketStatusRequest;
use App\Http\Resources\TicketStatusResource;
use App\Models\Ticket;
use App\Services\TicketService;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Str;

class TicketStatusController extends Controller
{
    use ApiResponses;

    public function __construct(private readonly TicketService $ticketService) {}

    public function update(UpdateTicketStatusRequest $request, string $ticketId): JsonResponse
    {
        $ticket = $this->findTicket($ticketId);

        if (! $ticket) {
            return $this->errorResponse('Ticket not found', null, 404);
        }

        $ticket = $this->ticketService->updateStatus($ticket, $request->user(), $request->validated());

        return $this->successResponse('Ticket status updated successfully', new TicketStatusResource($ticket));
    }

    private function findTicket(string $ticketId): ?Ticket
    {
        if (! Str::isUuid($ticketId)) {
            return null;
        }

        return Ticket::query()->find($ticketId);
    }
}
