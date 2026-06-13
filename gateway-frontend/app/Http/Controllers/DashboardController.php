<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        $user = session('user');
        $role = session('role');

        $client = \Illuminate\Support\Facades\Http::withToken(session('token'));
        $base = env('PROJECT_SERVICE_URL', 'http://127.0.0.1:8002');

        $teamsResp = $client->get($base.'/api/teams');
        $playersResp = $client->get($base.'/api/players');
        $matchesResp = $client->get($base.'/api/matches');
        $invResp = $client->get($base.'/api/inventories');

        $counts = [
            'teams' => $teamsResp->ok() ? count($teamsResp->json('data') ?? []) : 0,
            'players' => $playersResp->ok() ? count($playersResp->json('data') ?? []) : 0,
            'matches' => $matchesResp->ok() ? count($matchesResp->json('data') ?? []) : 0,
            'inventories' => $invResp->ok() ? count($invResp->json('data') ?? []) : 0,
        ];

        return view('dashboard', ['user' => $user, 'role' => $role, 'counts' => $counts]);
    }
}
