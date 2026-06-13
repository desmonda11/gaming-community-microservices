<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class TeamPageController extends Controller
{
    protected function client()
    {
        return Http::withToken(session('token'));
    }

    public function index()
    {
        $url = env('PROJECT_SERVICE_URL', 'http://127.0.0.1:8002') . '/api/teams';
        $resp = $this->client()->get($url);
        $teams = $resp->ok() ? $resp->json('data') : [];
        return view('teams.index', ['teams' => $teams, 'role' => session('role')]);
    }

    public function store(Request $request)
    {
        if (session('role') !== 'admin') {
            return back()->withErrors(['error' => 'Forbidden']);
        }

        $data = $request->validate(['name' => 'required', 'game' => 'required', 'description' => 'nullable']);
        $url = env('PROJECT_SERVICE_URL') . '/api/teams';
        $this->client()->post($url, $data);
        return redirect('/teams');
    }

    public function update(Request $request, $id)
    {
        if (session('role') !== 'admin') {
            return back()->withErrors(['error' => 'Forbidden']);
        }

        $data = $request->validate(['name' => 'sometimes', 'game' => 'sometimes', 'description' => 'nullable']);
        $url = env('PROJECT_SERVICE_URL') . "/api/teams/{$id}";
        $this->client()->put($url, $data);
        return redirect('/teams');
    }

    public function delete($id)
    {
        if (session('role') !== 'admin') {
            return back()->withErrors(['error' => 'Forbidden']);
        }

        $url = env('PROJECT_SERVICE_URL') . "/api/teams/{$id}";
        $this->client()->delete($url);
        return redirect('/teams');
    }
}
