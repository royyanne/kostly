<x-app>
    {{-- Navigation Bar --}}
    <header class="sticky top-0 z-50 border-b border-slate-200/80 bg-white/95 backdrop-blur-md">
        <div class="mx-auto flex max-w-7xl items-center justify-between px-4 py-3.5 sm:px-6 lg:px-8">
            {{-- Brand Logo --}}
            <a href="{{ route('landing') }}" class="flex items-center gap-2.5 group">
                <div class="flex h-10 w-10 items-center justify-center rounded-xl bg-[#1E3A8A] text-white shadow-md shadow-indigo-900/20 transition-transform group-hover:scale-105">
                    <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                    </svg>
                </div>
                <div class="flex flex-col">
                    <span class="text-xl font-bold tracking-tight text-[#1E3A8A]">Kostly<span class="text-emerald-500">.</span></span>
                    <span class="-mt-1 text-[10px] font-semibold tracking-wider text-slate-500 uppercase">Manajemen Kost</span>
                </div>
            </a>

            {{-- Desktop Navigation Links --}}
            <nav class="hidden md:flex items-center gap-8 text-sm font-medium text-slate-600">
                <a href="#daftar-kost" class="font-bold text-[#1E3A8A] transition-colors hover:text-indigo-800">Cari Kost 🔍</a>
                <a href="#fitur" class="transition-colors hover:text-[#1E3A8A]">Fitur Unggulan</a>
                <a href="#cara-kerja" class="transition-colors hover:text-[#1E3A8A]">Cara Kerja</a>
                <a href="#harga" class="transition-colors hover:text-[#1E3A8A]">Paket Harga</a>
                <a href="#testimoni" class="transition-colors hover:text-[#1E3A8A]">Testimoni</a>
            </nav>

            {{-- Auth Action Buttons --}}
            <div class="flex items-center gap-3">
                @auth
                    <a href="{{ route('owner.dashboard') }}" class="inline-flex items-center gap-2 rounded-xl bg-[#1E3A8A] px-4 py-2 text-sm font-semibold text-white shadow-sm hover:bg-[#172554] transition-all">
                        <span>Buka Dashboard</span>
                        <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"/></svg>
                    </a>
                @else
                    <a href="{{ route('login') }}" class="hidden sm:inline-flex rounded-xl px-4 py-2 text-sm font-semibold text-slate-700 hover:text-[#1E3A8A] hover:bg-slate-100 transition-colors">
                        Masuk
                    </a>
                    <a href="{{ route('register') }}" class="inline-flex items-center justify-center rounded-xl bg-[#1E3A8A] px-4 py-2 text-sm font-semibold text-white shadow-md shadow-indigo-900/15 hover:bg-[#172554] transition-all">
                        Daftar Gratis
                    </a>
                @endauth

                {{-- Mobile menu button --}}
                <button type="button" onclick="document.getElementById('mobile-menu').classList.toggle('hidden')" class="md:hidden inline-flex items-center justify-center p-2 rounded-lg text-slate-600 hover:bg-slate-100">
                    <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/></svg>
                </button>
            </div>
        </div>

        {{-- Mobile dropdown menu --}}
        <div id="mobile-menu" class="hidden md:hidden border-t border-slate-200 bg-white px-4 pt-2 pb-4 space-y-1">
            <a href="#fitur" class="block rounded-lg px-3 py-2 text-base font-medium text-slate-700 hover:bg-slate-100">Fitur Unggulan</a>
            <a href="#cara-kerja" class="block rounded-lg px-3 py-2 text-base font-medium text-slate-700 hover:bg-slate-100">Cara Kerja</a>
            <a href="#harga" class="block rounded-lg px-3 py-2 text-base font-medium text-slate-700 hover:bg-slate-100">Paket Harga</a>
            <a href="#testimoni" class="block rounded-lg px-3 py-2 text-base font-medium text-slate-700 hover:bg-slate-100">Testimoni</a>
            <div class="pt-2 border-t border-slate-100 flex flex-col gap-2">
                @auth
                    <a href="{{ route('owner.dashboard') }}" class="w-full text-center rounded-lg bg-[#1E3A8A] px-4 py-2 text-sm font-semibold text-white">Dashboard Pemilik</a>
                @else
                    <a href="{{ route('login') }}" class="w-full text-center rounded-lg border border-slate-300 px-4 py-2 text-sm font-semibold text-slate-700">Masuk Akun</a>
                @endauth
            </div>
        </div>
    </header>

    {{-- Hero Section --}}
    <section class="relative overflow-hidden pt-12 pb-20 lg:pt-20 lg:pb-28">
        {{-- Background blur gradients --}}
        <div class="pointer-events-none absolute -top-40 left-1/2 -z-10 -translate-x-1/2 transform-gpu blur-3xl sm:top-[-20rem]">
            <div class="aspect-[1155/678] w-[72rem] bg-gradient-to-tr from-indigo-200 via-sky-100 to-emerald-100 opacity-60"></div>
        </div>

        <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
            <div class="text-center">
                {{-- Badge Announcement --}}
                <div class="inline-flex items-center gap-2 rounded-full border border-indigo-100 bg-indigo-50/80 px-3.5 py-1.5 text-xs font-semibold text-[#1E3A8A] shadow-xs backdrop-blur-xs">
                    <span class="flex h-2 w-2 rounded-full bg-emerald-500 animate-pulse"></span>
                    <span>Kostly 2.0 • SaaS Manajemen Kost & Penagihan Otomatis</span>
                </div>

                {{-- Headline --}}
                <h1 class="mt-6 text-4xl font-extrabold tracking-tight text-slate-900 sm:text-5xl lg:text-6xl">
                    Kelola Bisnis Kost & Tagih Sewa <br class="hidden sm:inline" />
                    <span class="bg-gradient-to-r from-[#1E3A8A] via-indigo-700 to-emerald-600 bg-clip-text text-transparent">
                        Otomatis Tanpa Ribet
                    </span>
                </h1>

                {{-- Subheadline --}}
                <p class="mx-auto mt-6 max-w-2xl text-base text-slate-600 sm:text-lg lg:text-xl">
                    Tinggalkan buku catatan manual dan rasa sungkan menagih sewa. Kostly mengotomatiskan pengingat WhatsApp, pembayaran QRIS instan, grid visual status kamar, dan portal mandiri anak kost.
                </p>

                {{-- Action CTAs --}}
                <div class="mt-8 flex flex-col sm:flex-row items-center justify-center gap-3 sm:gap-4">
                    <a href="{{ route('register') }}" class="w-full sm:w-auto inline-flex items-center justify-center gap-2 rounded-xl bg-[#1E3A8A] px-7 py-3.5 text-base font-semibold text-white shadow-lg shadow-indigo-950/20 hover:bg-[#172554] transition-all">
                        <span>Mulai Gratis 14 Hari</span>
                        <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7l5 5m0 0l-5 5m5-5H6"/></svg>
                    </a>
                    <a href="{{ route('login') }}" class="w-full sm:w-auto inline-flex items-center justify-center gap-2 rounded-xl border border-slate-300 bg-white px-7 py-3.5 text-base font-semibold text-slate-700 shadow-xs hover:bg-slate-50 transition-all">
                        <svg class="h-5 w-5 text-emerald-500" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14.752 11.168l-3.197-2.132A1 1 0 0010 9.87v4.263a1 1 0 001.555.832l3.197-2.132a1 1 0 000-1.664z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                        <span>Coba Akun Demo</span>
                    </a>
                </div>

                {{-- Guarantee list --}}
                <div class="mt-6 flex flex-wrap items-center justify-center gap-x-6 gap-y-2 text-xs font-medium text-slate-500">
                    <span class="inline-flex items-center gap-1.5">
                        <svg class="h-4 w-4 text-emerald-500" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                        Tanpa Kartu Kredit
                    </span>
                    <span class="inline-flex items-center gap-1.5">
                        <svg class="h-4 w-4 text-emerald-500" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                        Setup Cuma 3 Menit
                    </span>
                    <span class="inline-flex items-center gap-1.5">
                        <svg class="h-4 w-4 text-emerald-500" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                        Dukungan WhatsApp 24/7
                    </span>
                </div>
            </div>

            {{-- Product Interactive Mockup Preview Card --}}
            <div class="mt-12 lg:mt-16 relative mx-auto max-w-5xl rounded-2xl border border-slate-200/90 bg-white p-4 sm:p-6 shadow-2xl shadow-indigo-950/10">
                <div class="flex items-center justify-between border-b border-slate-100 pb-4 mb-5">
                    <div class="flex items-center gap-2">
                        <span class="h-3 w-3 rounded-full bg-rose-400"></span>
                        <span class="h-3 w-3 rounded-full bg-amber-400"></span>
                        <span class="h-3 w-3 rounded-full bg-emerald-400"></span>
                        <span class="ml-2 text-xs font-medium text-slate-500 hidden sm:inline">kostly.id/dashboard • Kost Griya Nyaman Pasteur</span>
                    </div>
                    <div class="inline-flex items-center gap-1.5 rounded-md bg-emerald-50 px-2.5 py-1 text-xs font-semibold text-emerald-700">
                        <span class="h-2 w-2 rounded-full bg-emerald-500"></span>
                        Otomasi Tagihan Aktif
                    </div>
                </div>

                {{-- Mini UI Mockup inside --}}
                <div class="grid grid-cols-1 lg:grid-cols-3 gap-5">
                    {{-- Summary stats --}}
                    <div class="rounded-xl border border-slate-100 bg-slate-50/70 p-4">
                        <span class="text-xs font-medium text-slate-500">Pemasukan Bulan Ini</span>
                        <div class="mt-1 flex items-baseline gap-2">
                            <span class="text-2xl font-bold text-slate-900">Rp 12.650.000</span>
                            <span class="text-xs font-semibold text-emerald-600">+12%</span>
                        </div>
                        <div class="mt-3 flex items-center justify-between text-xs text-slate-600">
                            <span>8/10 Kamar Terisi (80%)</span>
                            <span class="font-medium text-emerald-600">7 Lunas</span>
                        </div>
                        <div class="mt-1.5 h-2 w-full overflow-hidden rounded-full bg-slate-200">
                            <div class="h-full bg-emerald-500 rounded-full" style="width: 80%"></div>
                        </div>
                    </div>

                    {{-- WhatsApp Reminder Notification Preview --}}
                    <div class="rounded-xl border border-emerald-100 bg-emerald-50/40 p-4">
                        <div class="flex items-center justify-between">
                            <span class="inline-flex items-center gap-1.5 text-xs font-semibold text-emerald-800">
                                <svg class="h-4 w-4 text-emerald-600" fill="currentColor" viewBox="0 0 24 24"><path d="M12.04 2C6.58 2 2.13 6.45 2.13 11.91C2.13 13.66 2.59 15.36 3.45 16.86L2.05 22L7.3 20.62C8.75 21.41 10.38 21.83 12.04 21.83C17.5 21.83 21.95 17.38 21.95 11.92C21.95 9.27 20.92 6.78 19.05 4.91C17.18 3.03 14.69 2 12.04 2M12.05 3.67C14.25 3.67 16.31 4.53 17.87 6.09C19.42 7.65 20.28 9.72 20.28 11.92C20.28 16.46 16.58 20.15 12.04 20.15C10.56 20.15 9.11 19.76 7.85 19.02L7.55 18.84L4.42 19.66L5.26 16.61L5.06 16.29C4.24 14.99 3.81 13.47 3.81 11.91C3.81 7.37 7.5 3.67 12.05 3.67Z"/></svg>
                                WhatsApp Bot Sent
                            </span>
                            <span class="text-[10px] text-slate-500">Otomatis H-3</span>
                        </div>
                        <p class="mt-2 text-xs text-slate-700 leading-relaxed font-sans">
                            <span class="font-medium text-slate-900">Halo Dimas!</span> Tagihan sewa Kamar 103 jatuh tempo 3 hari lagi. Klik link bayar via QRIS: <span class="text-indigo-600 underline">kostly.id/pay/INV-202611</span>
                        </p>
                    </div>

                    {{-- Room Visual Status Mini Grid --}}
                    <div class="rounded-xl border border-slate-100 bg-slate-50/70 p-4">
                        <div class="flex items-center justify-between mb-2">
                            <span class="text-xs font-medium text-slate-500">Status Kamar Realtime</span>
                            <span class="text-[10px] text-slate-400">10 Total</span>
                        </div>
                        <div class="grid grid-cols-5 gap-1.5 text-center text-[10px] font-bold">
                            <div class="rounded-md bg-blue-100 text-blue-800 p-1.5" title="Kamar 101 - Terisi">101</div>
                            <div class="rounded-md bg-blue-100 text-blue-800 p-1.5" title="Kamar 102 - Terisi">102</div>
                            <div class="rounded-md bg-blue-100 text-blue-800 p-1.5" title="Kamar 103 - Terisi">103</div>
                            <div class="rounded-md bg-blue-100 text-blue-800 p-1.5" title="Kamar 104 - Terisi">104</div>
                            <div class="rounded-md bg-blue-100 text-blue-800 p-1.5" title="Kamar 105 - Terisi">105</div>
                            <div class="rounded-md bg-blue-100 text-blue-800 p-1.5" title="Kamar 201 - Terisi">201</div>
                            <div class="rounded-md bg-blue-100 text-blue-800 p-1.5" title="Kamar 202 - Terisi">202</div>
                            <div class="rounded-md bg-emerald-100 text-emerald-800 p-1.5" title="Kamar 203 - Kosong">203</div>
                            <div class="rounded-md bg-emerald-100 text-emerald-800 p-1.5" title="Kamar 204 - Kosong">204</div>
                            <div class="rounded-md bg-amber-100 text-amber-800 p-1.5" title="Kamar 205 - Maintenance">205</div>
                        </div>
                        <div class="mt-2 flex items-center justify-between text-[10px] text-slate-500 pt-1">
                            <span class="flex items-center gap-1"><span class="h-2 w-2 rounded-full bg-blue-500"></span>Terisi (7)</span>
                            <span class="flex items-center gap-1"><span class="h-2 w-2 rounded-full bg-emerald-500"></span>Kosong (2)</span>
                            <span class="flex items-center gap-1"><span class="h-2 w-2 rounded-full bg-amber-500"></span>Perbaikan (1)</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    {{-- Public Kost Listing & Search Section --}}
    <section id="daftar-kost" class="border-t border-slate-200/80 bg-slate-50/70 py-16 lg:py-24">
        <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
            <div class="text-center max-w-3xl mx-auto">
                <span class="inline-flex items-center gap-1.5 rounded-full bg-emerald-100 px-3 py-1 text-xs font-bold text-emerald-800">
                    <span class="h-2 w-2 rounded-full bg-emerald-500"></span>
                    Katalog Kamar Kosong
                </span>
                <h2 class="mt-3 text-3xl font-extrabold tracking-tight text-slate-900 sm:text-4xl">
                    Cari & Sewa Kost Idaman Anda
                </h2>
                <p class="mt-3 text-base text-slate-600">
                    Pilihan kost terverifikasi dengan kamar siap huni, fasilitas lengkap, dan pengajuan sewa online tanpa perantara.
                </p>
            </div>

            {{-- Filter & Search Bar --}}
            <div class="mt-10 rounded-3xl border border-slate-200 bg-white p-4 sm:p-6 shadow-sm">
                <form action="{{ route('landing') }}#daftar-kost" method="GET" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-3 sm:gap-4 items-end">
                    {{-- Filter Kata Kunci --}}
                    <div>
                        <label for="filter-search" class="block text-xs font-bold text-slate-700 mb-1">Cari Nama / Alamat</label>
                        <div class="relative">
                            <input type="text" id="filter-search" name="search" value="{{ $search ?? '' }}" placeholder="Contoh: Pasteur, Maranatha..." class="w-full rounded-xl border border-slate-200 bg-slate-50 px-3.5 py-2.5 text-xs sm:text-sm text-slate-800 focus:border-indigo-400 focus:bg-white focus:outline-none">
                        </div>
                    </div>

                    {{-- Filter Kota --}}
                    <div>
                        <label for="filter-city" class="block text-xs font-bold text-slate-700 mb-1">Pilih Kota</label>
                        <select id="filter-city" name="city" class="w-full rounded-xl border border-slate-200 bg-slate-50 px-3.5 py-2.5 text-xs sm:text-sm text-slate-800 focus:border-indigo-400 focus:bg-white focus:outline-none">
                            <option value="">Semua Kota</option>
                            @foreach($availableCities ?? [] as $c)
                                <option value="{{ $c }}" @selected(($selectedCity ?? '') === $c)>{{ $c }}</option>
                            @endforeach
                        </select>
                    </div>

                    {{-- Filter Harga Maksimal --}}
                    <div>
                        <label for="filter-price" class="block text-xs font-bold text-slate-700 mb-1">Maksimal Harga / Bln</label>
                        <select id="filter-price" name="max_price" class="w-full rounded-xl border border-slate-200 bg-slate-50 px-3.5 py-2.5 text-xs sm:text-sm text-slate-800 focus:border-indigo-400 focus:bg-white focus:outline-none">
                            <option value="">Semua Kisaran Harga</option>
                            <option value="1500000" @selected(($maxPrice ?? '') == 1500000)>s.d. Rp 1.500.000</option>
                            <option value="2000000" @selected(($maxPrice ?? '') == 2000000)>s.d. Rp 2.000.000</option>
                            <option value="2500000" @selected(($maxPrice ?? '') == 2500000)>s.d. Rp 2.500.000</option>
                            <option value="3500000" @selected(($maxPrice ?? '') == 3500000)>s.d. Rp 3.500.000</option>
                        </select>
                    </div>

                    {{-- Action Button --}}
                    <div class="flex items-center gap-2">
                        <button type="submit" class="flex-1 rounded-xl bg-[#1E3A8A] hover:bg-[#172554] py-2.5 px-4 text-xs sm:text-sm font-bold text-white shadow-xs transition-colors flex items-center justify-center gap-1.5">
                            <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                            <span>Filter Kost</span>
                        </button>
                        @if(($search ?? false) || ($selectedCity ?? false) || ($maxPrice ?? false))
                            <a href="{{ route('landing') }}#daftar-kost" class="rounded-xl border border-slate-200 bg-white py-2.5 px-3 text-xs font-semibold text-slate-600 hover:bg-slate-100 transition-colors">
                                Reset
                            </a>
                        @endif
                    </div>
                </form>
            </div>

            {{-- Property Cards Grid --}}
            <div class="mt-8">
                @if(isset($properties) && $properties->isNotEmpty())
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 sm:gap-8">
                        @foreach($properties as $prop)
                            @php
                                $availRooms = $prop->rooms->where('status', \App\Enums\RoomStatus::Available);
                                $minPrice = $availRooms->min('price');
                            @endphp
                            <div class="group rounded-3xl border border-slate-200/90 bg-white overflow-hidden shadow-xs hover:shadow-xl hover:border-indigo-200 transition-all flex flex-col justify-between">
                                <div>
                                    {{-- Image Thumbnail with Badges --}}
                                    <div class="relative h-48 bg-gradient-to-tr from-indigo-900 to-slate-800 p-4 flex flex-col justify-between text-white">
                                        <div class="flex items-center justify-between">
                                            <span class="rounded-full bg-white/20 backdrop-blur-md px-3 py-1 text-xs font-semibold">
                                                📍 {{ $prop->city ?: 'Kota Bandung' }}
                                            </span>
                                            @if($availRooms->isNotEmpty())
                                                <span class="rounded-full bg-emerald-500 px-2.5 py-1 text-[11px] font-extrabold text-white shadow-xs">
                                                    {{ $availRooms->count() }} Kamar Kosong
                                                </span>
                                            @else
                                                <span class="rounded-full bg-rose-500 px-2.5 py-1 text-[11px] font-extrabold text-white shadow-xs">
                                                    Penuh
                                                </span>
                                            @endif
                                        </div>

                                        <div>
                                            <h3 class="text-xl font-black text-white group-hover:text-emerald-300 transition-colors line-clamp-1">
                                                {{ $prop->name }}
                                            </h3>
                                        </div>
                                    </div>

                                    {{-- Card Body --}}
                                    <div class="p-5 sm:p-6 space-y-3">
                                        <p class="text-xs text-slate-500 line-clamp-2 flex items-start gap-1">
                                            <svg class="h-4 w-4 shrink-0 text-slate-400 mt-0.5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                                            <span>{{ $prop->address }}</span>
                                        </p>

                                        {{-- Facilities Preview --}}
                                        <div class="flex flex-wrap gap-1.5 pt-1">
                                            <span class="rounded-lg bg-slate-100 px-2 py-0.5 text-[10px] font-medium text-slate-600">WiFi Cepat</span>
                                            <span class="rounded-lg bg-slate-100 px-2 py-0.5 text-[10px] font-medium text-slate-600">KM Dalam</span>
                                            <span class="rounded-lg bg-slate-100 px-2 py-0.5 text-[10px] font-medium text-slate-600">AC/Fan</span>
                                        </div>
                                    </div>
                                </div>

                                {{-- Card Footer: Price & CTA --}}
                                <div class="p-5 sm:p-6 pt-0 border-t border-slate-100 mt-3 flex items-center justify-between">
                                    <div>
                                        <span class="text-[10px] text-slate-400 uppercase font-semibold">Mulai Dari</span>
                                        <div class="text-base font-extrabold text-[#1E3A8A]">
                                            @if($minPrice)
                                                Rp {{ number_format((float) $minPrice, 0, ',', '.') }}<span class="text-xs font-normal text-slate-500">/bln</span>
                                            @else
                                                Hubungi Pengelola
                                            @endif
                                        </div>
                                    </div>
                                    <a href="{{ route('public.kost.show', $prop->slug ?? $prop->id) }}" class="inline-flex items-center gap-1.5 rounded-xl bg-[#1E3A8A] hover:bg-[#172554] px-4 py-2 text-xs font-bold text-white transition-all shadow-xs">
                                        <span>Lihat Kamar</span>
                                        <svg class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7m0 0l-7 7m7-7H3"/></svg>
                                    </a>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @else
                    <div class="rounded-3xl border border-dashed border-slate-300 bg-white p-12 text-center max-w-xl mx-auto">
                        <svg class="mx-auto h-12 w-12 text-slate-400" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/></svg>
                        <h4 class="mt-3 text-base font-bold text-slate-800">Tidak ada kost yang sesuai filter</h4>
                        <p class="text-xs sm:text-sm text-slate-500 mt-1">Coba sesuaikan pencarian kota atau rentang harga Anda.</p>
                        <a href="{{ route('landing') }}#daftar-kost" class="mt-4 inline-flex items-center gap-2 rounded-xl bg-slate-100 hover:bg-slate-200 px-4 py-2 text-xs font-bold text-slate-700">
                            Reset Filter Pencarian
                        </a>
                    </div>
                @endif
            </div>
        </div>
    </section>

    {{-- Problem vs Solution Section --}}
    <section class="border-y border-slate-200/80 bg-white py-16 lg:py-24">
        <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
            <div class="text-center max-w-3xl mx-auto">
                <h2 class="text-xs font-bold uppercase tracking-wider text-emerald-600">Tinggalkan Cara Lama</h2>
                <p class="mt-2 text-3xl font-extrabold text-slate-900 sm:text-4xl">
                    Masih Mengelola Kost dengan Spreadsheet & Chat Manual?
                </p>
                <p class="mt-4 text-base text-slate-600">
                    Bandingkan repotnya cara konvensional dengan kepraktisan otomatisasi Kostly.
                </p>
            </div>

            <div class="mt-12 grid grid-cols-1 md:grid-cols-2 gap-8">
                {{-- Cara Lama --}}
                <div class="rounded-2xl border border-rose-200/80 bg-rose-50/30 p-6 sm:p-8">
                    <div class="flex items-center gap-3 mb-6">
                        <div class="flex h-10 w-10 items-center justify-center rounded-xl bg-rose-100 text-rose-600">
                            <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                        </div>
                        <div>
                            <h3 class="text-lg font-bold text-rose-950">Cara Lama yang Melelahkan</h3>
                            <p class="text-xs text-rose-700">Rentan lupa, canggung menagih, dan data hilang</p>
                        </div>
                    </div>
                    <ul class="space-y-4 text-sm text-slate-700">
                        <li class="flex items-start gap-3">
                            <span class="text-rose-500 font-bold">✕</span>
                            <span><strong>Rasa sungkan menagih:</strong> Harus chat satu-satu setiap awal bulan dan sering menghadapi alasan klise.</span>
                        </li>
                        <li class="flex items-start gap-3">
                            <span class="text-rose-500 font-bold">✕</span>
                            <span><strong>Cek mutasi bank manual:</strong> Mencocokkan bukti transfer transfer palsu/rekayasa yang dikirim anak kost.</span>
                        </li>
                        <li class="flex items-start gap-3">
                            <span class="text-rose-500 font-bold">✕</span>
                            <span><strong>Status kamar membingungkan:</strong> Tidak tahu pasti kamar mana yang kosong atau akan habis masa sewanya.</span>
                        </li>
                        <li class="flex items-start gap-3">
                            <span class="text-rose-500 font-bold">✕</span>
                            <span><strong>Komplain tenggelam di chat:</strong> Laporan kran bocor atau AC mati tertumpuk obrolan pribadi di WhatsApp.</span>
                        </li>
                    </ul>
                </div>

                {{-- Cara Kostly --}}
                <div class="rounded-2xl border border-emerald-200/80 bg-emerald-50/30 p-6 sm:p-8">
                    <div class="flex items-center gap-3 mb-6">
                        <div class="flex h-10 w-10 items-center justify-center rounded-xl bg-emerald-100 text-emerald-700">
                            <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                        </div>
                        <div>
                            <h3 class="text-lg font-bold text-emerald-950">Solusi Modern Bersama Kostly</h3>
                            <p class="text-xs text-emerald-700">Semua serba otomatis, rapi, dan terpusat</p>
                        </div>
                    </div>
                    <ul class="space-y-4 text-sm text-slate-700">
                        <li class="flex items-start gap-3">
                            <span class="text-emerald-600 font-bold">✓</span>
                            <span><strong>Robot WhatsApp Otomatis:</strong> Tagihan terkirim terjadwal H-3 & Hari-H dengan bahasa sopan dan tautan pembayaran.</span>
                        </li>
                        <li class="flex items-start gap-3">
                            <span class="text-emerald-600 font-bold">✓</span>
                            <span><strong>Bayar Instan QRIS & VA:</strong> Status otomatis berubah "Lunas" saat anak kost membayar, kuitansi langsung terbit.</span>
                        </li>
                        <li class="flex items-start gap-3">
                            <span class="text-emerald-600 font-bold">✓</span>
                            <span><strong>Visual Room Grid:</strong> Pantau ketersediaan kamar secara visual dengan warna status interaktif.</span>
                        </li>
                        <li class="flex items-start gap-3">
                            <span class="text-emerald-600 font-bold">✓</span>
                            <span><strong>Portal Mandiri & Tiket Komplain:</strong> Anak kost lapor kerusakan lewat link portal tanpa perlu instal aplikasi.</span>
                        </li>
                    </ul>
                </div>
            </div>
        </div>
    </section>

    {{-- Key Features Section --}}
    <section id="fitur" class="py-16 lg:py-24">
        <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
            <div class="text-center max-w-3xl mx-auto">
                <span class="text-xs font-bold uppercase tracking-wider text-[#1E3A8A]">Fitur Lengkap</span>
                <h2 class="mt-2 text-3xl font-extrabold text-slate-900 sm:text-4xl">
                    Semua yang Anda Butuhkan untuk Mengembangkan Bisnis Kost
                </h2>
                <p class="mt-4 text-base text-slate-600">
                    Dirancang khusus sesuai kebiasaan pemilik kost dan kenyamanan penghuni di Indonesia.
                </p>
            </div>

            <div class="mt-14 grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-8">
                {{-- Feature 1 --}}
                <div class="group rounded-2xl border border-slate-200/90 bg-white p-7 shadow-xs hover:border-indigo-300 hover:shadow-lg transition-all">
                    <div class="flex h-12 w-12 items-center justify-center rounded-xl bg-indigo-50 text-[#1E3A8A] group-hover:bg-[#1E3A8A] group-hover:text-white transition-colors">
                        <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z"/></svg>
                    </div>
                    <h3 class="mt-5 text-lg font-bold text-slate-900">Visual Room Grid</h3>
                    <p class="mt-2 text-sm text-slate-600 leading-relaxed">
                        Lihat kondisi seluruh kamar dalam denah grid visual. Tahu langsung kamar yang terisi, kosong, atau dalam tahap pembersihan dengan kode warna cerdas.
                    </p>
                </div>

                {{-- Feature 2 --}}
                <div class="group rounded-2xl border border-slate-200/90 bg-white p-7 shadow-xs hover:border-emerald-300 hover:shadow-lg transition-all">
                    <div class="flex h-12 w-12 items-center justify-center rounded-xl bg-emerald-50 text-emerald-600 group-hover:bg-emerald-600 group-hover:text-white transition-colors">
                        <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"/></svg>
                    </div>
                    <h3 class="mt-5 text-lg font-bold text-slate-900">Pengingat Tagihan WhatsApp</h3>
                    <p class="mt-2 text-sm text-slate-600 leading-relaxed">
                        Pengingat terkirim otomatis pada H-3 dan Hari-H lewat WhatsApp. Pesan sopan, personal dengan rincian biaya dan tombol pembayaran online langsung.
                    </p>
                </div>

                {{-- Feature 3 --}}
                <div class="group rounded-2xl border border-slate-200/90 bg-white p-7 shadow-xs hover:border-indigo-300 hover:shadow-lg transition-all">
                    <div class="flex h-12 w-12 items-center justify-center rounded-xl bg-sky-50 text-sky-600 group-hover:bg-sky-600 group-hover:text-white transition-colors">
                        <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"/></svg>
                    </div>
                    <h3 class="mt-5 text-lg font-bold text-slate-900">QRIS & Virtual Account</h3>
                    <p class="mt-2 text-sm text-slate-600 leading-relaxed">
                        Terima pembayaran melalui scan QRIS (GoPay, OVO, Dana, BCA, Livin) serta Virtual Account bank nasional. Verifikasi realtime tanpa upload bukti transfer.
                    </p>
                </div>

                {{-- Feature 4 --}}
                <div class="group rounded-2xl border border-slate-200/90 bg-white p-7 shadow-xs hover:border-indigo-300 hover:shadow-lg transition-all">
                    <div class="flex h-12 w-12 items-center justify-center rounded-xl bg-purple-50 text-purple-600 group-hover:bg-purple-600 group-hover:text-white transition-colors">
                        <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 18h.01M8 21h8a2 2 0 002-2V5a2 2 0 00-2-2H8a2 2 0 00-2 2v14a2 2 0 002 2z"/></svg>
                    </div>
                    <h3 class="mt-5 text-lg font-bold text-slate-900">Portal Penghuni Tanpa Login</h3>
                    <p class="mt-2 text-sm text-slate-600 leading-relaxed">
                        Anak kost dapat mengakses kuitansi pembayaran dan riwayat sewa melalui tautan token unik tanpa perlu registrasi atau menghafal username/password.
                    </p>
                </div>

                {{-- Feature 5 --}}
                <div class="group rounded-2xl border border-slate-200/90 bg-white p-7 shadow-xs hover:border-indigo-300 hover:shadow-lg transition-all">
                    <div class="flex h-12 w-12 items-center justify-center rounded-xl bg-amber-50 text-amber-600 group-hover:bg-amber-600 group-hover:text-white transition-colors">
                        <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 7h6m0 10v-3m-3 3h.01M9 17h.01M9 14h.01M12 14h.01M15 11h.01M12 11h.01M9 11h.01M7 21h10a2 2 0 002-2V5a2 2 0 00-2-2H7a2 2 0 00-2 2v14a2 2 0 002 2z"/></svg>
                    </div>
                    <h3 class="mt-5 text-lg font-bold text-slate-900">Pencatatan Biaya Operasional</h3>
                    <p class="mt-2 text-sm text-slate-600 leading-relaxed">
                        Pantau pengeluaran listrik, tagihan air PDAM, WiFi, gaji penjaga, dan perbaikan. Dapatkan laporan laba-rugi bersih secara otomatis setiap bulan.
                    </p>
                </div>

                {{-- Feature 6 --}}
                <div class="group rounded-2xl border border-slate-200/90 bg-white p-7 shadow-xs hover:border-indigo-300 hover:shadow-lg transition-all">
                    <div class="flex h-12 w-12 items-center justify-center rounded-xl bg-rose-50 text-rose-600 group-hover:bg-rose-600 group-hover:text-white transition-colors">
                        <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                    </div>
                    <h3 class="mt-5 text-lg font-bold text-slate-900">Manajemen Komplain Terpadu</h3>
                    <p class="mt-2 text-sm text-slate-600 leading-relaxed">
                        Penghuni dapat melaporkan kerusakan fasilitas lengkap dengan foto. Anda dapat memonitor progres perbaikan dari status "Menunggu" hingga "Selesai".
                    </p>
                </div>
            </div>
        </div>
    </section>

    {{-- How It Works Section --}}
    <section id="cara-kerja" class="border-y border-slate-200/80 bg-white py-16 lg:py-24">
        <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
            <div class="text-center max-w-3xl mx-auto">
                <span class="text-xs font-bold uppercase tracking-wider text-emerald-600">Alur Sederhana</span>
                <h2 class="mt-2 text-3xl font-extrabold text-slate-900 sm:text-4xl">
                    Mulai dalam 4 Langkah Mudah
                </h2>
                <p class="mt-4 text-base text-slate-600">
                    Tidak butuh keahlian teknis. Siapapun bisa menggunakan Kostly dalam hitungan menit.
                </p>
            </div>

            <div class="mt-14 grid grid-cols-1 md:grid-cols-4 gap-8">
                {{-- Step 1 --}}
                <div class="relative text-center">
                    <div class="mx-auto flex h-14 w-14 items-center justify-center rounded-2xl bg-[#1E3A8A] text-xl font-bold text-white shadow-md shadow-indigo-950/20">
                        1
                    </div>
                    <h4 class="mt-4 text-base font-bold text-slate-900">Daftarkan Properti</h4>
                    <p class="mt-2 text-xs text-slate-600 leading-relaxed">
                        Buat akun, masukkan nama properti kost Anda, dan atur daftar nomor kamar serta harga sewanya.
                    </p>
                </div>

                {{-- Step 2 --}}
                <div class="relative text-center">
                    <div class="mx-auto flex h-14 w-14 items-center justify-center rounded-2xl bg-[#1E3A8A] text-xl font-bold text-white shadow-md shadow-indigo-950/20">
                        2
                    </div>
                    <h4 class="mt-4 text-base font-bold text-slate-900">Input Data Penghuni</h4>
                    <p class="mt-2 text-xs text-slate-600 leading-relaxed">
                        Masukkan nama penyewa, nomor WhatsApp, dan tanggal jatuh tempo tagihan setiap bulannya.
                    </p>
                </div>

                {{-- Step 3 --}}
                <div class="relative text-center">
                    <div class="mx-auto flex h-14 w-14 items-center justify-center rounded-2xl bg-emerald-600 text-xl font-bold text-white shadow-md shadow-emerald-950/20">
                        3
                    </div>
                    <h4 class="mt-4 text-base font-bold text-slate-900">Tagihan Otomatis</h4>
                    <p class="mt-2 text-xs text-slate-600 leading-relaxed">
                        Kostly otomatis membuat tagihan dan mengirimkan notifikasi link bayar ke WhatsApp penghuni.
                    </p>
                </div>

                {{-- Step 4 --}}
                <div class="relative text-center">
                    <div class="mx-auto flex h-14 w-14 items-center justify-center rounded-2xl bg-emerald-600 text-xl font-bold text-white shadow-md shadow-emerald-950/20">
                        4
                    </div>
                    <h4 class="mt-4 text-base font-bold text-slate-900">Terima Pembayaran</h4>
                    <p class="mt-2 text-xs text-slate-600 leading-relaxed">
                        Uang sewa langsung masuk, status terupdate otomatis, dan kuitansi terkirim tanpa Anda sentuh.
                    </p>
                </div>
            </div>
        </div>
    </section>

    {{-- Pricing Section --}}
    <section id="harga" class="py-16 lg:py-24">
        <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
            <div class="text-center max-w-3xl mx-auto">
                <span class="text-xs font-bold uppercase tracking-wider text-[#1E3A8A]">Biaya Transparan</span>
                <h2 class="mt-2 text-3xl font-extrabold text-slate-900 sm:text-4xl">
                    Pilihan Paket yang Sesuai Skala Bisnis Anda
                </h2>
                <p class="mt-4 text-base text-slate-600">
                    Mulai gratis untuk kosan skala kecil, upgrade ke Pro saat kamar bertambah banyak.
                </p>
            </div>

            <div class="mt-14 max-w-4xl mx-auto grid grid-cols-1 md:grid-cols-2 gap-8 items-stretch">
                {{-- Free Plan --}}
                <div class="rounded-3xl border border-slate-200 bg-white p-8 shadow-xs flex flex-col justify-between">
                    <div>
                        <span class="inline-flex rounded-full bg-slate-100 px-3 py-1 text-xs font-semibold text-slate-700">Paket Pemula</span>
                        <h3 class="mt-4 text-2xl font-bold text-slate-900">Gratis Selamanya</h3>
                        <p class="mt-2 text-xs text-slate-500">Cocok untuk pemilik kost dengan jumlah kamar sedikit.</p>
                        <div class="mt-6 flex items-baseline gap-1">
                            <span class="text-4xl font-extrabold text-slate-900">Rp 0</span>
                            <span class="text-xs text-slate-500">/ bulan</span>
                        </div>

                        <ul class="mt-6 space-y-3.5 text-xs text-slate-600">
                            <li class="flex items-center gap-2.5">
                                <svg class="h-4 w-4 text-emerald-500" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                                <span>Maksimal hingga <strong>5 Kamar</strong></span>
                            </li>
                            <li class="flex items-center gap-2.5">
                                <svg class="h-4 w-4 text-emerald-500" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                                <span>Visual Room Grid dasar</span>
                            </li>
                            <li class="flex items-center gap-2.5">
                                <svg class="h-4 w-4 text-emerald-500" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                                <span>Pencatatan data penghuni</span>
                            </li>
                            <li class="flex items-center gap-2.5 text-slate-400">
                                <span>✕</span>
                                <span>Pengingat WhatsApp Otomatis</span>
                            </li>
                            <li class="flex items-center gap-2.5 text-slate-400">
                                <span>✕</span>
                                <span>Portal Mandiri Anak Kost</span>
                            </li>
                        </ul>
                    </div>

                    <a href="{{ route('register') }}" class="mt-8 block w-full rounded-xl border border-slate-300 py-3 text-center text-sm font-semibold text-slate-700 hover:bg-slate-50 transition-colors">
                        Pilih Paket Gratis
                    </a>
                </div>

                {{-- Pro Plan (Featured) --}}
                <div class="relative rounded-3xl border-2 border-[#1E3A8A] bg-white p-8 shadow-xl shadow-indigo-950/10 flex flex-col justify-between">
                    <div class="absolute -top-3.5 right-6 rounded-full bg-emerald-500 px-3 py-1 text-[11px] font-bold text-white shadow-xs">
                        Paling Populer
                    </div>

                    <div>
                        <span class="inline-flex rounded-full bg-indigo-50 px-3 py-1 text-xs font-semibold text-[#1E3A8A]">Juragan Pro</span>
                        <h3 class="mt-4 text-2xl font-bold text-slate-900">Kostly Pro</h3>
                        <p class="mt-2 text-xs text-slate-500">Untuk pemilik kost yang ingin lepas tangan dari urusan tagihan.</p>
                        <div class="mt-6 flex items-baseline gap-1">
                            <span class="text-4xl font-extrabold text-[#1E3A8A]">Rp 49.000</span>
                            <span class="text-xs text-slate-500">/ bulan</span>
                        </div>

                        <ul class="mt-6 space-y-3.5 text-xs text-slate-700">
                            <li class="flex items-center gap-2.5">
                                <svg class="h-4 w-4 text-emerald-500" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                                <span><strong>Unlimited Kamar</strong> & Multi-Properti</span>
                            </li>
                            <li class="flex items-center gap-2.5">
                                <svg class="h-4 w-4 text-emerald-500" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                                <span><strong>Pengingat WhatsApp Otomatis</strong> (H-3 & Hari-H)</span>
                            </li>
                            <li class="flex items-center gap-2.5">
                                <svg class="h-4 w-4 text-emerald-500" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                                <span>Integrasi <strong>QRIS & Virtual Account</strong></span>
                            </li>
                            <li class="flex items-center gap-2.5">
                                <svg class="h-4 w-4 text-emerald-500" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                                <span><strong>Portal Mandiri Penghuni</strong> (Tanpa Login)</span>
                            </li>
                            <li class="flex items-center gap-2.5">
                                <svg class="h-4 w-4 text-emerald-500" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                                <span>Pencatatan Pengeluaran & Tiket Komplain</span>
                            </li>
                        </ul>
                    </div>

                    <a href="{{ route('register') }}" class="mt-8 block w-full rounded-xl bg-[#1E3A8A] py-3 text-center text-sm font-semibold text-white shadow-md shadow-indigo-950/20 hover:bg-[#172554] transition-colors">
                        Mulai Coba Pro Gratis 14 Hari
                    </a>
                </div>
            </div>
        </div>
    </section>

    {{-- Testimonials Section --}}
    <section id="testimoni" class="border-t border-slate-200/80 bg-slate-100/60 py-16 lg:py-24">
        <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
            <div class="text-center max-w-3xl mx-auto">
                <span class="text-xs font-bold uppercase tracking-wider text-emerald-600">Testimoni Pengguna</span>
                <h2 class="mt-2 text-3xl font-extrabold text-slate-900 sm:text-4xl">
                    Kata Mereka yang Sudah Beralih ke Kostly
                </h2>
            </div>

            <div class="mt-14 grid grid-cols-1 md:grid-cols-3 gap-8">
                {{-- Testimonial 1 --}}
                <div class="rounded-2xl border border-slate-200 bg-white p-6 shadow-xs">
                    <div class="flex items-center gap-1 text-amber-400">
                        ★★★★★
                    </div>
                    <p class="mt-4 text-sm text-slate-600 leading-relaxed italic">
                        "Dulu paling stres kalau tanggal 1 datang, harus nagih 18 anak kost satu per satu dan nungguin bukti transfer. Sekarang begitu H-3 WhatsApp bot Kostly yang kirim, anak kost langsung bayar via QRIS. Sangat membantu!"
                    </p>
                    <div class="mt-6 flex items-center gap-3">
                        <div class="h-10 w-10 rounded-full bg-indigo-100 text-indigo-700 flex items-center justify-center font-bold">
                            HS
                        </div>
                        <div>
                            <h4 class="text-sm font-bold text-slate-900">H. Suhendra</h4>
                            <p class="text-xs text-slate-500">Pemilik Kost Putri Sakura, Bandung (24 Kamar)</p>
                        </div>
                    </div>
                </div>

                {{-- Testimonial 2 --}}
                <div class="rounded-2xl border border-slate-200 bg-white p-6 shadow-xs">
                    <div class="flex items-center gap-1 text-amber-400">
                        ★★★★★
                    </div>
                    <p class="mt-4 text-sm text-slate-600 leading-relaxed italic">
                        "Fitur portal penghuni tanpa login jenius banget. Anak kost gak perlu bikin akun atau download app, tinggal buka link di WA langsung bisa kirim foto komplain AC rusak atau unduh bukti bayar."
                    </p>
                    <div class="mt-6 flex items-center gap-3">
                        <div class="h-10 w-10 rounded-full bg-emerald-100 text-emerald-700 flex items-center justify-center font-bold">
                            RW
                        </div>
                        <div>
                            <h4 class="text-sm font-bold text-slate-900">Rina Widyastuti</h4>
                            <p class="text-xs text-slate-500">Pengelola Kost D’Haven, Yogyakarta (16 Kamar)</p>
                        </div>
                    </div>
                </div>

                {{-- Testimonial 3 --}}
                <div class="rounded-2xl border border-slate-200 bg-white p-6 shadow-xs">
                    <div class="flex items-center gap-1 text-amber-400">
                        ★★★★★
                    </div>
                    <p class="mt-4 text-sm text-slate-600 leading-relaxed italic">
                        "Visual grid kamarnya sangat bersih dan enak dilihat di HP. Begitu buka dashboard langsung kelihatan kamar mana yang occupied dan kamar mana yang siap ditawarkan ke calon penyewa."
                    </p>
                    <div class="mt-6 flex items-center gap-3">
                        <div class="h-10 w-10 rounded-full bg-sky-100 text-sky-700 flex items-center justify-center font-bold">
                            AW
                        </div>
                        <div>
                            <h4 class="text-sm font-bold text-slate-900">Agus Wijaya</h4>
                            <p class="text-xs text-slate-500">Juragan Kost Graha Mandiri, Jakarta Barat (32 Kamar)</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    {{-- Call to Action Section --}}
    <section class="relative overflow-hidden bg-[#1E3A8A] py-16 lg:py-20 text-white">
        <div class="mx-auto max-w-5xl px-4 text-center sm:px-6 lg:px-8">
            <h2 class="text-3xl font-extrabold sm:text-4xl">
                Siap Mengubah Manajemen Kost Anda Lebih Santai & Rapi?
            </h2>
            <p class="mx-auto mt-4 max-w-2xl text-base text-indigo-100 sm:text-lg">
                Daftar sekarang dalam 3 menit. Dapatkan uji coba gratis seluruh fitur Pro selama 14 hari tanpa ikatan.
            </p>
            <div class="mt-8 flex flex-col sm:flex-row items-center justify-center gap-4">
                <a href="{{ route('register') }}" class="w-full sm:w-auto inline-flex items-center justify-center gap-2 rounded-xl bg-emerald-500 px-8 py-3.5 text-base font-bold text-white shadow-lg shadow-emerald-950/20 hover:bg-emerald-600 transition-all">
                    Daftar Akun Gratis Sekarang
                </a>
                <a href="{{ route('login') }}" class="w-full sm:w-auto inline-flex items-center justify-center gap-2 rounded-xl border border-indigo-400 bg-white/10 px-8 py-3.5 text-base font-semibold text-white backdrop-blur-xs hover:bg-white/20 transition-all">
                    Masuk Akun Pemilik
                </a>
            </div>
        </div>
    </section>

    {{-- Footer --}}
    <footer class="border-t border-slate-200 bg-white py-12 text-slate-600 text-xs">
        <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
            <div class="flex flex-col sm:flex-row items-center justify-between gap-6">
                <div class="flex items-center gap-2.5">
                    <div class="flex h-8 w-8 items-center justify-center rounded-lg bg-[#1E3A8A] text-white">
                        <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/></svg>
                    </div>
                    <span class="text-base font-bold text-[#1E3A8A]">Kostly<span class="text-emerald-500">.</span></span>
                    <span class="text-slate-400">|</span>
                    <span class="text-slate-500">SaaS Manajemen Kost & Penagihan Otomatis</span>
                </div>
                <div class="flex items-center gap-6">
                    <a href="#fitur" class="hover:text-[#1E3A8A]">Fitur</a>
                    <a href="#cara-kerja" class="hover:text-[#1E3A8A]">Cara Kerja</a>
                    <a href="#harga" class="hover:text-[#1E3A8A]">Harga</a>
                    <a href="{{ route('login') }}" class="hover:text-[#1E3A8A]">Login</a>
                    <a href="{{ route('register') }}" class="hover:text-[#1E3A8A]">Daftar</a>
                </div>
            </div>
            <div class="mt-8 border-t border-slate-100 pt-6 text-center text-slate-400">
                &copy; {{ date('Y') }} Kostly Indonesia. Hak Cipta Dilindungi Undang-Undang.
            </div>
        </div>
    </footer>
</x-app>

