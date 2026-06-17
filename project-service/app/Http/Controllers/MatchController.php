<?php

namespace App\Http\Controllers;

use App\Models\MatchModel;
use Illuminate\Http\Request;

class MatchController extends Controller
{
    public function index(Request $request)
    {
        $matches = MatchModel::with('team')->get();
        return response()->json(['data' => $matches]);
    }

    public function store(Request $request)
    {
        $role = $request->auth_role ?? $request->get('auth_role');
        if ($role !== 'admin') {
            return response()->json(['error' => 'Forbidden', 'message' => 'Access denied'], 403);
        }

        $data = $request->validate([
            'team_id' => 'required|integer|exists:teams,id',
            'opponent' => 'required|string',
            'match_date' => 'nullable|date',
            'result' => 'nullable|string',
            'score_team' => 'nullable|integer',
            'score_opponent' => 'nullable|integer',
        ]);

        $match = MatchModel::create($data);
        return response()->json(['data' => $match], 201);
    }

    public function update(Request $request, $id)
    {
        $role = $request->auth_role ?? $request->get('auth_role');
        if ($role !== 'admin') {
            return response()->json(['error' => 'Forbidden', 'message' => 'Access denied'], 403);
        }

        $match = MatchModel::findOrFail($id);
        $data = $request->validate([
            'team_id' => 'sometimes|integer|exists:teams,id',
            'opponent' => 'sometimes|string',
            'match_date' => 'nullable|date',
            'result' => 'nullable|string',
            'score_team' => 'nullable|integer',
            'score_opponent' => 'nullable|integer',
        ]);

        $match->update($data);
        return response()->json(['data' => $match]);
    }

    public function destroy(Request $request, $id)
    {
        $role = $request->auth_role ?? $request->get('auth_role');
        if ($role !== 'admin') {
            return response()->json(['error' => 'Forbidden', 'message' => 'Access denied'], 403);
        }

        $match = MatchModel::findOrFail($id);
        $match->delete();
        return response()->json(['message' => 'Deleted']);
    }

    public function show($id)
    {
        $match = MatchModel::with('team')->findOrFail($id);
        return response()->json(['data' => $match]);
    }
}
