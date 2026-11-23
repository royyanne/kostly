<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="h-full bg-slate-50">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ $title ?? 'Portal Penghuni Kost' }} - Kostly</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600,700,800,900&display=swap" rel="stylesheet" />

    <!-- Scripts & Styles -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="h-full font-sans antialiased text-slate-800 flex flex-col bg-slate-50">

    {{-- Top Header Nav --}}
    <header class="sticky top-0 z-40 bg-white border-b border-slate-200/90 shadow-xs">
        <div class="mx-auto max-w-6xl px-4 sm:px-6 lg:px-8">
            <div class="flex h-16 items-center justify-between">
                {{-- Brand --}}
                <div class="flex items-center gap-6">
                    <a href="{{ route('tenant.dashboard') }}" class="flex items-center gap-2.5 group">
                        <div class="flex h-9 w-9 items-center justify-center rounded-xl bg-[#1E3A8A] text-white shadow-xs">
                            <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                            </svg>
                        </div>
                        <div class="flex flex-col">
                            <span class="text-lg font-bold tracking-tight text-[#1E3A8A]">Kostly<span class="text-emerald-500">.</span></span>
                            <span class="-mt-1 text-[9px] font-bold tracking-wider text-emerald-600 uppercase">Portal Anak Kost</span>
                        </div>
                    </a>

                    {{-- Desktop Menu Links --}}
                    <nav class="hidden sm:flex items-center gap-1.5 text-xs font-bold">
                        <a href="{{ route('tenant.dashboard') }}" class="px-3.5 py-2 rounded-xl transition-all {{ request()->routeIs('tenant.dashboard') ? 'bg-indigo-50 text-[#1E3A8A]' : 'text-slate-600 hover:text-slate-900 hover:bg-slate-100' }}">
                            Kamar Saya
                        </a>
                        <a href="{{ route('tenant.invoices.index') }}" class="px-3.5 py-2 rounded-xl transition-all {{ request()->routeIs('tenant.invoices.*') ? 'bg-indigo-50 text-[#1E3A8A]' : 'text-slate-600 hover:text-slate-900 hover:bg-slate-100' }}">
                            Riwayat Tagihan
                        </a>
                        <a href="{{ route('tenant.complaints.index') }}" class="px-3.5 py-2 rounded-xl transition-all {{ request()->routeIs('tenant.complaints.*') ? 'bg-indigo-50 text-[#1E3A8A]' : 'text-slate-600 hover:text-slate-900 hover:bg-slate-100' }}">
                            Komplain Kerusakan
                        </a>
                    </nav>
                </div>

                {{-- User Profile & Logout --}}
                <div class="flex items-center gap-3">
                    {{-- Bell Notifikasi In-App --}}
                    <x-notification-bell />

                    <div class="hidden md:flex flex-col text-right">
                        <span class="text-xs font-bold text-slate-800">{{ auth()->user()->name }}</span>
                        <span class="text-[10px] text-slate-500">{{ auth()->user()->email }}</span>
                    </div>

                    <form action="{{ route('logout') }}" method="POST">
                        @csrf
                        <button type="submit" title="Keluar Akun" class="inline-flex items-center gap-1.5 rounded-xl border border-slate-200 bg-white hover:bg-slate-50 px-3 py-1.5 text-xs font-bold text-slate-700 transition-colors shadow-xs">
                            <svg class="h-4 w-4 text-slate-500" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/></svg>
                            <span class="hidden sm:inline">Keluar</span>
                        </button>
                    </form>
                </div>
            </div>
        </div>

        {{-- Mobile Subnav Tabs --}}
        <div class="sm:hidden border-t border-slate-100 bg-slate-50/80 px-4 py-2 flex items-center justify-around text-xs font-bold">
            <a href="{{ route('tenant.dashboard') }}" class="py-1 px-2.5 rounded-lg {{ request()->routeIs('tenant.dashboard') ? 'bg-indigo-100 text-[#1E3A8A]' : 'text-slate-600' }}">
                Kamar
            </a>
            <a href="{{ route('tenant.invoices.index') }}" class="py-1 px-2.5 rounded-lg {{ request()->routeIs('tenant.invoices.*') ? 'bg-indigo-100 text-[#1E3A8A]' : 'text-slate-600' }}">
                Tagihan
            </a>
            <a href="{{ route('tenant.complaints.index') }}" class="py-1 px-2.5 rounded-lg {{ request()->routeIs('tenant.complaints.*') ? 'bg-indigo-100 text-[#1E3A8A]' : 'text-slate-600' }}">
                Komplain
            </a>
        </div>
    </header>

    {{-- Main Container --}}
    <main class="flex-1 mx-auto w-full max-w-6xl px-4 py-6 sm:px-6 lg:px-8">
        {{-- Flash Alerts --}}
        @if(session('success'))
            <div class="mb-5 rounded-2xl border border-emerald-200 bg-emerald-50 p-4 shadow-xs flex items-center gap-3">
                <svg class="h-5 w-5 text-emerald-600 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                <p class="text-xs sm:text-sm font-semibold text-emerald-800">{{ session('success') }}</p>
            </div>
        @endif

        @if(session('error'))
            <div class="mb-5 rounded-2xl border border-rose-200 bg-rose-50 p-4 shadow-xs flex items-center gap-3">
                <svg class="h-5 w-5 text-rose-600 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                <p class="text-xs sm:text-sm font-semibold text-rose-800">{{ session('error') }}</p>
            </div>
        @endif

        @if(session('info'))
            <div class="mb-5 rounded-2xl border border-blue-200 bg-blue-50 p-4 shadow-xs flex items-center gap-3">
                <svg class="h-5 w-5 text-blue-600 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                <p class="text-xs sm:text-sm font-semibold text-blue-800">{{ session('info') }}</p>
            </div>
        @endif

        {{ $slot }}
    </main>

    {{-- Footer --}}
    <footer class="mt-auto border-t border-slate-200 bg-white py-4 text-center text-xs text-slate-400">
        Kostly &copy; {{ now()->year }} &middot; Platform Manajemen Kost & Tagihan Digital
    </footer>
</body>
</html>
