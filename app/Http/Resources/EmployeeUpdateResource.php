<?php

namespace App\Http\Resources;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * Serializes the response for updating employee data.
 *
 * @mixin User
 */
class EmployeeUpdateResource extends JsonResource
{
    /**
     * Convert an employee into the update response shape.
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
            'updated_at' => $this->updated_at?->format('Y-m-d H:i:s'),
        ];
    }
}
