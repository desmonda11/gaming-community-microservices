<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class StatisticPageController extends Controller
{
    protected function client()
    {
        return Http::withToken(session('token'));
    }

    public function index(Request $request)
    {
        $base = env('PROJECT_SERVICE_URL', 'http://127.0.0.1:8002');
        // fetch teams, players, matches, statistics
        $teamsResp = $this->client()->get($base.'/api/teams');
        $teams = $teamsResp->ok() ? $teamsResp->json('data') : [];

        $playersResp = $this->client()->get($base.'/api/players');
        $players = $playersResp->ok() ? $playersResp->json('data') : [];

        $matchesResp = $this->client()->get($base.'/api/matches');
        $matches = $matchesResp->ok() ? $matchesResp->json('data') : [];

        $statsResp = $this->client()->get($base.'/api/statistics');
        $statistics = $statsResp->ok() ? $statsResp->json('data') : [];

        // build mappings
        $teamsMap = [];
        foreach ($teams as $t) { $teamsMap[$t['id']] = $t; }

        $playersMap = [];
        foreach ($players as $p) { $playersMap[$p['id']] = $p; }

        // group matches by team_id and compute wins/losses
        $matchesByTeam = [];
        foreach ($matches as $m) {
            $tid = $m['team_id'] ?? null;
            $matchesByTeam[$tid][] = $m;
        }

        // group statistics by player and sum (in case multiple entries)
        $statsByPlayer = [];
        foreach ($statistics as $s) {
            $pid = $s['player_id'];
            if (!isset($statsByPlayer[$pid])) {
                $statsByPlayer[$pid] = ['matches_played'=>0,'win'=>0,'lose'=>0,'kill'=>0,'death'=>0,'assist'=>0,'stat_id'=>null];
            }
            $statsByPlayer[$pid]['matches_played'] += intval($s['matches_played'] ?? 0);
            $statsByPlayer[$pid]['win'] += intval($s['win'] ?? 0);
            $statsByPlayer[$pid]['lose'] += intval($s['lose'] ?? 0);
            $statsByPlayer[$pid]['kill'] += intval($s['kill'] ?? 0);
            $statsByPlayer[$pid]['death'] += intval($s['death'] ?? 0);
            $statsByPlayer[$pid]['assist'] += intval($s['assist'] ?? 0);
            // keep a reference to a statistic record id for update/delete actions (last one wins)
            $statsByPlayer[$pid]['stat_id'] = $s['id'] ?? $statsByPlayer[$pid]['stat_id'];
        }

        // extract unique games
        $games = [];
        foreach ($teams as $team) {
            if (!in_array($team['game'], $games)) $games[] = $team['game'];
        }

        // apply optional game filter from query
        $selectedGame = request()->query('game');
        $teamsForLoop = $teams;
        if ($selectedGame && $selectedGame !== 'all') {
            $teamsForLoop = array_values(array_filter($teams, function($t) use ($selectedGame) { return ($t['game'] ?? null) == $selectedGame; }));
        }

        // prepare per-team aggregated data
        $teamsData = [];
        foreach ($teamsForLoop as $team) {
            $tid = $team['id'];
            $teamMatches = $matchesByTeam[$tid] ?? [];
            $totalMatches = count($teamMatches);
            $wins = 0; $losses = 0;
            foreach ($teamMatches as $m) {
                if (!empty($m['result']) && strtolower($m['result']) === 'win') {
                    $wins++;
                } elseif (!empty($m['result']) && strtolower($m['result']) === 'lose') {
                    $losses++;
                } else {
                    // fallback to score comparison
                    if (isset($m['score_team']) && isset($m['score_opponent'])) {
                        if (intval($m['score_team']) > intval($m['score_opponent'])) $wins++;
                        if (intval($m['score_team']) < intval($m['score_opponent'])) $losses++;
                    }
                }
            }
            $winRate = $totalMatches > 0 ? round(($wins / $totalMatches) * 100, 2) : 0;

            // players in this team
            $teamPlayers = array_values(array_filter($players, function($p) use ($tid) { return ($p['team_id'] ?? null) == $tid; }));
            $playerStats = [];
            foreach ($teamPlayers as $p) {
                $pid = $p['id'];
                $s = $statsByPlayer[$pid] ?? null;
                if ($s) {
                    $kill = $s['kill']; $death = $s['death']; $assist = $s['assist'];
                    $kda = $death > 0 ? round(($kill + $assist) / $death, 2) : ($kill + $assist);
                    $playerStats[] = array_merge(['player' => $p], $s, ['kda' => $kda]);
                } else {
                    $playerStats[] = array_merge(['player' => $p], ['matches_played'=>0,'win'=>0,'lose'=>0,'kill'=>0,'death'=>0,'assist'=>0,'kda'=>0]);
                }
            }

            $teamsData[] = ['team' => $team, 'total_matches' => $totalMatches, 'wins' => $wins, 'losses' => $losses, 'win_rate' => $winRate, 'players' => $playerStats];
        }

        // determine session user id early (used for dropdown filtering)
        $sessUser = session('user');
        $sessUserId = session('user_id') ?? null;
        if (!$sessUserId) {
            if (is_array($sessUser) && isset($sessUser['id'])) $sessUserId = $sessUser['id'];
            if (is_object($sessUser) && isset($sessUser->id)) $sessUserId = $sessUser->id;
        }

        // prepare dropdown labels: "Nickname - Team - Game"
        $playerDropdown = [];
        $role = session('role');
        foreach ($players as $p) {
            $t = $teamsMap[$p['team_id']] ?? null;
            // if a game filter is active, skip players not in that game
            if ($selectedGame && $selectedGame !== 'all' && $t && ($t['game'] ?? '') !== $selectedGame) continue;
            // if user, only include players from teams they own
            if ($role !== 'admin') {
                // if team lacks owner info, skip (server should provide owner_user_id)
                if (! $t || !isset($t['owner_user_id']) || $t['owner_user_id'] != ($sessUserId ?? null)) continue;
            }
            $label = $p['nickname'] . ($t ? ' - ' . ($t['name'] ?? '') . ' - ' . ($t['game'] ?? '') : '');
            $playerDropdown[] = ['id' => $p['id'], 'label' => $label];
        }

        $sessUser = session('user');
        $sessUserId = session('user_id') ?? null;
        if (!$sessUserId) {
            if (is_array($sessUser) && isset($sessUser['id'])) $sessUserId = $sessUser['id'];
            if (is_object($sessUser) && isset($sessUser->id)) $sessUserId = $sessUser->id;
        }

        // support optional editing via ?edit={stat_id}
        $editing = false;
        $editingStat = null;
        $editId = $request->query('edit');
        if ($editId) {
            $resp = $this->client()->get($base . "/api/statistics/{$editId}");
            if ($resp->ok()) {
                $editing = true;
                $editingStat = $resp->json('data');
            }
        }

        return view('statistics.index', ['teamsData' => $teamsData, 'role' => session('role'), 'user_id' => $sessUserId, 'playerDropdown' => $playerDropdown, 'games' => $games, 'selectedGame' => $selectedGame ?? 'all', 'editing' => $editing, 'editingStat' => $editingStat]);
    }

    public function store(Request $request)
    {
        // only admin may create statistics from frontend
        if (session('role') !== 'admin') {
            return back()->withErrors(['error' => 'Only admin can manage KDA statistics']);
        }

        $data = $request->validate(['player_id'=>'required|integer','matches_played'=>'nullable|integer','win'=>'nullable|integer','lose'=>'nullable|integer','kill'=>'nullable|integer','death'=>'nullable|integer','assist'=>'nullable|integer','kda'=>'nullable|numeric']);
        $url = env('PROJECT_SERVICE_URL') . '/api/statistics';
        $this->client()->post($url, $data);
        return redirect('/statistics');
    }

    public function update(Request $request, $id)
    {
        if (session('role') !== 'admin') {
            return back()->withErrors(['error' => 'Only admin can manage KDA statistics']);
        }

        $data = $request->validate(['matches_played'=>'nullable|integer','win'=>'nullable|integer','lose'=>'nullable|integer','kill'=>'nullable|integer','death'=>'nullable|integer','assist'=>'nullable|integer','kda'=>'nullable|numeric']);
        $url = env('PROJECT_SERVICE_URL') . "/api/statistics/{$id}";
        $this->client()->put($url, $data);
        return redirect('/statistics');
    }

    public function delete($id)
    {
        if (session('role') !== 'admin') {
            return back()->withErrors(['error' => 'Forbidden']);
        }

        $url = env('PROJECT_SERVICE_URL') . "/api/statistics/{$id}";
        $this->client()->delete($url);
        return redirect('/statistics');
    }
}
