<?php

namespace App\Http\Requests\Auth;

use App\Http\Requests\ApiFormRequest;

/**
 * Validates username/password login requests.
 *
 * Login uses `username` and `password`; no email or register flow is exposed.
 */
class LoginRequest extends ApiFormRequest
{
    /**
     * Get validation rules for POST /api/auth/login.
     *
     * @return array<string, list<string>>
     */
    public function rules(): array
    {
        return [
            'username' => ['required', 'string'],
            'password' => ['required', 'string'],
        ];
    }
}
