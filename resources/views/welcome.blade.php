<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="scroll-smooth">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>KasirKantin - Solusi Kasir Modern Untuk Kantin Sekolah dan Kampus</title>
    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    
    <!-- Tailwind CSS (via Vite) -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    
    <style>
        body {
            font-family: 'Plus Jakarta Sans', sans-serif;
        }
        @keyframes fadeInUp {
            from {
                opacity: 0;
                transform: translateY(20px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }
        .animate-fade-in-up {
            animation: fadeInUp 0.8s cubic-bezier(0.16, 1, 0.3, 1) forwards;
        }
        .animate-delay-100 {
            animation-delay: 100ms;
        }
        .animate-delay-200 {
            animation-delay: 200ms;
        }
        .animate-delay-300 {
            animation-delay: 300ms;
        }
    </style>
</head>
<body class="bg-slate-50 text-slate-900 antialiased selection:bg-indigo-500 selection:text-white overflow-x-hidden">

    <!-- Header / Navbar -->
    <header class="sticky top-0 z-50 bg-white/70 backdrop-blur-lg border-b border-slate-100/60 transition-all duration-300">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex items-center justify-between h-20">
                <!-- Logo -->
                <div class="flex items-center gap-2.5">
                    <div class="w-10 h-10 rounded-xl bg-indigo-600 flex items-center justify-center text-white font-extrabold text-xl shadow-md shadow-indigo-600/10">
                        K
                    </div>
                    <span class="text-xl font-extrabold tracking-tight text-slate-900">Kasir<span class="text-indigo-600">Kantin</span></span>
                </div>

                <!-- Desktop Menu -->
                <nav class="hidden md:flex items-center gap-8 text-sm font-semibold">
                    <a href="#home" class="nav-link-item text-indigo-600 border-b-2 border-indigo-600 pb-1.5 transition-all" id="nav-home">Home</a>
                    <a href="#fitur" class="nav-link-item text-slate-600 hover:text-indigo-600 border-b-2 border-transparent pb-1.5 transition-all" id="nav-fitur">Fitur</a>
                    <a href="#tentang" class="nav-link-item text-slate-600 hover:text-indigo-600 border-b-2 border-transparent pb-1.5 transition-all" id="nav-tentang">Tentang Kami</a>
                </nav>

                <!-- Auth Action Buttons -->
                <div class="hidden md:flex items-center gap-4">
                    @auth
                        <a href="{{ route('dashboard') }}" class="inline-flex items-center justify-center px-5 py-2.5 rounded-xl text-xs font-bold text-white bg-indigo-600 hover:bg-indigo-700 shadow-md shadow-indigo-600/10 transform hover:-translate-y-0.5 active:translate-y-0 transition-all duration-200">
                            Buka Dashboard
                        </a>
                        <form method="POST" action="{{ route('logout') }}" class="inline">
                            @csrf
                            <button type="submit" class="text-xs font-bold text-slate-400 hover:text-rose-500 transition-colors">
                                Logout
                            </button>
                        </form>
                    @else
                        <a href="{{ route('login') }}" class="text-xs font-bold text-slate-700 hover:text-indigo-600 transition-colors">
                            Login
                        </a>
                        <a href="{{ route('register') }}" class="inline-flex items-center justify-center px-5 py-2.5 rounded-xl text-xs font-bold text-white bg-indigo-600 hover:bg-indigo-700 shadow-md shadow-indigo-600/10 transform hover:-translate-y-0.5 active:translate-y-0 transition-all duration-200">
                            Daftar Owner
                        </a>
                    @endauth
                </div>

                <!-- Mobile Menu Button -->
                <button type="button" id="mobile-menu-btn" class="md:hidden p-2 rounded-lg text-slate-600 hover:bg-slate-100 focus:outline-none transition-colors">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16m-7 6h7"/>
                    </svg>
                </button>
            </div>
        </div>

        <!-- Mobile Menu -->
        <div id="mobile-menu" class="hidden md:hidden border-t border-slate-100 bg-white px-4 py-6 space-y-4 shadow-inner">
            <nav class="flex flex-col gap-4 text-base font-semibold text-slate-600">
                <a href="#home" class="hover:text-indigo-600">Home</a>
                <a href="#fitur" class="hover:text-indigo-600">Fitur</a>
                <a href="#tentang" class="hover:text-indigo-600">Tentang Kami</a>
            </nav>
            <div class="border-t border-slate-100 pt-4 flex flex-col gap-3">
                @auth
                    <a href="{{ route('dashboard') }}" class="w-full inline-flex items-center justify-center px-5 py-3 rounded-xl text-base font-bold text-white bg-indigo-600 hover:bg-indigo-700 transition-all">
                        Buka Dashboard
                    </a>
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button type="submit" class="w-full inline-flex items-center justify-center px-5 py-3 rounded-xl text-base font-bold text-rose-500 border border-rose-100 hover:bg-rose-50 transition-all">
                            Logout
                        </button>
                    </form>
                @else
                    <a href="{{ route('login') }}" class="w-full inline-flex items-center justify-center px-5 py-3 rounded-xl text-base font-bold text-slate-700 border border-slate-200 hover:bg-slate-50 transition-all">
                        Login
                    </a>
                    <a href="{{ route('register') }}" class="w-full inline-flex items-center justify-center px-5 py-3 rounded-xl text-base font-bold text-white bg-indigo-600 hover:bg-indigo-700 transition-all">
                        Daftar Owner
                    </a>
                @endauth
            </div>
        </div>
    </header>

    <!-- Session Alerts -->
    @if(session('error'))
        <div id="session-alert" class="fixed top-24 left-1/2 transform -translate-x-1/2 z-[60] bg-rose-500 text-white px-6 py-3 rounded-xl shadow-lg shadow-rose-500/20 flex items-center gap-3 animate-fade-in-up">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
            <span class="text-sm font-semibold">{{ session('error') }}</span>
            <button onclick="document.getElementById('session-alert').remove()" class="ml-4 text-white/80 hover:text-white">
                &times;
            </button>
        </div>
    @endif

    <!-- Hero Section -->
    <section id="home" class="relative overflow-hidden py-24 lg:py-36 bg-gradient-to-b from-indigo-50/40 via-white to-slate-50">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-16 lg:gap-24 items-center">
                <!-- Text Column -->
                <div class="space-y-8 text-center lg:text-left animate-fade-in-up">
                    <span class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-full text-xs font-bold bg-indigo-50 text-indigo-700 border border-indigo-100">
                        ⚡ Sistem POS Akademik Terintegrasi
                    </span>
                    <h1 class="text-4xl sm:text-5xl lg:text-6xl font-extrabold tracking-tight text-slate-900 leading-[1.12]">
                        Sistem Kasir Digital untuk <span class="text-indigo-600 bg-gradient-to-r from-indigo-600 to-indigo-500 bg-clip-text text-transparent">Kantin Sekolah & Kampus</span>
                    </h1>
                    <p class="text-base sm:text-lg text-slate-500 max-w-xl mx-auto lg:mx-0 leading-relaxed">
                        Kelola transaksi, inventaris, pelanggan, serta laporan penjualan secara real-time dalam satu platform yang aman dan mudah digunakan.
                    </p>
                    <div class="flex flex-col sm:flex-row items-center justify-center lg:justify-start gap-4">
                        @auth
                            <a href="{{ route('dashboard') }}" class="w-full sm:w-auto inline-flex items-center justify-center px-8 py-4 rounded-2xl text-sm font-bold text-white bg-indigo-600 hover:bg-indigo-700 shadow-md hover:shadow-lg hover:shadow-indigo-600/20 transform hover:-translate-y-0.5 active:translate-y-0 transition-all duration-200">
                                Buka Dashboard
                            </a>
                        @else
                            <a href="{{ route('register') }}" class="w-full sm:w-auto inline-flex items-center justify-center px-8 py-4 rounded-2xl text-sm font-bold text-white bg-indigo-600 hover:bg-indigo-700 shadow-md hover:shadow-lg hover:shadow-indigo-600/20 transform hover:-translate-y-0.5 active:translate-y-0 transition-all duration-200">
                                Daftar Owner
                            </a>
                            <button type="button" onclick="openDemoModal()" class="w-full sm:w-auto inline-flex items-center justify-center px-8 py-4 rounded-2xl text-sm font-bold text-slate-700 bg-white border border-slate-200 hover:bg-slate-50 hover:border-slate-300 shadow-sm hover:shadow transform hover:-translate-y-0.5 active:translate-y-0 transition-all duration-200 cursor-pointer focus:outline-none">
                                Lihat Demo
                            </button>
                        @endauth
                    </div>
                </div>

                <!-- Mockup POS Column -->
                <div class="relative lg:mt-0 animate-fade-in-up animate-delay-150">
                    <div class="absolute inset-0 bg-gradient-to-tr from-indigo-500 to-amber-500 rounded-3xl blur-3xl opacity-20 -rotate-6 scale-95"></div>
                    <div class="relative bg-slate-900 rounded-3xl border border-slate-800 shadow-2xl overflow-hidden text-slate-100">
                        <!-- Header mockup -->
                        <div class="flex items-center justify-between bg-slate-950 px-5 py-3 border-b border-slate-850">
                            <div class="flex items-center gap-3">
                                <div class="flex gap-1.5">
                                    <span class="w-3 h-3 rounded-full bg-rose-500"></span>
                                    <span class="w-3 h-3 rounded-full bg-amber-500"></span>
                                    <span class="w-3 h-3 rounded-full bg-emerald-500"></span>
                                </div>
                                <span class="text-xs font-bold text-slate-400 tracking-wide">POS Kasir - Stan Bu Sri</span>
                            </div>
                            <span class="inline-flex items-center gap-1.5 px-2 py-0.5 rounded bg-emerald-500/10 text-emerald-400 text-[10px] font-extrabold uppercase">
                                <span class="w-1.5 h-1.5 rounded-full bg-emerald-500 animate-ping"></span> Live
                            </span>
                        </div>
                        
                        <!-- Mockup POS Grid -->
                        <div class="grid grid-cols-3 divide-x divide-slate-800 bg-slate-900/90 h-[380px]">
                            <!-- Catalog (left 2 cols) -->
                            <div class="col-span-2 p-4 space-y-4 flex flex-col justify-between">
                                <!-- Search and filter mockup -->
                                <div class="space-y-2">
                                    <div class="bg-slate-955 border border-slate-800 rounded-xl px-3 py-1.5 flex items-center justify-between">
                                        <span class="text-[11px] text-slate-400">🔍 Cari mie goreng, es teh...</span>
                                        <span class="text-[10px] text-slate-650 font-mono">F3</span>
                                    </div>
                                    <div class="flex gap-1.5 text-[9px] font-bold">
                                        <span class="px-2.5 py-1 rounded-lg bg-indigo-600 text-white">Semua</span>
                                        <span class="px-2.5 py-1 rounded-lg bg-slate-950 text-slate-400 border border-slate-800">Makanan</span>
                                        <span class="px-2.5 py-1 rounded-lg bg-slate-950 text-slate-400 border border-slate-800">Minuman</span>
                                    </div>
                                </div>

                                <!-- Product Grid -->
                                <div class="grid grid-cols-2 gap-2.5 my-auto">
                                    <!-- Item 1 -->
                                    <div class="bg-slate-950 border border-slate-800/80 rounded-xl p-2.5 space-y-2 relative overflow-hidden group">
                                        <div class="h-16 bg-slate-900/50 rounded-lg flex items-center justify-center text-2xl">
                                            🍛
                                        </div>
                                        <div>
                                            <span class="text-[9px] font-extrabold text-indigo-400 uppercase tracking-wide">Makanan</span>
                                            <h4 class="text-[10px] font-bold text-white truncate">Nasi Goreng Telur</h4>
                                            <div class="flex justify-between items-center mt-1">
                                                <span class="text-[9px] font-extrabold text-slate-200">Rp 12.000</span>
                                                <span class="px-1.5 py-0.5 bg-indigo-500/10 text-indigo-450 rounded text-[8px] font-bold">+</span>
                                            </div>
                                        </div>
                                    </div>
                                    <!-- Item 2 -->
                                    <div class="bg-slate-950 border border-slate-800/80 rounded-xl p-2.5 space-y-2 relative overflow-hidden">
                                        <div class="h-16 bg-slate-900/50 rounded-lg flex items-center justify-center text-2xl">
                                            🍜
                                        </div>
                                        <div>
                                            <span class="text-[9px] font-extrabold text-indigo-400 uppercase tracking-wide">Makanan</span>
                                            <h4 class="text-[10px] font-bold text-white truncate">Mie Ayam Pangsit</h4>
                                            <div class="flex justify-between items-center mt-1">
                                                <span class="text-[9px] font-extrabold text-slate-200">Rp 14.000</span>
                                                <span class="px-1.5 py-0.5 bg-indigo-500/10 text-indigo-450 rounded text-[8px] font-bold">+</span>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- Footer info inside mockup -->
                                <div class="text-[8px] text-slate-500 flex justify-between items-center border-t border-slate-805 pt-2">
                                    <span>Kasir: Rina Amanda</span>
                                    <span>Stok Sinkron: 100%</span>
                                </div>
                            </div>

                            <!-- Cart sidebar (right 1 col) -->
                            <div class="p-3 flex flex-col justify-between bg-slate-950/50">
                                <div class="space-y-3">
                                    <div class="pb-2 border-b border-slate-800 flex justify-between items-center">
                                        <span class="text-[10px] font-extrabold text-slate-300 uppercase tracking-wider">Keranjang</span>
                                        <span class="text-[8px] text-slate-500 font-mono">2 Items</span>
                                    </div>

                                    <!-- Cart List -->
                                    <div class="space-y-2">
                                        <div class="space-y-0.5">
                                            <div class="flex justify-between text-[9px] font-bold text-white">
                                                <span class="truncate">Mie Ayam</span>
                                                <span>Rp14k</span>
                                            </div>
                                            <div class="flex justify-between text-[8px] text-slate-400">
                                                <span>1x Qty</span>
                                                <span class="text-[7px] text-slate-600">Stok: 24</span>
                                            </div>
                                        </div>
                                        <div class="space-y-0.5">
                                            <div class="flex justify-between text-[9px] font-bold text-white">
                                                <span class="truncate">Es Teh Manis</span>
                                                <span>Rp6k</span>
                                            </div>
                                            <div class="flex justify-between text-[8px] text-slate-400">
                                                <span>2x Qty</span>
                                                <span class="text-[7px] text-slate-600">Stok: 50</span>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- Billing calculations -->
                                <div class="space-y-2 pt-2 border-t border-slate-800">
                                    <!-- Discount badge -->
                                    <div class="bg-indigo-950/60 border border-indigo-900/40 rounded-lg p-1 text-[8px] text-indigo-400 flex items-center justify-between font-bold">
                                        <span>Discount Member 10%</span>
                                        <span>-Rp2k</span>
                                    </div>
                                    <div class="space-y-1 text-[8px] text-slate-400">
                                        <div class="flex justify-between">
                                            <span>Subtotal</span>
                                            <span>Rp 20.000</span>
                                        </div>
                                        <div class="flex justify-between font-extrabold text-white text-[9px]">
                                            <span>Total</span>
                                            <span class="text-indigo-405">Rp 18.000</span>
                                        </div>
                                    </div>
                                    <!-- Checkout button mockup -->
                                    <div class="w-full py-1.5 bg-indigo-600 text-white rounded-lg text-[9px] font-extrabold text-center tracking-wide shadow-lg shadow-indigo-600/20">
                                        Bayar & Cetak
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Trust Section -->
    <section class="py-24 bg-slate-50/50 border-b border-slate-100/80">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center space-y-4 mb-16 animate-fade-in-up">
                <h2 class="text-3xl font-extrabold text-slate-900 tracking-tight">Mengelola Kantin Jadi Lebih Mudah</h2>
                <p class="text-slate-500 max-w-xl mx-auto text-sm">KasirKantin menyederhanakan operasional harian Anda agar Anda bisa fokus pada pertumbuhan bisnis.</p>
            </div>

            <!-- 4 Grid Cards -->
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-8">
                <!-- Card 1: Transaksi Cepat -->
                <div class="p-8 bg-white rounded-3xl border border-slate-100/80 shadow-[0_8px_30px_rgb(0,0,0,0.015)] hover:shadow-[0_15px_40px_rgba(0,0,0,0.04)] hover:-translate-y-1 transition-all duration-300 flex flex-col justify-between space-y-6 animate-fade-in-up animate-delay-100">
                    <div class="w-12 h-12 rounded-2xl bg-indigo-50/80 text-indigo-600 flex items-center justify-center font-bold text-xl shadow-inner">⚡</div>
                    <div class="space-y-2">
                        <h3 class="text-lg font-bold text-slate-800">Transaksi Cepat</h3>
                        <p class="text-slate-500 text-xs leading-relaxed">Proses pembayaran lebih cepat dan akurat untuk menekan waktu antrean saat jam sibuk.</p>
                    </div>
                </div>

                <!-- Card 2: Manajemen Produk -->
                <div class="p-8 bg-white rounded-3xl border border-slate-100/80 shadow-[0_8px_30px_rgb(0,0,0,0.015)] hover:shadow-[0_15px_40px_rgba(0,0,0,0.04)] hover:-translate-y-1 transition-all duration-300 flex flex-col justify-between space-y-6 animate-fade-in-up animate-delay-150">
                    <div class="w-12 h-12 rounded-2xl bg-amber-50/80 text-amber-600 flex items-center justify-center font-bold text-xl shadow-inner">📦</div>
                    <div class="space-y-2">
                        <h3 class="text-lg font-bold text-slate-800">Manajemen Produk</h3>
                        <p class="text-slate-500 text-xs leading-relaxed">Kelola kategori menu, pengaturan harga jual, dan sinkronisasi sisa stok secara real-time.</p>
                    </div>
                </div>

                <!-- Card 3: Laporan Penjualan -->
                <div class="p-8 bg-white rounded-3xl border border-slate-100/80 shadow-[0_8px_30px_rgb(0,0,0,0.015)] hover:shadow-[0_15px_40px_rgba(0,0,0,0.04)] hover:-translate-y-1 transition-all duration-300 flex flex-col justify-between space-y-6 animate-fade-in-up animate-delay-200">
                    <div class="w-12 h-12 rounded-2xl bg-emerald-50/80 text-emerald-600 flex items-center justify-center font-bold text-xl shadow-inner">📊</div>
                    <div class="space-y-2">
                        <h3 class="text-lg font-bold text-slate-800">Laporan Penjualan</h3>
                        <p class="text-slate-500 text-xs leading-relaxed">Pantau grafik pemasukan harian dan bulanan secara otomatis tanpa rekapitulasi manual.</p>
                    </div>
                </div>

                <!-- Card 4: Multi User -->
                <div class="p-8 bg-white rounded-3xl border border-slate-100/80 shadow-[0_8px_30px_rgb(0,0,0,0.015)] hover:shadow-[0_15px_40px_rgba(0,0,0,0.04)] hover:-translate-y-1 transition-all duration-300 flex flex-col justify-between space-y-6 animate-fade-in-up animate-delay-250">
                    <div class="w-12 h-12 rounded-2xl bg-rose-50/80 text-rose-600 flex items-center justify-center font-bold text-xl shadow-inner">👥</div>
                    <div class="space-y-2">
                        <h3 class="text-lg font-bold text-slate-800">Multi User</h3>
                        <p class="text-slate-500 text-xs leading-relaxed">Akses terbagi berdasarkan peran Owner, Admin, dan Kasir dengan hak keamanan yang berbeda.</p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Fitur Section -->
    <section id="fitur" class="py-20 bg-white border-y border-slate-100/80">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center space-y-4 mb-16">
                <h2 class="text-3xl font-extrabold text-slate-900">Semua yang Kamu Butuhin Buat Kelola Kantin</h2>
                <p class="text-slate-500 max-w-xl mx-auto text-sm">Kami merancang fitur yang praktis dan gampang dipakai kasir sekolah sekalipun, tanpa perlu training lama.</p>
            </div>

            <!-- Features Cards (6 Cards) -->
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
                <!-- Card 1 -->
                <div class="p-8 bg-slate-50 rounded-2xl border border-slate-100 space-y-4 hover:shadow-md transition-shadow">
                    <div class="w-12 h-12 rounded-xl bg-indigo-50 text-indigo-600 flex items-center justify-center font-bold text-xl">👥</div>
                    <h3 class="text-lg font-bold text-slate-800">Akses Staf Sesuai Tugas</h3>
                    <p class="text-slate-500 text-xs leading-relaxed">
                        Bagi tugas dengan aman. Owner bisa pantau semua keuangan, Admin kelola stok & menu, dan Kasir fokus layani pembeli dengan cepat.
                    </p>
                </div>

                <!-- Card 2 -->
                <div class="p-8 bg-slate-50 rounded-2xl border border-slate-100 space-y-4 hover:shadow-md transition-shadow">
                    <div class="w-12 h-12 rounded-xl bg-purple-50 text-purple-600 flex items-center justify-center font-bold text-xl">🏪</div>
                    <h3 class="text-lg font-bold text-slate-800">Kelola Banyak Cabang / Stand</h3>
                    <p class="text-slate-500 text-xs leading-relaxed">
                        Punya lebih dari satu stan atau cabang kantin? Pantau semuanya dalam satu dashboard terpusat tanpa harus keliling lokasi setiap hari.
                    </p>
                </div>

                <!-- Card 3 -->
                <div class="p-8 bg-slate-50 rounded-2xl border border-slate-100 space-y-4 hover:shadow-md transition-shadow">
                    <div class="w-12 h-12 rounded-xl bg-emerald-50 text-emerald-600 flex items-center justify-center font-bold text-xl">📊</div>
                    <h3 class="text-lg font-bold text-slate-800">Laporan Otomatis Real-time</h3>
                    <p class="text-slate-500 text-xs leading-relaxed">
                        Nggak usah hitung manual pakai kalkulator tiap sore. Laporan laba kotor, volume penjualan, dan data transaksi harian langsung jadi instan.
                    </p>
                </div>

                <!-- Card 4 -->
                <div class="p-8 bg-slate-50 rounded-2xl border border-slate-100 space-y-4 hover:shadow-md transition-shadow">
                    <div class="w-12 h-12 rounded-xl bg-sky-50 text-sky-600 flex items-center justify-center font-bold text-xl">🍔</div>
                    <h3 class="text-lg font-bold text-slate-800">Atur Menu & Stok Gampang</h3>
                    <p class="text-slate-500 text-xs leading-relaxed">
                        Tambah makanan baru, update harga jual, kelola stok harian, sampai matikan menu yang habis langsung lewat HP atau komputer.
                    </p>
                </div>

                <!-- Card 5 -->
                <div class="p-8 bg-slate-50 rounded-2xl border border-slate-100 space-y-4 hover:shadow-md transition-shadow">
                    <div class="w-12 h-12 rounded-xl bg-yellow-50 text-yellow-600 flex items-center justify-center font-bold text-xl">⚙️</div>
                    <h3 class="text-lg font-bold text-slate-800">Kontrol Staf & Hak Akses</h3>
                    <p class="text-slate-500 text-xs leading-relaxed">
                        Daftarkan akun kasir baru, batasi hak akses stand tertentu, hingga ganti kata sandi staf yang lupa password langsung dari dashboard Owner.
                    </p>
                </div>

                <!-- Card 6 -->
                <div class="p-8 bg-slate-50 rounded-2xl border border-slate-100 space-y-4 hover:shadow-md transition-shadow">
                    <div class="w-12 h-12 rounded-xl bg-pink-50 text-pink-600 flex items-center justify-center font-bold text-xl">⏳</div>
                    <h3 class="text-lg font-bold text-slate-800">Pantau Transaksi Langsung</h3>
                    <p class="text-slate-500 text-xs leading-relaxed">
                        Setiap transaksi kasir di stand kantin langsung tercatat dan diperbarui detik demi detik, memberi Anda pengawasan penuh secara instan.
                    </p>
                </div>
            </div>
        </div>
    </section>

    <!-- Statistics Section -->
    <section class="py-16 bg-indigo-950 text-white">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="grid grid-cols-2 md:grid-cols-4 gap-8 text-center">
                <div class="space-y-1">
                    <span class="block text-3xl sm:text-4xl font-extrabold text-indigo-400">1000+</span>
                    <span class="text-xs sm:text-sm font-semibold uppercase tracking-wider text-slate-300">Transaksi Sukses</span>
                </div>
                <div class="space-y-1">
                    <span class="block text-3xl sm:text-4xl font-extrabold text-indigo-400">100+</span>
                    <span class="text-xs sm:text-sm font-semibold uppercase tracking-wider text-slate-300">Produk Terdaftar</span>
                </div>
                <div class="space-y-1">
                    <span class="block text-3xl sm:text-4xl font-extrabold text-indigo-400">50+</span>
                    <span class="text-xs sm:text-sm font-semibold uppercase tracking-wider text-slate-300">Owner Bergabung</span>
                </div>
                <div class="space-y-1">
                    <span class="block text-3xl sm:text-4xl font-extrabold text-indigo-400">99.9%</span>
                    <span class="text-xs sm:text-sm font-semibold uppercase tracking-wider text-slate-300">Sistem Uptime</span>
                </div>
            </div>
        </div>
    </section>

    <!-- Tentang Kami Section -->
    <section id="tentang" class="py-20 bg-white">
        <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 text-center space-y-6">
            <h2 class="text-3xl font-extrabold text-slate-900">Kenapa Kami Membangun KasirKantin?</h2>
            <div class="w-12 h-1 bg-indigo-600 mx-auto rounded-full"></div>
            <p class="text-base sm:text-lg text-slate-600 leading-relaxed font-medium">
                Kami tahu betul repotnya mengelola kantin sekolah atau kampus—mulai dari harus melayani ratusan siswa lapar dalam waktu istirahat yang cuma 15-30 menit, hingga mencatat total omzet harian dari stan makanan yang berbeda-beda. KasirKantin hadir untuk mempermudah semuanya. Kami ingin membantu sekolah dan pemilik stand memiliki pencatatan keuangan yang rapi, cepat, dan transparan tanpa perlu pusing dengan mesin kasir yang mahal atau sistem POS yang rumit.
            </p>
        </div>
    </section>

    <!-- CTA Section -->
    <section class="py-16 bg-slate-50 border-t border-slate-100">
        <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 text-center space-y-8">
            <h2 class="text-3xl font-extrabold text-slate-900 leading-tight">Siap Merapikan Transaksi Kantin Anda?</h2>
            <p class="text-slate-500 max-w-lg mx-auto text-sm">Bergabunglah bersama kami dan nikmati kemudahan mengontrol stand kantin Anda secara real-time dari mana saja.</p>
            <div class="flex flex-col sm:flex-row justify-center items-center gap-4">
                @auth
                    <a href="{{ route('dashboard') }}" class="w-full sm:w-auto inline-flex items-center justify-center px-8 py-3.5 bg-indigo-600 hover:bg-indigo-700 text-white rounded-xl font-bold text-sm shadow-md transition-colors">
                        Buka Dashboard Kasir
                    </a>
                @else
                    <a href="{{ route('register') }}" class="w-full sm:w-auto inline-flex items-center justify-center px-8 py-3.5 bg-indigo-600 hover:bg-indigo-700 text-white rounded-xl font-bold text-sm shadow-md transition-colors">
                        Buat Akun Owner Baru
                    </a>
                    <a href="{{ route('login') }}" class="w-full sm:w-auto inline-flex items-center justify-center px-8 py-3.5 bg-white text-slate-700 border border-slate-200 hover:bg-slate-50 rounded-xl font-bold text-sm shadow-sm transition-colors">
                        Login ke Akun
                    </a>
                @endauth
            </div>
        </div>
    </section>

    <!-- Footer Modern -->
    <footer class="bg-slate-950 text-slate-400 py-12 text-center border-t border-slate-900">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 space-y-4">
            <p class="text-sm">&copy; {{ date('Y') }} KasirKantin. Seluruh Hak Cipta Dilindungi.</p>
            <p class="text-[10px] text-slate-600 font-mono">Sistem POS Pintar Terintegrasi.</p>
        </div>
    </footer>

    <!-- Hidden form for demo quick login -->
    <form id="quick-login-form" method="POST" action="{{ route('login') }}" class="hidden">
        @csrf
        <input type="hidden" name="email" id="quick-email">
        <input type="hidden" name="password" id="quick-password">
    </form>

    <!-- Demo Account Modal -->
    <div id="demo-modal" class="fixed inset-0 z-50 hidden items-center justify-center p-4">
        <!-- Backdrop -->
        <div class="absolute inset-0 bg-slate-950/60 backdrop-blur-sm" onclick="closeDemoModal()"></div>
        
        <!-- Modal Content Box -->
        <div class="relative bg-white rounded-3xl border border-slate-100 shadow-2xl w-full max-w-2xl overflow-hidden z-10 transform scale-95 opacity-0 transition-all duration-300 ease-out" id="demo-modal-content">
            <!-- Header bar with gradient border -->
            <div class="absolute top-0 left-0 w-full h-[4px] bg-gradient-to-r from-indigo-500 via-amber-500 to-orange-400"></div>
            
            <div class="p-6 sm:p-8 space-y-6">
                <!-- Header Title & Close Button -->
                <div class="flex items-center justify-between pb-4 border-b border-slate-100">
                    <div>
                        <h3 class="text-xl font-extrabold text-slate-900 tracking-tight">Pilih Akses Akun Demo</h3>
                        <p class="text-slate-500 text-xs mt-1">Eksplorasi KasirKantin sesuai kebutuhan peran operasional.</p>
                    </div>
                    <button onclick="closeDemoModal()" class="text-slate-400 hover:text-slate-600 font-semibold text-2xl p-1 leading-none">&times;</button>
                </div>

                <!-- 3 Role Options -->
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                    <!-- Option 1: Owner -->
                    <div class="p-5 bg-slate-50 rounded-2xl border border-slate-100/80 hover:border-indigo-200 hover:shadow-md transition-all duration-200 flex flex-col justify-between space-y-4">
                        <div class="space-y-2">
                            <span class="text-2xl">🏪</span>
                            <h4 class="font-extrabold text-slate-800 text-sm">Owner / Pemilik</h4>
                            <p class="text-[10px] text-slate-400 leading-normal">Akses penuh: kelola stan, pantau staf, laporan keuangan, & audit sistem.</p>
                        </div>
                        <div class="space-y-2 pt-2 border-t border-slate-150">
                            <div class="text-[9px] font-mono text-slate-500 truncate">owner@example.com</div>
                            <button type="button" onclick="quickLogin('owner@example.com', 'password')" class="w-full py-2 bg-indigo-600 hover:bg-indigo-700 text-white rounded-xl text-[10px] font-bold shadow-sm transition-colors cursor-pointer">
                                Masuk Instan
                            </button>
                        </div>
                    </div>

                    <!-- Option 2: Admin -->
                    <div class="p-5 bg-slate-50 rounded-2xl border border-slate-100/80 hover:border-indigo-200 hover:shadow-md transition-all duration-200 flex flex-col justify-between space-y-4">
                        <div class="space-y-2">
                            <span class="text-2xl">📁</span>
                            <h4 class="font-extrabold text-slate-800 text-sm">Admin Operasional</h4>
                            <p class="text-[10px] text-slate-400 leading-normal">Akses manajemen: kelola menu makanan, atur harga, & sisa stok produk.</p>
                        </div>
                        <div class="space-y-2 pt-2 border-t border-slate-150">
                            <div class="text-[9px] font-mono text-slate-500 truncate">admin@example.com</div>
                            <button type="button" onclick="quickLogin('admin@example.com', 'password')" class="w-full py-2 bg-indigo-600 hover:bg-indigo-700 text-white rounded-xl text-[10px] font-bold shadow-sm transition-colors cursor-pointer">
                                Masuk Instan
                            </button>
                        </div>
                    </div>

                    <!-- Option 3: Kasir -->
                    <div class="p-5 bg-slate-50 rounded-2xl border border-slate-100/80 hover:border-indigo-200 hover:shadow-md transition-all duration-200 flex flex-col justify-between space-y-4">
                        <div class="space-y-2">
                            <span class="text-2xl">⚡</span>
                            <h4 class="font-extrabold text-slate-800 text-sm">Kasir Operator</h4>
                            <p class="text-[10px] text-slate-400 leading-normal">Akses operasional: antarmuka POS penjualan, input keranjang, & pembayaran.</p>
                        </div>
                        <div class="space-y-2 pt-2 border-t border-slate-150">
                            <div class="text-[9px] font-mono text-slate-500 truncate">kasir@example.com</div>
                            <button type="button" onclick="quickLogin('kasir@example.com', 'password')" class="w-full py-2 bg-indigo-600 hover:bg-indigo-700 text-white rounded-xl text-[10px] font-bold shadow-sm transition-colors cursor-pointer">
                                Masuk Instan
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        // Toggle mobile menu view
        const mobileMenuBtn = document.getElementById('mobile-menu-btn');
        const mobileMenu = document.getElementById('mobile-menu');

        mobileMenuBtn?.addEventListener('click', () => {
            mobileMenu.classList.toggle('hidden');
        });

        // Scroll spy for active navbar link
        const sections = document.querySelectorAll("section[id]");
        const navLinks = document.querySelectorAll(".nav-link-item");

        window.addEventListener("scroll", () => {
            let current = "";
            sections.forEach((section) => {
                const sectionTop = section.offsetTop;
                if (window.scrollY >= sectionTop - 120) {
                    current = section.getAttribute("id");
                }
            });

            navLinks.forEach((link) => {
                link.classList.remove("text-indigo-600", "border-indigo-600");
                link.classList.add("text-slate-600", "border-transparent");
                if (link.getAttribute("href") === `#${current}`) {
                    link.classList.add("text-indigo-600", "border-indigo-600");
                    link.classList.remove("text-slate-600", "border-transparent");
                }
            });
        });

        // Demo Modal Handlers
        const demoModal = document.getElementById('demo-modal');
        const demoModalContent = document.getElementById('demo-modal-content');

        function openDemoModal() {
            demoModal.classList.remove('hidden');
            demoModal.classList.add('flex');
            setTimeout(() => {
                demoModalContent.classList.remove('scale-95', 'opacity-0');
                demoModalContent.classList.add('scale-100', 'opacity-100');
            }, 10);
        }

        function closeDemoModal() {
            demoModalContent.classList.remove('scale-100', 'opacity-100');
            demoModalContent.classList.add('scale-95', 'opacity-0');
            setTimeout(() => {
                demoModal.classList.add('hidden');
                demoModal.classList.remove('flex');
            }, 300);
        }

        function quickLogin(email, password) {
            document.getElementById('quick-email').value = email;
            document.getElementById('quick-password').value = password;
            
            // Show loading state
            const activeBtn = event.currentTarget;
            activeBtn.disabled = true;
            activeBtn.innerHTML = 'Memproses...';
            
            document.getElementById('quick-login-form').submit();
        }
    </script>
</body>
</html>
