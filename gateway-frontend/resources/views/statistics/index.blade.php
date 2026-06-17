@extends('layouts.app')

@section('pageTitle','Statistik KDA')

@section('content')
<div class="space-y-6">
  <div class="flex items-center justify-between">
    <h1 class="text-2xl font-bold">Statistik KDA</h1>
    <form method="GET" action="/statistics" class="flex items-center">
      <select name="game" class="p-2 bg-indigo-800 rounded">
        <option value="all" @if(($selectedGame??'all')=='all') selected @endif>Semua Game</option>
        @foreach($games as $g)
          <option value="{{ $g }}" @if(($selectedGame??'all')==$g) selected @endif>{{ $g }}</option>
        @endforeach
      </select>
      <button class="ml-2 bg-electric text-white px-3 py-1 rounded">Filter</button>
    </form>
  </div>

  @if($role === 'admin')
  <div class="bg-indigo-900 p-4 rounded card-shadow">
    @if(!empty($editing) && $editing && !empty($editingStat))
      <form method="POST" action="/statistics/{{ $editingStat['id'] }}/update" class="grid grid-cols-1 md:grid-cols-4 gap-3">
        @csrf
        <select name="player_id" class="p-2 bg-indigo-800 rounded">
          <option value="">-- Pilih Player --</option>
          @foreach($playerDropdown as $p)
            <option value="{{ $p['id'] }}" @if(($editingStat['player_id'] ?? '') == $p['id']) selected @endif>{{ $p['label'] }}</option>
          @endforeach
        </select>
        <input name="matches_played" value="{{ $editingStat['matches_played'] ?? '' }}" placeholder="Matches Played" class="p-2 bg-indigo-800 rounded" />
        <input name="win" value="{{ $editingStat['win'] ?? '' }}" placeholder="Win" class="p-2 bg-indigo-800 rounded" />
        <input name="lose" value="{{ $editingStat['lose'] ?? '' }}" placeholder="Lose" class="p-2 bg-indigo-800 rounded" />
        <input name="kill" value="{{ $editingStat['kill'] ?? '' }}" placeholder="Kill" class="p-2 bg-indigo-800 rounded" />
        <input name="death" value="{{ $editingStat['death'] ?? '' }}" placeholder="Death" class="p-2 bg-indigo-800 rounded" />
        <input name="assist" value="{{ $editingStat['assist'] ?? '' }}" placeholder="Assist" class="p-2 bg-indigo-800 rounded" />
        <div class="md:col-span-4 text-right"><a href="/statistics" class="mr-2 px-4 py-2 rounded bg-gray-600 text-white">Batal</a><button class="bg-yellow-500 px-4 py-2 rounded">Simpan Perubahan</button></div>
      </form>
    @else
      <form method="POST" action="/statistics" class="grid grid-cols-1 md:grid-cols-4 gap-3">
        @csrf
        <select name="player_id" class="p-2 bg-indigo-800 rounded">
          <option value="">-- Pilih Player --</option>
          @foreach($playerDropdown as $p)
            <option value="{{ $p['id'] }}">{{ $p['label'] }}</option>
          @endforeach
        </select>
        <input name="matches_played" placeholder="Matches Played" class="p-2 bg-indigo-800 rounded" />
        <input name="win" placeholder="Win" class="p-2 bg-indigo-800 rounded" />
        <input name="lose" placeholder="Lose" class="p-2 bg-indigo-800 rounded" />
        <input name="kill" placeholder="Kill" class="p-2 bg-indigo-800 rounded" />
        <input name="death" placeholder="Death" class="p-2 bg-indigo-800 rounded" />
        <input name="assist" placeholder="Assist" class="p-2 bg-indigo-800 rounded" />
        <div class="md:col-span-4 text-right"><button class="bg-green-600 px-4 py-2 rounded">Tambah Statistik</button></div>
      </form>
    @endif
  </div>
  @endif

  <div class="grid grid-cols-1 gap-4">
    @foreach($teamsData as $td)
    <div class="bg-indigo-900 p-4 rounded shadow">
      <div class="flex justify-between items-start">
        <div>
          <h2 class="text-lg font-bold">{{ $td['team']['name'] }}</h2>
          <div class="text-sm text-indigo-200">Game: {{ $td['team']['game'] }}</div>
        </div>
        <div class="space-x-2 text-sm">
          <span class="px-2 py-1 bg-indigo-800 text-white rounded">Total Match: {{ $td['total_matches'] }}</span>
          <span class="px-2 py-1 bg-green-700 text-white rounded">Win: {{ $td['wins'] }}</span>
          <span class="px-2 py-1 bg-red-700 text-white rounded">Lose: {{ $td['losses'] }}</span>
          <span class="px-2 py-1 bg-yellow-600 text-black rounded">Win Rate: {{ $td['win_rate'] }}%</span>
        </div>
      </div>

      <div class="mt-3 bg-indigo-800 p-2 rounded">
        @if(count($td['players']) === 0)
          <div class="text-indigo-200">Belum ada statistik player</div>
        @else
          <div class="overflow-x-auto">
            <table class="w-full text-left text-sm">
              <thead><tr class="text-indigo-100"><th class="p-2">Player</th><th>Role</th><th>MP</th><th>W</th><th>L</th><th>K</th><th>D</th><th>A</th><th>KDA</th><th>Aksi</th></tr></thead>
              <tbody>
                @foreach($td['players'] as $ps)
                  <tr class="border-t border-indigo-700">
                    <td class="p-2">{{ $ps['player']['nickname'] }}</td>
                    <td>{{ $ps['player']['role'] ?? '-' }}</td>
                    <td>{{ $ps['matches_played'] }}</td>
                    <td>{{ $ps['win'] }}</td>
                    <td>{{ $ps['lose'] }}</td>
                    <td>{{ $ps['kill'] }}</td>
                    <td>{{ $ps['death'] }}</td>
                    <td>{{ $ps['assist'] }}</td>
                    <td>{{ $ps['kda'] }}</td>
                    <td>
                      <div class="space-x-2">
                        @if($role === 'admin')
                          @if(!empty($ps['stat_id']))
                            <a href="/statistics?edit={{ $ps['stat_id'] }}" class="inline bg-yellow-500 text-black px-2 py-1 rounded">Edit</a>
                            <form method="POST" action="/statistics/{{ $ps['stat_id'] ?? '' }}/delete" class="inline">@csrf <button class="bg-red-600 px-2 py-1 rounded">Hapus</button></form>
                          @else
                            <span class="text-sm text-yellow-300">-</span>
                          @endif
                        @endif
                      </div>
                    </td>
                  </tr>
                @endforeach
              </tbody>
            </table>
          </div>
        @endif
      </div>
    </div>
    @endforeach
  </div>
</div>
@endsection
