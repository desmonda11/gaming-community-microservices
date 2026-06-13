<?php

namespace App\Http\Middleware;

use Closure;
use Firebase\JWT\JWT;
use Firebase\JWT\Key;
use Illuminate\Http\Request;

class JwtMiddleware
{
    public function handle(Request $request, Closure $next)
    {
        try {
            $authHeader = $request->header('Authorization');

            if (!$authHeader || !str_starts_with($authHeader, 'Bearer ')) {
                return response()->json([
                    'error' => 'Token not provided'
                ], 401);
            }

            $token = substr($authHeader, 7);

            $decoded = JWT::decode(
                $token,
                new Key(env('JWT_SECRET', 'changeme'), 'HS256')
            );

            $request->merge([
                'auth_user_id' => $decoded->sub,
                'auth_role' => $decoded->role,
            ]);

            return $next($request);
        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Unauthenticated',
                'message' => $e->getMessage()
            ], 401);
        }
    }
}