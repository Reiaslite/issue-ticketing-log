<?php

namespace App\Services;

use App\Models\Ticket;
use App\Models\TicketTracking;
use App\Models\User;
use App\Support\Uuid;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class TicketService
{
    public function findActiveTicket(string $ticketId): ?Ticket
    {
        if (! Uuid::isUuidV7($ticketId)) {
            return null;
        }

        return Ticket::query()->find($ticketId);
    }

    /**
     * @param  array<string, mixed>  $data
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
     * @param  array<string, mixed>  $data
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
     * @param  array<string, mixed>  $data
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
     * @param  array{status: string, note: string}  $data
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

    public function deleteTicket(Ticket $ticket, User $user): Ticket
    {
        return DB::transaction(function () use ($ticket, $user) {
            $ticket->forceFill(['deleted_by' => $user->id])->save();
            $ticket->delete();

            return $ticket->refresh();
        });
    }

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

    private function ensureStatusTransitionIsAllowed(Ticket $ticket, User $user, string $status): void
    {
        if ($status !== 'done' || $ticket->status === 'solved' || $this->isSuperadmin($user)) {
            return;
        }

        throw ValidationException::withMessages([
            'status' => ['The ticket status must be solved before it can be marked as done.'],
        ]);
    }

    private function isSuperadmin(User $user): bool
    {
        return $user->role === 'superadmin';
    }
}
