@extends('layouts.app')

@section('pageTitle','Tim Esport')

@section('content')
<div class="space-y-6">
  <div class="flex items-center justify-between">
    <h1 class="text-2xl font-bold">Tim Esport</h1>
  </div>

  @if($role === 'admin' || ($user_id ?? null))
  <div class="bg-indigo-900 p-4 rounded card-shadow">
    <form method="POST" action="/teams" class="grid grid-cols-1 md:grid-cols-3 gap-3">
      @csrf
      <input name="name" placeholder="Nama Tim" class="p-2 bg-indigo-800 rounded" />
      <input name="game" placeholder="Game" class="p-2 bg-indigo-800 rounded" />
      <input name="description" placeholder="Deskripsi" class="p-2 bg-indigo-800 rounded" />
      <div class="md:col-span-3 text-right">
        <button class="bg-green-600 px-4 py-2 rounded">
          @if($role === 'admin') Tambah Tim @else Buat Tim Saya @endif
        </button>
      </div>
    </form>
  </div>
  @endif

  <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
    @foreach($teams as $t)
      <div class="bg-indigo-800 p-4 rounded shadow">
        <div class="flex items-center justify-between mb-2">
          <div>
            <div class="text-lg font-bold">{{ $t['name'] }}</div>
            <div class="text-sm text-indigo-200">{{ $t['game'] }}</div>
          </div>
          <div class="text-sm text-indigo-200">ID: {{ $t['id'] }}</div>
        </div>
        <div class="text-sm text-indigo-100 mb-3">{{ $t['description'] }}</div>
        <div class="flex items-center space-x-2">
          @if($role === 'admin')
            <form method="POST" action="/teams/{{ $t['id'] }}/update" class="inline">@csrf <input name="name" placeholder="Nama Tim" class="p-1 bg-indigo-700 rounded"/> <button class="bg-yellow-500 px-3 py-1 rounded">Edit</button></form>
            <form method="POST" action="/teams/{{ $t['id'] }}/delete" class="inline">@csrf <button class="bg-red-600 px-3 py-1 rounded">Hapus</button></form>
          @else
            @if(isset($t['owner_user_id']) && $t['owner_user_id'] == ($user_id ?? null))
              <form method="POST" action="/teams/{{ $t['id'] }}/update" class="inline">@csrf <input name="name" placeholder="Nama Tim" class="p-1 bg-indigo-700 rounded"/> <button class="bg-yellow-500 px-3 py-1 rounded">Edit Tim Saya</button></form>
            @endif
          @endif
        </div>
      </div>
    @endforeach
  </div>
</div>
@endsection
