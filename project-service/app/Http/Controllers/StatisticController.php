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
        $role = $request->get('auth_role');
        if ($role !== 'admin') {
            return response()->json(['error' => 'Forbidden'], 403);
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

        $stat = Statistic::create($data);
        return response()->json(['data' => $stat], 201);
    }

    public function update(Request $request, $id)
    {
        $authRole = $request->get('auth_role');
        $authUserId = $request->get('auth_user_id');

        $stat = Statistic::findOrFail($id);

        // Admin can update any, user only if the stat belongs to their player record
        if ($authRole !== 'admin') {
            $player = $stat->player;
            if (! $player || $player->user_id != $authUserId) {
                return response()->json(['error' => 'Forbidden'], 403);
            }
        }

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
        $role = $request->get('auth_role');
        if ($role !== 'admin') {
            return response()->json(['error' => 'Forbidden'], 403);
        }

        $stat = Statistic::findOrFail($id);
        $stat->delete();
        return response()->json(['message' => 'Deleted']);
    }
}
