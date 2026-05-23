<?php

namespace App\Http\Requests\Employees;

use App\Http\Requests\ApiFormRequest;
use App\Models\User;
use Illuminate\Validation\Rule;

/**
 * Validates mutable employee fields.
 */
class UpdateEmployeeRequest extends ApiFormRequest
{
    /**
     * Get validation rules for PUT /api/employees/{employee_id}.
     *
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        $employeeId = $this->route('employee_id');

        return [
            'name' => ['sometimes', 'required', 'string', 'min:2'],
            'username' => [
                'sometimes',
                'required',
                'string',
                'min:3',
                'max:255',
                Rule::unique('users', 'username')->ignore($employeeId),
            ],
            'email' => [
                'sometimes',
                'nullable',
                'email',
                'max:255',
                Rule::unique('users', 'email')->ignore($employeeId),
            ],
            'role' => ['sometimes', 'required', 'string', Rule::in(User::ROLES)],
            'password' => ['sometimes', 'required', 'string', 'min:8'],
        ];
    }
}
