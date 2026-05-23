<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Carbon;

/**
 * Stores hashed Bearer tokens issued by the login endpoint.
 *
 * The plain token is only returned once on login; this model stores the
 * SHA-256 hash plus optional expiry/usage timestamps.
 *
 * @property string $id UUID v7 primary key.
 * @property string $user_id Owner user UUID v7.
 * @property string $name Token label.
 * @property string $token SHA-256 token hash.
 * @property Carbon|null $expires_at
 * @property Carbon|null $last_used_at
 * @property-read User $user
 */
class ApiAccessToken extends Model
{
    use HasUuids;

    public $incrementing = false;

    protected $keyType = 'string';

    protected $fillable = [
        'user_id',
        'name',
        'token',
        'expires_at',
        'last_used_at',
    ];

    protected function casts(): array
    {
        return [
            'expires_at' => 'datetime',
            'last_used_at' => 'datetime',
        ];
    }

    /**
     * User that owns this API token.
     *
     * @return BelongsTo<User, $this>
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
