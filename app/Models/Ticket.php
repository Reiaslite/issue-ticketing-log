<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Ticket extends Model
{
    use HasUuids, SoftDeletes;

    public const STATUSES = [
        'open',
        'assigned',
        'in_progress',
        'pending',
        'solved',
        'done',
        'cancelled',
    ];

    public const CLOSED_STATUSES = [
        'done',
        'cancelled',
    ];

    public const SEVERITY_LEVELS = [
        'low',
        'medium',
        'high',
        'critical',
    ];

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

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function staff(): BelongsTo
    {
        return $this->belongsTo(User::class, 'staff_id');
    }

    public function trackingLogs(): HasMany
    {
        return $this->hasMany(TicketTracking::class)->orderBy('created_at');
    }

    public function latestTracking(): HasMany
    {
        return $this->hasMany(TicketTracking::class)->latest('created_at');
    }
}
