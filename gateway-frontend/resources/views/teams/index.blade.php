@extends('layouts.app')

@section('content')
<div>
  <h1 class="text-xl mb-4">Tim Esport</h1>

  @if($role === 'admin')
  <form method="POST" action="/teams" class="mb-4 bg-gray-800 p-4 rounded">
    @csrf
    <div class="grid grid-cols-3 gap-2">
      <input name="name" placeholder="Nama Tim" class="p-2 bg-gray-700 rounded" />
      <input name="game" placeholder="Game" class="p-2 bg-gray-700 rounded" />
      <input name="description" placeholder="Deskripsi" class="p-2 bg-gray-700 rounded" />
    </div>
    <div class="mt-2"><button class="bg-green-600 px-3 py-1 rounded">Tambah Tim</button></div>
  </form>
  @endif

  <table class="w-full text-left bg-gray-800 rounded overflow-hidden">
    <thead class="bg-blue-800"><tr><th class="p-2">ID</th><th>Nama Tim</th><th>Game</th><th>Deskripsi</th><th>Aksi</th></tr></thead>
    <tbody>
      @foreach($teams as $t)
      <tr class="border-b border-gray-700"><td class="p-2">{{ $t['id'] }}</td><td class="p-2">{{ $t['name'] }}</td><td>{{ $t['game'] }}</td><td>{{ $t['description'] }}</td>
        <td class="p-2">
          @if($role === 'admin')
            <form method="POST" action="/teams/{{ $t['id'] }}/update" class="inline">@csrf <input name="name" placeholder="Nama Tim" class="p-1 bg-gray-700"/> <button class="bg-yellow-500 px-2">Edit</button></form>
            <form method="POST" action="/teams/{{ $t['id'] }}/delete" class="inline">@csrf <button class="bg-red-600 px-2">Hapus</button></form>
          @endif
        </td>
      </tr>
      @endforeach
    </tbody>
  </table>
</div>
@endsection
