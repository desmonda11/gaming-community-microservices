@extends('layouts.app')

@section('pageTitle','Login')

@section('content')
<div class="min-h-screen flex items-center justify-center">
  <div class="w-full max-w-md">
    <div class="bg-gradient-to-br from-indigo-900 to-blue-900 p-8 rounded-lg card-shadow">
      <h2 class="text-2xl font-bold mb-2">Sistem Manajemen Komunitas Gaming</h2>
      <p class="text-sm text-indigo-200 mb-6">Platform manajemen tim esports, roster pemain, jadwal pertandingan, statistik KDA, dan inventaris gaming.</p>

      @if($errors->any())
        <div class="mb-4 p-3 bg-red-700 text-white rounded">
          <ul class="list-disc list-inside">
            @foreach($errors->all() as $e)
              <li>{{ $e }}</li>
            @endforeach
          </ul>
        </div>
      @endif

      <form method="POST" action="/login">
        @csrf
        <div class="space-y-3">
          <input name="email" placeholder="Email" value="{{ old('email') }}" class="w-full px-3 py-2 rounded bg-indigo-800 placeholder-indigo-300" />
          <input name="password" type="password" placeholder="Password" class="w-full px-3 py-2 rounded bg-indigo-800 placeholder-indigo-300" />
        </div>
        <div class="mt-4 flex items-center justify-between">
          <button class="px-4 py-2 bg-green-600 rounded text-white">Login</button>
          <a href="/register" class="text-sm text-indigo-300">Belum punya akun? Daftar</a>
        </div>
      </form>
    </div>
  </div>
</div>
@endsection
