@extends('layouts.app')

@section('content')
<div>
  <h1 class="text-xl mb-4">Statistik KDA</h1>
  <div class="flex items-center mb-4 space-x-4">
    <form method="GET" action="/statistics" class="flex items-center">
      <select name="game" class="p-2 bg-blue-800 rounded">
        <option value="all" @if(($selectedGame??'all')=='all') selected @endif>Semua Game</option>
        @foreach($games as $g)
          <option value="{{ $g }}" @if(($selectedGame??'all')==$g) selected @endif>{{ $g }}</option>
        @endforeach
      </select>
      <button class="ml-2 bg-blue-600 px-3 py-1 rounded">Filter</button>
    </form>
  </div>

  @if($role === 'admin')
  <form method="POST" action="/statistics" class="mb-4 bg-blue-900 p-4 rounded">
    @csrf
    <div class="grid grid-cols-4 gap-2">
      <select name="player_id" class="p-2 bg-blue-800 rounded">
        <option value="">-- Pilih Player --</option>
        @foreach($playerDropdown as $p)
          <option value="{{ $p['id'] }}">{{ $p['label'] }}</option>
        @endforeach
      </select>
      <input name="matches_played" placeholder="Matches Played" class="p-2 bg-blue-800 rounded" />
      <input name="win" placeholder="Win" class="p-2 bg-blue-800 rounded" />
      <input name="lose" placeholder="Lose" class="p-2 bg-blue-800 rounded" />
    </div>
    <div class="grid grid-cols-3 gap-2 mt-2">
      <input name="kill" placeholder="Kill" class="p-2 bg-blue-800 rounded" />
      <input name="death" placeholder="Death" class="p-2 bg-blue-800 rounded" />
      <input name="assist" placeholder="Assist" class="p-2 bg-blue-800 rounded" />
    </div>
    <div class="mt-2"><button class="bg-green-600 px-3 py-1 rounded">Tambah Statistik</button></div>
  </form>
  @endif
  <div class="grid grid-cols-1 gap-4">
    @foreach($teamsData as $td)
    <div class="bg-blue-900 p-4 rounded shadow">
      <div class="flex justify-between items-start">
        <div>
          <h2 class="text-lg font-bold">{{ $td['team']['name'] }}</h2>
          <div class="text-sm text-blue-200">Game: {{ $td['team']['game'] }}</div>
        </div>
        <div class="space-x-2 text-sm">
          <span class="bg-blue-800 text-white px-2 py-1 rounded">Total Match: {{ $td['total_matches'] }}</span>
          <span class="bg-green-700 text-white px-2 py-1 rounded">Win: {{ $td['wins'] }}</span>
          <span class="bg-red-700 text-white px-2 py-1 rounded">Lose: {{ $td['losses'] }}</span>
          <span class="bg-yellow-600 text-black px-2 py-1 rounded">Win Rate: {{ $td['win_rate'] }}%</span>
        </div>
      </div>

      <div class="mt-3 bg-blue-800 p-2 rounded">
        @if(count($td['players']) === 0)
          <div class="text-blue-200">Belum ada statistik player</div>
        @else
          <table class="w-full text-left text-sm">
            <thead><tr class="text-blue-100"><th class="p-1">Player</th><th>Role</th><th>MP</th><th>W</th><th>L</th><th>K</th><th>D</th><th>A</th><th>KDA</th></tr></thead>
            <tbody>
              @foreach($td['players'] as $ps)
                <tr class="border-t border-blue-700">
                  <td class="p-1">{{ $ps['player']['nickname'] }}</td>
                  <td>{{ $ps['player']['role'] ?? '-' }}</td>
                  <td>{{ $ps['matches_played'] }}</td>
                  <td>{{ $ps['win'] }}</td>
                  <td>{{ $ps['lose'] }}</td>
                  <td>{{ $ps['kill'] }}</td>
                  <td>{{ $ps['death'] }}</td>
                  <td>{{ $ps['assist'] }}</td>
                  <td>{{ $ps['kda'] }}</td>
                </tr>
              @endforeach
            </tbody>
          </table>
        @endif
      </div>
    </div>
    @endforeach
  </div>
</div>
@endsection
