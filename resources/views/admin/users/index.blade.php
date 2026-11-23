<x-layouts.admin>
    <x-slot:title>Manajemen Pemilik Kost</x-slot:title>
    <x-slot:header>Manajemen Pemilik Kost (Owners)</x-slot:header>

    <div class="space-y-6 pt-2">
        {{-- Header & Penjelasan Singkat --}}
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
            <div>
                <h2 class="text-xl font-bold tracking-tight text-slate-900">Daftar Akun Pemilik Kost</h2>
                <p class="text-xs sm:text-sm text-slate-500">Kelola paket langganan SaaS (Free/Pro) dan status keaktifan akun pemilik properti di Kostly.</p>
            </div>
        </div>

        {{-- Filter & Pencarian Bar --}}
        <div class="rounded-2xl border border-slate-200/90 bg-white p-4 sm:p-5 shadow-xs">
            <form method="GET" action="{{ route('admin.users.index') }}" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-3 sm:gap-4 items-end">
                {{-- Input Cari Nama/Email/Telepon --}}
                <div class="lg:col-span-2">
                    <label for="search" class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">Pencarian</label>
                    <div class="relative">
                        <div class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3">
                            <svg class="h-4 w-4 text-slate-400" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                        </div>
                        <input type="text" name="search" id="search" value="{{ request('search') }}" placeholder="Cari berdasarkan nama, email, atau no. telepon..."
                            class="w-full rounded-xl border border-slate-200 bg-slate-50/50 py-2.5 pl-9 pr-3 text-xs sm:text-sm focus:border-indigo-500 focus:bg-white focus:outline-hidden focus:ring-1 focus:ring-indigo-500">
                    </div>
                </div>

                {{-- Filter Paket Langganan --}}
                <div>
                    <label for="plan" class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">Paket Langganan</label>
                    <select name="plan" id="plan"
                        class="w-full rounded-xl border border-slate-200 bg-slate-50/50 py-2.5 px-3 text-xs sm:text-sm focus:border-indigo-500 focus:bg-white focus:outline-hidden focus:ring-1 focus:ring-indigo-500">
                        <option value="">Semua Paket</option>
                        <option value="free" {{ request('plan') === 'free' ? 'selected' : '' }}>Free</option>
                        <option value="pro" {{ request('plan') === 'pro' ? 'selected' : '' }}>Pro</option>
                    </select>
                </div>

                {{-- Filter Status Akun & Tombol Submit --}}
                <div>
                    <label for="status" class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">Status Akun</label>
                    <div class="flex items-center gap-2">
                        <select name="status" id="status"
                            class="w-full rounded-xl border border-slate-200 bg-slate-50/50 py-2.5 px-3 text-xs sm:text-sm focus:border-indigo-500 focus:bg-white focus:outline-hidden focus:ring-1 focus:ring-indigo-500">
                            <option value="">Semua Status</option>
                            <option value="active" {{ request('status') === 'active' ? 'selected' : '' }}>Aktif</option>
                            <option value="suspended" {{ request('status') === 'suspended' ? 'selected' : '' }}>Ditangguhkan</option>
                        </select>
                        <button type="submit" class="inline-flex items-center justify-center rounded-xl bg-indigo-600 px-4 py-2.5 text-xs sm:text-sm font-bold text-white shadow-xs hover:bg-indigo-700 transition-colors shrink-0">
                            Filter
                        </button>
                        @if (request()->hasAny(['search', 'plan', 'status']))
                            <a href="{{ route('admin.users.index') }}" class="p-2.5 rounded-xl border border-slate-200 text-slate-500 hover:bg-slate-100 hover:text-slate-700 transition-colors shrink-0" title="Reset Filter">
                                <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                            </a>
                        @endif
                    </div>
                </div>
            </form>
        </div>

        {{-- Tabel Data Pemilik Kost --}}
        <div class="rounded-2xl border border-slate-200/90 bg-white shadow-xs overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full text-left text-sm text-slate-600">
                    <thead class="bg-slate-50/80 text-xs font-bold uppercase tracking-wider text-slate-500 border-b border-slate-100">
                        <tr>
                            <th class="py-3.5 px-6">Informasi Pemilik</th>
                            <th class="py-3.5 px-6">Paket Langganan</th>
                            <th class="py-3.5 px-6">Jumlah Properti</th>
                            <th class="py-3.5 px-6">Status Akun</th>
                            <th class="py-3.5 px-6">Terdaftar Sejak</th>
                            <th class="py-3.5 px-6 text-center">Aksi Manajemen</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @forelse ($owners as $owner)
                            <tr class="hover:bg-slate-50/60 transition-colors">
                                {{-- Info Pemilik --}}
                                <td class="py-4 px-6">
                                    <div class="flex items-center gap-3">
                                        <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-xl {{ $owner->is_active ? 'bg-indigo-50 text-indigo-700' : 'bg-slate-100 text-slate-400' }} font-bold text-sm">
                                            {{ substr($owner->name, 0, 2) }}
                                        </div>
                                        <div class="min-w-0">
                                            <div class="font-bold text-slate-900 truncate">{{ $owner->name }}</div>
                                            <div class="text-xs text-slate-500 truncate">{{ $owner->email }}</div>
                                            <div class="text-xs text-slate-400 mt-0.5">{{ $owner->phone }}</div>
                                        </div>
                                    </div>
                                </td>

                                {{-- Paket Langganan --}}
                                <td class="py-4 px-6">
                                    @if ($owner->subscription_plan->value === 'pro')
                                        <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-bold bg-amber-50 text-amber-800 border border-amber-200 shadow-xs">
                                            <span>⭐</span>
                                            <span>Pro Plan</span>
                                        </span>
                                    @else
                                        <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-semibold bg-slate-100 text-slate-700 border border-slate-200">
                                            <span>Free Plan</span>
                                        </span>
                                    @endif
                                </td>

                                {{-- Jumlah Properti & Kamar --}}
                                <td class="py-4 px-6">
                                    <div class="inline-flex flex-col">
                                        <span class="font-bold text-slate-900">{{ $owner->properties_count }} Properti</span>
                                        <span class="text-xs text-slate-500">{{ $owner->rooms_count }} Kamar total • {{ $owner->tenants_count }} Penghuni</span>
                                    </div>
                                </td>

                                {{-- Status Akun --}}
                                <td class="py-4 px-6">
                                    @if ($owner->is_active)
                                        <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-md text-xs font-semibold bg-emerald-50 text-emerald-700 border border-emerald-200/60">
                                            <span class="h-1.5 w-1.5 rounded-full bg-emerald-500"></span>
                                            Aktif
                                        </span>
                                    @else
                                        <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-md text-xs font-semibold bg-rose-50 text-rose-700 border border-rose-200/60">
                                            <span class="h-1.5 w-1.5 rounded-full bg-rose-500"></span>
                                            Ditangguhkan
                                        </span>
                                    @endif
                                </td>

                                {{-- Terdaftar Sejak --}}
                                <td class="py-4 px-6 text-xs text-slate-500">
                                    <div>{{ $owner->created_at->translatedFormat('d M Y') }}</div>
                                    <div class="text-[11px] text-slate-400">{{ $owner->created_at->diffForHumans() }}</div>
                                </td>

                                {{-- Tombol Aksi --}}
                                <td class="py-4 px-6">
                                    <div class="flex items-center justify-center gap-2">
                                        {{-- Ubah Paket (Free <-> Pro) --}}
                                        <form action="{{ route('admin.users.updatePlan', $owner) }}" method="POST" onsubmit="return confirm('Apakah Anda yakin ingin mengubah paket langganan {{ $owner->name }} menjadi {{ $owner->subscription_plan->value === 'pro' ? 'Free' : 'Pro' }}?')">
                                            @csrf
                                            @method('PATCH')
                                            @if ($owner->subscription_plan->value === 'pro')
                                                <input type="hidden" name="subscription_plan" value="free">
                                                <button type="submit" class="inline-flex items-center gap-1 px-2.5 py-1.5 rounded-lg border border-slate-200 bg-white text-xs font-semibold text-slate-700 hover:bg-slate-50 hover:border-slate-300 transition-colors shadow-xs" title="Turunkan ke Paket Free">
                                                    <span>Ubah ke Free</span>
                                                </button>
                                            @else
                                                <input type="hidden" name="subscription_plan" value="pro">
                                                <button type="submit" class="inline-flex items-center gap-1 px-2.5 py-1.5 rounded-lg border border-amber-200 bg-amber-50 text-xs font-bold text-amber-800 hover:bg-amber-100 transition-colors shadow-xs" title="Upgrade ke Paket Pro">
                                                    <span>⭐ Upgrade Pro</span>
                                                </button>
                                            @endif
                                        </form>

                                        {{-- Tangguhkan / Aktifkan Akun --}}
                                        <form action="{{ route('admin.users.toggleStatus', $owner) }}" method="POST" onsubmit="return confirm('Apakah Anda yakin ingin {{ $owner->is_active ? 'menangguhkan' : 'mengaktifkan kembali' }} akun {{ $owner->name }}?')">
                                            @csrf
                                            @method('PATCH')
                                            @if ($owner->is_active)
                                                <button type="submit" class="inline-flex items-center gap-1 px-2.5 py-1.5 rounded-lg border border-rose-200 bg-white text-xs font-semibold text-rose-600 hover:bg-rose-50 hover:border-rose-300 transition-colors shadow-xs" title="Tangguhkan Akun">
                                                    <svg class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728A9 9 0 015.636 5.636m12.728 12.728L5.636 5.636"/></svg>
                                                    <span>Tangguhkan</span>
                                                </button>
                                            @else
                                                <button type="submit" class="inline-flex items-center gap-1 px-2.5 py-1.5 rounded-lg border border-emerald-200 bg-emerald-50 text-xs font-semibold text-emerald-700 hover:bg-emerald-100 transition-colors shadow-xs" title="Aktifkan Kembali Akun">
                                                    <svg class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                                    <span>Aktifkan</span>
                                                </button>
                                            @endif
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="py-12 text-center text-slate-400">
                                    <svg class="mx-auto h-10 w-10 text-slate-300 mb-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
                                    </svg>
                                    <p class="font-bold text-slate-600">Tidak ada pemilik kost yang cocok</p>
                                    <p class="text-xs text-slate-400 mt-1">Coba sesuaikan kata kunci pencarian atau filter yang Anda pilih.</p>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if ($owners->hasPages())
                <div class="p-4 border-t border-slate-100 bg-slate-50/50">
                    {{ $owners->links() }}
                </div>
            @endif
        </div>
    </div>
</x-layouts.admin>
