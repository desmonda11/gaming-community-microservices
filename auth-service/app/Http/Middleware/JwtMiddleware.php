<?php

namespace App\Http\Middleware;

use App\Models\User;
use Closure;
use Firebase\JWT\JWT;
use Firebase\JWT\Key;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class JwtMiddleware
{
    protected function jwtSecret(): string
    {
        return env('JWT_SECRET', 'changeme');
    }

    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next)
    {
        $header = $request->header('Authorization', '');

        if (! $header || ! preg_match('/Bearer\s+(.*)$/i', $header, $matches)) {
            return response()->json(['error' => 'Token not provided'], 401);
        }

        $token = $matches[1];

        try {
            $decoded = JWT::decode($token, new Key($this->jwtSecret(), 'HS256'));

            $userId = $decoded->sub ?? null;

            if (! $userId) {
                return response()->json(['error' => 'Invalid token payload'], 401);
            }

            $user = User::find($userId);

            if (! $user) {
                return response()->json(['error' => 'User not found'], 401);
            }

            Auth::setUser($user);

            return $next($request);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Token is invalid: ' . $e->getMessage()], 401);
        }
    }
}
