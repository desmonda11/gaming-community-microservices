<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class MatchPageController extends Controller
{
    protected function client()
    {
        return Http::withToken(session('token'));
    }

    public function index()
    {
        $base = env('PROJECT_SERVICE_URL', 'http://127.0.0.1:8002');
        $resp = $this->client()->get($base.'/api/matches');
        $matches = $resp->ok() ? $resp->json('data') : [];

        // fetch teams for dropdown and mapping
        $tresp = $this->client()->get($base.'/api/teams');
        $teams = $tresp->ok() ? $tresp->json('data') : [];
        $teamsMap = [];
        foreach ($teams as $t) { $teamsMap[$t['id']] = $t['name'].' ('.$t['game'].')'; }

        // build games list and apply optional filter
        $games = [];
        foreach ($teams as $t) { if (!in_array($t['game'], $games)) $games[] = $t['game']; }
        $selectedGame = request()->query('game');
        if ($selectedGame && $selectedGame !== 'all') {
            $matches = array_values(array_filter($matches, function($m) use ($teams) {
                $team = null; foreach ($teams as $t) { if ($t['id'] == ($m['team_id'] ?? null)) { $team = $t; break; } }
                return $team && ($team['game'] ?? null) == request()->query('game');
            }));
        }

        // support edit: optional ?edit={id}
        $editing = null;
        $editId = request()->query('edit');
        if ($editId) {
            $eresp = $this->client()->get($base.'/api/matches/'.$editId);
            if ($eresp->ok()) { $editing = $eresp->json('data'); }
        }

        $sessUser = session('user');
        $sessUserId = session('user_id') ?? null;
        if (!$sessUserId) {
            if (is_array($sessUser) && isset($sessUser['id'])) $sessUserId = $sessUser['id'];
            if (is_object($sessUser) && isset($sessUser->id)) $sessUserId = $sessUser->id;
        }

        return view('matches.index', ['matches' => $matches, 'role' => session('role'), 'teams' => $teams, 'teamsMap' => $teamsMap, 'editing' => $editing, 'user_id' => $sessUserId]);
    }

    public function store(Request $request)
    {
        if (session('role') !== 'admin') {
            return back()->withErrors(['error' => 'Forbidden']);
        }

        $data = $request->validate(['team_id'=>'required|integer','opponent'=>'required','match_date'=>'nullable|date','result'=>'nullable','score_team'=>'nullable|integer','score_opponent'=>'nullable|integer']);
        $url = env('PROJECT_SERVICE_URL') . '/api/matches';
        $this->client()->post($url, $data);
        return redirect('/matches');
    }

    public function update(Request $request, $id)
    {
        if (session('role') !== 'admin') {
            return back()->withErrors(['error' => 'Forbidden']);
        }

        $data = $request->validate(['team_id'=>'sometimes|integer','opponent'=>'nullable','match_date'=>'nullable|date','result'=>'nullable','score_team'=>'nullable|integer','score_opponent'=>'nullable|integer']);
        $url = env('PROJECT_SERVICE_URL') . "/api/matches/{$id}";
        $this->client()->put($url, $data);
        return redirect('/matches');
    }

    public function delete($id)
    {
        if (session('role') !== 'admin') {
            return back()->withErrors(['error' => 'Forbidden']);
        }

        $url = env('PROJECT_SERVICE_URL') . "/api/matches/{$id}";
        $this->client()->delete($url);
        return redirect('/matches');
    }
}
