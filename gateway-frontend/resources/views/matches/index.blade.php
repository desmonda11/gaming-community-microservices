@extends('layouts.app')

@section('content')
<div>
  <h1 class="text-xl mb-4">Jadwal Pertandingan</h1>
  <div class="flex items-center mb-4 space-x-4">
    <form method="GET" action="/matches" class="flex items-center">
      <select name="game" class="p-2 bg-gray-700 rounded">
        <option value="all">Semua Game</option>
        @foreach($teams as $t)
          @php $games = $games ?? []; if(!in_array($t['game'],$games)) $games[] = $t['game']; @endphp
        @endforeach
        @foreach($games as $g)
          <option value="{{ $g }}" @if(request()->query('game')==$g) selected @endif>{{ $g }}</option>
        @endforeach
      </select>
      <button class="ml-2 bg-blue-600 px-3 py-1 rounded">Filter</button>
    </form>
  </div>

  @if($role === 'admin')
  <form method="POST" action="/matches" class="mb-4 bg-gray-800 p-4 rounded">
    @csrf
    <div class="grid grid-cols-3 gap-2">
      <select name="team_id" class="p-2 bg-gray-700 rounded">
        <option value="">-- Pilih Tim --</option>
        @foreach($teams as $t)
          <option value="{{ $t['id'] }}">{{ $t['name'] }} ({{ $t['game'] }})</option>
        @endforeach
      </select>
      <input name="opponent" placeholder="Lawan" class="p-2 bg-gray-700 rounded" />
      <input name="match_date" placeholder="YYYY-MM-DD" class="p-2 bg-gray-700 rounded" />
    </div>
    <div class="grid grid-cols-3 gap-2 mt-2">
      <select name="result" class="p-2 bg-gray-700 rounded">
        <option value="">-- Hasil --</option>
        <option value="win">Win</option>
        <option value="lose">Lose</option>
        <option value="draw">Draw</option>
      </select>
      <input name="score_team" placeholder="Skor Tim" class="p-2 bg-gray-700 rounded" />
      <input name="score_opponent" placeholder="Skor Lawan" class="p-2 bg-gray-700 rounded" />
    </div>
    <div class="mt-2"><button class="bg-green-600 px-3 py-1 rounded">Tambah Pertandingan</button></div>
  </form>
  @endif

  @if(isset($editing) && $editing)
    <div class="mb-4 bg-yellow-900 p-4 rounded">
      <h3 class="font-bold">Edit Pertandingan - ID: {{ $editing['id'] }}</h3>
      <form method="POST" action="/matches/{{ $editing['id'] }}/update" class="mt-2">
        @csrf
        <div class="grid grid-cols-3 gap-2">
          <select name="team_id" class="p-2 bg-yellow-800 rounded">
            @foreach($teams as $t)
              <option value="{{ $t['id'] }}" @if($t['id']==($editing['team_id']??null)) selected @endif>{{ $t['name'] }} ({{ $t['game'] }})</option>
            @endforeach
          </select>
          <input name="opponent" value="{{ $editing['opponent'] }}" placeholder="Lawan" class="p-2 bg-yellow-800 rounded" />
          <input name="match_date" value="{{ $editing['match_date'] }}" placeholder="YYYY-MM-DD" class="p-2 bg-yellow-800 rounded" />
        </div>
        <div class="grid grid-cols-3 gap-2 mt-2">
          <select name="result" class="p-2 bg-yellow-800 rounded">
            <option value="">-- Hasil --</option>
            <option value="win" @if(($editing['result']??'')=='win') selected @endif>Win</option>
            <option value="lose" @if(($editing['result']??'')=='lose') selected @endif>Lose</option>
            <option value="draw" @if(($editing['result']??'')=='draw') selected @endif>Draw</option>
          </select>
          <input name="score_team" value="{{ $editing['score_team'] }}" placeholder="Skor Tim" class="p-2 bg-yellow-800 rounded" />
          <input name="score_opponent" value="{{ $editing['score_opponent'] }}" placeholder="Skor Lawan" class="p-2 bg-yellow-800 rounded" />
        </div>
        <div class="mt-2"><button class="bg-blue-600 px-3 py-1 rounded">Simpan Perubahan</button></div>
      </form>
    </div>
  @endif

  <table class="w-full text-left bg-gray-800 rounded overflow-hidden">
    <thead class="bg-blue-800"><tr><th class="p-2">Nama Tim</th><th>Lawan</th><th>Tanggal</th><th>Hasil</th><th>Skor</th><th>Aksi</th></tr></thead>
    <tbody>
      @foreach($matches as $m)
      <tr class="border-b border-gray-700"><td class="p-2">{{ $teamsMap[$m['team_id']] ?? ('Team ID: '.$m['team_id']) }}</td><td>{{ $m['opponent'] }}</td><td>{{ $m['match_date'] }}</td><td>{{ $m['result'] }}</td><td>{{ $m['score_team'] }} - {{ $m['score_opponent'] }}</td>
        <td class="p-2">
          @if($role === 'admin')
            <a href="/matches?edit={{ $m['id'] }}" class="bg-yellow-500 px-2 py-1 rounded inline-block">Edit</a>
            <form method="POST" action="/matches/{{ $m['id'] }}/delete" class="inline">@csrf <button class="bg-red-600 px-2">Hapus</button></form>
          @endif
        </td>
      </tr>
      @endforeach
    </tbody>
  </table>
</div>
@endsection
