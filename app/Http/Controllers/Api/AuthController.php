<?php

namespace App\Http\Controllers\Api;

use App\Http\Concerns\ApiResponses;
use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use App\Http\Resources\AuthUserResource;
use App\Models\ApiAccessToken;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

/**
 * Handles API authentication.
 *
 * The login endpoint accepts username and password credentials and returns
 * a short-lived Bearer token that must be sent to protected API endpoints.
 */
class AuthController extends Controller
{
    use ApiResponses;

    /**
     * Authenticate a user by username and password.
     *
     * On success, stores a hashed access token server-side and returns the
     * plain Bearer token once to the client. Invalid credentials return 401.
     *
     * @param  LoginRequest  $request  Validated login payload.
     */
    public function login(LoginRequest $request): JsonResponse
    {
        $user = User::query()
            ->where('username', (string) $request->string('username'))
            ->first();

        if (! $user || ! Hash::check((string) $request->string('password'), $user->password)) {
            return $this->errorResponse('Invalid username or password', null, 401);
        }

        $plainToken = Str::random(80);
        $expiresIn = 3600;

        ApiAccessToken::create([
            'user_id' => $user->id,
            'name' => 'login',
            'token' => hash('sha256', $plainToken),
            'expires_at' => now()->addSeconds($expiresIn),
        ]);

        return $this->successResponse('Login successful', [
            'access_token' => $plainToken,
            'token_type' => 'Bearer',
            'expires_in' => $expiresIn,
            'user' => new AuthUserResource($user),
        ]);
    }
}
