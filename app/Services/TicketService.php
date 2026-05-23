<?php

namespace App\Services;

use App\Models\Ticket;
use App\Models\TicketTracking;
use App\Models\User;
use App\Support\Uuid;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

/**
 * Coordinates ticket business rules and multi-table writes.
 *
 * This service owns transaction boundaries for ticket creation, status/tracking
 * updates, and soft delete audit fields. It keeps tickets.status synchronized
 * with the latest ticket_trackings.status.
 */
class TicketService
{
    /**
     * Find a non-deleted ticket by UUID v7.
     *
     * Invalid UUID v7 values intentionally return null so controllers can emit
     * the contract-specific 404 response.
     *
     * @param  string  $ticketId  UUID v7 route parameter.
     */
    public function findActiveTicket(string $ticketId): ?Ticket
    {
        if (! Uuid::isUuidV7($ticketId)) {
            return null;
        }

        return Ticket::query()->find($ticketId);
    }

    /**
     * Create a ticket and initial `open` tracking log.
     *
     * Backend-generated fields:
     * user_id, ticket_code, status=open, created_by, created_at.
     *
     * @param  User  $user  Authenticated requester.
     * @param  array{staff_id?: string|null, issues: string, description: string, severity_level: string, priority_level: string}  $data
     */
    public function createTicket(User $user, array $data): Ticket
    {
        return DB::transaction(function () use ($user, $data) {
            $ticket = Ticket::create([
                'user_id' => $user->id,
                'staff_id' => $data['staff_id'] ?? null,
                'ticket_code' => $this->generateTicketCode(),
                'issues' => $data['issues'],
                'description' => $data['description'],
                'severity_level' => $data['severity_level'],
                'priority_level' => $data['priority_level'],
                'status' => 'open',
                'created_by' => $user->id,
            ]);

            Ticket::withoutTimestamps(fn () => $ticket->forceFill(['updated_at' => null])->save());

            TicketTracking::create([
                'ticket_id' => $ticket->id,
                'status' => 'open',
                'note' => 'Ticket created by user.',
                'created_by' => $user->id,
            ]);

            return $ticket->refresh();
        });
    }

    /**
     * Update mutable main ticket fields.
     *
     * Closed tickets (`done`, `cancelled`) cannot be updated unless the user is
     * superadmin. updated_by is set from the authenticated user.
     *
     * @param  Ticket  $ticket  Active ticket being updated.
     * @param  User  $user  Authenticated user performing the update.
     * @param  array<string, mixed>  $data  Validated mutable ticket fields.
     *
     * @throws AuthorizationException
     */
    public function updateTicket(Ticket $ticket, User $user, array $data): Ticket
    {
        $this->ensureTicketCanBeChanged($ticket, $user);

        return DB::transaction(function () use ($ticket, $user, $data) {
            $ticket->fill($data);
            $ticket->updated_by = $user->id;
            $ticket->save();

            return $ticket->refresh();
        });
    }

    /**
     * Add a ticket tracking row and synchronize the parent ticket status.
     *
     * If handled_by is supplied, the ticket staff assignment is also updated.
     *
     * @param  Ticket  $ticket  Active ticket receiving progress.
     * @param  User  $user  Authenticated user creating the tracking row.
     * @param  array{status: string, note: string, handled_by?: string|null}  $data
     *
     * @throws AuthorizationException
     * @throws ValidationException
     */
    public function addTracking(Ticket $ticket, User $user, array $data): TicketTracking
    {
        $this->ensureTicketCanBeChanged($ticket, $user);
        $this->ensureStatusTransitionIsAllowed($ticket, $user, $data['status']);

        return DB::transaction(function () use ($ticket, $user, $data) {
            $tracking = TicketTracking::create([
                'ticket_id' => $ticket->id,
                'status' => $data['status'],
                'note' => $data['note'],
                'handled_by' => $data['handled_by'] ?? null,
                'created_by' => $user->id,
            ]);

            $ticket->forceFill([
                'status' => $data['status'],
                'staff_id' => $data['handled_by'] ?? $ticket->staff_id,
                'updated_by' => $user->id,
            ])->save();

            return $tracking->refresh();
        });
    }

    /**
     * Update ticket status without changing main ticket issue fields.
     *
     * This creates a tracking row and synchronizes tickets.status. `done` is
     * only allowed after `solved`, except for superadmin.
     *
     * @param  Ticket  $ticket  Active ticket being updated.
     * @param  User  $user  Authenticated user performing the status change.
     * @param  array{status: string, note: string}  $data
     *
     * @throws AuthorizationException
     * @throws ValidationException
     */
    public function updateStatus(Ticket $ticket, User $user, array $data): Ticket
    {
        $this->ensureTicketCanBeChanged($ticket, $user);
        $this->ensureStatusTransitionIsAllowed($ticket, $user, $data['status']);

        return DB::transaction(function () use ($ticket, $user, $data) {
            TicketTracking::create([
                'ticket_id' => $ticket->id,
                'status' => $data['status'],
                'note' => $data['note'],
                'handled_by' => $user->id,
                'created_by' => $user->id,
            ]);

            $ticket->forceFill([
                'status' => $data['status'],
                'updated_by' => $user->id,
            ])->save();

            return $ticket->refresh()->load('latestTracking');
        });
    }

    /**
     * Soft delete a ticket and set deleted_by from the authenticated user.
     *
     * Tracking logs remain in the database; the ticket row is soft deleted.
     *
     * @param  Ticket  $ticket  Active ticket being soft deleted.
     * @param  User  $user  Authenticated user deleting the ticket.
     */
    public function deleteTicket(Ticket $ticket, User $user): Ticket
    {
        return DB::transaction(function () use ($ticket, $user) {
            $ticket->forceFill(['deleted_by' => $user->id])->save();
            $ticket->delete();

            return $ticket->refresh();
        });
    }

    /**
     * Generate the next daily ticket code.
     *
     * Format: TCK-YYYYMMDD-0001. The lookup runs inside the create transaction
     * and uses a row lock to reduce duplicate code risk.
     */
    private function generateTicketCode(): string
    {
        $prefix = 'TCK-'.now()->format('Ymd').'-';
        $latestCode = Ticket::withTrashed()
            ->where('ticket_code', 'like', $prefix.'%')
            ->orderByDesc('ticket_code')
            ->lockForUpdate()
            ->value('ticket_code');

        $nextNumber = $latestCode ? ((int) substr($latestCode, -4)) + 1 : 1;

        return $prefix.str_pad((string) $nextNumber, 4, '0', STR_PAD_LEFT);
    }

    /**
     * Prevent non-superadmin users from changing closed tickets.
     *
     * @throws AuthorizationException
     */
    private function ensureTicketCanBeChanged(Ticket $ticket, User $user): void
    {
        if (! $ticket->isClosed()) {
            return;
        }

        if ($this->isSuperadmin($user)) {
            return;
        }

        throw new AuthorizationException('You do not have permission to access this resource');
    }

    /**
     * Enforce workflow rule for marking a ticket as done.
     *
     * `done` requires current status `solved`, unless the actor is superadmin.
     *
     * @throws ValidationException
     */
    private function ensureStatusTransitionIsAllowed(Ticket $ticket, User $user, string $status): void
    {
        if ($status !== 'done' || $ticket->status === 'solved' || $this->isSuperadmin($user)) {
            return;
        }

        throw ValidationException::withMessages([
            'status' => ['The ticket status must be solved before it can be marked as done.'],
        ]);
    }

    /**
     * Determine whether a user can bypass restricted workflow rules.
     */
    private function isSuperadmin(User $user): bool
    {
        return $user->role === 'superadmin';
    }
}
