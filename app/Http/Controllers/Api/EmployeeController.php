<?php

namespace App\Http\Controllers\Api;

use App\Http\Concerns\ApiResponses;
use App\Http\Controllers\Controller;
use App\Http\Requests\Employees\ListEmployeesRequest;
use App\Http\Requests\Employees\StoreEmployeeRequest;
use App\Http\Requests\Employees\UpdateEmployeeRequest;
use App\Http\Resources\EmployeeListResource;
use App\Http\Resources\EmployeeResource;
use App\Http\Resources\EmployeeUpdateResource;
use App\Models\User;
use App\Services\EmployeeService;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Http\JsonResponse;

/**
 * Manages employee API endpoints.
 *
 * Protected by Bearer token middleware in routes/api.php.
 */
class EmployeeController extends Controller
{
    use ApiResponses;

    public function __construct(private readonly EmployeeService $employeeService) {}

    /**
     * Return a paginated employee list with optional filters.
     *
     * Supported filters: page, limit, role, search.
     *
     * @param  ListEmployeesRequest  $request  Validated query parameters.
     */
    public function index(ListEmployeesRequest $request): JsonResponse
    {
        $filters = $request->validated();
        $limit = (int) ($filters['limit'] ?? 10);

        $query = User::query()->latest();

        $query->when($filters['role'] ?? null, fn ($query, $value) => $query->where('role', $value));

        $query->when($filters['search'] ?? null, function ($query, string $search) {
            $query->where(function ($query) use ($search) {
                $query->where('name', 'ilike', '%'.$search.'%')
                    ->orWhere('username', 'ilike', '%'.$search.'%')
                    ->orWhere('email', 'ilike', '%'.$search.'%');
            });
        });

        $employees = $query->paginate($limit);

        return $this->successResponse(
            'Employee list retrieved successfully',
            EmployeeListResource::collection($employees->getCollection()),
            200,
            [
                'meta' => [
                    'page' => $employees->currentPage(),
                    'limit' => $employees->perPage(),
                    'total' => $employees->total(),
                    'total_page' => $employees->lastPage(),
                ],
            ]
        );
    }

    /**
     * Create a new employee.
     *
     * @param  StoreEmployeeRequest  $request  Validated employee fields.
     *
     * @throws AuthorizationException
     */
    public function store(StoreEmployeeRequest $request): JsonResponse
    {
        $employee = $this->employeeService->createEmployee($request->user(), $request->validated());

        return $this->successResponse('Employee created successfully', new EmployeeResource($employee), 201);
    }

    /**
     * Return one active employee.
     *
     * @param  string  $employeeId  UUID v7 employee identifier from the route.
     */
    public function show(string $employeeId): JsonResponse
    {
        $employee = $this->employeeService->findActiveEmployee($employeeId);

        if (! $employee) {
            return $this->employeeNotFound();
        }

        return $this->successResponse('Employee detail retrieved successfully', new EmployeeResource($employee));
    }

    /**
     * Update mutable employee fields.
     *
     * @param  UpdateEmployeeRequest  $request  Validated mutable employee fields.
     * @param  string  $employeeId  UUID v7 employee identifier from the route.
     *
     * @throws AuthorizationException
     */
    public function update(UpdateEmployeeRequest $request, string $employeeId): JsonResponse
    {
        $employee = $this->employeeService->findActiveEmployee($employeeId);

        if (! $employee) {
            return $this->employeeNotFound();
        }

        $employee = $this->employeeService->updateEmployee($employee, $request->user(), $request->validated());

        return $this->successResponse('Employee updated successfully', new EmployeeUpdateResource($employee));
    }

    /**
     * Soft delete an employee.
     *
     * @param  string  $employeeId  UUID v7 employee identifier from the route.
     *
     * @throws AuthorizationException
     */
    public function destroy(string $employeeId): JsonResponse
    {
        $employee = $this->employeeService->findActiveEmployee($employeeId);

        if (! $employee) {
            return $this->employeeNotFound();
        }

        $employee = $this->employeeService->deleteEmployee($employee, request()->user());

        return $this->successResponse('Employee deleted successfully', [
            'id' => $employee->id,
            'deleted_by' => $employee->deleted_by,
            'deleted_at' => $employee->deleted_at?->format('Y-m-d H:i:s'),
        ]);
    }

    /**
     * Build the contract-specific not-found response for employee routes.
     */
    private function employeeNotFound(): JsonResponse
    {
        return $this->errorResponse('Employee not found', null, 404);
    }
}
