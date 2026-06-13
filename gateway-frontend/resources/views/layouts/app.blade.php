<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width,initial-scale=1">
  <title>Gaming Community - Gateway</title>
  <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
</head>
<body class="bg-gray-900 text-gray-100">
  <div class="min-h-screen flex">
    <aside class="w-64 bg-blue-900 p-4">
    <h2 class="text-2xl font-bold mb-4">Sistem Manajemen Komunitas Gaming</h2>
      <div class="mb-6">
        @if(session('user'))
          <div class="text-sm">{{ session('user')['name'] ?? 'Guest' }}</div>
          <div class="text-xs text-gray-300">{{ session('role') ?? '' }}</div>
        @endif
      </div>
      <nav class="space-y-2">
        <a href="/dashboard" class="block px-2 py-1 rounded hover:bg-blue-800">Dashboard</a>
        <a href="/teams" class="flex justify-between items-center px-2 py-1 rounded hover:bg-blue-800">Tim Esport <span class="text-xs bg-blue-700 px-2 py-0.5 rounded">Teams</span></a>
        <a href="/players" class="flex justify-between items-center px-2 py-1 rounded hover:bg-blue-800">Roster Pemain <span class="text-xs bg-blue-700 px-2 py-0.5 rounded">Roster</span></a>
        <a href="/matches" class="flex justify-between items-center px-2 py-1 rounded hover:bg-blue-800">Jadwal Pertandingan <span class="text-xs bg-blue-700 px-2 py-0.5 rounded">Jadwal</span></a>
        <a href="/statistics" class="flex justify-between items-center px-2 py-1 rounded hover:bg-blue-800">Statistik KDA <span class="text-xs bg-blue-700 px-2 py-0.5 rounded">KDA</span></a>
        <a href="/inventories" class="flex justify-between items-center px-2 py-1 rounded hover:bg-blue-800">Inventaris Gaming <span class="text-xs bg-blue-700 px-2 py-0.5 rounded">Inventaris</span></a>
        <form method="POST" action="/logout">@csrf <button class="w-full text-left px-2 py-1 rounded hover:bg-blue-800">Logout</button></form>
      </nav>
    </aside>
    <main class="flex-1 p-8">
      <div class="container mx-auto">
        @if(session('status'))<div class="mb-4 p-2 bg-green-600 rounded">{{ session('status') }}</div>@endif
        @yield('content')
      </div>
    </main>
  </div>
</body>
</html>
