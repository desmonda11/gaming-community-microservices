<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width,initial-scale=1">
  <title>Gaming Community Hub</title>
  <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
  <style>
    /* Theme colors */
    .bg-navy { background: #071029; }
    .bg-electric { background: linear-gradient(90deg,#0066FF,#7C3AED); }
    .text-electric { color: #06f; }
    .card-shadow { box-shadow: 0 6px 18px rgba(3,7,18,0.6); }
  </style>
</head>
<body class="bg-navy text-gray-100 min-h-screen">
  <div class="flex h-screen">
    <aside class="w-72 bg-gradient-to-b from-blue-900 via-indigo-900 to-purple-900 p-6 hidden md:block">
      <div class="flex items-center space-x-3 mb-6">
        <div class="w-12 h-12 bg-electric rounded-full flex items-center justify-center text-white text-xl font-bold">GC</div>
        <div>
          <div class="text-lg font-bold">Gaming Community Hub</div>
          <div class="text-xs text-indigo-200">Sistem Manajemen Komunitas</div>
        </div>
      </div>

      <div class="mb-6 px-1">
        @if(session('user'))
          <div class="text-sm font-semibold">{{ session('user')['name'] ?? 'Guest' }}</div>
          <div class="text-xs text-indigo-200">{{ session('role') ?? '' }}</div>
        @else
          <div class="text-sm font-semibold">Guest</div>
        @endif
      </div>

      <nav class="space-y-2 text-sm">
        @php $path = request()->path(); @endphp
        <a href="/dashboard" class="flex items-center justify-between px-3 py-2 rounded-lg hover:bg-indigo-800 {{ $path=='dashboard' ? 'bg-electric text-white' : 'text-indigo-100' }}">Dashboard <span class="text-xs opacity-80">🏆</span></a>
        <a href="/teams" class="flex items-center justify-between px-3 py-2 rounded-lg hover:bg-indigo-800 {{ strpos($path,'teams')===0 ? 'bg-electric text-white' : 'text-indigo-100' }}">Tim Esport <span class="text-xs opacity-80">🎮</span></a>
        <a href="/players" class="flex items-center justify-between px-3 py-2 rounded-lg hover:bg-indigo-800 {{ strpos($path,'players')===0 ? 'bg-electric text-white' : 'text-indigo-100' }}">Roster Pemain <span class="text-xs opacity-80">🧑‍🤝‍🧑</span></a>
        <a href="/matches" class="flex items-center justify-between px-3 py-2 rounded-lg hover:bg-indigo-800 {{ strpos($path,'matches')===0 ? 'bg-electric text-white' : 'text-indigo-100' }}">Jadwal Pertandingan <span class="text-xs opacity-80">📅</span></a>
        <a href="/statistics" class="flex items-center justify-between px-3 py-2 rounded-lg hover:bg-indigo-800 {{ strpos($path,'statistics')===0 ? 'bg-electric text-white' : 'text-indigo-100' }}">Statistik KDA <span class="text-xs opacity-80">📊</span></a>
        <!-- Inventaris links removed from frontend -->

        <form method="POST" action="/logout" class="mt-4">@csrf <button class="w-full text-left px-3 py-2 rounded-lg hover:bg-red-700 bg-red-600 text-white">Logout 🔒</button></form>
      </nav>
    </aside>

    <div class="flex-1 flex flex-col overflow-auto">
      <header class="flex items-center justify-between p-4 border-b border-indigo-900">
        <div class="flex items-center space-x-3">
          <button class="md:hidden px-3 py-2 bg-indigo-800 rounded text-sm">☰</button>
          <h1 class="text-xl font-bold">@yield('pageTitle', 'Dashboard')</h1>
        </div>
        <div class="flex items-center space-x-3">
          <form method="GET" action="/search" class="hidden sm:block">
            <input name="q" placeholder="Search..." class="px-3 py-2 rounded bg-indigo-900 placeholder-indigo-300 text-sm" />
            <button class="ml-2 px-3 py-2 bg-electric text-white rounded text-sm">Filter</button>
          </form>
          <div class="text-sm text-indigo-200">{{ session('user')['name'] ?? 'Guest' }}</div>
        </div>
      </header>

      <main class="p-6">
        <div class="container mx-auto">
          @if(session('status'))
            <div class="mb-4 p-3 rounded bg-green-700 text-white">{{ session('status') }}</div>
          @endif
          @yield('content')
        </div>
      </main>
    </div>
  </div>
</body>
</html>
