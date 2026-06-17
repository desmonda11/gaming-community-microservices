@extends('layouts.app')

@section('pageTitle','Roster Pemain')

@section('content')
<div class="space-y-6">
  <div class="flex items-center justify-between">
    <h1 class="text-2xl font-bold">Roster Pemain</h1>
  </div>

  @if($role === 'admin' || (!empty($teamsForDropdown) && count($teamsForDropdown) > 0))
    <div class="bg-indigo-900 p-4 rounded card-shadow">
    <form method="POST" action="/players" class="grid grid-cols-1 md:grid-cols-5 gap-3">
      @csrf
      {{-- user_id is taken from session on backend; do not accept manual user_id input --}}
      <select name="team_id" class="p-2 bg-indigo-800 rounded">
        <option value="">-- Pilih Tim --</option>
        @foreach($teamsForDropdown as $t)
          <option value="{{ $t['id'] }}">{{ $t['name'] }} - {{ $t['game'] }}</option>
        @endforeach
      </select>
      <input name="nickname" placeholder="Nickname" class="p-2 bg-indigo-800 rounded" />
      <input name="role_in_game" placeholder="Role dalam game" class="p-2 bg-indigo-800 rounded" />
      <input name="rank" placeholder="Rank" class="p-2 bg-indigo-800 rounded" />
      <div class="md:col-span-5 text-right">
        <button class="bg-green-600 px-4 py-2 rounded">Tambah Roster</button>
      </div>
    </form>
  </div>
  @endif

  @if(isset($editing) && $editing)
    <div class="mb-4 bg-yellow-900 p-4 rounded">
      <h3 class="font-bold">Edit Roster - ID: {{ $editing['id'] }}</h3>
      <form method="POST" action="/players/{{ $editing['id'] }}/update" class="mt-2 grid grid-cols-1 md:grid-cols-5 gap-3">
        @csrf
        <select name="team_id" class="p-2 bg-yellow-800 rounded">
          @foreach($teamsForDropdown as $t)
            <option value="{{ $t['id'] }}" @if(($editing['team_id']??'')==$t['id']) selected @endif>{{ $t['name'] }} - {{ $t['game'] }}</option>
          @endforeach
        </select>
        <input name="nickname" value="{{ $editing['nickname'] ?? '' }}" placeholder="Nickname" class="p-2 bg-yellow-800 rounded" />
        <input name="role_in_game" value="{{ $editing['role_in_game'] ?? '' }}" placeholder="Role dalam game" class="p-2 bg-yellow-800 rounded" />
        <input name="rank" value="{{ $editing['rank'] ?? '' }}" placeholder="Rank" class="p-2 bg-yellow-800 rounded" />
        <div class="md:col-span-5 text-right">
          <button class="bg-blue-600 px-4 py-2 rounded">Simpan Perubahan</button>
          <a href="/players" class="ml-2 text-sm text-indigo-200">Batal</a>
        </div>
      </form>
    </div>
  @endif

  <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
    @foreach($teams as $t)
      @php $plist = $grouped[$t['id']] ?? []; @endphp
      <div class="bg-indigo-800 p-4 rounded shadow">
        <div class="flex justify-between items-center mb-2">
          <div>
            <div class="text-lg font-bold">{{ $t['name'] }}</div>
            <div class="text-sm text-indigo-200">Game: <span class="font-semibold">{{ $t['game'] }}</span></div>
          </div>
          <div class="text-right">
            <div class="text-sm text-indigo-200">Total Roster</div>
            <div class="text-xl font-bold">{{ count($plist) }} Pemain</div>
          </div>
        </div>

        <ul class="space-y-2">
          @if(count($plist) === 0)
            <li class="text-indigo-300">Belum ada roster untuk tim ini.</li>
          @else
            @foreach($plist as $p)
              <li class="flex justify-between items-center p-2 bg-indigo-900 rounded">
                <div>
                  <div class="font-semibold">{{ $p['nickname'] }}</div>
                  <div class="text-sm text-indigo-200">Role: {{ $p['role_in_game'] ?? '-' }} | Rank: {{ $p['rank'] ?? '-' }}</div>
                </div>
                <div class="space-x-2">
                  @php $teamOwnerId = $t['owner_user_id'] ?? null; @endphp
                  @if($role === 'admin' || ($user_id && $teamOwnerId == $user_id))
                    @if($role === 'admin')
                      <a href="/players?edit={{ $p['id'] }}" class="bg-yellow-500 text-black px-3 py-1 rounded inline-block">Edit</a>
                    @else
                      <a href="/players?edit={{ $p['id'] }}" class="bg-yellow-500 text-black px-3 py-1 rounded inline-block">Edit Roster Tim Saya</a>
                    @endif
                  @endif
                  @if($role === 'admin')
                    <form method="POST" action="/players/{{ $p['id'] }}/delete" class="inline">@csrf <button class="bg-red-600 px-3 py-1 rounded">Hapus</button></form>
                  @endif
                </div>
              </li>
            @endforeach
          @endif
        </ul>
      </div>
    @endforeach
  </div>
</div>
@endsection
