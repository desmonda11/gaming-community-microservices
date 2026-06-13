@extends('layouts.app')

@section('content')
<div class="max-w-md mx-auto bg-gray-800 p-6 rounded">
  <h1 class="text-xl mb-4">Login</h1>

  @if($errors->any())<div class="mb-2 text-red-400">{{ $errors->first() }}</div>@endif

  <form method="POST" action="/login">
    @csrf
    <div class="mb-2">
      <label class="block text-sm">Email</label>
      <input name="email" value="{{ old('email') }}" class="w-full p-2 rounded bg-gray-700" />
    </div>
    <div class="mb-4">
      <label class="block text-sm">Password</label>
      <input type="password" name="password" class="w-full p-2 rounded bg-gray-700" />
    </div>
    <div class="flex justify-between items-center">
      <button class="bg-blue-600 px-4 py-2 rounded">Login</button>
      <a href="/register" class="text-sm text-blue-300">Register</a>
    </div>
  </form>
</div>
@endsection
