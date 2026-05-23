<?php

namespace App\Services;

use App\Models\User;
use App\Support\Uuid;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Support\Facades\DB;

/**
 * Coordinates employee management rules and audit fields.
 */
class EmployeeService
{
    /**
     * Find a non-deleted employee by UUID v7.
     *
     * @param  string  $employeeId  UUID v7 employee identifier.
     */
    public function findActiveEmployee(string $employeeId): ?User
    {
        if (! Uuid::isUuidV7($employeeId)) {
            return null;
        }

        return User::query()->find($employeeId);
    }

    /**
     * Create a new employee.
     *
     * @param  User  $actor  Authenticated requester.
     * @param  array{name: string, username: string, email?: string|null, role: string, password: string}  $data
     *
     * @throws AuthorizationException
     */
    public function createEmployee(User $actor, array $data): User
    {
        $this->ensureSuperadmin($actor);

        return DB::transaction(function () use ($data) {
            $employee = User::create([
                'name' => $data['name'],
                'username' => $data['username'],
                'email' => $data['email'] ?? null,
                'role' => $data['role'],
                'password' => $data['password'],
            ]);

            return $employee->refresh();
        });
    }

    /**
     * Update mutable employee fields.
     *
     * @param  User  $employee  Employee being updated.
     * @param  User  $actor  Authenticated requester.
     * @param  array<string, mixed>  $data
     *
     * @throws AuthorizationException
     */
    public function updateEmployee(User $employee, User $actor, array $data): User
    {
        $this->ensureSuperadmin($actor);

        return DB::transaction(function () use ($employee, $data) {
            $employee->fill($data);
            $employee->save();

            return $employee->refresh();
        });
    }

    /**
     * Soft delete an employee and set deleted_by.
     *
     * @param  User  $employee  Employee being deleted.
     * @param  User  $actor  Authenticated requester.
     *
     * @throws AuthorizationException
     */
    public function deleteEmployee(User $employee, User $actor): User
    {
        $this->ensureSuperadmin($actor);

        return DB::transaction(function () use ($employee, $actor) {
            $employee->forceFill(['deleted_by' => $actor->id])->save();
            $employee->delete();

            return $employee->refresh();
        });
    }

    /**
     * Restrict employee writes to superadmin only.
     *
     * @throws AuthorizationException
     */
    private function ensureSuperadmin(User $user): void
    {
        if ($user->role === 'superadmin') {
            return;
        }

        throw new AuthorizationException('You do not have permission to access this resource');
    }
}
