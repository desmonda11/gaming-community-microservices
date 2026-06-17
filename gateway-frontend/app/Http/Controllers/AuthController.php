<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class AuthController extends Controller
{
    public function showLogin()
    {
        return view('auth.login');
    }

    public function login(Request $request)
    {
        $data = $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        $url = env('AUTH_SERVICE_URL', 'http://127.0.0.1:8001') . '/api/login';

        $resp = Http::post($url, $data);

        if ($resp->failed()) {
            return back()->withErrors(['login' => $resp->json('error') ?? 'Login failed'])->withInput();
        }

        $json = $resp->json();

        // normalize user and user_id into session
        $user = $json['user'] ?? null;
        $role = $json['role'] ?? ($user['role'] ?? null);
        $userId = $user['id'] ?? $json['user_id'] ?? null;

        // try to extract from JWT payload if still null
        if (!$userId && !empty($json['token'])) {
            $parts = explode('.', $json['token']);
            if (count($parts) >= 2) {
                $payload = $parts[1];
                $decoded = json_decode(base64_decode(strtr($payload, '-_', '+/')), true);
                if (is_array($decoded)) {
                    $userId = $decoded['user_id'] ?? $decoded['id'] ?? $decoded['sub'] ?? $userId;
                }
            }
        }

        session(['token' => $json['token'], 'user' => $user, 'role' => $role, 'user_id' => $userId]);

        return redirect('/dashboard');
    }

    public function showRegister()
    {
        return view('auth.register');
    }

    public function register(Request $request)
    {
        $data = $request->validate([
            'name' => 'required|string',
            'email' => 'required|email',
            'password' => 'required|min:6',
        ]);

        $url = env('AUTH_SERVICE_URL', 'http://127.0.0.1:8001') . '/api/register';

        $resp = Http::post($url, $data);

        if ($resp->failed()) {
            return back()->withErrors(['register' => $resp->json('error') ?? 'Register failed'])->withInput();
        }

        return redirect('/login')->with('status', 'Registration successful, please login');
    }

    public function logout(Request $request)
    {
        $request->session()->forget(['token', 'user', 'role', 'user_id']);
        return redirect('/login');
    }
}
