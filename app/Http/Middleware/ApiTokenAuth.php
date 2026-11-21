<?php

namespace App\Http\Middleware;

use App\Models\User;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class ApiTokenAuth
{
    public function handle(Request $request, Closure $next): Response
    {
        $token = $request->bearerToken();

        if (! $token) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthenticated. Please provide a Bearer token.',
            ], 401);
        }

        $user = null;

        if (method_exists(User::class, 'tokens') && class_exists(\Laravel\Sanctum\PersonalAccessToken::class)) {
            $pat = \Laravel\Sanctum\PersonalAccessToken::findToken($token);
            if ($pat && $pat->tokenable instanceof User) {
                $user = $pat->tokenable;
            }
        }

        if (! $user) {
            $hashed = hash('sha256', $token);
            $user = User::where('remember_token', $hashed)
                ->orWhere('remember_token', $token)
                ->first();
        }

        if (! $user) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid or expired API token.',
            ], 401);
        }

        Auth::setUser($user);
        $request->setUserResolver(fn () => $user);

        return $next($request);
    }
}
