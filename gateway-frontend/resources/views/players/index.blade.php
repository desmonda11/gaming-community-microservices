@extends('layouts.app')

@section('content')
<div>
  <h1 class="text-xl mb-4">Roster Pemain</h1>

  @if($role === 'admin')
  <form method="POST" action="/players" class="mb-6 bg-gray-800 p-4 rounded">
    @csrf
    <div class="grid grid-cols-5 gap-2">
      <input name="user_id" placeholder="User ID" class="p-2 bg-gray-700 rounded" />
      <select name="team_id" class="p-2 bg-gray-700 rounded">
        <option value="">-- Pilih Tim --</option>
        @foreach($teams as $t)
          <option value="{{ $t['id'] }}">{{ $t['name'] }} - {{ $t['game'] }}</option>
        @endforeach
      </select>
      <input name="nickname" placeholder="Nickname" class="p-2 bg-gray-700 rounded" />
      <input name="role_in_game" placeholder="Role dalam game" class="p-2 bg-gray-700 rounded" />
      <input name="rank" placeholder="Rank" class="p-2 bg-gray-700 rounded" />
    </div>
    <div class="mt-2"><button class="bg-green-600 px-3 py-1 rounded">Tambah Roster</button></div>
  </form>
  @endif

  <div class="grid grid-cols-2 gap-4">
    @foreach($teams as $t)
      @php $plist = $grouped[$t['id']] ?? []; @endphp
      <div class="bg-blue-800 p-4 rounded shadow">
        <div class="flex justify-between items-center mb-2">
          <div>
            <div class="text-lg font-bold">{{ $t['name'] }}</div>
            <div class="text-sm text-gray-300">Game: <span class="font-semibold">{{ $t['game'] }}</span></div>
          </div>
          <div class="text-right">
            <div class="text-sm text-gray-300">Total Roster</div>
            <div class="text-xl font-bold">{{ count($plist) }} Pemain</div>
          </div>
        </div>

        <ul class="space-y-2">
          @if(count($plist) === 0)
            <li class="text-gray-400">Belum ada roster untuk tim ini.</li>
          @else
            @foreach($plist as $p)
              <li class="flex justify-between items-center p-2 bg-gray-900 rounded">
                <div>
                  <div class="font-semibold">{{ $p['nickname'] }}</div>
                  <div class="text-sm text-gray-400">Role: {{ $p['role_in_game'] ?? '-' }} | Rank: {{ $p['rank'] ?? '-' }}</div>
                </div>
                <div class="space-x-2">
                  @if($role === 'admin' || ($user_id && $p['user_id'] == $user_id))
                    <form method="POST" action="/players/{{ $p['id'] }}/update" class="inline">@csrf <button class="bg-yellow-500 text-black px-3 py-1 rounded">Edit</button></form>
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
