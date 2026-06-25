<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
  <meta charset="utf-8" />
  <meta name="viewport" content="width=device-width,initial-scale=1" />
  <meta name="csrf-token" content="{{ csrf_token() }}">

  <title>{{ config('app.name', 'KasirKantin') }}</title>

  <!-- Google Fonts -->
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">

  @vite(['resources/css/app.css', 'resources/js/app.js'])
  <style>
      body {
          font-family: 'Plus Jakarta Sans', sans-serif;
      }
  </style>
</head>
<body class="bg-slate-50 min-h-screen text-slate-800 antialiased">

  <div class="min-h-screen flex">

    <!-- Sidebar -->
    <aside id="sidebar" class="w-64 bg-white border-r border-slate-100 hidden md:block flex-shrink-0">
      <div class="px-6 py-5 border-b border-slate-50 flex items-center gap-2">
        <div class="w-8 h-8 rounded-lg bg-indigo-600 flex items-center justify-center text-white font-extrabold text-lg">
            K
        </div>
        <a href="{{ route('dashboard') }}" class="text-lg font-extrabold text-slate-800 tracking-tight">
          Kasir<span class="text-indigo-600">Kantin</span>
        </a>
      </div>

      <nav class="px-4 py-6 space-y-1 text-sm font-semibold text-slate-600">
        @auth
          @if(auth()->user()->role === 'owner')
            <!-- Owner Navigation -->
            <span class="px-3 text-[10px] font-bold uppercase tracking-wider text-slate-400 block mb-2">Owner Menu</span>
            <a href="{{ route('dashboard') }}" class="flex items-center gap-2.5 px-3 py-2.5 rounded-xl hover:bg-slate-50 hover:text-slate-900 transition-colors {{ request()->is('owner/dashboard*') ? 'bg-indigo-50/50 text-indigo-600 font-bold' : '' }}">
              <span>📊</span> Dashboard
            </a>
            <a href="{{ route('owner.users.index') }}" class="flex items-center gap-2.5 px-3 py-2.5 rounded-xl hover:bg-slate-50 hover:text-slate-900 transition-colors {{ request()->is('owner/users*') ? 'bg-indigo-50/50 text-indigo-600 font-bold' : '' }}">
              <span>👥</span> Kelola Staff (User)
            </a>
            <a href="{{ route('categories.index') }}" class="flex items-center gap-2.5 px-3 py-2.5 rounded-xl hover:bg-slate-50 hover:text-slate-900 transition-colors {{ request()->is('categories*') ? 'bg-indigo-50/50 text-indigo-600 font-bold' : '' }}">
              <span>📁</span> Kategori Menu
            </a>
            <a href="{{ route('products.index') }}" class="flex items-center gap-2.5 px-3 py-2.5 rounded-xl hover:bg-slate-50 hover:text-slate-900 transition-colors {{ request()->is('products*') ? 'bg-indigo-50/50 text-indigo-600 font-bold' : '' }}">
              <span>🍔</span> Produk &amp; Stok
            </a>
            <a href="{{ route('customers.index') }}" class="flex items-center gap-2.5 px-3 py-2.5 rounded-xl hover:bg-slate-50 hover:text-slate-900 transition-colors {{ request()->is('customers*') ? 'bg-indigo-50/50 text-indigo-600 font-bold' : '' }}">
              <span>👤</span> Pelanggan (Member)
            </a>
            <a href="{{ route('transactions.index') }}" class="flex items-center gap-2.5 px-3 py-2.5 rounded-xl hover:bg-slate-50 hover:text-slate-900 transition-colors {{ request()->is('transactions*') ? 'bg-indigo-50/50 text-indigo-600 font-bold' : '' }}">
              <span>🧾</span> Laporan Transaksi
            </a>
            <a href="{{ route('kasir') }}" class="flex items-center gap-2.5 px-3 py-2.5 rounded-xl hover:bg-slate-50 hover:text-slate-900 transition-colors {{ request()->is('kasir*') ? 'bg-indigo-50/50 text-indigo-600 font-bold' : '' }}">
              <span>🛒</span> Buka Transaksi POS
            </a>

          @elseif(auth()->user()->role === 'admin')
            <!-- Admin Navigation -->
            <span class="px-3 text-[10px] font-bold uppercase tracking-wider text-slate-400 block mb-2">Admin Menu</span>
            <a href="{{ route('dashboard') }}" class="flex items-center gap-2.5 px-3 py-2.5 rounded-xl hover:bg-slate-50 hover:text-slate-900 transition-colors {{ request()->is('admin/dashboard*') ? 'bg-indigo-50/50 text-indigo-600 font-bold' : '' }}">
              <span>📊</span> Dashboard
            </a>
            <a href="{{ route('categories.index') }}" class="flex items-center gap-2.5 px-3 py-2.5 rounded-xl hover:bg-slate-50 hover:text-slate-900 transition-colors {{ request()->is('categories*') ? 'bg-indigo-50/50 text-indigo-600 font-bold' : '' }}">
              <span>📁</span> Kategori Menu
            </a>
            <a href="{{ route('products.index') }}" class="flex items-center gap-2.5 px-3 py-2.5 rounded-xl hover:bg-slate-50 hover:text-slate-900 transition-colors {{ request()->is('products*') ? 'bg-indigo-50/50 text-indigo-600 font-bold' : '' }}">
              <span>🍔</span> Produk &amp; Stok
            </a>
            <a href="{{ route('customers.index') }}" class="flex items-center gap-2.5 px-3 py-2.5 rounded-xl hover:bg-slate-50 hover:text-slate-900 transition-colors {{ request()->is('customers*') ? 'bg-indigo-50/50 text-indigo-600 font-bold' : '' }}">
              <span>👤</span> Pelanggan (Member)
            </a>
            <a href="{{ route('transactions.index') }}" class="flex items-center gap-2.5 px-3 py-2.5 rounded-xl hover:bg-slate-50 hover:text-slate-900 transition-colors {{ request()->is('transactions*') ? 'bg-indigo-50/50 text-indigo-600 font-bold' : '' }}">
              <span>🧾</span> Laporan Transaksi
            </a>
            <a href="{{ route('kasir') }}" class="flex items-center gap-2.5 px-3 py-2.5 rounded-xl hover:bg-slate-50 hover:text-slate-900 transition-colors {{ request()->is('kasir*') ? 'bg-indigo-50/50 text-indigo-600 font-bold' : '' }}">
              <span>🛒</span> Buka Transaksi POS
            </a>

          @else
            <!-- Kasir Navigation -->
            <span class="px-3 text-[10px] font-bold uppercase tracking-wider text-slate-400 block mb-2">Kasir Menu</span>
            <a href="{{ route('dashboard') }}" class="flex items-center gap-2.5 px-3 py-2.5 rounded-xl hover:bg-slate-50 hover:text-slate-900 transition-colors {{ request()->is('kasir/dashboard*') ? 'bg-indigo-50/50 text-indigo-600 font-bold' : '' }}">
              <span>📊</span> Dashboard
            </a>
            <a href="{{ route('kasir') }}" class="flex items-center gap-2.5 px-3 py-2.5 rounded-xl hover:bg-slate-50 hover:text-slate-900 transition-colors {{ request()->is('kasir') ? 'bg-indigo-50/50 text-indigo-600 font-bold' : '' }}">
              <span>🛒</span> Transaksi POS Baru
            </a>
            <a href="{{ route('customers.index') }}" class="flex items-center gap-2.5 px-3 py-2.5 rounded-xl hover:bg-slate-50 hover:text-slate-900 transition-colors {{ request()->is('customers*') ? 'bg-indigo-50/50 text-indigo-600 font-bold' : '' }}">
              <span>👤</span> Pelanggan (Member)
            </a>
            <a href="{{ route('transactions.index') }}" class="flex items-center gap-2.5 px-3 py-2.5 rounded-xl hover:bg-slate-50 hover:text-slate-900 transition-colors {{ request()->is('transactions*') ? 'bg-indigo-50/50 text-indigo-600 font-bold' : '' }}">
              <span>🧾</span> Riwayat Penjualan
            </a>
          @endif
        @endauth
      </nav>
    </aside>

    <!-- Main content area -->
    <div class="flex-1 min-w-0 flex flex-col">
      <!-- Topbar -->
      <header class="bg-white border-b border-slate-100 flex-shrink-0">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 flex items-center justify-between h-16">
          <div class="flex items-center gap-4">
            <!-- mobile menu toggle -->
            <button id="mobileMenuBtn" class="md:hidden p-2 rounded-lg hover:bg-slate-100">
              <svg class="w-6 h-6 text-slate-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/></svg>
            </button>
            <h2 class="text-sm font-bold text-slate-800">
                @if (isset($header))
                    {{ $header }}
                @else
                    @yield('page-title', '')
                @endif
            </h2>
          </div>

          <div class="flex items-center gap-4">
            @auth
              <div class="flex items-center gap-2">
                <span class="text-xs font-semibold text-slate-500">Hi,</span>
                <span class="text-xs font-bold text-slate-700 bg-slate-100 px-2.5 py-1 rounded-lg">{{ auth()->user()->name }}</span>
              </div>
              <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button type="submit" class="text-xs font-bold text-red-500 hover:text-red-700 bg-red-50 hover:bg-red-100/70 px-3 py-1.5 rounded-lg transition-colors">Logout</button>
              </form>
            @endauth
          </div>
        </div>
      </header>

      <!-- Page content -->
      <main class="py-6 flex-1 overflow-y-auto">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 space-y-6">
          {{-- Flash Success Alert --}}
          @if(session('success'))
            <div class="rounded-xl bg-emerald-50 border border-emerald-100 p-4 text-sm font-semibold text-emerald-800 flex items-center gap-2">
                <span>✓</span> {{ session('success') }}
            </div>
          @endif
          {{-- Flash Error Alert --}}
          @if(session('error'))
            <div class="rounded-xl bg-red-50 border border-red-100 p-4 text-sm font-semibold text-red-800 flex items-center gap-2">
                <span>⚠️</span> {{ session('error') }}
            </div>
          @endif

          {{-- page inner card --}}
          <div>
            @if (isset($slot) && trim($slot) !== '')
                {{ $slot }}
            @else
                @yield('content')
            @endif
          </div>

        </div>
      </main>
    </div>
  </div>

  <script>
    document.getElementById('mobileMenuBtn')?.addEventListener('click', function(){
      document.getElementById('sidebar')?.classList.toggle('hidden');
    });
  </script>
</body>
</html>
