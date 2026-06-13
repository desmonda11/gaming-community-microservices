@extends('layouts.app')

@section('content')
<div class="text-white">
  <h1 class="text-2xl font-bold">Sistem Manajemen Komunitas Gaming</h1>
  <p class="mt-2 text-gray-300">Platform manajemen tim esports untuk mengelola roster pemain, jadwal pertandingan, statistik win/loss/KDA, dan inventaris perangkat gaming.</p>

  <div class="mt-6 grid grid-cols-4 gap-4">
    <div class="p-4 bg-blue-800 rounded">
      <div class="text-sm text-gray-300">Total Teams</div>
      <div class="text-2xl font-bold">{{ $counts['teams'] ?? 0 }}</div>
    </div>
    <div class="p-4 bg-blue-800 rounded">
      <div class="text-sm text-gray-300">Total Players</div>
      <div class="text-2xl font-bold">{{ $counts['players'] ?? 0 }}</div>
    </div>
    <div class="p-4 bg-blue-800 rounded">
      <div class="text-sm text-gray-300">Total Matches</div>
      <div class="text-2xl font-bold">{{ $counts['matches'] ?? 0 }}</div>
    </div>
    <div class="p-4 bg-blue-800 rounded">
      <div class="text-sm text-gray-300">Total Inventories</div>
      <div class="text-2xl font-bold">{{ $counts['inventories'] ?? 0 }}</div>
    </div>
  </div>

  <div class="mt-6 grid grid-cols-3 gap-4">
    <a href="/teams" class="p-4 bg-blue-700 rounded">Tim Esport</a>
    <a href="/players" class="p-4 bg-blue-700 rounded">Roster Pemain</a>
    <a href="/matches" class="p-4 bg-blue-700 rounded">Jadwal Pertandingan</a>
  </div>
</div>
@endsection
