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
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Http\JsonResponse;

/**
 * Manages the main ticket issue API endpoints.
 *
 * Protected by Bearer token middleware in routes/api.php. System-managed
 * fields such as user_id, ticket_code, status, created_by, updated_by, and
 * deleted_by are always produced from backend state, never frontend input.
 */
class TicketController extends Controller
{
    use ApiResponses;

    public function __construct(private readonly TicketService $ticketService) {}

    /**
     * Return a paginated ticket list with optional filters.
     *
     * Supported filters: page, limit, status, priority_level, severity_level,
     * staff_id, user_id, search, start_date, and end_date.
     *
     * @param  ListTicketsRequest  $request  Validated query parameters.
     */
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

    /**
     * Create a ticket issue and its initial tracking log.
     *
     * The authenticated user becomes user_id and created_by. The service also
     * generates ticket_code, sets initial status to open, and writes the first
     * tracking entry.
     *
     * @param  StoreTicketRequest  $request  Validated frontend ticket fields only.
     */
    public function store(StoreTicketRequest $request): JsonResponse
    {
        $ticket = $this->ticketService->createTicket($request->user(), $request->validated());

        return $this->successResponse('Ticket issue created successfully', new TicketResource($ticket), 201);
    }

    /**
     * Return one active ticket with its tracking logs.
     *
     * @param  string  $ticketId  UUID v7 ticket identifier from the route.
     */
    public function show(string $ticketId): JsonResponse
    {
        $ticket = $this->ticketService->findActiveTicket($ticketId);

        if (! $ticket) {
            return $this->ticketNotFound();
        }

        return $this->successResponse(
            'Ticket detail retrieved successfully',
            new TicketResource($ticket->load('trackingLogs'))
        );
    }

    /**
     * Update mutable ticket issue fields.
     *
     * Closed tickets (`done` or `cancelled`) cannot be updated unless the
     * authenticated user is superadmin. updated_by is always set by backend.
     *
     * @param  UpdateTicketRequest  $request  Validated mutable ticket fields.
     * @param  string  $ticketId  UUID v7 ticket identifier from the route.
     *
     * @throws AuthorizationException
     */
    public function update(UpdateTicketRequest $request, string $ticketId): JsonResponse
    {
        $ticket = $this->ticketService->findActiveTicket($ticketId);

        if (! $ticket) {
            return $this->ticketNotFound();
        }

        $ticket = $this->ticketService->updateTicket($ticket, $request->user(), $request->validated());

        return $this->successResponse('Ticket issue updated successfully', new TicketUpdateResource($ticket));
    }

    /**
     * Soft delete a ticket.
     *
     * The service sets deleted_by from the authenticated user and Eloquent
     * soft deletes the row by setting deleted_at.
     *
     * @param  string  $ticketId  UUID v7 ticket identifier from the route.
     */
    public function destroy(string $ticketId): JsonResponse
    {
        $ticket = $this->ticketService->findActiveTicket($ticketId);

        if (! $ticket) {
            return $this->ticketNotFound();
        }

        $ticket = $this->ticketService->deleteTicket($ticket, request()->user());

        return $this->successResponse('Ticket issue deleted successfully', [
            'id' => $ticket->id,
            'deleted_by' => $ticket->deleted_by,
            'deleted_at' => $ticket->deleted_at?->format('Y-m-d H:i:s'),
        ]);
    }

    /**
     * Build the contract-specific not-found response for ticket routes.
     */
    private function ticketNotFound(): JsonResponse
    {
        return $this->errorResponse('Ticket not found', null, 404);
    }
}
