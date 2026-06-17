@extends('layouts.app')

@section('pageTitle','Jadwal Pertandingan')

@section('content')
<div class="space-y-6">
  <div class="flex items-center justify-between">
    <h1 class="text-2xl font-bold">Jadwal Pertandingan</h1>
    <form method="GET" action="/matches" class="flex items-center">
      <select name="game" class="p-2 bg-indigo-800 rounded">
        <option value="all">Semua Game</option>
        @foreach($teams as $t)
          @php $games = $games ?? []; if(!in_array($t['game'],$games)) $games[] = $t['game']; @endphp
        @endforeach
        @foreach($games as $g)
          <option value="{{ $g }}" @if(request()->query('game')==$g) selected @endif>{{ $g }}</option>
        @endforeach
      </select>
      <button class="ml-2 bg-electric text-white px-3 py-1 rounded">Filter</button>
    </form>
  </div>

  @if($role === 'admin')
  <div class="bg-indigo-900 p-4 rounded card-shadow">
    <form method="POST" action="/matches" class="grid grid-cols-1 md:grid-cols-3 gap-3">
      @csrf
      <select name="team_id" class="p-2 bg-indigo-800 rounded">
        <option value="">-- Pilih Tim --</option>
        @foreach($teams as $t)
          <option value="{{ $t['id'] }}">{{ $t['name'] }} ({{ $t['game'] }})</option>
        @endforeach
      </select>
      <input name="opponent" placeholder="Lawan" class="p-2 bg-indigo-800 rounded" />
      <input name="match_date" placeholder="YYYY-MM-DD" class="p-2 bg-indigo-800 rounded" />
      <select name="result" class="p-2 bg-indigo-800 rounded">
        <option value="">-- Hasil --</option>
        <option value="win">Win</option>
        <option value="lose">Lose</option>
        <option value="draw">Draw</option>
      </select>
      <input name="score_team" placeholder="Skor Tim" class="p-2 bg-indigo-800 rounded" />
      <input name="score_opponent" placeholder="Skor Lawan" class="p-2 bg-indigo-800 rounded" />
      <div class="md:col-span-3 text-right"><button class="bg-green-600 px-4 py-2 rounded">Tambah Pertandingan</button></div>
    </form>
  </div>
  @endif

  @if(isset($editing) && $editing)
    <div class="mb-4 bg-yellow-900 p-4 rounded">
      <h3 class="font-bold">Edit Pertandingan - ID: {{ $editing['id'] }}</h3>
      <form method="POST" action="/matches/{{ $editing['id'] }}/update" class="mt-2 grid grid-cols-1 md:grid-cols-3 gap-3">
        @csrf
        <select name="team_id" class="p-2 bg-yellow-800 rounded">
          @foreach($teams as $t)
            <option value="{{ $t['id'] }}" @if($t['id']==($editing['team_id']??null)) selected @endif>{{ $t['name'] }} ({{ $t['game'] }})</option>
          @endforeach
        </select>
        <input name="opponent" value="{{ $editing['opponent'] }}" placeholder="Lawan" class="p-2 bg-yellow-800 rounded" />
        <input name="match_date" value="{{ $editing['match_date'] }}" placeholder="YYYY-MM-DD" class="p-2 bg-yellow-800 rounded" />
        <select name="result" class="p-2 bg-yellow-800 rounded">
          <option value="">-- Hasil --</option>
          <option value="win" @if(($editing['result']??'')=='win') selected @endif>Win</option>
          <option value="lose" @if(($editing['result']??'')=='lose') selected @endif>Lose</option>
          <option value="draw" @if(($editing['result']??'')=='draw') selected @endif>Draw</option>
        </select>
        <input name="score_team" value="{{ $editing['score_team'] }}" placeholder="Skor Tim" class="p-2 bg-yellow-800 rounded" />
        <input name="score_opponent" value="{{ $editing['score_opponent'] }}" placeholder="Skor Lawan" class="p-2 bg-yellow-800 rounded" />
        <div class="md:col-span-3 text-right"><button class="bg-blue-600 px-4 py-2 rounded">Simpan Perubahan</button></div>
      </form>
    </div>
  @endif

  <div class="overflow-x-auto bg-indigo-900 rounded">
    <table class="w-full text-left text-sm">
      <thead class="bg-indigo-800"><tr><th class="p-3">Nama Tim</th><th>Game</th><th>Lawan</th><th>Tanggal</th><th>Hasil</th><th>Skor</th><th>Aksi</th></tr></thead>
      <tbody>
        @foreach($matches as $m)
          <tr class="border-t border-indigo-800">
            <td class="p-3">{{ $teamsMap[$m['team_id']] ?? ('Team ID: '.$m['team_id']) }}</td>
            <td class="p-3">{{ $m['game'] ?? '-' }}</td>
            <td class="p-3">{{ $m['opponent'] }}</td>
            <td class="p-3">{{ $m['match_date'] }}</td>
            <td class="p-3">
              @if(($m['result'] ?? '')=='win')<span class="px-2 py-1 bg-green-600 rounded">Win</span>
              @elseif(($m['result'] ?? '')=='lose')<span class="px-2 py-1 bg-red-600 rounded">Lose</span>
              @else<span class="px-2 py-1 bg-yellow-500 rounded">Pending</span>@endif
            </td>
            <td class="p-3">{{ $m['score_team'] ?? '-' }} - {{ $m['score_opponent'] ?? '-' }}</td>
            <td class="p-3">
              @if($role === 'admin')
                <a href="/matches?edit={{ $m['id'] }}" class="bg-yellow-500 px-3 py-1 rounded inline-block">Edit</a>
                <form method="POST" action="/matches/{{ $m['id'] }}/delete" class="inline">@csrf <button class="bg-red-600 px-3 py-1 rounded">Hapus</button></form>
              @endif
            </td>
          </tr>
        @endforeach
      </tbody>
    </table>
  </div>
</div>
@endsection
