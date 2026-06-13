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
        $role = $request->get('auth_role');
        if ($role !== 'admin') {
            return response()->json(['error' => 'Forbidden'], 403);
        }

        $data = $request->validate([
            'name' => 'required|string',
            'game' => 'required|string',
            'description' => 'nullable|string',
        ]);

        $team = Team::create($data);
        return response()->json(['data' => $team], 201);
    }

    public function update(Request $request, $id)
    {
        $role = $request->get('auth_role');
        if ($role !== 'admin') {
            return response()->json(['error' => 'Forbidden'], 403);
        }

        $team = Team::findOrFail($id);
        $data = $request->validate([
            'name' => 'sometimes|string',
            'game' => 'sometimes|string',
            'description' => 'nullable|string',
        ]);

        $team->update($data);
        return response()->json(['data' => $team]);
    }

    public function destroy(Request $request, $id)
    {
        $role = $request->get('auth_role');
        if ($role !== 'admin') {
            return response()->json(['error' => 'Forbidden'], 403);
        }

        $team = Team::findOrFail($id);
        $team->delete();
        return response()->json(['message' => 'Deleted']);
    }
}
