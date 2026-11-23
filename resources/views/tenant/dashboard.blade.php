<x-layouts.tenant>
    {{-- Welcome Banner --}}
    <div class="rounded-3xl bg-gradient-to-r from-[#1E3A8A] via-indigo-900 to-slate-900 p-6 sm:p-8 text-white shadow-xl mb-6">
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
            <div>
                <span class="inline-flex items-center gap-1.5 rounded-full bg-emerald-500/20 border border-emerald-400/30 px-3 py-1 text-xs font-bold text-emerald-300 mb-2">
                    <span class="h-2 w-2 rounded-full bg-emerald-400 animate-pulse"></span>
                    Portal Penyewa Aktif
                </span>
                <h1 class="text-2xl sm:text-3xl font-extrabold tracking-tight">Halo, {{ $user->name }}! 👋</h1>
                <p class="text-xs sm:text-sm text-slate-300 mt-1">
                    Kelola kamar sewa, cek riwayat pembayaran bulanan, dan sampaikan komplain perbaikan kamar.
                </p>
            </div>
            @if($tenant && $tenant->property?->user?->phone)
                <div>
                    <a href="https://wa.me/{{ preg_replace('/[^0-9]/', '', $tenant->property->user->phone) }}?text={{ urlencode('Halo pengelola ' . $tenant->property->name . ', saya penghuni Kamar ' . ($tenant->room?->room_number ?? '')) }}" target="_blank" class="inline-flex items-center gap-2 rounded-2xl bg-emerald-500 hover:bg-emerald-600 px-4 py-2.5 text-xs font-bold text-white shadow-md transition-colors">
                        <span>Hubungi Pemilik Kost</span>
                    </a>
                </div>
            @endif
        </div>
    </div>

    {{-- Unpaid Invoice Alert (If Any) --}}
    @if($activeInvoice)
        <div class="mb-6 rounded-2xl border border-amber-300 bg-amber-50 p-5 shadow-xs flex flex-col sm:flex-row sm:items-center justify-between gap-4">
            <div class="flex items-start gap-3">
                <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-xl bg-amber-100 text-amber-700">
                    <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
                </div>
                <div>
                    <div class="flex items-center gap-2">
                        <span class="text-xs font-bold uppercase tracking-wider text-amber-800">Tagihan Sewa Perlu Dibayar</span>
                        <span class="text-xs font-bold text-amber-900">#{{ $activeInvoice->invoice_code }}</span>
                    </div>
                    <div class="text-xl font-black text-amber-950 mt-0.5">
                        Rp {{ number_format((float) $activeInvoice->total_amount, 0, ',', '.') }}
                    </div>
                    <p class="text-xs text-amber-700 mt-0.5">
                        Jatuh tempo: <strong>{{ \Carbon\Carbon::parse($activeInvoice->due_date)->translatedFormat('d F Y') }}</strong>
                    </p>
                </div>
            </div>
            <div>
                <a href="{{ route('public.pay', $activeInvoice->invoice_code) }}" class="inline-flex items-center gap-2 rounded-xl bg-amber-600 hover:bg-amber-700 px-5 py-2.5 text-xs sm:text-sm font-bold text-white shadow-sm transition-all">
                    <span>Bayar Sekarang</span>
                    <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"/></svg>
                </a>
            </div>
        </div>
    @endif

    {{-- Info Kamar Saya & Status Sewa --}}
    @if($tenant && $tenant->room)
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
            {{-- Kartu Kamar --}}
            <div class="md:col-span-2 rounded-3xl border border-slate-200 bg-white p-6 sm:p-8 shadow-xs">
                <div class="flex items-center justify-between pb-4 border-b border-slate-100 mb-5">
                    <div>
                        <span class="text-xs font-bold uppercase tracking-wider text-slate-400">Informasi Kamar Aktif</span>
                        <h2 class="text-2xl font-black text-indigo-950 mt-0.5">
                            Kamar {{ $tenant->room->room_number }}
                            <span class="text-base font-semibold text-slate-600">({{ $tenant->room->type }})</span>
                        </h2>
                    </div>
                    <span class="rounded-full bg-emerald-100 border border-emerald-300 px-3 py-1 text-xs font-bold text-emerald-800">
                        Aktif Terisi
                    </span>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 text-xs sm:text-sm">
                    <div>
                        <span class="text-slate-400 font-medium">Properti Kost:</span>
                        <div class="font-bold text-slate-800 mt-0.5">{{ $tenant->property->name }}</div>
                        <p class="text-xs text-slate-500 mt-0.5">{{ $tenant->property->address }}</p>
                    </div>

                    <div>
                        <span class="text-slate-400 font-medium">Tarif Sewa Bulanan:</span>
                        <div class="font-extrabold text-[#1E3A8A] text-base mt-0.5">
                            Rp {{ number_format((float) $tenant->room->price, 0, ',', '.') }} / bulan
                        </div>
                    </div>

                    <div>
                        <span class="text-slate-400 font-medium">Tanggal Mulai Menghuni:</span>
                        <div class="font-bold text-slate-800 mt-0.5">
                            {{ $tenant->start_date ? $tenant->start_date->translatedFormat('d F Y') : '-' }}
                        </div>
                    </div>

                    <div>
                        <span class="text-slate-400 font-medium">Jatuh Tempo Bulanan:</span>
                        <div class="font-bold text-slate-800 mt-0.5">
                            Tiap tanggal {{ $tenant->due_day }}
                        </div>
                    </div>
                </div>

                <div class="mt-6 pt-5 border-t border-slate-100 flex flex-wrap items-center gap-3">
                    <a href="{{ route('tenant.invoices.index') }}" class="rounded-xl bg-slate-100 hover:bg-slate-200 px-4 py-2 text-xs font-bold text-slate-700 transition-colors">
                        Lihat Tagihan Saya &rarr;
                    </a>
                    <a href="{{ route('tenant.complaints.index') }}" class="rounded-xl border border-slate-200 hover:bg-slate-50 px-4 py-2 text-xs font-bold text-slate-700 transition-colors">
                        Lapor Kendala Kamar &rarr;
                    </a>
                </div>
            </div>

            {{-- Ringkasan Akun Penyewa --}}
            <div class="rounded-3xl border border-slate-200 bg-white p-6 shadow-xs flex flex-col justify-between">
                <div>
                    <h3 class="text-sm font-bold uppercase tracking-wider text-slate-400 mb-4">Profil Penghuni</h3>

                    <div class="space-y-3 text-xs sm:text-sm">
                        <div>
                            <span class="text-slate-400">Nama:</span>
                            <div class="font-bold text-slate-800">{{ $tenant->name }}</div>
                        </div>
                        <div>
                            <span class="text-slate-400">WhatsApp / HP:</span>
                            <div class="font-bold text-slate-800">{{ $tenant->phone }}</div>
                        </div>
                        @if($tenant->emergency_contact)
                            <div>
                                <span class="text-slate-400">Kontak Darurat:</span>
                                <div class="font-bold text-slate-800">{{ $tenant->emergency_contact }}</div>
                            </div>
                        @endif
                        <div>
                            <span class="text-slate-400">Kode Akses Magic Link:</span>
                            <div class="font-mono text-xs text-indigo-700 bg-indigo-50 p-2 rounded-lg mt-1 break-all">
                                {{ $tenant->access_token }}
                            </div>
                        </div>
                    </div>
                </div>

                <div class="mt-6 pt-4 border-t border-slate-100">
                    <a href="{{ route('public.portal', $tenant->access_token) }}" target="_blank" class="w-full inline-flex items-center justify-center gap-1.5 rounded-xl border border-indigo-200 bg-indigo-50 hover:bg-indigo-100 py-2 text-xs font-bold text-[#1E3A8A] transition-colors">
                        <span>Buka Magic Link Publik</span>
                        <svg class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/></svg>
                    </a>
                </div>
            </div>
        </div>
    @else
        {{-- Jika belum ada kamar aktif yang terhubung --}}
        <div class="mb-8 rounded-3xl border border-dashed border-slate-300 bg-white p-8 text-center">
            <svg class="mx-auto h-12 w-12 text-slate-300" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/></svg>
            <h3 class="mt-3 text-base font-bold text-slate-800">Belum Ada Kamar Aktif</h3>
            <p class="text-xs sm:text-sm text-slate-500 mt-1 max-w-md mx-auto">
                Anda belum terhubung sebagai penyewa aktif di properti kost. Ajukan sewa kamar dari katalog publik atau tunggu persetujuan dari pemilik kost.
            </p>
            <a href="{{ route('landing') }}#daftar-kost" class="mt-4 inline-flex items-center gap-1.5 rounded-xl bg-[#1E3A8A] px-4 py-2 text-xs font-bold text-white shadow-xs">
                <span>Jelajahi Pilihan Kost</span>
                <svg class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"/></svg>
            </a>
        </div>
    @endif

    {{-- Riwayat Pengajuan Sewa (Bookings) --}}
    @if(isset($recentBookings) && $recentBookings->isNotEmpty())
        <div class="rounded-3xl border border-slate-200 bg-white p-6 sm:p-8 shadow-xs mb-8">
            <h3 class="text-base font-bold text-slate-900 mb-4">Riwayat Pengajuan Sewa Kamar</h3>
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-slate-100 text-xs sm:text-sm">
                    <thead>
                        <tr class="text-slate-400 text-left uppercase text-[10px] tracking-wider">
                            <th class="py-2.5">Properti & Kamar</th>
                            <th class="py-2.5">Tanggal Masuk</th>
                            <th class="py-2.5">Durasi</th>
                            <th class="py-2.5">Total Harga</th>
                            <th class="py-2.5 text-center">Status</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-50">
                        @foreach($recentBookings as $b)
                            <tr>
                                <td class="py-3 font-semibold text-slate-800">
                                    {{ $b->property->name }} — Kamar {{ $b->room->room_number }}
                                </td>
                                <td class="py-3 text-slate-600">{{ $b->start_date->translatedFormat('d M Y') }}</td>
                                <td class="py-3 text-slate-600">{{ $b->duration_months }} Bulan</td>
                                <td class="py-3 font-extrabold text-[#1E3A8A]">Rp {{ number_format((float) $b->total_price, 0, ',', '.') }}</td>
                                <td class="py-3 text-center">
                                    @if($b->status === \App\Enums\BookingStatus::Pending)
                                        <span class="inline-flex items-center gap-1 rounded-full bg-amber-50 border border-amber-200 px-2.5 py-0.5 text-xs font-bold text-amber-700">
                                            Menunggu Konfirmasi
                                        </span>
                                    @elseif($b->status === \App\Enums\BookingStatus::Approved)
                                        <span class="inline-flex items-center gap-1 rounded-full bg-emerald-50 border border-emerald-200 px-2.5 py-0.5 text-xs font-bold text-emerald-700">
                                            ✓ Disetujui
                                        </span>
                                    @else
                                        <span class="inline-flex items-center gap-1 rounded-full bg-rose-50 border border-rose-200 px-2.5 py-0.5 text-xs font-bold text-rose-700">
                                            ✕ Ditolak
                                        </span>
                                    @endif
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    @endif
</x-layouts.tenant>
