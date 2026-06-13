<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class InventoryPageController extends Controller
{
    protected function client()
    {
        return Http::withToken(session('token'));
    }

    public function index()
    {
        $base = env('PROJECT_SERVICE_URL', 'http://127.0.0.1:8002');
        $resp = $this->client()->get($base.'/api/inventories');
        $items = $resp->ok() ? $resp->json('data') : [];

        // fetch teams for dropdown and mapping
        $tresp = $this->client()->get($base.'/api/teams');
        $teams = $tresp->ok() ? $tresp->json('data') : [];
        $teamsMap = [];
        foreach ($teams as $t) { $teamsMap[$t['id']] = $t['name'].' ('.$t['game'].')'; }

        // build games list
        $games = [];
        foreach ($teams as $t) { if (!in_array($t['game'], $games)) $games[] = $t['game']; }
        $selectedGame = request()->query('game');
        if ($selectedGame && $selectedGame !== 'all') {
            $items = array_values(array_filter($items, function($it) use ($teams, $selectedGame) {
                $team = null; foreach ($teams as $t) { if ($t['id'] == ($it['team_id'] ?? null)) { $team = $t; break; } }
                return $team && ($team['game'] ?? null) == $selectedGame;
            }));
        }

        return view('inventories.index', ['items' => $items, 'role' => session('role'), 'teams' => $teams, 'teamsMap' => $teamsMap, 'games' => $games, 'selectedGame' => $selectedGame ?? 'all']);
    }

    public function store(Request $request)
    {
        if (session('role') !== 'admin') {
            return back()->withErrors(['error' => 'Forbidden']);
        }

        $data = $request->validate(['team_id'=>'required|integer','item_name'=>'required','category'=>'nullable','quantity'=>'nullable|integer','condition'=>'nullable','notes'=>'nullable']);
        $url = env('PROJECT_SERVICE_URL') . '/api/inventories';
        $this->client()->post($url, $data);
        return redirect('/inventories');
    }

    public function update(Request $request, $id)
    {
        if (session('role') !== 'admin') {
            return back()->withErrors(['error' => 'Forbidden']);
        }

        $data = $request->validate(['item_name'=>'nullable','category'=>'nullable','quantity'=>'nullable|integer','condition'=>'nullable','notes'=>'nullable']);
        $url = env('PROJECT_SERVICE_URL') . "/api/inventories/{$id}";
        $this->client()->put($url, $data);
        return redirect('/inventories');
    }

    public function delete($id)
    {
        if (session('role') !== 'admin') {
            return back()->withErrors(['error' => 'Forbidden']);
        }

        $url = env('PROJECT_SERVICE_URL') . "/api/inventories/{$id}";
        $this->client()->delete($url);
        return redirect('/inventories');
    }
}
