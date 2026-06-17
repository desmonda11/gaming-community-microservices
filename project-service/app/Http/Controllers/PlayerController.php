<?php

namespace App\Http\Controllers;

use App\Models\Player;
use App\Models\Team;
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
        $role = $request->auth_role ?? $request->get('auth_role');
        $authUserId = $request->auth_user_id ?? $request->get('auth_user_id');

        $data = $request->validate([
            'user_id' => 'nullable|integer',
            'team_id' => 'nullable|integer|exists:teams,id',
            'nickname' => 'required|string',
            'role_in_game' => 'nullable|string',
            'rank' => 'nullable|string',
        ]);

        // if user role, ensure they can only add players to their own teams
        if ($role !== 'admin') {
            if (empty($data['team_id'])) {
                return response()->json(['error' => 'Forbidden', 'message' => 'You can only manage your own team data'], 403);
            }
            $team = Team::find($data['team_id']);
            if (! $team || ($team->owner_user_id ?? null) != $authUserId) {
                return response()->json(['error' => 'Forbidden', 'message' => 'You can only manage your own team data'], 403);
            }
        }

        $player = Player::create($data);
        return response()->json(['data' => $player], 201);
    }

    public function update(Request $request, $id)
    {
        $authRole = $request->auth_role ?? $request->get('auth_role');
        $authUserId = $request->auth_user_id ?? $request->get('auth_user_id');

        $player = Player::findOrFail($id);

        // Admin can update any, user only players that belong to teams they own
        if ($authRole !== 'admin') {
            $team = $player->team;
            if (! $team || ($team->owner_user_id ?? null) != $authUserId) {
                return response()->json(['error' => 'Forbidden', 'message' => 'You can only manage your own team data'], 403);
            }
        }

        $data = $request->validate([
            'team_id' => 'nullable|integer|exists:teams,id',
            'nickname' => 'sometimes|string',
            'role_in_game' => 'nullable|string',
            'rank' => 'nullable|string',
        ]);

        // if user is moving player to another team, ensure ownership of target team
        if ($authRole !== 'admin' && isset($data['team_id'])) {
            $target = Team::find($data['team_id']);
            if (! $target || ($target->owner_user_id ?? null) != $authUserId) {
                return response()->json(['error' => 'Forbidden', 'message' => 'You can only manage your own team data'], 403);
            }
        }

        $player->update($data);
        return response()->json(['data' => $player]);
    }

    public function destroy(Request $request, $id)
    {
        $role = $request->auth_role ?? $request->get('auth_role');
        if ($role !== 'admin') {
            return response()->json(['error' => 'Forbidden', 'message' => 'Access denied'], 403);
        }

        $player = Player::findOrFail($id);
        $player->delete();
        return response()->json(['message' => 'Deleted']);
    }
}
