<x-layouts.owner>
    <x-slot:header>
        Ringkasan Bisnis Kost
    </x-slot:header>

    {{-- Welcome Banner & Quick Property Switcher --}}
    <div class="mt-4 flex flex-col md:flex-row md:items-center justify-between gap-4 bg-white border border-slate-200/90 rounded-2xl p-5 sm:p-6 shadow-xs">
        <div>
            <div class="inline-flex items-center gap-2 px-2.5 py-1 rounded-full bg-indigo-50 text-[11px] font-bold text-[#1E3A8A] mb-2">
                <span>Properti Aktif</span>
            </div>
            <h2 class="text-xl sm:text-2xl font-black text-slate-900 tracking-tight">
                Halo, {{ $user->name }}! 👋
            </h2>
            <p class="text-xs sm:text-sm text-slate-500 mt-1">
                Berikut adalah performa operasional kost Anda hari ini, {{ now()->translatedFormat('d F Y') }}.
            </p>
        </div>

        {{-- Property Filter / Actions --}}
        <div class="flex flex-wrap items-center gap-2.5">
            @if ($properties->count() > 1)
                <form action="{{ route('owner.dashboard') }}" method="GET" class="inline-flex">
                    <select name="property_id" onchange="this.form.submit()" class="rounded-xl border border-slate-300 bg-white py-2 px-3 text-xs font-semibold text-slate-700 shadow-xs focus:border-[#1E3A8A] focus:outline-hidden">
                        <option value="">Semua Properti ({{ $properties->count() }})</option>
                        @foreach ($properties as $prop)
                            <option value="{{ $prop->id }}" {{ $selectedPropertyId == $prop->id ? 'selected' : '' }}>
                                {{ $prop->name }}
                            </option>
                        @endforeach
                    </select>
                </form>
            @elseif ($properties->isNotEmpty())
                <div class="inline-flex items-center gap-2 rounded-xl border border-slate-200 bg-slate-50 px-3 py-2 text-xs font-medium text-slate-700">
                    <svg class="h-4 w-4 text-[#1E3A8A]" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/></svg>
                    <span>{{ $properties->first()->name }}</span>
                </div>
            @endif

            <a href="{{ route('owner.rooms.index') }}" class="inline-flex items-center gap-1.5 rounded-xl bg-[#1E3A8A] px-4 py-2 text-xs font-bold text-white shadow-xs hover:bg-[#172554] transition-colors">
                <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                <span>Kelola Kamar</span>
            </a>
        </div>
    </div>

    {{-- Top Statistics 4-Cards Grid --}}
    <div class="mt-6 grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 sm:gap-6">
        {{-- Card 1: Okupansi Kamar --}}
        <div class="rounded-2xl border border-slate-200/90 bg-white p-5 shadow-xs flex flex-col justify-between">
            <div>
                <div class="flex items-center justify-between text-slate-500">
                    <span class="text-xs font-semibold uppercase tracking-wider">Okupansi Kamar</span>
                    <div class="flex h-9 w-9 items-center justify-center rounded-xl bg-blue-50 text-blue-700">
                        <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/></svg>
                    </div>
                </div>
                <div class="mt-3 flex items-baseline gap-2">
                    <span class="text-3xl font-extrabold text-slate-900">{{ $occupancyRate }}%</span>
                    <span class="text-xs text-slate-500 font-medium">({{ $occupiedRooms }}/{{ $totalRooms }} Kamar)</span>
                </div>
            </div>
            <div class="mt-4 pt-3 border-t border-slate-100">
                <div class="h-2 w-full rounded-full bg-slate-100 overflow-hidden mb-2">
                    <div class="h-full bg-[#1E3A8A] rounded-full" style="width: {{ $occupancyRate }}%"></div>
                </div>
                <div class="flex items-center justify-between text-[11px] text-slate-500">
                    <span class="text-emerald-600 font-semibold">{{ $availableRooms }} Tersedia</span>
                    <span class="text-amber-600 font-semibold">{{ $maintenanceRooms }} Perbaikan</span>
                </div>
            </div>
        </div>

        {{-- Card 2: Pemasukan Bulan Ini --}}
        <div class="rounded-2xl border border-slate-200/90 bg-white p-5 shadow-xs flex flex-col justify-between">
            <div>
                <div class="flex items-center justify-between text-slate-500">
                    <span class="text-xs font-semibold uppercase tracking-wider">Pemasukan ({{ now()->translatedFormat('F') }})</span>
                    <div class="flex h-9 w-9 items-center justify-center rounded-xl bg-emerald-50 text-emerald-700">
                        <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    </div>
                </div>
                <div class="mt-3">
                    <span class="text-2xl sm:text-3xl font-extrabold text-slate-900 tracking-tight">
                        Rp {{ number_format($monthlyIncome, 0, ',', '.') }}
                    </span>
                </div>
            </div>
            <div class="mt-4 pt-3 border-t border-slate-100 flex items-center justify-between text-xs">
                <span class="text-slate-500">Status Pembayaran:</span>
                <span class="inline-flex items-center gap-1 font-bold text-emerald-600">
                    <span class="h-2 w-2 rounded-full bg-emerald-500"></span>
                    Sudah Masuk
                </span>
            </div>
        </div>

        {{-- Card 3: Tagihan Tertunda --}}
        <div class="rounded-2xl border border-slate-200/90 bg-white p-5 shadow-xs flex flex-col justify-between">
            <div>
                <div class="flex items-center justify-between text-slate-500">
                    <span class="text-xs font-semibold uppercase tracking-wider">Tagihan Menunggu</span>
                    <div class="flex h-9 w-9 items-center justify-center rounded-xl bg-amber-50 text-amber-700">
                        <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    </div>
                </div>
                <div class="mt-3">
                    <span class="text-2xl sm:text-3xl font-extrabold text-amber-600 tracking-tight">
                        Rp {{ number_format($pendingIncome, 0, ',', '.') }}
                    </span>
                </div>
            </div>
            <div class="mt-4 pt-3 border-t border-slate-100 flex items-center justify-between text-xs">
                <span class="text-slate-500">Belum Lunas & Lewat:</span>
                <a href="{{ route('owner.invoices.index') }}" class="font-bold text-[#1E3A8A] hover:underline">
                    Lihat Tagihan →
                </a>
            </div>
        </div>

        {{-- Card 4: Laba Bersih --}}
        <div class="rounded-2xl border border-slate-200/90 bg-white p-5 shadow-xs flex flex-col justify-between">
            <div>
                <div class="flex items-center justify-between text-slate-500">
                    <span class="text-xs font-semibold uppercase tracking-wider">Estimasi Laba Bersih</span>
                    <div class="flex h-9 w-9 items-center justify-center rounded-xl bg-indigo-50 text-[#1E3A8A]">
                        <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/></svg>
                    </div>
                </div>
                <div class="mt-3">
                    <span class="text-2xl sm:text-3xl font-extrabold {{ $netProfit >= 0 ? 'text-slate-900' : 'text-rose-600' }} tracking-tight">
                        Rp {{ number_format($netProfit, 0, ',', '.') }}
                    </span>
                </div>
            </div>
            <div class="mt-4 pt-3 border-t border-slate-100 flex items-center justify-between text-xs text-slate-500">
                <span>Pengeluaran:</span>
                <span class="font-semibold text-rose-600">Rp {{ number_format($monthlyExpenses, 0, ',', '.') }}</span>
            </div>
        </div>
    </div>

    {{-- Visual Room Grid Section --}}
    <div class="mt-8 bg-white border border-slate-200/90 rounded-2xl p-5 sm:p-6 shadow-xs">
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 border-b border-slate-100 pb-4 mb-5">
            <div>
                <h3 class="text-base sm:text-lg font-bold text-slate-900">
                    Denah Visual Kamar Kost (Visual Room Grid)
                </h3>
                <p class="text-xs text-slate-500 mt-0.5">
                    Indikator realtime ketersediaan kamar. Klik kamar untuk melihat rincian atau mengubah status.
                </p>
            </div>

            {{-- Legend Indicators --}}
            <div class="flex flex-wrap items-center gap-3 text-xs">
                <span class="inline-flex items-center gap-1.5 font-medium text-slate-600">
                    <span class="h-3 w-3 rounded-md bg-emerald-500"></span> Tersedia ({{ $availableRooms }})
                </span>
                <span class="inline-flex items-center gap-1.5 font-medium text-slate-600">
                    <span class="h-3 w-3 rounded-md bg-blue-500"></span> Terisi ({{ $occupiedRooms }})
                </span>
                <span class="inline-flex items-center gap-1.5 font-medium text-slate-600">
                    <span class="h-3 w-3 rounded-md bg-amber-500"></span> Perbaikan ({{ $maintenanceRooms }})
                </span>
                <a href="{{ route('owner.rooms.index') }}" class="text-xs font-bold text-[#1E3A8A] hover:underline ml-2">
                    Buka Halaman Kamar →
                </a>
            </div>
        </div>

        {{-- Room Grid Container --}}
        @if ($previewRooms->isEmpty())
            <div class="text-center py-10">
                <p class="text-sm text-slate-500">Belum ada kamar yang terdaftar.</p>
                <a href="{{ route('owner.rooms.index') }}" class="mt-3 inline-flex items-center gap-2 rounded-xl bg-[#1E3A8A] px-4 py-2 text-xs font-bold text-white">
                    Tambah Kamar Pertama
                </a>
            </div>
        @else
            <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-5 gap-3.5 sm:gap-4">
                @foreach ($previewRooms as $room)
                    <div class="group relative rounded-2xl border p-4 transition-all hover:shadow-md
                        {{ $room->status === App\Enums\RoomStatus::Available ? 'border-emerald-200 bg-emerald-50/20 hover:border-emerald-400' : '' }}
                        {{ $room->status === App\Enums\RoomStatus::Occupied ? 'border-blue-200 bg-blue-50/20 hover:border-blue-400' : '' }}
                        {{ $room->status === App\Enums\RoomStatus::Maintenance ? 'border-amber-200 bg-amber-50/20 hover:border-amber-400' : '' }}
                    ">
                        <div class="flex items-center justify-between mb-2">
                            <span class="text-base font-extrabold text-slate-900">
                                {{ $room->room_number }}
                            </span>
                            <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-md text-[10px] font-bold {{ $room->status->badgeClass() }}">
                                <span class="h-1.5 w-1.5 rounded-full {{ $room->status->dotClass() }}"></span>
                                {{ $room->status->label() }}
                            </span>
                        </div>

                        <p class="text-xs text-slate-600 font-medium truncate">{{ $room->type }}</p>
                        <p class="mt-1 text-xs font-bold text-slate-900">
                            Rp {{ number_format((float) $room->price, 0, ',', '.') }}<span class="text-[10px] text-slate-400 font-normal">/bln</span>
                        </p>

                        {{-- Tenant Name Info --}}
                        <div class="mt-3 pt-2.5 border-t border-slate-200/60 text-[11px] truncate">
                            @if ($room->currentTenant)
                                <div class="flex items-center gap-1.5 text-blue-900 font-semibold truncate">
                                    <svg class="h-3.5 w-3.5 text-blue-600 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
                                    <span class="truncate">{{ $room->currentTenant->name }}</span>
                                </div>
                            @elseif ($room->status === App\Enums\RoomStatus::Maintenance)
                                <span class="text-amber-700 italic">Sedang Renovasi</span>
                            @else
                                <span class="text-emerald-700 font-medium">Siap Dihuni</span>
                            @endif
                        </div>
                    </div>
                @endforeach
            </div>
        @endif
    </div>

    {{-- Two Column Bottom Section: Urgent Invoices & Recent Complaints --}}
    <div class="mt-8 grid grid-cols-1 lg:grid-cols-2 gap-6">
        {{-- Left: Urgent Invoices (Tagihan Jatuh Tempo & H-3) --}}
        <div class="bg-white border border-slate-200/90 rounded-2xl p-5 sm:p-6 shadow-xs flex flex-col justify-between">
            <div>
                <div class="flex items-center justify-between border-b border-slate-100 pb-3 mb-4">
                    <div>
                        <h4 class="text-base font-bold text-slate-900">Tagihan Perlu Perhatian</h4>
                        <p class="text-xs text-slate-500">Tagihan mendekati jatuh tempo atau telah lewat</p>
                    </div>
                    <a href="{{ route('owner.invoices.index') }}" class="text-xs font-bold text-[#1E3A8A] hover:underline">
                        Lihat Semua
                    </a>
                </div>

                @if ($urgentInvoices->isEmpty())
                    <div class="text-center py-8 text-xs text-slate-500">
                        <svg class="h-8 w-8 text-emerald-500 mx-auto mb-2" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                        Tidak ada tagihan tertunda yang mendesak saat ini. Semua sewa tertib!
                    </div>
                @else
                    <div class="divide-y divide-slate-100">
                        @foreach ($urgentInvoices as $inv)
                            <div class="py-3 flex items-center justify-between gap-3 text-xs">
                                <div>
                                    <div class="flex items-center gap-2">
                                        <span class="font-bold text-slate-900">{{ $inv->tenant->name }}</span>
                                        <span class="px-1.5 py-0.5 rounded-md bg-slate-100 text-[10px] font-mono font-semibold text-slate-700">
                                            Kamar {{ $inv->tenant->room?->room_number ?? '-' }}
                                        </span>
                                    </div>
                                    <p class="text-[11px] text-slate-500 mt-0.5">
                                        Tempo: <span class="font-semibold text-slate-700">{{ $inv->due_date->translatedFormat('d M Y') }}</span>
                                        ({{ $inv->due_date->diffForHumans() }})
                                    </p>
                                </div>
                                <div class="text-right">
                                    <span class="block font-bold text-slate-900">
                                        Rp {{ number_format((float) $inv->total_amount, 0, ',', '.') }}
                                    </span>
                                    <span class="inline-block mt-0.5 px-2 py-0.5 rounded-full text-[10px] font-bold {{ $inv->status->badgeClass() }}">
                                        {{ $inv->status->label() }}
                                    </span>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @endif
            </div>

            <div class="mt-4 pt-3 border-t border-slate-100 text-center">
                <a href="{{ route('owner.invoices.index') }}" class="text-xs font-bold text-[#1E3A8A] hover:underline">
                    Buka Modul Penagihan & WhatsApp Bot →
                </a>
            </div>
        </div>

        {{-- Right: Recent Complaints (Komplain Terkini) --}}
        <div class="bg-white border border-slate-200/90 rounded-2xl p-5 sm:p-6 shadow-xs flex flex-col justify-between">
            <div>
                <div class="flex items-center justify-between border-b border-slate-100 pb-3 mb-4">
                    <div>
                        <h4 class="text-base font-bold text-slate-900">Komplain Penghuni Aktif</h4>
                        <p class="text-xs text-slate-500">Laporan kerusakan fasilitas yang membutuhkan tindak lanjut</p>
                    </div>
                    <a href="{{ route('owner.complaints.index') }}" class="text-xs font-bold text-[#1E3A8A] hover:underline">
                        Lihat Semua
                    </a>
                </div>

                @if ($activeComplaints->isEmpty())
                    <div class="text-center py-8 text-xs text-slate-500">
                        <svg class="h-8 w-8 text-emerald-500 mx-auto mb-2" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14.828 14.828a4 4 0 01-5.656 0M9 10h.01M15 10h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                        Tidak ada laporan komplain yang aktif saat ini. Semua fasilitas aman!
                    </div>
                @else
                    <div class="space-y-3">
                        @foreach ($activeComplaints as $complaint)
                            <div class="rounded-xl border border-slate-100 bg-slate-50/50 p-3 text-xs">
                                <div class="flex items-center justify-between mb-1">
                                    <span class="font-bold text-slate-900 truncate">
                                        {{ $complaint->title }}
                                    </span>
                                    <span class="px-2 py-0.5 rounded-md text-[10px] font-bold shrink-0 {{ $complaint->status->badgeClass() }}">
                                        {{ $complaint->status->label() }}
                                    </span>
                                </div>
                                <p class="text-slate-600 line-clamp-2 text-[11px] leading-relaxed">
                                    {{ $complaint->description }}
                                </p>
                                <div class="mt-2 flex items-center justify-between text-[10px] text-slate-400 pt-1.5 border-t border-slate-200/40">
                                    <span>Oleh: <strong class="text-slate-700">{{ $complaint->tenant->name }}</strong> (Kamar {{ $complaint->room->room_number }})</span>
                                    <span>{{ $complaint->created_at->diffForHumans() }}</span>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @endif
            </div>

            <div class="mt-4 pt-3 border-t border-slate-100 text-center">
                <a href="{{ route('owner.complaints.index') }}" class="text-xs font-bold text-[#1E3A8A] hover:underline">
                    Kelola Seluruh Tiket Komplain →
                </a>
            </div>
        </div>
    </div>
</x-layouts.owner>

