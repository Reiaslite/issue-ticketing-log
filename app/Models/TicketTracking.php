<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Carbon;

/**
 * Historical status/progress entry for a ticket.
 *
 * Each status change creates a row here, and the parent ticket status is kept
 * synchronized with the latest tracking status by TicketService.
 *
 * @property string $id UUID v7 primary key.
 * @property string $ticket_id Parent ticket UUID v7.
 * @property string $status Allowed: open, assigned, in_progress, pending, solved, done, cancelled.
 * @property string $note Progress/status note.
 * @property string|null $handled_by Staff user UUID v7 handling this step.
 * @property string $created_by User UUID v7 that created this tracking row.
 * @property Carbon|null $created_at
 * @property-read Ticket $ticket
 * @property-read User|null $handler
 * @property-read User $creator
 */
class TicketTracking extends Model
{
    use HasUuids;

    public const UPDATED_AT = null;

    public $incrementing = false;

    protected $keyType = 'string';

    protected $fillable = [
        'ticket_id',
        'status',
        'note',
        'handled_by',
        'created_by',
    ];

    /**
     * Ticket this tracking row belongs to.
     *
     * @return BelongsTo<Ticket, $this>
     */
    public function ticket(): BelongsTo
    {
        return $this->belongsTo(Ticket::class);
    }

    /**
     * Staff member handling this tracking step, when assigned.
     *
     * @return BelongsTo<User, $this>
     */
    public function handler(): BelongsTo
    {
        return $this->belongsTo(User::class, 'handled_by');
    }

    /**
     * User that created this tracking row.
     *
     * @return BelongsTo<User, $this>
     */
    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }
}
