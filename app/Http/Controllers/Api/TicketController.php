<?php

namespace App\Http\Controllers\Api;

use App\Http\Concerns\ApiResponses;
use App\Http\Controllers\Controller;
use App\Http\Requests\Tickets\ListTicketsRequest;
use App\Http\Requests\Tickets\StoreTicketRequest;
use App\Http\Requests\Tickets\UpdateTicketRequest;
use App\Http\Resources\TicketListResource;
use App\Http\Resources\TicketResource;
use App\Http\Resources\TicketUpdateResource;
use App\Models\Ticket;
use App\Services\TicketService;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Str;

class TicketController extends Controller
{
    use ApiResponses;

    public function __construct(private readonly TicketService $ticketService) {}

    public function index(ListTicketsRequest $request): JsonResponse
    {
        $filters = $request->validated();
        $limit = (int) ($filters['limit'] ?? 10);

        $query = Ticket::query()
            ->with(['user:id,name', 'staff:id,name'])
            ->latest();

        foreach (['status', 'priority_level', 'severity_level', 'staff_id', 'user_id'] as $field) {
            $query->when($filters[$field] ?? null, fn ($query, $value) => $query->where($field, $value));
        }

        $query->when($filters['search'] ?? null, function ($query, string $search) {
            $query->where(function ($query) use ($search) {
                $query->where('ticket_code', 'ilike', '%'.$search.'%')
                    ->orWhere('issues', 'ilike', '%'.$search.'%');
            });
        });

        $query->when($filters['start_date'] ?? null, fn ($query, $date) => $query->whereDate('created_at', '>=', $date));
        $query->when($filters['end_date'] ?? null, fn ($query, $date) => $query->whereDate('created_at', '<=', $date));

        $tickets = $query->paginate($limit);

        return $this->successResponse('Ticket list retrieved successfully', TicketListResource::collection($tickets->getCollection()), 200, [
            'meta' => [
                'page' => $tickets->currentPage(),
                'limit' => $tickets->perPage(),
                'total' => $tickets->total(),
                'total_page' => $tickets->lastPage(),
            ],
        ]);
    }

    public function store(StoreTicketRequest $request): JsonResponse
    {
        $ticket = $this->ticketService->createTicket($request->user(), $request->validated());

        return $this->successResponse('Ticket issue created successfully', new TicketResource($ticket), 201);
    }

    public function show(string $ticketId): JsonResponse
    {
        $ticket = $this->findTicket($ticketId);

        if (! $ticket) {
            return $this->ticketNotFound();
        }

        return $this->successResponse(
            'Ticket detail retrieved successfully',
            new TicketResource($ticket->load('trackingLogs'))
        );
    }

    public function update(UpdateTicketRequest $request, string $ticketId): JsonResponse
    {
        $ticket = $this->findTicket($ticketId);

        if (! $ticket) {
            return $this->ticketNotFound();
        }

        $ticket = $this->ticketService->updateTicket($ticket, $request->user(), $request->validated());

        return $this->successResponse('Ticket issue updated successfully', new TicketUpdateResource($ticket));
    }

    public function destroy(string $ticketId): JsonResponse
    {
        $ticket = $this->findTicket($ticketId);

        if (! $ticket) {
            return $this->ticketNotFound();
        }

        $ticket->forceFill(['deleted_by' => request()->user()->id])->save();
        $ticket->delete();

        return $this->successResponse('Ticket issue deleted successfully', [
            'id' => $ticket->id,
            'deleted_by' => $ticket->deleted_by,
            'deleted_at' => $ticket->deleted_at?->format('Y-m-d H:i:s'),
        ]);
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
