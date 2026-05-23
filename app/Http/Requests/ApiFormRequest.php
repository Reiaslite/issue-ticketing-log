<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Validation\ValidationException;

/**
 * Base request for API validation.
 *
 * Converts Laravel validation failures into the API contract error envelope:
 * success=false, message="Validation error", and field-level errors.
 */
abstract class ApiFormRequest extends FormRequest
{
    /**
     * Allow authorization decisions to be handled by middleware/services.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Return validation errors as JSON instead of redirecting.
     *
     * @param  Validator  $validator  Failed validator instance.
     *
     * @throws HttpResponseException
     */
    protected function failedValidation(Validator $validator): void
    {
        throw new HttpResponseException(response()->json([
            'success' => false,
            'message' => 'Validation error',
            'errors' => (new ValidationException($validator))->errors(),
        ], 422));
    }
}
