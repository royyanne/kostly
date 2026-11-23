<x-app>
    <x-slot:title>
        Portal Penghuni - {{ $tenant->name }} ({{ $tenant->property->name }})
    </x-slot:title>

    <div class="min-h-screen bg-slate-50 py-6 sm:py-10 px-4 sm:px-6 lg:px-8">
        <div class="max-w-3xl mx-auto space-y-6">
            {{-- Header Top Bar --}}
            <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 pb-5 border-b border-slate-200">
                <a href="{{ route('landing') }}" class="flex items-center gap-2.5 group">
                    <div class="flex h-10 w-10 items-center justify-center rounded-xl bg-[#1E3A8A] text-white shadow-md shadow-indigo-950/20 group-hover:scale-105 transition-transform">
                        <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                        </svg>
                    </div>
                    <div>
                        <span class="text-xl font-black tracking-tight text-[#1E3A8A]">Kostly<span class="text-emerald-500">.</span></span>
                        <span class="block -mt-1 text-[10px] font-bold text-slate-400 uppercase tracking-wider">Portal Mandiri Anak Kost</span>
                    </div>
                </a>

                @if ($tenant->property->user?->phone)
                    <div class="text-xs text-slate-600 flex items-center gap-2">
                        <span class="text-slate-400">Pengelola Kost:</span>
                        <a href="https://wa.me/{{ preg_replace('/[^0-9]/', '', (str_starts_with($tenant->property->user->phone, '0') ? '62' . substr($tenant->property->user->phone, 1) : $tenant->property->user->phone)) }}"
                            target="_blank" class="inline-flex items-center gap-1.5 font-bold text-emerald-700 bg-emerald-50 px-3 py-1.5 rounded-xl border border-emerald-200/60 hover:bg-emerald-100 transition-colors">
                            <svg class="h-3.5 w-3.5" fill="currentColor" viewBox="0 0 24 24"><path d="M12.04 2C6.58 2 2.13 6.45 2.13 11.91C2.13 13.66 2.59 15.36 3.45 16.86L2.05 22L7.3 20.62C8.75 21.41 10.38 21.83 12.04 21.83C17.5 21.83 21.95 17.38 21.95 11.92C21.95 9.27 20.92 6.78 19.05 4.91C17.18 3.03 14.69 2 12.04 2M12.05 3.67C14.25 3.67 16.31 4.53 17.87 6.09C19.42 7.65 20.28 9.72 20.28 11.92C20.28 16.46 16.58 20.15 12.04 20.15C10.56 20.15 9.11 19.76 7.85 19.02L7.55 18.84L4.42 19.66L5.26 16.61L5.06 16.29C4.24 14.99 3.81 13.47 3.81 11.91C3.81 7.37 7.5 3.67 12.05 3.67Z"/></svg>
                            <span>Hubungi Pemilik via WA</span>
                        </a>
                    </div>
                @endif
            </div>

            {{-- Flash Alert Messages --}}
            @if (session('success'))
                <div class="rounded-2xl border border-emerald-200 bg-emerald-50 p-4 text-xs sm:text-sm text-emerald-800 shadow-xs flex items-center justify-between">
                    <div class="flex items-center gap-2.5">
                        <svg class="h-5 w-5 text-emerald-600 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                        <span>{{ session('success') }}</span>
                    </div>
                    <button type="button" onclick="this.parentElement.remove()" class="text-emerald-600 font-bold ml-2">✕</button>
                </div>
            @endif

            {{-- Welcome & Tenant Profile Overview Card --}}
            <div class="bg-white border border-slate-200/90 rounded-3xl p-6 sm:p-8 shadow-xs">
                <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 border-b border-slate-100 pb-5">
                    <div>
                        <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full bg-indigo-50 text-[11px] font-bold text-[#1E3A8A] mb-2">
                            <span class="h-1.5 w-1.5 rounded-full bg-emerald-500"></span>
                            Akses Portal Mandiri (Tanpa Login)
                        </span>
                        <h1 class="text-2xl sm:text-3xl font-black text-slate-900 tracking-tight">Halo, {{ $tenant->name }}! 👋</h1>
                        <p class="text-xs sm:text-sm text-slate-500 mt-1">
                            {{ $tenant->property->name }} • {{ $tenant->property->address }}
                        </p>
                    </div>

                    <div class="sm:text-right">
                        <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-xl bg-blue-50 text-blue-800 font-bold text-sm">
                            Kamar {{ $tenant->room?->room_number ?? '-' }}
                        </span>
                        <span class="block text-[11px] text-slate-500 mt-1 font-medium">{{ $tenant->room?->type }}</span>
                    </div>
                </div>

                {{-- Detail Data Profil Penghuni & Kamar --}}
                <div class="mt-5 grid grid-cols-2 sm:grid-cols-4 gap-3 text-xs">
                    <div class="rounded-2xl bg-slate-50 p-3.5">
                        <span class="text-[11px] text-slate-400 block font-medium">Biaya Sewa</span>
                        <strong class="text-slate-900 text-sm block mt-0.5">
                            Rp {{ number_format((float) ($tenant->room?->price ?? 0), 0, ',', '.') }}<span class="text-[10px] text-slate-400 font-normal">/bln</span>
                        </strong>
                    </div>

                    <div class="rounded-2xl bg-slate-50 p-3.5">
                        <span class="text-[11px] text-slate-400 block font-medium">Jatuh Tempo Bulanan</span>
                        <strong class="text-slate-900 text-sm block mt-0.5">
                            Setiap Tgl {{ $tenant->due_day }}
                        </strong>
                    </div>

                    <div class="rounded-2xl bg-slate-50 p-3.5">
                        <span class="text-[11px] text-slate-400 block font-medium">No. Telepon / WA</span>
                        <strong class="text-slate-900 text-xs sm:text-sm block mt-0.5 font-mono truncate">
                            {{ $tenant->phone }}
                        </strong>
                    </div>

                    <div class="rounded-2xl bg-slate-50 p-3.5">
                        <span class="text-[11px] text-slate-400 block font-medium">Kontak Darurat</span>
                        <strong class="text-slate-900 text-xs block mt-0.5 truncate" title="{{ $tenant->emergency_contact ?? '-' }}">
                            {{ $tenant->emergency_contact ?? '-' }}
                        </strong>
                    </div>
                </div>
            </div>

            {{-- ACTIVE INVOICE ALERT (If Unpaid / Overdue) --}}
            @if ($activeInvoice)
                <div class="bg-gradient-to-r from-amber-500/10 via-amber-500/5 to-transparent border-2 border-amber-400/80 rounded-3xl p-6 sm:p-7 shadow-xs">
                    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
                        <div>
                            <div class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-full text-[10px] font-bold {{ $activeInvoice->status->badgeClass() }} mb-2">
                                {{ $activeInvoice->status->label() }}
                            </div>
                            <h2 class="text-lg font-bold text-slate-900">Tagihan Sewa Bulan Ini Menunggu Pembayaran</h2>
                            <p class="text-xs text-slate-600 mt-1">
                                Kode: <strong class="font-mono">#{{ $activeInvoice->invoice_code }}</strong> •
                                Batas Bayar: <strong>{{ $activeInvoice->due_date->translatedFormat('d F Y') }}</strong>
                                ({{ $activeInvoice->due_date->diffForHumans() }})
                            </p>
                            <div class="mt-3 text-2xl font-black text-[#1E3A8A]">
                                Rp {{ number_format((float) $activeInvoice->total_amount, 0, ',', '.') }}
                            </div>
                        </div>

                        <div>
                            <a href="{{ route('public.pay', $activeInvoice->invoice_code) }}"
                                class="inline-flex items-center justify-center gap-2 rounded-2xl bg-[#1E3A8A] px-6 py-3.5 text-xs sm:text-sm font-bold text-white shadow-lg shadow-indigo-950/20 hover:bg-[#172554] transition-all">
                                <span>Bayar Sekarang (QRIS / VA)</span>
                                <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"/></svg>
                            </a>
                        </div>
                    </div>
                </div>
            @else
                <div class="rounded-3xl border border-emerald-200 bg-emerald-50/70 p-5 flex items-center gap-3.5 shadow-xs">
                    <div class="flex h-11 w-11 shrink-0 items-center justify-center rounded-2xl bg-emerald-500 text-white font-bold shadow-xs">
                        <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                    </div>
                    <div>
                        <h2 class="text-sm font-bold text-emerald-950">Semua Tagihan Anda Telah Lunas! ✨</h2>
                        <p class="text-xs text-emerald-800 mt-0.5">Terima kasih telah membayar sewa tepat waktu. Kuitansi resmi Anda dapat dicetak di tabel riwayat di bawah.</p>
                    </div>
                </div>
            @endif

            {{-- BILLING HISTORY & KUITANSI SECTION --}}
            <div class="bg-white border border-slate-200/90 rounded-3xl p-6 sm:p-7 shadow-xs">
                <div class="flex items-center justify-between border-b border-slate-100 pb-4 mb-4">
                    <div>
                        <h2 class="text-base font-bold text-slate-900">Riwayat Tagihan & Kuitansi Pembayaran</h2>
                        <p class="text-xs text-slate-400">Seluruh riwayat tagihan sewa dan kuitansi resmi yang dapat Anda unduh atau cetak</p>
                    </div>
                </div>

                @if ($tenant->invoices->isEmpty())
                    <p class="text-xs text-slate-500 py-6 text-center">Belum ada riwayat tagihan yang tercatat.</p>
                @else
                    <div class="divide-y divide-slate-100 text-xs">
                        @foreach ($tenant->invoices as $inv)
                            <div class="py-4 flex flex-col sm:flex-row sm:items-center justify-between gap-3">
                                <div class="min-w-0">
                                    <div class="flex items-center gap-2">
                                        <span class="font-mono font-bold text-slate-900">#{{ $inv->invoice_code }}</span>
                                        <span class="px-2.5 py-0.5 rounded-full text-[10px] font-bold {{ $inv->status->badgeClass() }}">
                                            {{ $inv->status->label() }}
                                        </span>
                                    </div>
                                    <p class="text-slate-500 mt-1 text-[11px]">
                                        Jatuh Tempo: {{ $inv->due_date->translatedFormat('d F Y') }}
                                        @if ($inv->paid_at)
                                            • <span class="text-emerald-700 font-medium">Lunas: {{ $inv->paid_at->translatedFormat('d/m/Y') }} ({{ $inv->payment_method ?? 'Digital' }})</span>
                                        @endif
                                    </p>
                                </div>

                                <div class="flex items-center justify-between sm:justify-end gap-3 shrink-0">
                                    <span class="text-sm font-black text-slate-900">
                                        Rp {{ number_format((float) $inv->total_amount, 0, ',', '.') }}
                                    </span>

                                    @if ($inv->status === App\Enums\InvoiceStatus::Paid)
                                        {{-- Tombol Lihat / Cetak Kuitansi Resmi --}}
                                        <a href="{{ route('public.portal.receipt', [$tenant->access_token, $inv->invoice_code]) }}"
                                            class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-xl border border-emerald-300 bg-emerald-50 text-emerald-800 font-bold hover:bg-emerald-100 transition-colors text-[11px] shadow-2xs">
                                            <svg class="h-3.5 w-3.5 text-emerald-600" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                                            <span>Lihat / Cetak Kuitansi</span>
                                        </a>
                                    @else
                                        {{-- Tombol Bayar Sekarang --}}
                                        <a href="{{ route('public.pay', $inv->invoice_code) }}"
                                            class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-xl bg-[#1E3A8A] text-white font-bold hover:bg-[#172554] transition-colors text-[11px] shadow-2xs">
                                            <span>Bayar Sekarang</span>
                                            <svg class="h-3 w-3" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                                        </a>
                                    @endif
                                </div>
                            </div>
                        @endforeach
                    </div>
                @endif
            </div>

            {{-- COMPLAINT SUBMISSION & TRACKING SECTION --}}
            <div class="bg-white border border-slate-200/90 rounded-3xl p-6 sm:p-8 shadow-xs">
                <div class="border-b border-slate-100 pb-4 mb-5">
                    <h2 class="text-base sm:text-lg font-bold text-slate-900">Lapor Kerusakan & Komplain Kamar</h2>
                    <p class="text-xs text-slate-500 mt-0.5">Ada masalah pada AC, air, lampu, kunci, atau fasilitas kost? Kirimkan laporan langsung ke pemilik kost.</p>
                </div>

                {{-- Complaint Form With File Upload --}}
                <form action="{{ route('public.portal.complaint', $tenant->access_token) }}" method="POST" enctype="multipart/form-data" class="space-y-4 mb-8">
                    @csrf

                    <div>
                        <label for="complaint-title" class="block text-xs font-semibold uppercase tracking-wider text-slate-700">
                            Judul Masalah / Kerusakan <span class="text-rose-500">*</span>
                        </label>
                        <input type="text" id="complaint-title" name="title" required value="{{ old('title') }}"
                            class="mt-1.5 block w-full rounded-xl border border-slate-300 px-3.5 py-2.5 text-xs sm:text-sm text-slate-900 placeholder-slate-400 focus:border-[#1E3A8A] focus:outline-hidden"
                            placeholder="Misal: AC kamar 101 tidak dingin, kran wastafel bocor, lampu mati">
                        @error('title')
                            <p class="mt-1 text-xs text-rose-600 font-medium">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="complaint-desc" class="block text-xs font-semibold uppercase tracking-wider text-slate-700">
                            Rincian Kendala / Kerusakan <span class="text-rose-500">*</span>
                        </label>
                        <textarea id="complaint-desc" name="description" rows="3" required
                            class="mt-1.5 block w-full rounded-xl border border-slate-300 px-3.5 py-2.5 text-xs sm:text-sm text-slate-900 placeholder-slate-400 focus:border-[#1E3A8A] focus:outline-hidden"
                            placeholder="Jelaskan detail kendala, kapan mulai terjadi, atau bagian mana yang membutuhkan perbaikan...">{{ old('description') }}</textarea>
                        @error('description')
                            <p class="mt-1 text-xs text-rose-600 font-medium">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="complaint-photo" class="block text-xs font-semibold uppercase tracking-wider text-slate-700">
                            Foto Bukti Kerusakan <span class="text-slate-400 font-normal lowercase">(opsional, maks 5 MB)</span>
                        </label>
                        <input type="file" id="complaint-photo" name="photo" accept="image/*"
                            class="mt-1.5 block w-full text-xs text-slate-500 file:mr-4 file:py-2 file:px-4 file:rounded-xl file:border-0 file:text-xs file:font-semibold file:bg-indigo-50 file:text-[#1E3A8A] hover:file:bg-indigo-100">
                        @error('photo')
                            <p class="mt-1 text-xs text-rose-600 font-medium">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="pt-2">
                        <button type="submit" class="inline-flex items-center gap-2 rounded-xl bg-[#1E3A8A] px-5 py-2.5 text-xs sm:text-sm font-bold text-white shadow-md shadow-indigo-950/20 hover:bg-[#172554] transition-colors">
                            <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8"/></svg>
                            <span>Kirim Laporan Kerusakan</span>
                        </button>
                    </div>
                </form>

                {{-- Previous Complaints List --}}
                <div class="pt-6 border-t border-slate-100">
                    <h3 class="text-xs font-bold uppercase tracking-wider text-slate-500 mb-3 flex items-center justify-between">
                        <span>Riwayat Laporan Kerusakan Anda</span>
                        <span class="text-slate-400 font-normal lowercase">({{ $tenant->complaints->count() }} laporan)</span>
                    </h3>

                    @if ($tenant->complaints->isEmpty())
                        <p class="text-xs text-slate-400 italic py-3 text-center">Belum ada laporan kerusakan yang pernah Anda kirimkan.</p>
                    @else
                        <div class="space-y-3">
                            @foreach ($tenant->complaints as $comp)
                                <div class="rounded-2xl border border-slate-100 bg-slate-50/70 p-4 text-xs hover:border-slate-200 transition-colors">
                                    <div class="flex items-center justify-between mb-2">
                                        <h4 class="font-bold text-slate-900 text-sm">{{ $comp->title }}</h4>
                                        <span class="px-2.5 py-0.5 rounded-full text-[10px] font-bold {{ $comp->status->badgeClass() }}">
                                            {{ $comp->status->label() }}
                                        </span>
                                    </div>
                                    <p class="text-slate-600 text-xs leading-relaxed">{{ $comp->description }}</p>

                                    @if ($comp->photo_path)
                                        <div class="mt-2.5">
                                            <a href="{{ asset('storage/' . $comp->photo_path) }}" target="_blank"
                                                class="inline-flex items-center gap-1.5 text-[11px] font-bold text-indigo-700 bg-indigo-50 px-2.5 py-1 rounded-lg hover:bg-indigo-100 transition-colors">
                                                <svg class="h-3.5 w-3.5 text-indigo-600" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                                                <span>Lihat Foto Lampiran</span>
                                            </a>
                                        </div>
                                    @endif

                                    <div class="mt-3 pt-2 border-t border-slate-200/50 flex items-center justify-between text-[11px] text-slate-400">
                                        <span>Dilaporkan: {{ $comp->created_at->translatedFormat('d M Y - H:i') }} WIB</span>
                                        <span>Kamar {{ $comp->room->room_number }}</span>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @endif
                </div>
            </div>

            {{-- Footer Note --}}
            <div class="text-center text-xs text-slate-400 py-4">
                <p>&copy; {{ date('Y') }} Kostly Indonesia • Portal Penghuni Properti {{ $tenant->property->name }}</p>
            </div>
        </div>
    </div>
</x-app>
