<x-app>
    <x-slot:title>
        Tagihan #{{ $invoice->invoice_code }} - {{ $invoice->property->name }}
    </x-slot:title>

    <div class="min-h-screen bg-slate-50 py-8 px-4 sm:px-6 lg:px-8">
        <div class="max-w-2xl mx-auto">
            {{-- Header Bar --}}
            <div class="flex items-center justify-between pb-6 mb-6 border-b border-slate-200">
                <a href="{{ route('landing') }}" class="flex items-center gap-2 group">
                    <div class="flex h-10 w-10 items-center justify-center rounded-xl bg-[#1E3A8A] text-white shadow-md shadow-indigo-950/20 group-hover:scale-105 transition-transform">
                        <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                        </svg>
                    </div>
                    <div>
                        <span class="text-xl font-black tracking-tight text-[#1E3A8A]">Kostly<span class="text-emerald-500">.</span></span>
                        <span class="block -mt-1 text-[10px] font-bold text-slate-400 uppercase tracking-wider">Payment Portal</span>
                    </div>
                </a>

                @if ($invoice->tenant?->access_token)
                    <a href="{{ route('public.portal', $invoice->tenant->access_token) }}" class="inline-flex items-center gap-1.5 text-xs font-semibold text-[#1E3A8A] hover:underline">
                        <span>Buka Portal Penghuni</span>
                        <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/></svg>
                    </a>
                @endif
            </div>

            {{-- Flash Alert Messages --}}
            @if (session('success'))
                <div class="mb-5 rounded-2xl border border-emerald-200 bg-emerald-50 p-4 text-xs sm:text-sm text-emerald-800 shadow-xs flex items-center justify-between">
                    <div class="flex items-center gap-2.5">
                        <svg class="h-5 w-5 text-emerald-600 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                        <span>{{ session('success') }}</span>
                    </div>
                    <button type="button" onclick="this.parentElement.remove()" class="text-emerald-600 font-bold ml-2">✕</button>
                </div>
            @endif

            @if (session('info'))
                <div class="mb-5 rounded-2xl border border-blue-200 bg-blue-50 p-4 text-xs sm:text-sm text-blue-800 shadow-xs">
                    {{ session('info') }}
                </div>
            @endif

            {{-- MAIN CARD --}}
            <div id="receipt-card" class="bg-white border border-slate-200/90 rounded-3xl p-6 sm:p-8 shadow-xl shadow-slate-900/5">
                {{-- Property & Invoice Code Header --}}
                <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 border-b border-slate-100 pb-5">
                    <div>
                        <span class="inline-block text-[10px] font-bold uppercase tracking-wider text-slate-400">Tagihan Sewa Kost</span>
                        <h1 class="text-xl sm:text-2xl font-black text-slate-900">{{ $invoice->property->name }}</h1>
                        <p class="text-xs text-slate-500 mt-0.5">{{ $invoice->property->address }}</p>
                    </div>

                    <div class="sm:text-right">
                        <span class="text-xs text-slate-400 block font-mono">#{{ $invoice->invoice_code }}</span>
                        <div class="mt-1">
                            <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-bold {{ $invoice->status->badgeClass() }}">
                                @if ($invoice->status === App\Enums\InvoiceStatus::Paid)
                                    <svg class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                                @endif
                                {{ $invoice->status->label() }}
                            </span>
                        </div>
                    </div>
                </div>

                {{-- Status Banner If Paid --}}
                @if ($invoice->status === App\Enums\InvoiceStatus::Paid)
                    <div class="my-6 rounded-2xl bg-emerald-500 text-white p-5 text-center shadow-lg shadow-emerald-950/10">
                        <div class="flex h-12 w-12 items-center justify-center rounded-full bg-white text-emerald-600 mx-auto mb-2 font-bold shadow-xs">
                            <svg class="h-7 w-7" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/></svg>
                        </div>
                        <h2 class="text-xl font-black tracking-tight uppercase">Pembayaran Telah Lunas</h2>
                        <p class="text-xs text-emerald-100 mt-1">
                            Diterima via <strong>{{ $invoice->payment_method ?? 'Metode Digital' }}</strong> pada {{ $invoice->paid_at?->translatedFormat('d F Y - H:i') ?? '-' }} WIB
                        </p>
                    </div>
                @endif

                {{-- Tenant & Room Meta Box --}}
                <div class="my-6 grid grid-cols-2 sm:grid-cols-3 gap-3 rounded-2xl bg-slate-50/70 p-4 text-xs">
                    <div>
                        <span class="text-[11px] text-slate-400 block font-medium">Nama Penghuni</span>
                        <strong class="text-slate-900 text-sm block mt-0.5">{{ $invoice->tenant->name }}</strong>
                    </div>

                    <div>
                        <span class="text-[11px] text-slate-400 block font-medium">Kamar Kost</span>
                        <strong class="text-slate-900 text-sm block mt-0.5">
                            Kamar {{ $invoice->tenant->room?->room_number ?? '-' }}
                            <span class="font-normal text-xs text-slate-500">({{ $invoice->tenant->room?->type }})</span>
                        </strong>
                    </div>

                    <div class="col-span-2 sm:col-span-1">
                        <span class="text-[11px] text-slate-400 block font-medium">Batas Jatuh Tempo</span>
                        <strong class="text-slate-900 text-sm block mt-0.5">{{ $invoice->due_date->translatedFormat('d F Y') }}</strong>
                    </div>
                </div>

                {{-- Price Breakdown --}}
                <div class="border-y border-slate-100 py-4 space-y-2.5 text-xs sm:text-sm">
                    <div class="flex items-center justify-between text-slate-600">
                        <span>Biaya Sewa Kamar (1 Bulan)</span>
                        <span class="font-bold text-slate-900">Rp {{ number_format((float) $invoice->amount, 0, ',', '.') }}</span>
                    </div>

                    @if ($invoice->additional_fee > 0)
                        <div class="flex items-center justify-between text-slate-600">
                            <span>Biaya Tambahan / Listrik Tambahan</span>
                            <span class="font-bold text-slate-900">+ Rp {{ number_format((float) $invoice->additional_fee, 0, ',', '.') }}</span>
                        </div>
                    @endif

                    <div class="pt-2 border-t border-slate-100 flex items-baseline justify-between">
                        <span class="text-sm font-bold text-slate-900">Total Tagihan</span>
                        <span class="text-2xl sm:text-3xl font-black text-[#1E3A8A]">
                            Rp {{ number_format((float) $invoice->total_amount, 0, ',', '.') }}
                        </span>
                    </div>
                </div>

                {{-- IF UNPAID: Payment Channels Section --}}
                @if ($invoice->status !== App\Enums\InvoiceStatus::Paid)
                    <div class="mt-8">
                        <h3 class="text-sm font-bold uppercase tracking-wider text-slate-900 mb-4 flex items-center gap-2">
                            <span class="flex h-5 w-5 items-center justify-center rounded-full bg-[#1E3A8A] text-white text-[10px]">✓</span>
                            <span>Pilih Metode Pembayaran</span>
                        </h3>

                        {{-- Payment Method Option 1: QRIS Dinamis --}}
                        <div class="rounded-2xl border-2 border-emerald-500/80 bg-emerald-50/20 p-5 mb-4">
                            <div class="flex items-center justify-between mb-3">
                                <div class="flex items-center gap-2">
                                    <span class="h-3 w-3 rounded-full bg-emerald-500"></span>
                                    <h4 class="text-sm font-bold text-slate-900">QRIS Instan (Scan Semua Aplikasi)</h4>
                                </div>
                                <span class="rounded-md bg-emerald-100 px-2 py-0.5 text-[10px] font-bold text-emerald-800">Verifikasi Otomatis</span>
                            </div>

                            <div class="flex flex-col sm:flex-row items-center gap-6 pt-2">
                                {{-- Simulated QRIS Code SVG --}}
                                <div class="p-3 bg-white border border-slate-200 rounded-2xl shadow-sm text-center">
                                    <div class="w-40 h-40 bg-slate-900 p-2.5 rounded-xl flex items-center justify-center text-white">
                                        {{-- SVG QR Code representation --}}
                                        <svg class="w-full h-full text-white" fill="currentColor" viewBox="0 0 24 24">
                                            <path d="M2 2h8v8H2V2zm2 2v4h4V4H4zm10-2h8v8h-8V2zm2 2v4h4V4h-4zM2 14h8v8H2v-8zm2 2v4h4v-4H4zm14 0h4v2h-4v-2zm-4 0h2v4h-2v-4zm2 2h2v4h-2v-4zm2 2h2v2h-2v-2zm-6-2h2v2h-2v-2zm0 2h2v2h-2v-2zm4-6h2v2h-2v-2zm-2-2h4v2h-4v-2zm-2 2h2v2h-2v-2zM5 5h2v2H5V5zm12 0h2v2h-2V5zM5 17h2v2H5v-2z"/>
                                        </svg>
                                    </div>
                                    <span class="block mt-1.5 text-[10px] font-bold tracking-widest text-slate-500 uppercase">QRIS NASIONAL</span>
                                </div>

                                <div class="text-xs text-slate-600 space-y-2">
                                    <p class="font-medium text-slate-800">Dapat di-scan menggunakan:</p>
                                    <div class="flex flex-wrap items-center gap-1.5 text-[11px] font-bold">
                                        <span class="px-2 py-0.5 rounded-md bg-white border border-slate-200">GoPay</span>
                                        <span class="px-2 py-0.5 rounded-md bg-white border border-slate-200">OVO</span>
                                        <span class="px-2 py-0.5 rounded-md bg-white border border-slate-200">Dana</span>
                                        <span class="px-2 py-0.5 rounded-md bg-white border border-slate-200">ShopeePay</span>
                                        <span class="px-2 py-0.5 rounded-md bg-white border border-slate-200">BCA Mobile</span>
                                        <span class="px-2 py-0.5 rounded-md bg-white border border-slate-200">Livin</span>
                                    </div>

                                    <p class="text-[11px] text-slate-500 pt-1">
                                        Nominal yang dibayar harus pas <strong>Rp {{ number_format((float) $invoice->total_amount, 0, ',', '.') }}</strong> agar sistem memvalidasi otomatis.
                                    </p>

                                    {{-- Simulated Instant Payment Trigger --}}
                                    <form action="{{ route('public.pay.confirm', $invoice->invoice_code) }}" method="POST" class="pt-2">
                                        @csrf
                                        <input type="hidden" name="payment_method" value="QRIS Dinamis">
                                        <button type="submit" class="w-full sm:w-auto inline-flex items-center justify-center gap-2 rounded-xl bg-emerald-600 px-5 py-2.5 text-xs font-bold text-white shadow-md shadow-emerald-950/20 hover:bg-emerald-700 transition-all">
                                            <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                            <span>Simulasikan Pembayaran Berhasil (QRIS)</span>
                                        </button>
                                    </form>
                                </div>
                            </div>
                        </div>

                        {{-- Payment Method Option 2: Virtual Account --}}
                        <div class="rounded-2xl border border-slate-200 bg-slate-50/50 p-5">
                            <div class="flex items-center justify-between mb-3">
                                <h4 class="text-sm font-bold text-slate-900">Transfer Virtual Account Bank</h4>
                                <span class="text-[10px] text-slate-400">Bebas Antre ATM / Mobile Banking</span>
                            </div>

                            <div class="space-y-3 text-xs">
                                {{-- BCA VA --}}
                                <div class="flex items-center justify-between rounded-xl bg-white p-3 border border-slate-200">
                                    <div>
                                        <span class="font-bold text-blue-900 block">Bank BCA</span>
                                        <span class="font-mono text-sm font-bold text-slate-800">80777{{ substr(preg_replace('/[^0-9]/', '', $invoice->tenant->phone), -8) }}</span>
                                    </div>
                                    <button type="button" onclick="navigator.clipboard.writeText('80777{{ substr(preg_replace('/[^0-9]/', '', $invoice->tenant->phone), -8) }}'); alert('Nomor Virtual Account BCA disalin!');"
                                        class="px-2.5 py-1 rounded-lg border border-slate-200 text-slate-600 hover:bg-slate-50 font-semibold text-[11px]">
                                        Salin
                                    </button>
                                </div>

                                {{-- Mandiri VA --}}
                                <div class="flex items-center justify-between rounded-xl bg-white p-3 border border-slate-200">
                                    <div>
                                        <span class="font-bold text-amber-900 block">Bank Mandiri</span>
                                        <span class="font-mono text-sm font-bold text-slate-800">89508{{ substr(preg_replace('/[^0-9]/', '', $invoice->tenant->phone), -8) }}</span>
                                    </div>
                                    <button type="button" onclick="navigator.clipboard.writeText('89508{{ substr(preg_replace('/[^0-9]/', '', $invoice->tenant->phone), -8) }}'); alert('Nomor Virtual Account Mandiri disalin!');"
                                        class="px-2.5 py-1 rounded-lg border border-slate-200 text-slate-600 hover:bg-slate-50 font-semibold text-[11px]">
                                        Salin
                                    </button>
                                </div>
                            </div>

                            {{-- Confirm Transfer Form --}}
                            <form action="{{ route('public.pay.confirm', $invoice->invoice_code) }}" method="POST" class="mt-4 pt-3 border-t border-slate-200/60">
                                @csrf
                                <input type="hidden" name="payment_method" value="Transfer Virtual Account">
                                <button type="submit" class="w-full inline-flex items-center justify-center gap-2 rounded-xl border border-slate-300 bg-white py-2.5 text-xs font-bold text-slate-700 hover:bg-slate-100 transition-colors">
                                    <span>Konfirmasi Sudah Melakukan Transfer</span>
                                </button>
                            </form>
                        </div>
                    </div>
                @else
                    {{-- IF PAID: Action Buttons (Print Receipt & Portal) --}}
                    <div class="mt-8 pt-6 border-t border-slate-100 flex flex-col sm:flex-row items-center justify-center gap-3">
                        <button type="button" onclick="window.print()" class="w-full sm:w-auto inline-flex items-center justify-center gap-2 rounded-xl border border-slate-300 bg-white px-6 py-2.5 text-xs font-bold text-slate-700 hover:bg-slate-50 transition-colors">
                            <svg class="h-4 w-4 text-slate-500" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/></svg>
                            <span>Cetak Kuitansi / Simpan PDF</span>
                        </button>

                        @if ($invoice->tenant?->access_token)
                            <a href="{{ route('public.portal', $invoice->tenant->access_token) }}" class="w-full sm:w-auto inline-flex items-center justify-center gap-2 rounded-xl bg-[#1E3A8A] px-6 py-2.5 text-xs font-bold text-white hover:bg-[#172554] transition-colors">
                                <span>Buka Portal Penghuni Saya</span>
                                <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"/></svg>
                            </a>
                        @endif
                    </div>
                @endif
            </div>

            {{-- Support & Security Note --}}
            <div class="mt-6 text-center text-xs text-slate-400 space-y-1">
                <p class="inline-flex items-center gap-1.5">
                    <svg class="h-3.5 w-3.5 text-emerald-500" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/></svg>
                    <span>Transaksi ini diamankan secara enkripsi melalui Kostly Payment Gateway.</span>
                </p>
                @if ($invoice->property->user?->phone)
                    <p>
                        Butuh bantuan? Hubungi pemilik kost via WhatsApp:
                        <a href="https://wa.me/{{ preg_replace('/[^0-9]/', '', (str_starts_with($invoice->property->user->phone, '0') ? '62' . substr($invoice->property->user->phone, 1) : $invoice->property->user->phone)) }}"
                            target="_blank" class="font-bold text-[#1E3A8A] hover:underline">
                            {{ $invoice->property->user->phone }}
                        </a>
                    </p>
                @endif
            </div>
        </div>
    </div>
</x-app>
