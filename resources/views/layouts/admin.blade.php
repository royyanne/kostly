<!DOCTYPE html>
<html lang="id" class="h-full bg-slate-50">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ $title ?? 'Admin Panel' }} - Kostly Super Admin</title>

    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=instrument-sans:400,500,600,700" rel="stylesheet" />

    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="h-full font-sans antialiased text-slate-800 bg-slate-50">
    <div class="min-h-full flex flex-col lg:flex-row">
        {{-- Mobile Overlay Backdrop --}}
        <div id="mobile-sidebar-backdrop" onclick="toggleSidebar()" class="fixed inset-0 z-40 bg-slate-900/50 backdrop-blur-xs hidden lg:hidden"></div>

        {{-- Sidebar Navigasi Admin --}}
        <aside id="main-sidebar" class="fixed inset-y-0 left-0 z-50 w-72 bg-slate-900 text-white flex flex-col justify-between -translate-x-full lg:translate-x-0 transition-transform duration-200 ease-in-out shadow-2xl lg:shadow-none">
            <div>
                {{-- Logo & App Brand --}}
                <div class="h-16 flex items-center justify-between px-6 border-b border-slate-800">
                    <a href="{{ route('admin.dashboard') }}" class="flex items-center gap-2.5">
                        <div class="flex h-10 w-10 items-center justify-center rounded-xl bg-indigo-600 text-white shadow-md shadow-indigo-500/20">
                            <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z" />
                            </svg>
                        </div>
                        <div class="flex flex-col">
                            <span class="text-xl font-black tracking-tight text-white">Kostly<span class="text-emerald-400">.</span></span>
                            <span class="-mt-1 text-[10px] font-bold tracking-wider text-indigo-400 uppercase">Super Admin Portal</span>
                        </div>
                    </a>

                    <button type="button" onclick="toggleSidebar()" class="lg:hidden p-1.5 rounded-lg text-slate-400 hover:text-white hover:bg-slate-800">
                        <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                    </button>
                </div>

                {{-- Admin Status Badge --}}
                <div class="px-6 py-3 border-b border-slate-800 bg-slate-950/60 flex items-center justify-between">
                    <div class="flex items-center gap-2 text-xs">
                        <span class="h-2 w-2 rounded-full bg-emerald-400 animate-pulse"></span>
                        <span class="font-medium text-slate-400">Peran:</span>
                        <span class="font-bold text-indigo-300">Super Admin</span>
                    </div>
                    <span class="text-[10px] uppercase font-extrabold px-2 py-0.5 rounded-md bg-indigo-500/20 text-indigo-300 border border-indigo-500/30">Root</span>
                </div>

                {{-- Navigation Links --}}
                <nav class="p-4 space-y-1.5 text-sm font-medium">
                    {{-- Dashboard Ringkasan SaaS --}}
                    <a href="{{ route('admin.dashboard') }}"
                        class="flex items-center gap-3 px-3.5 py-2.5 rounded-xl transition-all {{ request()->routeIs('admin.dashboard') ? 'bg-indigo-600 text-white font-bold shadow-lg shadow-indigo-600/30' : 'text-slate-300 hover:bg-slate-800 hover:text-white' }}">
                        <svg class="h-5 w-5 shrink-0 {{ request()->routeIs('admin.dashboard') ? 'text-white' : 'text-slate-400' }}" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z" />
                        </svg>
                        <span>Dashboard SaaS</span>
                    </a>

                    {{-- Manajemen Seluruh Pemilik Kost --}}
                    <a href="{{ route('admin.users.index') }}"
                        class="flex items-center gap-3 px-3.5 py-2.5 rounded-xl transition-all {{ request()->routeIs('admin.users.*') ? 'bg-indigo-600 text-white font-bold shadow-lg shadow-indigo-600/30' : 'text-slate-300 hover:bg-slate-800 hover:text-white' }}">
                        <svg class="h-5 w-5 shrink-0 {{ request()->routeIs('admin.users.*') ? 'text-white' : 'text-slate-400' }}" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" />
                        </svg>
                        <span>Manajemen Owner</span>
                    </a>

                    {{-- Verifikasi Langganan Pro --}}
                    <a href="{{ route('admin.subscriptions.index') }}"
                        class="flex items-center justify-between px-3.5 py-2.5 rounded-xl transition-all {{ request()->routeIs('admin.subscriptions.*') ? 'bg-indigo-600 text-white font-bold shadow-lg shadow-indigo-600/30' : 'text-slate-300 hover:bg-slate-800 hover:text-white' }}">
                        <div class="flex items-center gap-3">
                            <svg class="h-5 w-5 shrink-0 {{ request()->routeIs('admin.subscriptions.*') ? 'text-white' : 'text-slate-400' }}" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z" />
                            </svg>
                            <span>Langganan Pro</span>
                        </div>
                        @php
                            $pendingSubCount = \App\Models\Subscription::where('status', 'pending')->count();
                        @endphp
                        @if ($pendingSubCount > 0)
                            <span class="px-2 py-0.5 rounded-full bg-amber-500 text-white font-bold text-[10px]">
                                {{ $pendingSubCount }}
                            </span>
                        @endif
                    </a>

                    {{-- Switch / Shortcut to Landing --}}
                    <div class="pt-4 border-t border-slate-800/80 mt-4">
                        <span class="px-3.5 text-[11px] font-semibold tracking-wider text-slate-500 uppercase">Akses Eksternal</span>
                        <a href="{{ route('landing') }}" target="_blank"
                            class="mt-2 flex items-center gap-3 px-3.5 py-2 rounded-xl text-slate-400 hover:bg-slate-800 hover:text-white transition-all text-xs">
                            <svg class="h-4 w-4 shrink-0 text-slate-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14" />
                            </svg>
                            <span>Landing Page Kostly</span>
                        </a>
                    </div>
                </nav>
            </div>

            {{-- Bottom User Profile & Logout --}}
            <div class="p-4 border-t border-slate-800 bg-slate-950/70">
                <div class="flex items-center justify-between mb-3">
                    <div class="flex items-center gap-2.5 min-w-0">
                        <div class="flex h-9 w-9 shrink-0 items-center justify-center rounded-xl bg-indigo-600 text-xs font-bold text-white shadow-xs">
                            {{ substr(auth()->user()->name, 0, 2) }}
                        </div>
                        <div class="truncate">
                            <p class="text-xs font-bold text-white truncate">{{ auth()->user()->name }}</p>
                            <p class="text-[11px] text-slate-400 truncate">{{ auth()->user()->email }}</p>
                        </div>
                    </div>
                </div>

                <form action="{{ route('logout') }}" method="POST">
                    @csrf
                    <button type="submit" class="w-full inline-flex items-center justify-center gap-2 rounded-xl border border-slate-700 bg-slate-800 py-2 text-xs font-semibold text-rose-400 hover:bg-rose-950/40 hover:border-rose-900 transition-colors">
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
                        <h1 class="text-base sm:text-lg font-bold text-slate-900">{{ $header ?? 'Panel Super Admin' }}</h1>
                    </div>
                </div>

                <div class="flex items-center gap-3 sm:gap-4">
                    <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-md text-xs font-semibold bg-indigo-50 text-indigo-700 border border-indigo-200/60">
                        <span class="h-1.5 w-1.5 rounded-full bg-indigo-600"></span>
                        Super Admin Mode
                    </span>

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
