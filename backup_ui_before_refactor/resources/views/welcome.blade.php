<!slot>
<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="scroll-smooth">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>KasirKantin - Solusi Kasir Pintar Kantin Modern</title>
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
    </style>
</head>
<body class="bg-slate-50 text-slate-900 antialiased selection:bg-indigo-500 selection:text-white">

    <!-- Header / Navbar -->
    <header class="sticky top-0 z-50 bg-white/80 backdrop-blur-md border-b border-slate-100">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex items-center justify-between h-20">
                <!-- Logo -->
                <div class="flex items-center gap-2">
                    <div class="w-10 h-10 rounded-xl bg-indigo-600 flex items-center justify-center text-white font-bold text-xl shadow-lg shadow-indigo-600/30">
                        K
                    </div>
                    <span class="text-xl font-bold tracking-tight text-slate-800">Kasir<span class="text-indigo-600">Kantin</span></span>
                </div>

                <!-- Desktop Menu -->
                <nav class="hidden md:flex items-center gap-8 text-sm font-medium text-slate-600">
                    <a href="#home" class="hover:text-indigo-600 transition-colors">Home</a>
                    <a href="#fitur" class="hover:text-indigo-600 transition-colors">Fitur</a>
                    <a href="#tentang" class="hover:text-indigo-600 transition-colors">Tentang Kami</a>
                </nav>

                <!-- Auth Action Buttons -->
                <div class="hidden md:flex items-center gap-4">
                    @auth
                        <a href="{{ route('dashboard') }}" class="inline-flex items-center justify-center px-5 py-2.5 rounded-xl text-sm font-semibold text-white bg-indigo-600 hover:bg-indigo-700 shadow-md shadow-indigo-600/20 hover:shadow-indigo-600/30 transition-all">
                            Buka Dashboard
                        </a>
                    @else
                        <a href="{{ route('login') }}" class="text-sm font-semibold text-slate-700 hover:text-indigo-600 transition-colors">
                            Login
                        </a>
                        <a href="{{ route('register') }}" class="inline-flex items-center justify-center px-5 py-2.5 rounded-xl text-sm font-semibold text-white bg-indigo-600 hover:bg-indigo-700 shadow-md shadow-indigo-600/20 hover:shadow-indigo-600/30 transition-all">
                            Daftar Owner
                        </a>
                    @endauth
                </div>

                <!-- Mobile Menu Button -->
                <button type="button" id="mobile-menu-btn" class="md:hidden p-2 rounded-lg text-slate-600 hover:bg-slate-100">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16m-7 6h7"/>
                    </svg>
                </button>
            </div>
        </div>

        <!-- Mobile Menu -->
        <div id="mobile-menu" class="hidden md:hidden border-t border-slate-100 bg-white px-4 py-6 space-y-4">
            <nav class="flex flex-col gap-4 text-base font-medium text-slate-600">
                <a href="#home" class="hover:text-indigo-600">Home</a>
                <a href="#fitur" class="hover:text-indigo-600">Fitur</a>
                <a href="#tentang" class="hover:text-indigo-600">Tentang Kami</a>
            </nav>
            <div class="border-t border-slate-100 pt-4 flex flex-col gap-3">
                @auth
                    <a href="{{ route('dashboard') }}" class="w-full inline-flex items-center justify-center px-5 py-3 rounded-xl text-base font-semibold text-white bg-indigo-600 hover:bg-indigo-700 transition-all">
                        Buka Dashboard
                    </a>
                @else
                    <a href="{{ route('login') }}" class="w-full inline-flex items-center justify-center px-5 py-3 rounded-xl text-base font-semibold text-slate-700 border border-slate-200 hover:bg-slate-50 transition-all">
                        Login
                    </a>
                    <a href="{{ route('register') }}" class="w-full inline-flex items-center justify-center px-5 py-3 rounded-xl text-base font-semibold text-white bg-indigo-600 hover:bg-indigo-700 transition-all">
                        Daftar Owner
                    </a>
                @endauth
            </div>
        </div>
    </header>

    <!-- Hero Section -->
    <section id="home" class="relative overflow-hidden py-20 lg:py-32 bg-gradient-to-b from-indigo-50/50 via-white to-slate-50">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-12 lg:gap-20 items-center">
                <!-- Text Column -->
                <div class="space-y-8 text-center lg:text-left">
                    <span class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-full text-xs font-semibold bg-indigo-50 text-indigo-700 border border-indigo-100">
                        ⚡ Point of Sale Modern Terintegrasi
                    </span>
                    <h1 class="text-4xl sm:text-5xl lg:text-6xl font-extrabold tracking-tight text-slate-900 leading-[1.15]">
                        Kelola Transaksi Kantin Anda Lebih <span class="text-indigo-600 bg-clip-text">Cepat &amp; Mudah</span>
                    </h1>
                    <p class="text-lg text-slate-600 max-w-xl mx-auto lg:mx-0">
                        Solusi aplikasi kasir berbasis web yang dirancang khusus untuk pemilik kantin sekolah, kampus, dan perkantoran. Catat transaksi, kelola menu, dan pantau performa bisnis secara realtime.
                    </p>
                    <div class="flex flex-col sm:flex-row items-center justify-center lg:justify-start gap-4">
                        @auth
                            <a href="{{ route('dashboard') }}" class="w-full sm:w-auto inline-flex items-center justify-center px-8 py-4 rounded-2xl text-base font-bold text-white bg-indigo-600 hover:bg-indigo-700 shadow-xl shadow-indigo-600/20 hover:shadow-indigo-600/35 transform hover:-translate-y-0.5 transition-all">
                                Buka Dashboard Anda
                            </a>
                        @else
                            <a href="{{ route('register') }}" class="w-full sm:w-auto inline-flex items-center justify-center px-8 py-4 rounded-2xl text-base font-bold text-white bg-indigo-600 hover:bg-indigo-700 shadow-xl shadow-indigo-600/20 hover:shadow-indigo-600/35 transform hover:-translate-y-0.5 transition-all">
                                Daftar Owner Sekarang
                            </a>
                            <a href="{{ route('login') }}" class="w-full sm:w-auto inline-flex items-center justify-center px-8 py-4 rounded-2xl text-base font-bold text-slate-700 bg-white border border-slate-200 hover:bg-slate-50 shadow-sm hover:shadow transition-all">
                                Masuk ke Akun
                            </a>
                        @endauth
                    </div>
                </div>

                <!-- Graphic/Visual Column -->
                <div class="relative lg:mt-0">
                    <div class="absolute inset-0 bg-gradient-to-tr from-indigo-500 to-purple-500 rounded-3xl blur-3xl opacity-20 -rotate-6 scale-95"></div>
                    <div class="relative bg-white rounded-3xl border border-slate-200/60 shadow-2xl p-6 sm:p-8 space-y-6">
                        <!-- Dashboard Mockup Header -->
                        <div class="flex items-center justify-between border-b border-slate-100 pb-4">
                            <div class="flex items-center gap-3">
                                <div class="w-3 h-3 rounded-full bg-red-400"></div>
                                <div class="w-3 h-3 rounded-full bg-yellow-400"></div>
                                <div class="w-3 h-3 rounded-full bg-green-400"></div>
                            </div>
                            <span class="text-xs text-slate-400 font-medium font-mono">kantin-pos-mockup</span>
                        </div>
                        
                        <!-- Dashboard Mockup Content -->
                        <div class="grid grid-cols-3 gap-4">
                            <div class="bg-indigo-50/50 border border-indigo-100 p-4 rounded-2xl space-y-2">
                                <span class="text-xs text-slate-500 block">Total Omset</span>
                                <span class="text-sm sm:text-base font-bold text-indigo-700 block">Rp 2.450.000</span>
                            </div>
                            <div class="bg-emerald-50/50 border border-emerald-100 p-4 rounded-2xl space-y-2">
                                <span class="text-xs text-slate-500 block">Transaksi</span>
                                <span class="text-sm sm:text-base font-bold text-emerald-700 block">+142 Order</span>
                            </div>
                            <div class="bg-purple-50/50 border border-purple-100 p-4 rounded-2xl space-y-2">
                                <span class="text-xs text-slate-500 block">Pelanggan</span>
                                <span class="text-sm sm:text-base font-bold text-purple-700 block">95 Member</span>
                            </div>
                        </div>

                        <!-- Progress Bar & Table Mockup -->
                        <div class="space-y-3">
                            <h4 class="text-xs font-bold uppercase tracking-wider text-slate-400">Menu Terlaris</h4>
                            <div class="space-y-2.5">
                                <div class="space-y-1">
                                    <div class="flex justify-between text-xs font-semibold text-slate-700">
                                        <span>Nasi Goreng Spesial</span>
                                        <span>78 Porsi</span>
                                    </div>
                                    <div class="w-full bg-slate-100 h-2 rounded-full overflow-hidden">
                                        <div class="bg-indigo-600 h-full rounded-full" style="width: 80%"></div>
                                    </div>
                                </div>
                                <div class="space-y-1">
                                    <div class="flex justify-between text-xs font-semibold text-slate-700">
                                        <span>Es Teh Manis</span>
                                        <span>120 Gelas</span>
                                    </div>
                                    <div class="w-full bg-slate-100 h-2 rounded-full overflow-hidden">
                                        <div class="bg-indigo-600 h-full rounded-full" style="width: 95%"></div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Description & About Section -->
    <section id="tentang" class="py-20 bg-white">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="max-w-3xl mx-auto text-center space-y-6">
                <h2 class="text-3xl font-extrabold text-slate-900">
                    Mengenal KasirKantin
                </h2>
                <div class="w-16 h-1 bg-indigo-600 mx-auto rounded-full"></div>
                <p class="text-lg text-slate-600 leading-relaxed">
                    KasirKantin adalah platform manajemen kasir pintar yang dioptimalkan untuk menyederhanakan aktivitas penjualan harian di kantin. Menggabungkan kemudahan antarmuka kasir cepat, sistem multi-role (Owner, Admin, Kasir), serta integrasi manajemen stok dan riwayat pelanggan demi mendorong kemajuan bisnis kuliner Anda.
                </p>
            </div>
        </div>
    </section>

    <!-- Key Features Section -->
    <section id="fitur" class="py-20 bg-slate-50 border-y border-slate-100">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center space-y-4 mb-16">
                <h2 class="text-3xl font-extrabold text-slate-900">Fitur Utama Aplikasi</h2>
                <p class="text-slate-500 max-w-2xl mx-auto">Kami menyediakan segala yang Anda butuhkan untuk mempermudah operasional kantin dari satu dasbor terpadu.</p>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
                <!-- Feature 1 -->
                <div class="bg-white p-8 rounded-2xl border border-slate-200/60 shadow-sm hover:shadow-md transition-shadow space-y-6">
                    <div class="w-12 h-12 rounded-xl bg-indigo-50 text-indigo-600 flex items-center justify-center font-bold text-2xl">
                        🛒
                    </div>
                    <h3 class="text-xl font-bold text-slate-800">Sistem POS Kasir Cepat</h3>
                    <p class="text-slate-600 text-sm leading-relaxed">
                        Proses checkout belanja multi-produk dalam hitungan detik. Keranjang belanja interaktif yang menghitung otomatis subtotal, tagihan, dan uang kembalian pelanggan.
                    </p>
                </div>

                <!-- Feature 2 -->
                <div class="bg-white p-8 rounded-2xl border border-slate-200/60 shadow-sm hover:shadow-md transition-shadow space-y-6">
                    <div class="w-12 h-12 rounded-xl bg-purple-50 text-purple-600 flex items-center justify-center font-bold text-2xl">
                        👥
                    </div>
                    <h3 class="text-xl font-bold text-slate-800">Manajemen Multi-Role</h3>
                    <p class="text-slate-600 text-sm leading-relaxed">
                        Pengaturan hak akses terbagi menjadi 3 role terstruktur: Owner (Pemilik Utama), Admin (Pengelola Operasional), dan Kasir (Operator Penjualan). Keamanan data terjamin.
                    </p>
                </div>

                <!-- Feature 3 -->
                <div class="bg-white p-8 rounded-2xl border border-slate-200/60 shadow-sm hover:shadow-md transition-shadow space-y-6">
                    <div class="w-12 h-12 rounded-xl bg-emerald-50 text-emerald-600 flex items-center justify-center font-bold text-2xl">
                        📈
                    </div>
                    <h3 class="text-xl font-bold text-slate-800">Laporan Penjualan Realtime</h3>
                    <p class="text-slate-600 text-sm leading-relaxed">
                        Owner dapat memantau ringkasan omset harian, volume pesanan, stok produk yang menipis, serta aktivitas transaksi kasir secara langsung dari mana saja.
                    </p>
                </div>
            </div>
        </div>
    </section>

    <!-- Advantages Section -->
    <section class="py-20 bg-white">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-16 items-center">
                <!-- Text -->
                <div class="space-y-8">
                    <h2 class="text-3xl font-extrabold text-slate-900">Keunggulan Menggunakan KasirKantin</h2>
                    <p class="text-slate-600 leading-relaxed">
                        Kami merancang sistem ini dengan fokus utama pada efisiensi transaksi di kantin yang memiliki jam sibuk (seperti jam istirahat). Keuntungan yang akan Anda peroleh:
                    </p>
                    <ul class="space-y-4">
                        <li class="flex items-start gap-3">
                            <span class="flex-shrink-0 w-6 h-6 rounded-full bg-indigo-50 text-indigo-600 flex items-center justify-center font-bold text-sm">✓</span>
                            <div>
                                <h4 class="font-bold text-slate-800">Pendaftaran Owner Instan</h4>
                                <p class="text-slate-500 text-sm">Cukup daftar satu akun Owner untuk mulai membuat unit toko/kantin Anda sendiri.</p>
                            </div>
                        </li>
                        <li class="flex items-start gap-3">
                            <span class="flex-shrink-0 w-6 h-6 rounded-full bg-indigo-50 text-indigo-600 flex items-center justify-center font-bold text-sm">✓</span>
                            <div>
                                <h4 class="font-bold text-slate-800">Delegasi Staff Aman</h4>
                                <p class="text-slate-500 text-sm">Owner dapat dengan mudah menambahkan Admin &amp; Kasir dari dashboard pribadi tanpa perlu akses registrasi publik.</p>
                            </div>
                        </li>
                        <li class="flex items-start gap-3">
                            <span class="flex-shrink-0 w-6 h-6 rounded-full bg-indigo-50 text-indigo-600 flex items-center justify-center font-bold text-sm">✓</span>
                            <div>
                                <h4 class="font-bold text-slate-800">Integrasi Multi-Store</h4>
                                <p class="text-slate-500 text-sm">Satu Owner dapat mengontrol lebih dari satu toko/kantin cabang dan menugaskan staff ke masing-masing toko secara dinamis.</p>
                            </div>
                        </li>
                    </ul>
                </div>

                <!-- Visual CTA -->
                <div class="bg-indigo-600 rounded-3xl p-8 sm:p-12 text-white text-center space-y-8 shadow-xl shadow-indigo-600/30">
                    <h3 class="text-2xl sm:text-3xl font-extrabold">Mulai Digitalisasi Kantin Anda Sekarang</h3>
                    <p class="text-indigo-100 text-sm sm:text-base leading-relaxed">
                        Bergabunglah dengan ratusan pemilik usaha kantin lainnya yang telah memodernisasi layanan transaksi mereka.
                    </p>
                    <div class="pt-4 flex flex-col sm:flex-row justify-center gap-4">
                        @auth
                            <a href="{{ route('dashboard') }}" class="w-full sm:w-auto inline-flex items-center justify-center px-8 py-3.5 rounded-xl font-bold bg-white text-indigo-700 hover:bg-slate-50 transition-colors">
                                Buka Dasbor Kasir
                            </a>
                        @else
                            <a href="{{ route('register') }}" class="w-full sm:w-auto inline-flex items-center justify-center px-8 py-3.5 rounded-xl font-bold bg-white text-indigo-700 hover:bg-slate-50 transition-colors">
                                Daftar Sebagai Owner
                            </a>
                            <a href="{{ route('login') }}" class="w-full sm:w-auto inline-flex items-center justify-center px-8 py-3.5 rounded-xl font-bold text-white border border-indigo-500 hover:bg-indigo-700 transition-colors">
                                Login Sekarang
                            </a>
                        @endauth
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Footer -->
    <footer class="bg-slate-900 text-slate-400 py-12 border-t border-slate-800">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 text-center space-y-4">
            <p class="text-sm">&copy; {{ date('Y') }} KasirKantin. Seluruh Hak Cipta Dilindungi Undang-Undang.</p>
            <p class="text-xs text-slate-600">Dibuat dengan dedikasi tinggi untuk operasional kantin modern dan efisien.</p>
        </div>
    </footer>

    <script>
        // Toggle mobile menu
        const mobileMenuBtn = document.getElementById('mobile-menu-btn');
        const mobileMenu = document.getElementById('mobile-menu');

        mobileMenuBtn.addEventListener('click', () => {
            mobileMenu.classList.toggle('hidden');
        });
    </script>
</body>
</html>
