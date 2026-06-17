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

    public function index(Request $request)
    {
        $base = env('PROJECT_SERVICE_URL', 'http://127.0.0.1:8002');

        // get players
        $resp = $this->client()->get($base.'/api/players');
        $players = $resp->ok() ? $resp->json('data') : [];

        // get teams for dropdown and mapping
        $tresp = $this->client()->get($base.'/api/teams');
        $teams = $tresp->ok() ? $tresp->json('data') : [];
        // determine teams the logged-in user owns (for non-admins)
        $role = session('role');
        $sessUserId = session('user_id') ?? (is_array(session('user')) ? session('user')['id'] ?? null : (is_object(session('user')) ? session('user')->id ?? null : null));
        $teamsForDropdown = [];
        foreach ($teams as $t) {
            if ($role === 'admin') {
                $teamsForDropdown[] = $t;
            } else {
                if (isset($t['owner_user_id']) && $t['owner_user_id'] == $sessUserId) {
                    $teamsForDropdown[] = $t;
                }
            }
        }
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

        // determine logged-in user id (session may store user as array/object or user_id separately)
        $sessUser = session('user');
        $sessUserId = session('user_id') ?? null;
        if (!$sessUserId) {
            if (is_array($sessUser) && isset($sessUser['id'])) $sessUserId = $sessUser['id'];
            if (is_object($sessUser) && isset($sessUser->id)) $sessUserId = $sessUser->id;
        }

        // handle optional edit query param to prefill edit form
        $editing = null;
        $editId = $request->query('edit');
        if ($editId) {
            foreach ($players as $p) {
                if ((string)($p['id'] ?? '') === (string)$editId) { $editing = $p; break; }
            }
        }

        return view('players.index', [
            'players' => $players,
            'role' => session('role'),
            'user_id' => $sessUserId,
            'teams' => $teams,
            'teamsMap' => $teamsMap,
            'grouped' => $grouped,
            'teamsForDropdown' => $teamsForDropdown,
            'editing' => $editing,
        ]);
    }

    public function store(Request $request)
    {
        $role = session('role');
        $userId = session('user_id') ?? (is_array(session('user')) ? session('user')['id'] ?? null : (is_object(session('user')) ? session('user')->id ?? null : null));

        $data = $request->validate(['user_id'=>'nullable|integer','team_id'=>'nullable|integer','nickname'=>'required','role_in_game'=>'nullable','rank'=>'nullable']);

        // if user, ensure selected team belongs to them
        if ($role !== 'admin') {
            $teamsForDropdown = array_column($request->input('teams_for_dropdown', []), 'id');
            // fallback: we rely on server-enforced ownership. frontend prevents obvious mistakes by only showing owned teams.
        }

        $url = env('PROJECT_SERVICE_URL') . '/api/players';
        $this->client()->post($url, $data);
        return redirect('/players');
    }

    public function update(Request $request, $id)
    {
        $role = session('role');
        $userId = session('user')['id'] ?? null;
        // allow update; project-service will enforce ownership rules
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
