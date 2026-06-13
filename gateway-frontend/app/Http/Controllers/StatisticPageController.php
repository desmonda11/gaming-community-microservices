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

    public function index()
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
                $statsByPlayer[$pid] = ['matches_played'=>0,'win'=>0,'lose'=>0,'kill'=>0,'death'=>0,'assist'=>0];
            }
            $statsByPlayer[$pid]['matches_played'] += intval($s['matches_played'] ?? 0);
            $statsByPlayer[$pid]['win'] += intval($s['win'] ?? 0);
            $statsByPlayer[$pid]['lose'] += intval($s['lose'] ?? 0);
            $statsByPlayer[$pid]['kill'] += intval($s['kill'] ?? 0);
            $statsByPlayer[$pid]['death'] += intval($s['death'] ?? 0);
            $statsByPlayer[$pid]['assist'] += intval($s['assist'] ?? 0);
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

        // prepare dropdown labels: "Nickname - Team - Game"
        $playerDropdown = [];
        foreach ($players as $p) {
            $t = $teamsMap[$p['team_id']] ?? null;
            // if a game filter is active, skip players not in that game
            if ($selectedGame && $selectedGame !== 'all' && $t && ($t['game'] ?? '') !== $selectedGame) continue;
            $label = $p['nickname'] . ($t ? ' - ' . ($t['name'] ?? '') . ' - ' . ($t['game'] ?? '') : '');
            $playerDropdown[] = ['id' => $p['id'], 'label' => $label];
        }

        return view('statistics.index', ['teamsData' => $teamsData, 'role' => session('role'), 'user_id' => session('user')['id'] ?? null, 'playerDropdown' => $playerDropdown, 'games' => $games, 'selectedGame' => $selectedGame ?? 'all']);
    }

    public function store(Request $request)
    {
        if (session('role') !== 'admin') {
            return back()->withErrors(['error' => 'Forbidden']);
        }

        $data = $request->validate(['player_id'=>'required|integer','matches_played'=>'nullable|integer','win'=>'nullable|integer','lose'=>'nullable|integer','kill'=>'nullable|integer','death'=>'nullable|integer','assist'=>'nullable|integer','kda'=>'nullable|numeric']);
        $url = env('PROJECT_SERVICE_URL') . '/api/statistics';
        $this->client()->post($url, $data);
        return redirect('/statistics');
    }

    public function update(Request $request, $id)
    {
        $role = session('role');
        $userId = session('user')['id'] ?? null;

        // If not admin, ensure user owns the player via project-service enforcement
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
