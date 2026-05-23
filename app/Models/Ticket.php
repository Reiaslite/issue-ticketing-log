<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Carbon;

/**
 * Current state of an issue ticket.
 *
 * The ticket row stores the latest status while TicketTracking stores the
 * historical status/progress log. IDs are UUID v7 through HasUuids.
 *
 * @property string $id UUID v7 primary key.
 * @property string $user_id Requester user UUID v7.
 * @property string|null $staff_id Assigned staff UUID v7.
 * @property string $ticket_code Backend-generated code, e.g. TCK-20260516-0001.
 * @property string $issues
 * @property string $description
 * @property string $severity_level Allowed: low, medium, high, critical.
 * @property string $priority_level Allowed: low, medium, high, urgent.
 * @property string $status Allowed: open, assigned, in_progress, pending, solved, done, cancelled.
 * @property string $created_by User UUID v7 that created the ticket.
 * @property string|null $updated_by User UUID v7 that last updated the ticket.
 * @property string|null $deleted_by User UUID v7 that soft deleted the ticket.
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property Carbon|null $deleted_at
 * @property-read User $user
 * @property-read User|null $staff
 * @property-read Collection<int, TicketTracking> $trackingLogs
 * @property-read TicketTracking|null $latestTracking
 */
class Ticket extends Model
{
    use HasUuids, SoftDeletes;

    /**
     * Allowed ticket statuses.
     *
     * @var list<string>
     */
    public const STATUSES = [
        'open',
        'assigned',
        'in_progress',
        'pending',
        'solved',
        'done',
        'cancelled',
    ];

    /**
     * Statuses that lock ticket updates for non-superadmin users.
     *
     * @var list<string>
     */
    public const CLOSED_STATUSES = [
        'done',
        'cancelled',
    ];

    /**
     * Allowed severity levels.
     *
     * @var list<string>
     */
    public const SEVERITY_LEVELS = [
        'low',
        'medium',
        'high',
        'critical',
    ];

    /**
     * Allowed priority levels.
     *
     * @var list<string>
     */
    public const PRIORITY_LEVELS = [
        'low',
        'medium',
        'high',
        'urgent',
    ];

    public $incrementing = false;

    protected $keyType = 'string';

    protected $fillable = [
        'user_id',
        'staff_id',
        'ticket_code',
        'issues',
        'description',
        'severity_level',
        'priority_level',
        'status',
        'created_by',
        'updated_by',
        'deleted_by',
    ];

    /**
     * Requester that created the ticket.
     *
     * @return BelongsTo<User, $this>
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Staff user currently assigned to the ticket.
     *
     * @return BelongsTo<User, $this>
     */
    public function staff(): BelongsTo
    {
        return $this->belongsTo(User::class, 'staff_id');
    }

    /**
     * Chronological tracking/progress history for this ticket.
     *
     * Tracking logs are append-only during normal API flow and are used to
     * audit status changes.
     *
     * @return HasMany<TicketTracking, $this>
     */
    public function trackingLogs(): HasMany
    {
        return $this->hasMany(TicketTracking::class)->orderBy('created_at');
    }

    /**
     * Latest tracking log used by the status update response.
     *
     * @return HasOne<TicketTracking, $this>
     */
    public function latestTracking(): HasOne
    {
        return $this->hasOne(TicketTracking::class)->latest('created_at');
    }

    /**
     * Determine whether this ticket is closed for regular users.
     */
    public function isClosed(): bool
    {
        return in_array($this->status, self::CLOSED_STATUSES, true);
    }
}
