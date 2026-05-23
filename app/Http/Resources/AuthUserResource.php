<?php

namespace App\Http\Resources;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * Serializes the authenticated user returned by login.
 *
 * Password and token metadata are never exposed by this resource.
 *
 * @mixin User
 */
class AuthUserResource extends JsonResource
{
    /**
     * Convert the user into the login response shape.
     *
     * @param  Request  $request  Current HTTP request.
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'username' => $this->username,
            'role' => $this->role,
        ];
    }
}
