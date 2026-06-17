<?php

namespace App\Http\Controllers;

use App\Models\Team;
use Illuminate\Http\Request;

class TeamController extends Controller
{
    public function index(Request $request)
    {
        $teams = Team::all();
        return response()->json(['data' => $teams]);
    }

    public function store(Request $request)
    {
        $role = $request->auth_role ?? $request->get('auth_role');
        $authUserId = $request->auth_user_id ?? $request->get('auth_user_id');

        // both admin and user can create teams; users become owner automatically
        $data = $request->validate([
            'name' => 'required|string',
            'game' => 'required|string',
            'description' => 'nullable|string',
            'owner_user_id' => 'nullable|integer'
        ]);

        if ($role !== 'admin') {
            // set owner to the authenticated user
            $data['owner_user_id'] = $authUserId;
        } else {
            // admin may set owner_user_id or leave null
            $data['owner_user_id'] = $data['owner_user_id'] ?? null;
        }

        $team = Team::create($data);
        return response()->json(['data' => $team], 201);
    }

    public function update(Request $request, $id)
    {
        $role = $request->auth_role ?? $request->get('auth_role');
        $authUserId = $request->auth_user_id ?? $request->get('auth_user_id');

        $team = Team::findOrFail($id);

        // admin can update any, user only their own teams
        if ($role !== 'admin' && ($team->owner_user_id ?? null) != $authUserId) {
            return response()->json(['error' => 'Forbidden', 'message' => 'You can only manage your own team data'], 403);
        }

        $data = $request->validate([
            'name' => 'sometimes|string',
            'game' => 'sometimes|string',
            'description' => 'nullable|string',
            'owner_user_id' => 'nullable|integer'
        ]);

        // prevent user from changing owner_user_id
        if ($role !== 'admin') unset($data['owner_user_id']);

        $team->update($data);
        return response()->json(['data' => $team]);
    }

    public function destroy(Request $request, $id)
    {
        $role = $request->auth_role ?? $request->get('auth_role');
        if ($role !== 'admin') {
            return response()->json(['error' => 'Forbidden', 'message' => 'Access denied'], 403);
        }

        $team = Team::findOrFail($id);
        $team->delete();
        return response()->json(['message' => 'Deleted']);
    }
}
