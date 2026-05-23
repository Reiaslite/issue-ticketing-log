<?php

namespace App\Http\Middleware;

use App\Models\ApiAccessToken;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

/**
 * Authenticates protected API routes using the Authorization Bearer token.
 *
 * Tokens are compared by SHA-256 hash and must not be expired. On success the
 * resolved user is attached to Laravel's auth/user resolver for controllers.
 */
class AuthenticateApiToken
{
    /**
     * Validate the Bearer token and attach the authenticated user.
     *
     * @param  Request  $request  Incoming HTTP request.
     * @param  Closure(Request): Response  $next  Next middleware/controller.
     */
    public function handle(Request $request, Closure $next): Response
    {
        $plainToken = $request->bearerToken();

        if (! $plainToken) {
            return $this->unauthenticated();
        }

        $accessToken = ApiAccessToken::query()
            ->with('user')
            ->where('token', hash('sha256', $plainToken))
            ->where(function ($query) {
                $query->whereNull('expires_at')->orWhere('expires_at', '>', now());
            })
            ->first();

        if (! $accessToken || ! $accessToken->user) {
            return $this->unauthenticated();
        }

        $accessToken->forceFill(['last_used_at' => now()])->save();

        Auth::setUser($accessToken->user);
        $request->setUserResolver(fn () => $accessToken->user);

        return $next($request);
    }

    /**
     * Return the standard 401 API response for missing, invalid, or expired tokens.
     */
    private function unauthenticated(): Response
    {
        return response()->json([
            'success' => false,
            'message' => 'Unauthenticated',
            'errors' => null,
        ], 401);
    }
}
