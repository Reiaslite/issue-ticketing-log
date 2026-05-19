<?php

namespace App\Http\Middleware;

use App\Models\ApiAccessToken;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class AuthenticateApiToken
{
    /**
     * @param  Closure(Request): Response  $next
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

    private function unauthenticated(): Response
    {
        return response()->json([
            'success' => false,
            'message' => 'Unauthenticated',
            'errors' => null,
        ], 401);
    }
}
