<x-layouts.admin>
    <x-slot:title>Dashboard SaaS</x-slot:title>
    <x-slot:header>Ringkasan Platform SaaS</x-slot:header>

    <div class="space-y-6 pt-2">
        {{-- Banner Selamat Datang Admin --}}
        <div class="rounded-2xl bg-gradient-to-r from-slate-900 via-indigo-950 to-slate-900 p-6 text-white shadow-xl relative overflow-hidden">
            <div class="absolute right-0 top-0 bottom-0 w-1/3 opacity-10 bg-[radial-gradient(#ffffff_1px,transparent_1px)] [background-size:16px_16px]"></div>
            <div class="relative z-10 flex flex-col md:flex-row md:items-center justify-between gap-4">
                <div>
                    <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-xs font-semibold bg-indigo-500/20 text-indigo-300 border border-indigo-400/30 mb-3">
                        <span class="h-1.5 w-1.5 rounded-full bg-emerald-400"></span>
                        Platform Super Administrator
                    </span>
                    <h2 class="text-xl sm:text-2xl font-black tracking-tight">Halo, {{ auth()->user()->name }}! 👋</h2>
                    <p class="mt-1 text-sm text-slate-300 max-w-xl">
                        Selamat datang di kendali utama sistem SaaS Kostly. Pantau pertumbuhan pengguna pemilik kost, distribusi langganan, dan operasional properti secara real-time.
                    </p>
                </div>
                <div class="flex items-center gap-3">
                    <a href="{{ route('admin.users.index') }}" class="inline-flex items-center gap-2 rounded-xl bg-indigo-600 px-4 py-2.5 text-xs sm:text-sm font-bold text-white shadow-md hover:bg-indigo-500 transition-colors">
                        <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/></svg>
                        <span>Kelola Pemilik Kost</span>
                    </a>
                </div>
            </div>
        </div>

        {{-- Kartu Ringkasan Metrik SaaS --}}
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 sm:gap-5">
            {{-- Total Pemilik Kost --}}
            <div class="rounded-2xl border border-slate-200/90 bg-white p-5 shadow-xs hover:shadow-md transition-shadow">
                <div class="flex items-center justify-between">
                    <span class="text-xs font-bold uppercase tracking-wider text-slate-500">Total Pemilik Kost</span>
                    <div class="flex h-10 w-10 items-center justify-center rounded-xl bg-indigo-50 text-indigo-600">
                        <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
                        </svg>
                    </div>
                </div>
                <div class="mt-4 flex items-baseline gap-2">
                    <span class="text-3xl font-black tracking-tight text-slate-900">{{ number_format($totalOwners) }}</span>
                    <span class="text-xs font-semibold text-slate-500">akun terdaftar</span>
                </div>
                <div class="mt-3 flex items-center gap-3 pt-3 border-t border-slate-100 text-xs">
                    <span class="inline-flex items-center gap-1 text-emerald-600 font-semibold">
                        <span class="h-2 w-2 rounded-full bg-emerald-500"></span>
                        {{ $activeOwners }} Aktif
                    </span>
                    @if ($suspendedOwners > 0)
                        <span class="inline-flex items-center gap-1 text-rose-600 font-semibold">
                            <span class="h-2 w-2 rounded-full bg-rose-500"></span>
                            {{ $suspendedOwners }} Ditangguhkan
                        </span>
                    @endif
                </div>
            </div>

            {{-- Akun Free vs Pro --}}
            <div class="rounded-2xl border border-slate-200/90 bg-white p-5 shadow-xs hover:shadow-md transition-shadow">
                <div class="flex items-center justify-between">
                    <span class="text-xs font-bold uppercase tracking-wider text-slate-500">Distribusi Paket</span>
                    <div class="flex h-10 w-10 items-center justify-center rounded-xl bg-amber-50 text-amber-600">
                        <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z" />
                        </svg>
                    </div>
                </div>
                <div class="mt-4 flex items-baseline gap-3">
                    <span class="text-3xl font-black tracking-tight text-amber-600">{{ number_format($proOwners) }}</span>
                    <span class="text-xs font-bold px-2 py-0.5 rounded-md bg-amber-100 text-amber-800 uppercase">Pro</span>
                    <span class="text-slate-300">/</span>
                    <span class="text-xl font-bold text-slate-700">{{ number_format($freeOwners) }}</span>
                    <span class="text-xs font-bold px-2 py-0.5 rounded-md bg-slate-100 text-slate-600 uppercase">Free</span>
                </div>
                <div class="mt-3 pt-3 border-t border-slate-100">
                    @php
                        $proPercent = $totalOwners > 0 ? round(($proOwners / $totalOwners) * 100) : 0;
                    @endphp
                    <div class="flex items-center justify-between text-xs text-slate-500 mb-1">
                        <span>Konversi Pro</span>
                        <span class="font-bold text-slate-800">{{ $proPercent }}%</span>
                    </div>
                    <div class="h-2 w-full rounded-full bg-slate-100 overflow-hidden">
                        <div class="h-full bg-amber-500 rounded-full" style="width: {{ $proPercent }}%"></div>
                    </div>
                </div>
            </div>

            {{-- Total Properti Kost --}}
            <div class="rounded-2xl border border-slate-200/90 bg-white p-5 shadow-xs hover:shadow-md transition-shadow">
                <div class="flex items-center justify-between">
                    <span class="text-xs font-bold uppercase tracking-wider text-slate-500">Total Properti Kost</span>
                    <div class="flex h-10 w-10 items-center justify-center rounded-xl bg-blue-50 text-blue-600">
                        <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                        </svg>
                    </div>
                </div>
                <div class="mt-4 flex items-baseline gap-2">
                    <span class="text-3xl font-black tracking-tight text-slate-900">{{ number_format($totalProperties) }}</span>
                    <span class="text-xs font-semibold text-slate-500">lokasi kost</span>
                </div>
                <div class="mt-3 flex items-center justify-between pt-3 border-t border-slate-100 text-xs text-slate-500">
                    <span>Rata-rata properti per owner</span>
                    <span class="font-bold text-slate-800">{{ $totalOwners > 0 ? round($totalProperties / $totalOwners, 1) : 0 }}</span>
                </div>
            </div>

            {{-- Total Kamar di Sistem --}}
            <div class="rounded-2xl border border-slate-200/90 bg-white p-5 shadow-xs hover:shadow-md transition-shadow">
                <div class="flex items-center justify-between">
                    <span class="text-xs font-bold uppercase tracking-wider text-slate-500">Total Kamar Sistem</span>
                    <div class="flex h-10 w-10 items-center justify-center rounded-xl bg-emerald-50 text-emerald-600">
                        <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6" />
                        </svg>
                    </div>
                </div>
                <div class="mt-4 flex items-baseline gap-2">
                    <span class="text-3xl font-black tracking-tight text-slate-900">{{ number_format($totalRooms) }}</span>
                    <span class="text-xs font-semibold text-slate-500">kamar terdaftar</span>
                </div>
                <div class="mt-3 flex items-center justify-between pt-3 border-t border-slate-100 text-xs text-slate-500">
                    <span class="text-emerald-700 font-semibold">{{ number_format($occupiedRooms) }} Terisi</span>
                    <span>•</span>
                    <span class="text-slate-600 font-semibold">{{ number_format($totalTenants) }} Penghuni Aktif</span>
                </div>
            </div>
        </div>

        {{-- Tabel Pemilik Kost Terbaru --}}
        <div class="rounded-2xl border border-slate-200/90 bg-white shadow-xs overflow-hidden">
            <div class="p-5 sm:px-6 border-b border-slate-100 flex items-center justify-between">
                <div>
                    <h3 class="text-base font-bold text-slate-900">Pemilik Kost Terdaftar Terbaru</h3>
                    <p class="text-xs text-slate-500">Daftar akun pemilik yang baru mendaftar di Kostly</p>
                </div>
                <a href="{{ route('admin.users.index') }}" class="text-xs font-bold text-indigo-600 hover:text-indigo-800">
                    Lihat Semua Owner →
                </a>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full text-left text-sm text-slate-600">
                    <thead class="bg-slate-50/80 text-xs font-bold uppercase tracking-wider text-slate-500 border-b border-slate-100">
                        <tr>
                            <th class="py-3 px-6">Nama & Kontak</th>
                            <th class="py-3 px-6">Paket</th>
                            <th class="py-3 px-6">Properti & Kamar</th>
                            <th class="py-3 px-6">Status Akun</th>
                            <th class="py-3 px-6">Terdaftar Sejak</th>
                            <th class="py-3 px-6 text-right">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @forelse ($recentOwners as $owner)
                            <tr class="hover:bg-slate-50/60 transition-colors">
                                <td class="py-4 px-6">
                                    <div class="flex items-center gap-3">
                                        <div class="flex h-9 w-9 shrink-0 items-center justify-center rounded-xl bg-indigo-50 font-bold text-indigo-700 text-xs">
                                            {{ substr($owner->name, 0, 2) }}
                                        </div>
                                        <div>
                                            <div class="font-bold text-slate-900">{{ $owner->name }}</div>
                                            <div class="text-xs text-slate-500">{{ $owner->email }} • {{ $owner->phone }}</div>
                                        </div>
                                    </div>
                                </td>
                                <td class="py-4 px-6">
                                    @if ($owner->subscription_plan->value === 'pro')
                                        <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-xs font-bold bg-amber-50 text-amber-700 border border-amber-200">
                                            ⭐ Pro
                                        </span>
                                    @else
                                        <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-xs font-semibold bg-slate-100 text-slate-700 border border-slate-200">
                                            Free
                                        </span>
                                    @endif
                                </td>
                                <td class="py-4 px-6">
                                    <div class="font-semibold text-slate-800">{{ $owner->properties_count }} Properti</div>
                                    <div class="text-xs text-slate-500">{{ $owner->rooms_count }} Kamar total</div>
                                </td>
                                <td class="py-4 px-6">
                                    @if ($owner->is_active)
                                        <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-md text-xs font-semibold bg-emerald-50 text-emerald-700">
                                            <span class="h-1.5 w-1.5 rounded-full bg-emerald-500"></span>
                                            Aktif
                                        </span>
                                    @else
                                        <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-md text-xs font-semibold bg-rose-50 text-rose-700">
                                            <span class="h-1.5 w-1.5 rounded-full bg-rose-500"></span>
                                            Ditangguhkan
                                        </span>
                                    @endif
                                </td>
                                <td class="py-4 px-6 text-xs text-slate-500">
                                    {{ $owner->created_at->translatedFormat('d M Y, H:i') }}
                                </td>
                                <td class="py-4 px-6 text-right">
                                    <a href="{{ route('admin.users.index', ['search' => $owner->email]) }}" class="inline-flex items-center gap-1 px-3 py-1.5 rounded-lg border border-slate-200 text-xs font-semibold text-slate-700 hover:bg-slate-100 transition-colors">
                                        Kelola
                                    </a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="py-8 text-center text-slate-400 text-sm">
                                    Belum ada data pemilik kost yang terdaftar.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</x-layouts.admin>
