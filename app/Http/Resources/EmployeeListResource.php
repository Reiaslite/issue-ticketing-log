<?php

namespace App\Http\Resources;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * Serializes an employee row for the paginated list endpoint.
 *
 * @mixin User
 */
class EmployeeListResource extends JsonResource
{
    /**
     * Convert an employee into the list response shape.
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
            'email' => $this->email,
            'role' => $this->role,
            'created_at' => $this->created_at?->format('Y-m-d H:i:s'),
        ];
    }
}
