<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Carbon;

/**
 * Authenticatable user for username/password API login.
 *
 * Passwords are cast with Laravel's `hashed` cast so plain values assigned by
 * seeders or forms are stored securely.
 *
 * @property string $id UUID v7 primary key.
 * @property string $name
 * @property string $username Unique login username.
 * @property string|null $email
 * @property string $role Example values: user, staff, superadmin.
 * @property string $password Hashed password.
 * @property Carbon|null $email_verified_at
 * @property string|null $deleted_by
 * @property Carbon|null $deleted_at
 * @property-read Collection<int, ApiAccessToken> $apiAccessTokens
 * @property-read Collection<int, Ticket> $tickets
 */
class User extends Authenticatable
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, HasUuids, Notifiable, SoftDeletes;

    /**
     * Allowed role values.
     *
     * @var list<string>
     */
    public const ROLES = [
        'user',
        'staff',
        'superadmin',
    ];

    public $incrementing = false;

    protected $keyType = 'string';

    /**
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'username',
        'email',
        'role',
        'password',
        'deleted_by',
    ];

    /**
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * API access tokens issued to this user.
     *
     * @return HasMany<ApiAccessToken, $this>
     */
    public function apiAccessTokens(): HasMany
    {
        return $this->hasMany(ApiAccessToken::class);
    }

    /**
     * Tickets created by this user.
     *
     * @return HasMany<Ticket, $this>
     */
    public function tickets(): HasMany
    {
        return $this->hasMany(Ticket::class);
    }

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'deleted_at' => 'datetime',
        ];
    }
}
