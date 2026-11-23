<x-layouts.tenant>
    {{-- Header --}}
    <div class="mt-2 mb-6 flex flex-col sm:flex-row sm:items-center justify-between gap-4 bg-white border border-slate-200/90 rounded-2xl p-5 sm:p-6 shadow-xs">
        <div>
            <h1 class="text-xl font-bold text-slate-900 tracking-tight">Komplain & Kerusakan Fasilitas</h1>
            <p class="text-xs sm:text-sm text-slate-500 mt-1">
                Laporkan kerusakan fasilitas kamar (AC, kran air, lampu, wifi) langsung ke pemilik kost untuk perbaikan.
            </p>
        </div>
        @if($tenant)
            <div class="text-xs text-slate-600 sm:text-right">
                <div>Properti: <strong>{{ $tenant->property->name }}</strong></div>
                <div>Kamar: <strong>Kamar {{ $tenant->room?->room_number ?? '-' }}</strong></div>
            </div>
        @endif
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
        {{-- Form Lapor Komplain Baru --}}
        <div class="lg:col-span-1">
            <div class="rounded-3xl border border-slate-200 bg-white p-6 shadow-xs">
                <div class="flex items-center gap-2 mb-4 pb-3 border-b border-slate-100">
                    <div class="flex h-8 w-8 items-center justify-center rounded-lg bg-rose-50 text-rose-600">
                        <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
                    </div>
                    <div>
                        <h2 class="text-base font-bold text-slate-900">Kirim Laporan Baru</h2>
                        <p class="text-xs text-slate-400">Pemilik kost akan segera menerima notifikasi</p>
                    </div>
                </div>

                @if($tenant)
                    <form action="{{ route('tenant.complaints.store') }}" method="POST" class="space-y-4">
                        @csrf

                        <div>
                            <label for="title" class="block text-xs font-bold text-slate-700 mb-1">Judul Kendala <span class="text-rose-500">*</span></label>
                            <input type="text" id="title" name="title" value="{{ old('title') }}" required placeholder="Contoh: AC Kamar Kurang Dingin" class="w-full rounded-xl border border-slate-200 bg-slate-50 px-3.5 py-2 text-xs sm:text-sm text-slate-800 focus:border-indigo-400 focus:bg-white focus:outline-none">
                            @error('title')
                                <span class="text-[11px] text-rose-600 mt-1 block">{{ $message }}</span>
                            @enderror
                        </div>

                        <div>
                            <label for="description" class="block text-xs font-bold text-slate-700 mb-1">Rincian Kerusakan <span class="text-rose-500">*</span></label>
                            <textarea id="description" name="description" rows="4" required placeholder="Jelaskan sejak kapan kendala terjadi dan kondisi fasilitas saat ini..." class="w-full rounded-xl border border-slate-200 bg-slate-50 px-3.5 py-2 text-xs sm:text-sm text-slate-800 focus:border-indigo-400 focus:bg-white focus:outline-none">{{ old('description') }}</textarea>
                            @error('description')
                                <span class="text-[11px] text-rose-600 mt-1 block">{{ $message }}</span>
                            @enderror
                        </div>

                        <button type="submit" class="w-full rounded-xl bg-[#1E3A8A] hover:bg-[#172554] py-2.5 text-xs sm:text-sm font-bold text-white shadow-xs transition-colors flex items-center justify-center gap-1.5">
                            <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8"/></svg>
                            <span>Kirim Laporan Komplain</span>
                        </button>
                    </form>
                @else
                    <div class="rounded-2xl border border-dashed border-slate-200 p-6 text-center text-xs text-slate-500">
                        Anda belum terhubung ke kamar kost aktif.
                    </div>
                @endif
            </div>
        </div>

        {{-- Riwayat Komplain Saya --}}
        <div class="lg:col-span-2">
            <div class="rounded-3xl border border-slate-200 bg-white p-6 shadow-xs">
                <h2 class="text-base font-bold text-slate-900 mb-4">Riwayat Komplain Anda</h2>

                @if($complaints->isEmpty())
                    <div class="py-16 text-center">
                        <svg class="mx-auto h-12 w-12 text-slate-300" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                        <h4 class="mt-3 text-sm font-bold text-slate-700">Tidak Ada Komplain Aktif</h4>
                        <p class="text-xs text-slate-400 mt-1">Semua fasilitas kamar dalam keadaan baik.</p>
                    </div>
                @else
                    <div class="space-y-4">
                        @foreach($complaints as $c)
                            <div class="rounded-2xl border border-slate-200 p-4 sm:p-5 hover:border-slate-300 transition-colors bg-slate-50/40">
                                <div class="flex items-start justify-between gap-3 mb-2">
                                    <div>
                                        <h3 class="font-bold text-slate-900 text-sm sm:text-base">{{ $c->title }}</h3>
                                        <div class="text-[11px] text-slate-400 mt-0.5">
                                            Dilaporkan pada {{ $c->created_at->translatedFormat('d F Y, H:i') }} WIB
                                        </div>
                                    </div>
                                    <div>
                                        @if($c->status === \App\Enums\ComplaintStatus::Pending)
                                            <span class="inline-flex items-center gap-1 rounded-full bg-amber-50 border border-amber-200 px-2.5 py-0.5 text-xs font-bold text-amber-700">
                                                <span class="h-1.5 w-1.5 rounded-full bg-amber-500 animate-pulse"></span>
                                                Menunggu Respon
                                            </span>
                                        @elseif($c->status === \App\Enums\ComplaintStatus::InProgress)
                                            <span class="inline-flex items-center gap-1 rounded-full bg-blue-50 border border-blue-200 px-2.5 py-0.5 text-xs font-bold text-blue-700">
                                                Sedang Diproses
                                            </span>
                                        @else
                                            <span class="inline-flex items-center gap-1 rounded-full bg-emerald-50 border border-emerald-200 px-2.5 py-0.5 text-xs font-bold text-emerald-700">
                                                ✓ Selesai Diperbaiki
                                            </span>
                                        @endif
                                    </div>
                                </div>
                                <p class="text-xs sm:text-sm text-slate-600 leading-relaxed whitespace-pre-line">
                                    {{ $c->description }}
                                </p>

                                @if($c->owner_response)
                                    <div class="mt-3.5 p-3 rounded-xl bg-emerald-50 border border-emerald-200 text-xs text-emerald-900">
                                        <div class="flex items-center gap-1 font-bold text-emerald-800 mb-1">
                                            <svg class="h-4 w-4 text-emerald-600" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                            <span>Balasan / Tindak Lanjut Pemilik Kost:</span>
                                        </div>
                                        <p class="text-slate-700 leading-relaxed whitespace-pre-line">{{ $c->owner_response }}</p>
                                    </div>
                                @endif
                            </div>
                        @endforeach
                    </div>
                @endif
            </div>
        </div>
    </div>
</x-layouts.tenant>
