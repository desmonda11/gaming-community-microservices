<?php

namespace App\Http\Controllers;

use App\Models\User;
use Firebase\JWT\JWT;
use Firebase\JWT\Key;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    protected function jwtSecret(): string
    {
        return env('JWT_SECRET', 'changeme');
    }

    public function register(Request $request)
    {
        $data = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|string|min:6',
            'role' => 'sometimes|in:admin,user',
        ]);

        $user = User::create([
            'name' => $data['name'],
            'email' => $data['email'],
            'password' => Hash::make($data['password']),
            'role' => $data['role'] ?? 'user',
        ]);

        $payload = [
            'sub' => $user->id,
            'role' => $user->role,
            'iat' => time(),
            'exp' => time() + (60 * 60 * 24),
        ];

        $token = JWT::encode($payload, $this->jwtSecret(), 'HS256');

        return response()->json([
            'message' => 'Register successful',
            'token' => $token,
            'user' => $user,
            'role' => $user->role,
        ], 201);
    }

    public function login(Request $request)
    {
        $data = $request->validate([
            'email' => 'required|email',
            'password' => 'required|string',
        ]);

        $user = User::where('email', $data['email'])->first();

        if (!$user || !Hash::check($data['password'], $user->password)) {
            return response()->json([
                'error' => 'Credentials not match'
            ], 401);
        }

        $payload = [
            'sub' => $user->id,
            'role' => $user->role,
            'iat' => time(),
            'exp' => time() + (60 * 60 * 24),
        ];

        $token = JWT::encode($payload, $this->jwtSecret(), 'HS256');

        return response()->json([
            'message' => 'Login successful',
            'token' => $token,
            'user' => $user,
            'role' => $user->role,
        ]);
    }

    public function me(Request $request)
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
                new Key($this->jwtSecret(), 'HS256')
            );

            $user = User::find($decoded->sub);

            if (!$user) {
                return response()->json([
                    'error' => 'User not found'
                ], 404);
            }

            return response()->json([
                'user' => $user,
                'role' => $user->role
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Unauthenticated',
                'message' => $e->getMessage()
            ], 401);
        }
    }
}