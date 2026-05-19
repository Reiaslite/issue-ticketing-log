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

class AuthController extends Controller
{
    use ApiResponses;

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
