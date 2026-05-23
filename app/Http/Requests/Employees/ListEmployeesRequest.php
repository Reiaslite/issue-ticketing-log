<?php

namespace App\Http\Requests\Employees;

use App\Http\Requests\ApiFormRequest;
use App\Models\User;
use Illuminate\Validation\Rule;

/**
 * Validates employee list filters and pagination query parameters.
 */
class ListEmployeesRequest extends ApiFormRequest
{
    /**
     * Get validation rules for GET /api/employees.
     *
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        return [
            'page' => ['nullable', 'integer', 'min:1'],
            'limit' => ['nullable', 'integer', 'min:1', 'max:100'],
            'role' => ['nullable', 'string', Rule::in(User::ROLES)],
            'search' => ['nullable', 'string'],
        ];
    }
}
