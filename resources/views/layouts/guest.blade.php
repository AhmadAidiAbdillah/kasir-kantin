<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'KasirKantin') }}</title>

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.googleapis.com">
        <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
        <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">

        <!-- Scripts -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])

        <style>
            body {
                font-family: 'Outfit', sans-serif;
            }
            .bg-glow-orange {
                background: radial-gradient(circle, rgba(249, 115, 22, 0.12) 0%, rgba(249, 115, 22, 0) 70%);
            }
            .bg-glow-amber {
                background: radial-gradient(circle, rgba(251, 191, 36, 0.08) 0%, rgba(251, 191, 36, 0) 70%);
            }
            @keyframes float {
                0%, 100% { transform: translateY(0px) scale(1); }
                50% { transform: translateY(-20px) scale(1.05); }
            }
            .animate-float-slow {
                animation: float 8s ease-in-out infinite;
            }
            .animate-float-slower {
                animation: float 12s ease-in-out infinite;
                animation-delay: 2s;
            }
        </style>
    </head>
    <body class="text-slate-800 antialiased bg-slate-50 min-h-screen">
        <div class="w-full min-h-screen flex flex-col sm:justify-center items-center p-6 relative overflow-hidden">
            <!-- Background Ambient Blobs (Clipped inside the overflow-hidden wrapper) -->
            <div class="absolute top-[-10%] left-[-10%] w-[60vw] h-[60vw] rounded-full bg-glow-orange blur-[60px] pointer-events-none animate-float-slow"></div>
            <div class="absolute bottom-[-15%] right-[-10%] w-[60vw] h-[60vw] rounded-full bg-glow-amber blur-[60px] pointer-events-none animate-float-slower"></div>

            <!-- Brand Logo / Name -->
            <div class="mb-8 text-center relative z-10">
                <a href="/" class="inline-flex flex-col items-center gap-2 group">
                    <div class="w-16 h-16 bg-gradient-to-tr from-indigo-600 to-amber-500 rounded-2xl flex items-center justify-center shadow-lg shadow-indigo-500/20 group-hover:scale-105 transition-all duration-300">
                        <!-- POS/Store Cash Register SVG -->
                        <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" stroke-width="2.2" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg" aria-hidden="true">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M9 7h6m0 10v-3m-3 3h.01M9 17h.01M9 14h.01M12 14h.01M15 11h.01M12 11h.01M9 11h.01M7 21h10a2 2 0 002-2V5a2 2 0 00-2-2H7a2 2 0 00-2 2v14a2 2 0 002 2z"></path>
                        </svg>
                    </div>
                    <span class="text-2xl font-extrabold tracking-tight text-slate-800 mt-2">
                        Kasir<span class="text-indigo-600">Kantin</span>
                    </span>
                    <span class="text-xs text-slate-400 font-semibold tracking-wider uppercase">Aplikasi Kasir Kantin Sekolah & Kampus</span>
                </a>
            </div>

            <!-- Glassmorphic Card Container -->
            <div class="w-full sm:max-w-md bg-white/75 backdrop-blur-xl border border-white/60 shadow-[0_20px_50px_rgba(0,0,0,0.05)] rounded-[2.5rem] p-8 sm:p-10 relative overflow-hidden z-10">
                <div class="absolute top-0 left-0 w-full h-[4px] bg-gradient-to-r from-indigo-500 via-amber-500 to-orange-400"></div>
                {{ $slot }}
            </div>
        </div>
    </body>
</html>

