<?php

namespace App\Http\Controllers;

use App\Models\Statistic;
use Illuminate\Http\Request;

class StatisticController extends Controller
{
    public function index(Request $request)
    {
        $stats = Statistic::with('player')->get();
        return response()->json(['data' => $stats]);
    }

    public function store(Request $request)
    {
        $role = $request->auth_role ?? $request->get('auth_role');
        $authUserId = $request->auth_user_id ?? $request->get('auth_user_id');

        // Only admin may create statistics
        if ($role !== 'admin') {
            return response()->json(['error' => 'Forbidden', 'message' => 'Only admin can manage KDA statistics'], 403);
        }
        $data = $request->validate([
            'player_id' => 'required|integer|exists:players,id',
            'matches_played' => 'nullable|integer',
            'win' => 'nullable|integer',
            'lose' => 'nullable|integer',
            'kill' => 'nullable|integer',
            'death' => 'nullable|integer',
            'assist' => 'nullable|integer',
            'kda' => 'nullable|numeric',
        ]);

        // user may create statistic only for players in teams they own
        if ($role !== 'admin') {
            $player = \App\Models\Player::with('team')->find($data['player_id']);
            if (! $player || ! $player->team || ($player->team->owner_user_id ?? null) != $authUserId) {
                return response()->json(['error' => 'Forbidden', 'message' => 'You can only manage your own team data'], 403);
            }
        }

        $stat = Statistic::create($data);
        return response()->json(['data' => $stat], 201);
    }

    public function update(Request $request, $id)
    {
        $authRole = $request->auth_role ?? $request->get('auth_role');

        // Only admin may update statistics
        if ($authRole !== 'admin') {
            return response()->json(['error' => 'Forbidden', 'message' => 'Only admin can manage KDA statistics'], 403);
        }

        $stat = Statistic::findOrFail($id);

        $data = $request->validate([
            'matches_played' => 'nullable|integer',
            'win' => 'nullable|integer',
            'lose' => 'nullable|integer',
            'kill' => 'nullable|integer',
            'death' => 'nullable|integer',
            'assist' => 'nullable|integer',
            'kda' => 'nullable|numeric',
        ]);

        $stat->update($data);
        return response()->json(['data' => $stat]);
    }

    public function destroy(Request $request, $id)
    {
        $role = $request->auth_role ?? $request->get('auth_role');
        if ($role !== 'admin') {
            return response()->json(['error' => 'Forbidden', 'message' => 'Only admin can manage KDA statistics'], 403);
        }

        $stat = Statistic::findOrFail($id);
        $stat->delete();
        return response()->json(['message' => 'Deleted']);
    }
}
