<!DOCTYPE html>
<html lang="id" class="h-full bg-slate-50">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ $title ?? 'Dashboard' }} - Kostly Juragan</title>

    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=instrument-sans:400,500,600,700" rel="stylesheet" />

    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="h-full font-sans antialiased text-slate-800 bg-slate-50">
    <div class="min-h-full flex flex-col lg:flex-row">
        {{-- Mobile Overlay Backdrop --}}
        <div id="mobile-sidebar-backdrop" onclick="toggleSidebar()" class="fixed inset-0 z-40 bg-slate-900/50 backdrop-blur-xs hidden lg:hidden"></div>

        {{-- Sidebar Navigasi --}}
        <aside id="main-sidebar" class="fixed inset-y-0 left-0 z-50 w-72 bg-white border-r border-slate-200/90 flex flex-col justify-between -translate-x-full lg:translate-x-0 transition-transform duration-200 ease-in-out shadow-xl lg:shadow-none">
            <div>
                {{-- Logo & App Brand --}}
                <div class="h-16 flex items-center justify-between px-6 border-b border-slate-100">
                    <a href="{{ route('owner.dashboard') }}" class="flex items-center gap-2.5">
                        <div class="flex h-10 w-10 items-center justify-center rounded-xl bg-[#1E3A8A] text-white shadow-md shadow-indigo-950/20">
                            <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                            </svg>
                        </div>
                        <div class="flex flex-col">
                            <span class="text-xl font-black tracking-tight text-[#1E3A8A]">Kostly<span class="text-emerald-500">.</span></span>
                            <span class="-mt-1 text-[10px] font-bold tracking-wider text-slate-400 uppercase">Panel Pemilik</span>
                        </div>
                    </a>

                    <button type="button" onclick="toggleSidebar()" class="lg:hidden p-1.5 rounded-lg text-slate-400 hover:text-slate-700 hover:bg-slate-100">
                        <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                    </button>
                </div>

                {{-- User Plan Badge --}}
                <div class="px-6 py-3 border-b border-slate-100 bg-slate-50/50 flex items-center justify-between">
                    <div class="flex items-center gap-2 text-xs">
                        <span class="h-2 w-2 rounded-full bg-emerald-500 animate-pulse"></span>
                        <span class="font-medium text-slate-600">Paket:</span>
                        <span class="font-bold text-[#1E3A8A]">{{ auth()->user()->subscription_plan->label() }}</span>
                    </div>
                    <span class="text-[10px] uppercase font-bold px-2 py-0.5 rounded-md bg-emerald-100 text-emerald-800">Aktif</span>
                </div>

                {{-- Navigation Links --}}
                <nav class="p-4 space-y-1 text-sm font-medium">
                    {{-- Dashboard --}}
                    <a href="{{ route('owner.dashboard') }}"
                        class="flex items-center gap-3 px-3 py-2.5 rounded-xl transition-all {{ request()->routeIs('owner.dashboard') ? 'bg-indigo-50 text-[#1E3A8A] font-bold shadow-xs' : 'text-slate-600 hover:bg-slate-50 hover:text-slate-900' }}">
                        <svg class="h-5 w-5 shrink-0 {{ request()->routeIs('owner.dashboard') ? 'text-[#1E3A8A]' : 'text-slate-400' }}" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z" />
                        </svg>
                        <span>Dashboard</span>
                    </a>

                    {{-- Properti Kost --}}
                    <a href="{{ route('owner.properties.index') }}"
                        class="flex items-center gap-3 px-3 py-2.5 rounded-xl transition-all {{ request()->routeIs('owner.properties.*') ? 'bg-indigo-50 text-[#1E3A8A] font-bold shadow-xs' : 'text-slate-600 hover:bg-slate-50 hover:text-slate-900' }}">
                        <svg class="h-5 w-5 shrink-0 {{ request()->routeIs('owner.properties.*') ? 'text-[#1E3A8A]' : 'text-slate-400' }}" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                        </svg>
                        <span>Properti Kost</span>
                    </a>

                    {{-- Kamar & Visual Grid --}}
                    <a href="{{ route('owner.rooms.index') }}"
                        class="flex items-center gap-3 px-3 py-2.5 rounded-xl transition-all {{ request()->routeIs('owner.rooms.*') ? 'bg-indigo-50 text-[#1E3A8A] font-bold shadow-xs' : 'text-slate-600 hover:bg-slate-50 hover:text-slate-900' }}">
                        <svg class="h-5 w-5 shrink-0 {{ request()->routeIs('owner.rooms.*') ? 'text-[#1E3A8A]' : 'text-slate-400' }}" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6" />
                        </svg>
                        <div class="flex items-center justify-between w-full">
                            <span>Kamar Kost</span>
                            <span class="text-[10px] px-1.5 py-0.5 rounded-md bg-indigo-100 text-[#1E3A8A] font-bold">Grid</span>
                        </div>
                    </a>

                    {{-- Penghuni --}}
                    <a href="{{ route('owner.tenants.index') }}"
                        class="flex items-center gap-3 px-3 py-2.5 rounded-xl transition-all {{ request()->routeIs('owner.tenants.*') ? 'bg-indigo-50 text-[#1E3A8A] font-bold shadow-xs' : 'text-slate-600 hover:bg-slate-50 hover:text-slate-900' }}">
                        <svg class="h-5 w-5 shrink-0 {{ request()->routeIs('owner.tenants.*') ? 'text-[#1E3A8A]' : 'text-slate-400' }}" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" />
                        </svg>
                        <span>Penghuni</span>
                    </a>

                    {{-- Tagihan & Invoices --}}
                    <a href="{{ route('owner.invoices.index') }}"
                        class="flex items-center gap-3 px-3 py-2.5 rounded-xl transition-all {{ request()->routeIs('owner.invoices.*') ? 'bg-indigo-50 text-[#1E3A8A] font-bold shadow-xs' : 'text-slate-600 hover:bg-slate-50 hover:text-slate-900' }}">
                        <svg class="h-5 w-5 shrink-0 {{ request()->routeIs('owner.invoices.*') ? 'text-[#1E3A8A]' : 'text-slate-400' }}" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 14l6-6m-5.5.5h.01m4.99 5h.01M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16l3.5-2 3.5 2 3.5-2 3.5 2zM10 8.5a.5.5 0 11-1 0 .5.5 0 011 0zm5 5a.5.5 0 11-1 0 .5.5 0 011 0z" />
                        </svg>
                        <span>Tagihan & Invoice</span>
                    </a>

                    {{-- Pengeluaran --}}
                    <a href="{{ route('owner.expenses.index') }}"
                        class="flex items-center gap-3 px-3 py-2.5 rounded-xl transition-all {{ request()->routeIs('owner.expenses.*') ? 'bg-indigo-50 text-[#1E3A8A] font-bold shadow-xs' : 'text-slate-600 hover:bg-slate-50 hover:text-slate-900' }}">
                        <svg class="h-5 w-5 shrink-0 {{ request()->routeIs('owner.expenses.*') ? 'text-[#1E3A8A]' : 'text-slate-400' }}" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z" />
                        </svg>
                        <span>Pengeluaran</span>
                    </a>

                    {{-- Komplain Penghuni --}}
                    <a href="{{ route('owner.complaints.index') }}"
                        class="flex items-center gap-3 px-3 py-2.5 rounded-xl transition-all {{ request()->routeIs('owner.complaints.*') ? 'bg-indigo-50 text-[#1E3A8A] font-bold shadow-xs' : 'text-slate-600 hover:bg-slate-50 hover:text-slate-900' }}">
                        <svg class="h-5 w-5 shrink-0 {{ request()->routeIs('owner.complaints.*') ? 'text-[#1E3A8A]' : 'text-slate-400' }}" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                        </svg>
                        <span>Komplain</span>
                    </a>
                </nav>
            </div>

            {{-- Bottom User Profile & Logout --}}
            <div class="p-4 border-t border-slate-100 bg-slate-50/70">
                <div class="flex items-center justify-between mb-3">
                    <div class="flex items-center gap-2.5 min-w-0">
                        <div class="flex h-9 w-9 shrink-0 items-center justify-center rounded-xl bg-[#1E3A8A] text-xs font-bold text-white shadow-xs">
                            {{ substr(auth()->user()->name, 0, 2) }}
                        </div>
                        <div class="truncate">
                            <p class="text-xs font-bold text-slate-900 truncate">{{ auth()->user()->name }}</p>
                            <p class="text-[11px] text-slate-500 truncate">{{ auth()->user()->email }}</p>
                        </div>
                    </div>
                </div>

                <form action="{{ route('logout') }}" method="POST">
                    @csrf
                    <button type="submit" class="w-full inline-flex items-center justify-center gap-2 rounded-xl border border-slate-200 bg-white py-2 text-xs font-semibold text-rose-600 hover:bg-rose-50 hover:border-rose-200 transition-colors">
                        <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/></svg>
                        <span>Keluar Akun</span>
                    </button>
                </form>
            </div>
        </aside>

        {{-- Main Content Area --}}
        <div class="flex-1 lg:pl-72 flex flex-col min-h-screen">
            {{-- Top Header Bar --}}
            <header class="sticky top-0 z-30 h-16 bg-white/95 backdrop-blur-md border-b border-slate-200/80 px-4 sm:px-6 lg:px-8 flex items-center justify-between">
                <div class="flex items-center gap-3">
                    <button type="button" onclick="toggleSidebar()" class="lg:hidden p-2 rounded-lg text-slate-600 hover:bg-slate-100">
                        <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/></svg>
                    </button>
                    <div>
                        <h1 class="text-base sm:text-lg font-bold text-slate-900">{{ $header ?? 'Dashboard' }}</h1>
                    </div>
                </div>

                <div class="flex items-center gap-3 sm:gap-4">
                    {{-- Quick Public Portal shortcut --}}
                    <a href="{{ route('landing') }}" target="_blank" class="hidden sm:inline-flex items-center gap-1.5 text-xs font-semibold text-slate-600 hover:text-[#1E3A8A]">
                        <span>Lihat Website</span>
                        <svg class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/></svg>
                    </a>

                    {{-- Today's date --}}
                    <span class="text-xs text-slate-400 hidden md:inline">
                        {{ now()->translatedFormat('l, d F Y') }}
                    </span>
                </div>
            </header>

            {{-- Flash Alert Notifications --}}
            <div class="px-4 sm:px-6 lg:px-8 pt-4">
                @if (session('success'))
                    <div class="mb-4 rounded-xl border border-emerald-200 bg-emerald-50 p-4 text-sm text-emerald-800 shadow-xs flex items-center justify-between">
                        <div class="flex items-center gap-2.5">
                            <svg class="h-5 w-5 text-emerald-600 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                            <span>{{ session('success') }}</span>
                        </div>
                        <button type="button" onclick="this.parentElement.remove()" class="text-emerald-600 hover:text-emerald-900 font-bold ml-3">✕</button>
                    </div>
                @endif

                @if (session('error'))
                    <div class="mb-4 rounded-xl border border-rose-200 bg-rose-50 p-4 text-sm text-rose-800 shadow-xs flex items-center justify-between">
                        <div class="flex items-center gap-2.5">
                            <svg class="h-5 w-5 text-rose-600 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                            <span>{{ session('error') }}</span>
                        </div>
                        <button type="button" onclick="this.parentElement.remove()" class="text-rose-600 hover:text-rose-900 font-bold ml-3">✕</button>
                    </div>
                @endif
            </div>

            {{-- Main Content Slot --}}
            <main class="flex-1 px-4 sm:px-6 lg:px-8 pb-12">
                {{ $slot }}
            </main>
        </div>
    </div>

    <script>
        function toggleSidebar() {
            const sidebar = document.getElementById('main-sidebar');
            const backdrop = document.getElementById('mobile-sidebar-backdrop');
            sidebar.classList.toggle('-translate-x-full');
            backdrop.classList.toggle('hidden');
        }
    </script>
</body>
</html>

