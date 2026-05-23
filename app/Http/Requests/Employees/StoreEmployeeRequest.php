<?php

namespace App\Http\Requests\Employees;

use App\Http\Requests\ApiFormRequest;
use App\Models\User;
use Illuminate\Validation\Rule;

/**
 * Validates frontend-provided fields for creating an employee.
 */
class StoreEmployeeRequest extends ApiFormRequest
{
    /**
     * Get validation rules for POST /api/employees.
     *
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'min:2'],
            'username' => ['required', 'string', 'min:3', 'max:255', 'unique:users,username'],
            'email' => ['nullable', 'email', 'max:255', 'unique:users,email'],
            'role' => ['required', 'string', Rule::in(User::ROLES)],
            'password' => ['required', 'string', 'min:8'],
        ];
    }
}
