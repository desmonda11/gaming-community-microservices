@extends('layouts.app')

@section('content')
<div>
  <h1 class="text-xl mb-4">Inventories</h1>
  <div class="mb-3 text-blue-200">Inventaris Gaming digunakan untuk mencatat aset atau perlengkapan yang dimiliki setiap tim, seperti PC, headset, keyboard, mouse, monitor, jersey, dan perlengkapan turnamen.</div>

  <div class="flex items-center mb-4">
    <form method="GET" action="/inventories" class="flex items-center">
      <select name="game" class="p-2 bg-gray-700 rounded">
        <option value="all" @if(($selectedGame??'all')=='all') selected @endif>Semua Game</option>
        @foreach($games as $g)
          <option value="{{ $g }}" @if(($selectedGame??'all')==$g) selected @endif>{{ $g }}</option>
        @endforeach
      </select>
      <button class="ml-2 bg-blue-600 px-3 py-1 rounded">Filter</button>
    </form>
  </div>

  @if($role === 'admin')
  <form method="POST" action="/inventories" class="mb-4 bg-gray-800 p-4 rounded">
    @csrf
    <div class="grid grid-cols-4 gap-2">
      <select name="team_id" class="p-2 bg-gray-700 rounded">
        <option value="">-- Pilih Tim --</option>
        @foreach($teams as $t)
          <option value="{{ $t['id'] }}">{{ $t['name'] }} ({{ $t['game'] }})</option>
        @endforeach
      </select>
      <input name="item_name" placeholder="Nama Item" class="p-2 bg-gray-700 rounded" />
      <select name="category" class="p-2 bg-gray-700 rounded">
        <option value="Headset">Headset</option>
        <option value="Keyboard">Keyboard</option>
        <option value="Mouse">Mouse</option>
        <option value="Monitor">Monitor</option>
        <option value="Jersey">Jersey</option>
        <option value="Controller">Controller</option>
      </select>
      <input name="quantity" placeholder="Jumlah" class="p-2 bg-gray-700 rounded" />
    </div>
    <div class="mt-2"><input name="condition" placeholder="Kondisi" class="p-2 bg-gray-700 rounded" /> <input name="notes" placeholder="Catatan" class="p-2 bg-gray-700 rounded" /></div>
    <div class="mt-2"><button class="bg-green-600 px-3 py-1 rounded">Tambah Inventaris</button></div>
  </form>
  @endif

  <table class="w-full text-left bg-gray-800 rounded overflow-hidden">
    <thead class="bg-blue-800"><tr><th class="p-2">Team</th><th>Item</th><th>Category</th><th>Qty</th><th>Condition</th><th>Notes</th><th>Actions</th></tr></thead>
    <tbody>
      @foreach($items as $i)
      <tr class="border-b border-gray-700"><td class="p-2">{{ $teamsMap[$i['team_id']] ?? ('Team ID: '.$i['team_id']) }}</td><td>{{ $i['item_name'] }}</td><td>{{ $i['category'] }}</td><td>{{ $i['quantity'] }}</td><td>{{ $i['condition'] }}</td><td>{{ $i['notes'] }}</td>
        <td class="p-2">
          @if($role === 'admin')
            <form method="POST" action="/inventories/{{ $i['id'] }}/update" class="inline">@csrf <button class="bg-yellow-500 px-2">Edit</button></form>
            <form method="POST" action="/inventories/{{ $i['id'] }}/delete" class="inline">@csrf <button class="bg-red-600 px-2">Hapus</button></form>
          @endif
        </td>
      </tr>
      @endforeach
    </tbody>
  </table>
</div>
@endsection
