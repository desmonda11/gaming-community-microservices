@extends('layouts.app')

@section('pageTitle','Dashboard')

@section('content')
<div class="space-y-6">
  <section class="bg-gradient-to-r from-indigo-900 via-blue-900 to-purple-900 p-6 rounded-lg card-shadow">
    <h2 class="text-2xl font-bold">Sistem Manajemen Komunitas Gaming</h2>
    <p class="text-indigo-200 mt-2">Kelola tim esports, roster, pertandingan, statistik, inventaris, dan request perlengkapan dalam satu dashboard.</p>
  </section>

  <section class="grid grid-cols-1 md:grid-cols-4 gap-4">
    <div class="p-4 bg-indigo-800 rounded card-shadow">
      <div class="text-sm text-indigo-200">Total Tim Esport</div>
      <div class="text-3xl font-bold mt-2">{{ $counts['teams'] ?? 0 }}</div>
    </div>
    <div class="p-4 bg-indigo-800 rounded card-shadow">
      <div class="text-sm text-indigo-200">Total Roster Pemain</div>
      <div class="text-3xl font-bold mt-2">{{ $counts['players'] ?? 0 }}</div>
    </div>
    <div class="p-4 bg-indigo-800 rounded card-shadow">
      <div class="text-sm text-indigo-200">Total Jadwal Pertandingan</div>
      <div class="text-3xl font-bold mt-2">{{ $counts['matches'] ?? 0 }}</div>
    </div>
    <div class="p-4 bg-indigo-800 rounded card-shadow">
      <div class="text-sm text-indigo-200">Total Statistik KDA</div>
      <div class="text-3xl font-bold mt-2">{{ $counts['statistics'] ?? 0 }}</div>
    </div>
  </section>

  <section class="grid grid-cols-1 md:grid-cols-3 gap-4">
    <div class="bg-indigo-900 p-4 rounded">
      <h3 class="font-bold">Tim Esport</h3>
      <p class="text-sm text-indigo-200 mt-2">Kelola tim, pemilik, dan info game. Buat tim baru dan lihat roster pemain dengan cepat.</p>
    </div>
    <div class="bg-indigo-900 p-4 rounded">
      <h3 class="font-bold">Roster Pemain</h3>
      <p class="text-sm text-indigo-200 mt-2">Tambah pemain ke tim, atur role dan rank, serta lihat roster per tim.</p>
    </div>
    <div class="bg-indigo-900 p-4 rounded">
      <h3 class="font-bold">Jadwal & Statistik</h3>
      <p class="text-sm text-indigo-200 mt-2">Atur jadwal pertandingan, catat hasil, dan pantau statistik KDA tim serta pemain.</p>
    </div>
  </section>
</div>
@endsection
