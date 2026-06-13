<?php

namespace App\Http\Controllers;

use App\Models\Player;
use Illuminate\Http\Request;

class PlayerController extends Controller
{
    public function index(Request $request)
    {
        $players = Player::with('team')->get();
        return response()->json(['data' => $players]);
    }

    public function store(Request $request)
    {
        $role = $request->get('auth_role');
        if ($role !== 'admin') {
            return response()->json(['error' => 'Forbidden'], 403);
        }

        $data = $request->validate([
            'user_id' => 'nullable|integer',
            'team_id' => 'nullable|integer|exists:teams,id',
            'nickname' => 'required|string',
            'role_in_game' => 'nullable|string',
            'rank' => 'nullable|string',
        ]);

        $player = Player::create($data);
        return response()->json(['data' => $player], 201);
    }

    public function update(Request $request, $id)
    {
        $authRole = $request->get('auth_role');
        $authUserId = $request->get('auth_user_id');

        $player = Player::findOrFail($id);

        // Admin can update any, user only their own player record (user_id)
        if ($authRole !== 'admin' && $player->user_id != $authUserId) {
            return response()->json(['error' => 'Forbidden'], 403);
        }

        $data = $request->validate([
            'team_id' => 'nullable|integer|exists:teams,id',
            'nickname' => 'sometimes|string',
            'role_in_game' => 'nullable|string',
            'rank' => 'nullable|string',
        ]);

        $player->update($data);
        return response()->json(['data' => $player]);
    }

    public function destroy(Request $request, $id)
    {
        $role = $request->get('auth_role');
        if ($role !== 'admin') {
            return response()->json(['error' => 'Forbidden'], 403);
        }

        $player = Player::findOrFail($id);
        $player->delete();
        return response()->json(['message' => 'Deleted']);
    }
}
