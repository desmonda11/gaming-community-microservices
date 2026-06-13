<?php

namespace App\Http\Controllers;

use App\Models\Inventory;
use Illuminate\Http\Request;

class InventoryController extends Controller
{
    public function index(Request $request)
    {
        $items = Inventory::with('team')->get();
        return response()->json(['data' => $items]);
    }

    public function store(Request $request)
    {
        $role = $request->get('auth_role');
        if ($role !== 'admin') {
            return response()->json(['error' => 'Forbidden'], 403);
        }

        $data = $request->validate([
            'team_id' => 'required|integer|exists:teams,id',
            'item_name' => 'required|string',
            'category' => 'nullable|string',
            'quantity' => 'nullable|integer',
            'condition' => 'nullable|string',
            'notes' => 'nullable|string',
        ]);

        $item = Inventory::create($data);
        return response()->json(['data' => $item], 201);
    }

    public function update(Request $request, $id)
    {
        $role = $request->get('auth_role');
        if ($role !== 'admin') {
            return response()->json(['error' => 'Forbidden'], 403);
        }

        $item = Inventory::findOrFail($id);
        $data = $request->validate([
            'item_name' => 'sometimes|string',
            'category' => 'nullable|string',
            'quantity' => 'nullable|integer',
            'condition' => 'nullable|string',
            'notes' => 'nullable|string',
        ]);

        $item->update($data);
        return response()->json(['data' => $item]);
    }

    public function destroy(Request $request, $id)
    {
        $role = $request->get('auth_role');
        if ($role !== 'admin') {
            return response()->json(['error' => 'Forbidden'], 403);
        }

        $item = Inventory::findOrFail($id);
        $item->delete();
        return response()->json(['message' => 'Deleted']);
    }
}
