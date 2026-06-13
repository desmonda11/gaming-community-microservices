<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class PlayerPageController extends Controller
{
    protected function client()
    {
        return Http::withToken(session('token'));
    }

    public function index()
    {
        $base = env('PROJECT_SERVICE_URL', 'http://127.0.0.1:8002');

        // get players
        $resp = $this->client()->get($base.'/api/players');
        $players = $resp->ok() ? $resp->json('data') : [];

        // get teams for dropdown and mapping
        $tresp = $this->client()->get($base.'/api/teams');
        $teams = $tresp->ok() ? $tresp->json('data') : [];
        // prepare map and grouped players by team
        $teamsMap = [];
        $grouped = [];
        foreach ($teams as $t) {
            $teamsMap[$t['id']] = ['name' => $t['name'], 'game' => $t['game']];
            $grouped[$t['id']] = []; // initialize empty group for every team
        }

        foreach ($players as $p) {
            $tid = $p['team_id'] ?? null;
            if ($tid !== null && array_key_exists($tid, $grouped)) {
                $grouped[$tid][] = $p;
            } else {
                // players without a known team_id: group under null key
                if (! isset($grouped[null])) { $grouped[null] = []; }
                $grouped[null][] = $p;
            }
        }

        return view('players.index', [
            'players' => $players,
            'role' => session('role'),
            'user_id' => session('user')['id'] ?? null,
            'teams' => $teams,
            'teamsMap' => $teamsMap,
            'grouped' => $grouped,
        ]);
    }

    public function store(Request $request)
    {
        if (session('role') !== 'admin') {
            return back()->withErrors(['error' => 'Forbidden']);
        }

        $data = $request->validate(['user_id'=>'nullable|integer','team_id'=>'nullable|integer','nickname'=>'required','role_in_game'=>'nullable','rank'=>'nullable']);
        $url = env('PROJECT_SERVICE_URL') . '/api/players';
        $this->client()->post($url, $data);
        return redirect('/players');
    }

    public function update(Request $request, $id)
    {
        $role = session('role');
        $userId = session('user')['id'] ?? null;

        // Admin or owner only
        if ($role !== 'admin') {
            $payload = $request->all();
            // project-service will enforce ownership; here we optimistically allow edit if user_id matches
            if (isset($payload['user_id']) && $payload['user_id'] != $userId) {
                return back()->withErrors(['error' => 'Forbidden']);
            }
        }

        $data = $request->validate(['nickname'=>'nullable','role_in_game'=>'nullable','rank'=>'nullable','team_id'=>'nullable|integer','user_id'=>'nullable|integer']);
        $url = env('PROJECT_SERVICE_URL') . "/api/players/{$id}";
        $this->client()->put($url, $data);
        return redirect('/players');
    }

    public function delete($id)
    {
        if (session('role') !== 'admin') {
            return back()->withErrors(['error' => 'Forbidden']);
        }

        $url = env('PROJECT_SERVICE_URL') . "/api/players/{$id}";
        $this->client()->delete($url);
        return redirect('/players');
    }
}
