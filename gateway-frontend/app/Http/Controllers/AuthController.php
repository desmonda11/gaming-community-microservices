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

        session(['token' => $json['token'], 'user' => $json['user'], 'role' => $json['role'] ?? ($json['user']['role'] ?? null)]);

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
        $request->session()->forget(['token', 'user', 'role']);
        return redirect('/login');
    }
}
