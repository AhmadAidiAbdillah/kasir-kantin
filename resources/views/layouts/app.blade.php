<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
  <meta charset="utf-8" />
  <meta name="viewport" content="width=device-width,initial-scale=1" />
  <meta name="csrf-token" content="{{ csrf_token() }}">

  <title>{{ config('app.name', 'KasirKantin') }} - POS</title>

  <!-- Google Fonts -->
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">

  <!-- Tailwind CSS & Flowbite (via Vite) -->
  @vite(['resources/css/app.css', 'resources/js/app.js'])
  <style>
      body {
          font-family: 'Plus Jakarta Sans', sans-serif;
      }
      /* Custom scrollbar for sidebar */
      aside::-webkit-scrollbar {
          width: 4px;
      }
      aside::-webkit-scrollbar-track {
          background: transparent;
      }
      aside::-webkit-scrollbar-thumb {
          background: rgba(148, 163, 184, 0.1);
          border-radius: 4px;
      }
  </style>
</head>
<body class="bg-slate-50 min-h-screen text-slate-800 antialiased">

  <div class="min-h-screen flex relative">

    <!-- Sidebar Wrapper for responsive toggling -->
    <aside id="sidebar" class="w-64 bg-slate-900 text-slate-300 border-r border-slate-800 hidden md:flex flex-col flex-shrink-0 z-40 fixed md:sticky top-0 h-screen md:self-start transition-all duration-300">
      
      <!-- Brand Logo / Header -->
      <div class="px-6 py-5 border-b border-slate-800/80 flex items-center justify-between">
        <div class="flex items-center gap-2.5">
          <div class="w-8 h-8 rounded-lg bg-indigo-600 flex items-center justify-center text-white font-extrabold text-lg">
              K
          </div>
          <span class="text-base font-extrabold tracking-tight text-white">
            Kasir<span class="text-indigo-500">Kantin</span>
          </span>
        </div>
        <!-- Close button on Mobile -->
        <button id="closeSidebarBtn" class="md:hidden p-1.5 rounded-lg text-slate-400 hover:bg-slate-800 hover:text-white">
          <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
        </button>
      </div>

      <!-- User Info Badge in Sidebar -->
      <div class="px-5 py-4 border-b border-slate-800/40 flex items-center gap-3">
        <div class="w-9 h-9 rounded-full bg-slate-800 flex items-center justify-center text-indigo-400 font-bold text-sm">
          {{ strtoupper(substr(auth()->user()->name, 0, 2)) }}
        </div>
        <div class="space-y-0.5 min-w-0">
          <span class="block text-xs font-bold text-white truncate">{{ auth()->user()->name }}</span>
          <span class="inline-flex items-center px-1.5 py-0.5 rounded text-[9px] font-bold uppercase tracking-wider bg-indigo-950 text-indigo-400 border border-indigo-900/30">
            {{ auth()->user()->role }}
          </span>
        </div>
      </div>

      <!-- Navigation Menu -->
      <nav class="flex-1 px-4 py-6 space-y-1 text-sm font-semibold overflow-y-auto">
        @auth
          @if(auth()->user()->role === 'owner')
            <!-- ================= OWNER MENU ================= -->
            <span class="px-3 text-[10px] font-bold uppercase tracking-wider text-slate-500 block mb-2">Utama</span>
            
            <a href="{{ route('dashboard') }}" class="flex items-center gap-3 px-3 py-2.5 rounded-xl hover:bg-slate-800 hover:text-white transition-all {{ request()->is('owner/dashboard*') ? 'bg-indigo-600 text-white shadow-lg shadow-indigo-600/10' : 'text-slate-400' }}">
              <!-- Icon Home/Dashboard -->
              <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v4a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v4a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v4a2 2 0 01-2 2H6a2 2 0 01-2-2v-4zM14 16a2 2 0 012-2h2a2 2 0 012 2v4a2 2 0 01-2 2h-2a2 2 0 01-2-2v-4z"/></svg>
              <span>Dashboard</span>
            </a>
            
            <a href="{{ route('owner.users.index') }}" class="flex items-center gap-3 px-3 py-2.5 rounded-xl hover:bg-slate-800 hover:text-white transition-all {{ request()->is('owner/users*') ? 'bg-indigo-600 text-white shadow-lg shadow-indigo-600/10' : 'text-slate-400' }}">
              <!-- Icon Users -->
              <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/></svg>
              <span>Kelola Staff</span>
            </a>

            <a href="{{ route('owner.stores.index') }}" class="flex items-center gap-3 px-3 py-2.5 rounded-xl hover:bg-slate-800 hover:text-white transition-all {{ request()->is('owner/stores*') ? 'bg-indigo-600 text-white shadow-lg shadow-indigo-600/10' : 'text-slate-400' }}">
              <!-- Icon Store -->
              <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/></svg>
              <span>Kelola Toko</span>
            </a>

            <span class="px-3 text-[10px] font-bold uppercase tracking-wider text-slate-500 block mt-6 mb-2">Master Data</span>

            <a href="{{ route('categories.index') }}" class="flex items-center gap-3 px-3 py-2.5 rounded-xl hover:bg-slate-800 hover:text-white transition-all {{ request()->is('categories*') ? 'bg-indigo-600 text-white shadow-lg shadow-indigo-600/10' : 'text-slate-400' }}">
              <!-- Icon Folder -->
              <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 7v10a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-6l-2-2H5a2 2 0 00-2 2z"/></svg>
              <span>Kategori</span>
            </a>

            <a href="{{ route('products.index') }}" class="flex items-center gap-3 px-3 py-2.5 rounded-xl hover:bg-slate-800 hover:text-white transition-all {{ request()->is('products*') ? 'bg-indigo-600 text-white shadow-lg shadow-indigo-600/10' : 'text-slate-400' }}">
              <!-- Icon Database/Products -->
              <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/></svg>
              <span>Produk</span>
            </a>

            <a href="{{ route('customers.index') }}" class="flex items-center gap-3 px-3 py-2.5 rounded-xl hover:bg-slate-800 hover:text-white transition-all {{ request()->is('customers*') ? 'bg-indigo-600 text-white shadow-lg shadow-indigo-600/10' : 'text-slate-400' }}">
              <!-- Icon Customer Identification -->
              <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V8a2 2 0 00-2-2h-5m-4 0V5a2 2 0 114 0v1m-4 0a2 2 0 104 0m-5 8a2 2 0 100-4 2 2 0 000 4zm0 0c1.333 0 4 .667 4 2v1H5v-1c0-1.333 2.667-2 4-2z"/></svg>
              <span>Pelanggan</span>
            </a>

            <span class="px-3 text-[10px] font-bold uppercase tracking-wider text-slate-500 block mt-6 mb-2">Penjualan</span>

            <a href="{{ route('transactions.index') }}" class="flex items-center gap-3 px-3 py-2.5 rounded-xl hover:bg-slate-800 hover:text-white transition-all {{ request()->is('transactions*') ? 'bg-indigo-600 text-white shadow-lg shadow-indigo-600/10' : 'text-slate-400' }}">
              <!-- Icon Report -->
              <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
              <span>Laporan</span>
            </a>

            <a href="{{ route('kasir') }}" class="flex items-center gap-3 px-3 py-2.5 rounded-xl hover:bg-slate-800 hover:text-white transition-all {{ request()->is('kasir') ? 'bg-indigo-600 text-white shadow-lg shadow-indigo-600/10' : 'text-slate-400' }}">
              <!-- Icon Receipt/POS -->
              <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z"/></svg>
              <span>POS / Kasir</span>
            </a>

          @elseif(auth()->user()->role === 'admin')
            <!-- ================= ADMIN MENU ================= -->
            <span class="px-3 text-[10px] font-bold uppercase tracking-wider text-slate-500 block mb-2">Utama</span>
            
            <a href="{{ route('dashboard') }}" class="flex items-center gap-3 px-3 py-2.5 rounded-xl hover:bg-slate-800 hover:text-white transition-all {{ request()->is('admin/dashboard*') ? 'bg-indigo-600 text-white shadow-lg shadow-indigo-600/10' : 'text-slate-400' }}">
              <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v4a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v4a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v4a2 2 0 01-2 2H6a2 2 0 01-2-2v-4zM14 16a2 2 0 012-2h2a2 2 0 012 2v4a2 2 0 01-2 2h-2a2 2 0 01-2-2v-4z"/></svg>
              <span>Dashboard</span>
            </a>

            <span class="px-3 text-[10px] font-bold uppercase tracking-wider text-slate-500 block mt-6 mb-2">Master Data</span>

            <a href="{{ route('categories.index') }}" class="flex items-center gap-3 px-3 py-2.5 rounded-xl hover:bg-slate-800 hover:text-white transition-all {{ request()->is('categories*') ? 'bg-indigo-600 text-white shadow-lg shadow-indigo-600/10' : 'text-slate-400' }}">
              <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 7v10a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-6l-2-2H5a2 2 0 00-2 2z"/></svg>
              <span>Kategori</span>
            </a>

            <a href="{{ route('products.index') }}" class="flex items-center gap-3 px-3 py-2.5 rounded-xl hover:bg-slate-800 hover:text-white transition-all {{ request()->is('products*') ? 'bg-indigo-600 text-white shadow-lg shadow-indigo-600/10' : 'text-slate-400' }}">
              <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/></svg>
              <span>Produk</span>
            </a>

            <a href="{{ route('customers.index') }}" class="flex items-center gap-3 px-3 py-2.5 rounded-xl hover:bg-slate-800 hover:text-white transition-all {{ request()->is('customers*') ? 'bg-indigo-600 text-white shadow-lg shadow-indigo-600/10' : 'text-slate-400' }}">
              <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V8a2 2 0 00-2-2h-5m-4 0V5a2 2 0 114 0v1m-4 0a2 2 0 104 0m-5 8a2 2 0 100-4 2 2 0 000 4zm0 0c1.333 0 4 .667 4 2v1H5v-1c0-1.333 2.667-2 4-2z"/></svg>
              <span>Pelanggan</span>
            </a>

            <span class="px-3 text-[10px] font-bold uppercase tracking-wider text-slate-500 block mt-6 mb-2">Penjualan</span>

            <a href="{{ route('transactions.index') }}" class="flex items-center gap-3 px-3 py-2.5 rounded-xl hover:bg-slate-800 hover:text-white transition-all {{ request()->is('transactions*') ? 'bg-indigo-600 text-white shadow-lg shadow-indigo-600/10' : 'text-slate-400' }}">
              <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
              <span>Laporan</span>
            </a>

            <a href="{{ route('kasir') }}" class="flex items-center gap-3 px-3 py-2.5 rounded-xl hover:bg-slate-800 hover:text-white transition-all {{ request()->is('kasir') ? 'bg-indigo-600 text-white shadow-lg shadow-indigo-600/10' : 'text-slate-400' }}">
              <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z"/></svg>
              <span>POS / Kasir</span>
            </a>

          @else
            <!-- ================= KASIR MENU ================= -->
            <span class="px-3 text-[10px] font-bold uppercase tracking-wider text-slate-500 block mb-2">Utama</span>
            
            <a href="{{ route('dashboard') }}" class="flex items-center gap-3 px-3 py-2.5 rounded-xl hover:bg-slate-800 hover:text-white transition-all {{ request()->is('kasir/dashboard*') ? 'bg-indigo-600 text-white shadow-lg shadow-indigo-600/10' : 'text-slate-400' }}">
              <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v4a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v4a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v4a2 2 0 01-2 2H6a2 2 0 01-2-2v-4zM14 16a2 2 0 012-2h2a2 2 0 012 2v4a2 2 0 01-2 2h-2a2 2 0 01-2-2v-4z"/></svg>
              <span>Dashboard</span>
            </a>

            <a href="{{ route('kasir') }}" class="flex items-center gap-3 px-3 py-2.5 rounded-xl hover:bg-slate-800 hover:text-white transition-all {{ request()->is('kasir') ? 'bg-indigo-600 text-white shadow-lg shadow-indigo-600/10' : 'text-slate-400' }}">
              <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z"/></svg>
              <span>POS / Transaksi</span>
            </a>

            <span class="px-3 text-[10px] font-bold uppercase tracking-wider text-slate-500 block mt-6 mb-2">Data</span>

            <a href="{{ route('customers.index') }}" class="flex items-center gap-3 px-3 py-2.5 rounded-xl hover:bg-slate-800 hover:text-white transition-all {{ request()->is('customers*') ? 'bg-indigo-600 text-white shadow-lg shadow-indigo-600/10' : 'text-slate-400' }}">
              <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V8a2 2 0 00-2-2h-5m-4 0V5a2 2 0 114 0v1m-4 0a2 2 0 104 0m-5 8a2 2 0 100-4 2 2 0 000 4zm0 0c1.333 0 4 .667 4 2v1H5v-1c0-1.333 2.667-2 4-2z"/></svg>
              <span>Pelanggan</span>
            </a>

            <a href="{{ route('transactions.index') }}" class="flex items-center gap-3 px-3 py-2.5 rounded-xl hover:bg-slate-800 hover:text-white transition-all {{ request()->is('transactions*') ? 'bg-indigo-600 text-white shadow-lg shadow-indigo-600/10' : 'text-slate-400' }}">
              <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
              <span>Riwayat Penjualan</span>
            </a>
          @endif

        @endauth
      </nav>

      <!-- Sidebar Footer / Logout -->
      <div class="p-4 border-t border-slate-800/80 bg-slate-950/40">
        @auth
          <form method="POST" action="{{ route('logout') }}">
            @csrf
            <button type="submit" class="w-full flex items-center justify-center gap-2 px-4 py-2.5 bg-red-600/10 hover:bg-red-600 text-red-500 hover:text-white rounded-xl text-xs font-bold transition-all">
              <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/></svg>
              <span>Keluar (Logout)</span>
            </button>
          </form>
        @endauth
      </div>
    </aside>

    <!-- Overlay on mobile sidebar opened -->
    <div id="sidebarOverlay" class="fixed inset-0 bg-slate-950/50 backdrop-blur-sm z-30 hidden md:hidden"></div>

    <!-- Main Content Panel -->
    <div class="flex-1 min-w-0 flex flex-col min-h-screen">
      
      <!-- Topbar Header -->
      <header class="bg-white border-b border-slate-100/80 flex-shrink-0 sticky top-0 z-20">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 flex items-center justify-between h-16">
          <div class="flex items-center gap-4">
            <!-- hamburger button -->
            <button id="hamburgerBtn" class="md:hidden p-2 rounded-xl hover:bg-slate-50 text-slate-600 focus:outline-none">
              <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/></svg>
            </button>
            <h2 class="text-sm font-bold text-slate-800 uppercase tracking-wider">
                @if (isset($header))
                    {{ $header }}
                @else
                    @yield('page-title', 'KasirKantin')
                @endif
            </h2>
          </div>

          <!-- Topbar Actions -->
          <div class="flex items-center gap-4">
            @auth
              <div class="hidden sm:flex items-center gap-2">
                <span class="text-[11px] font-bold uppercase tracking-wider text-slate-400">Selamat datang,</span>
                <span class="text-xs font-bold text-slate-700 bg-slate-150/70 px-2.5 py-1 rounded-lg">{{ auth()->user()->name }}</span>
              </div>
            @endauth
          </div>
        </div>
      </header>

      <!-- Page Context Content -->
      <main class="py-6 flex-1 overflow-y-auto">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
          
          {{-- Dynamic Flash Notifications --}}
          @if(session('success'))
            <div class="mb-6 rounded-2xl bg-emerald-50 border border-emerald-100 p-4 text-xs font-bold text-emerald-800 flex items-center gap-2 shadow-sm animate-pulse">
                <span>✓</span> {{ session('success') }}
            </div>
          @endif
          @if(session('error'))
            <div class="mb-6 rounded-2xl bg-red-50 border border-red-100 p-4 text-xs font-bold text-red-800 flex items-center gap-2 shadow-sm animate-pulse">
                <span>⚠️</span> {{ session('error') }}
            </div>
          @endif

          <!-- Views slot insertion -->
          <div class="space-y-6">
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
    // Collapsible & Mobile Sidebar script
    const hamburgerBtn = document.getElementById('hamburgerBtn');
    const closeSidebarBtn = document.getElementById('closeSidebarBtn');
    const sidebar = document.getElementById('sidebar');
    const sidebarOverlay = document.getElementById('sidebarOverlay');

    function toggleMobileSidebar() {
      sidebar.classList.toggle('hidden');
      sidebar.classList.toggle('flex');
      sidebarOverlay.classList.toggle('hidden');
    }

    hamburgerBtn?.addEventListener('click', toggleMobileSidebar);
    closeSidebarBtn?.addEventListener('click', toggleMobileSidebar);
    sidebarOverlay?.addEventListener('click', toggleMobileSidebar);
  </script>
</body>
</html>
