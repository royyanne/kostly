<x-app>
    <x-slot:title>
        Kuitansi Pembayaran #{{ $invoice->invoice_code }} - {{ $tenant->name }}
    </x-slot:title>

    <style>
        @media print {
            body {
                background: white !important;
                color: black !important;
            }
            .no-print {
                display: none !important;
            }
            .print-border {
                border: 1px solid #cbd5e1 !important;
                box-shadow: none !important;
            }
            .print-p-0 {
                padding: 0 !important;
            }
        }
    </style>

    <div class="min-h-screen bg-slate-100 py-6 sm:py-10 px-4 sm:px-6 lg:px-8">
        <div class="max-w-2xl mx-auto">
            {{-- Action Buttons Bar (Hidden on Print) --}}
            <div class="no-print flex items-center justify-between gap-3 mb-6">
                <a href="{{ route('public.portal', $tenant->access_token) }}" class="inline-flex items-center gap-1.5 text-xs font-bold text-slate-600 hover:text-[#1E3A8A] transition-colors">
                    <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
                    <span>Kembali ke Portal Penghuni</span>
                </a>

                <div class="flex items-center gap-2">
                    <button type="button" onclick="window.print()" class="inline-flex items-center gap-2 rounded-xl bg-[#1E3A8A] px-4 py-2 text-xs sm:text-sm font-bold text-white shadow-md hover:bg-[#172554] transition-all">
                        <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/></svg>
                        <span>Cetak / Simpan PDF</span>
                    </button>
                </div>
            </div>

            {{-- OFFICIAL RECEIPT CARD --}}
            <div class="bg-white border border-slate-200/90 rounded-3xl p-6 sm:p-10 shadow-xl shadow-slate-900/5 print-border relative overflow-hidden">
                {{-- Decorative Paid Stamp Watermark --}}
                <div class="absolute right-6 top-6 sm:right-10 sm:top-10 rotate-[-12deg] pointer-events-none select-none opacity-90">
                    <div class="border-4 border-dashed border-emerald-600 rounded-2xl px-4 py-2 text-center bg-emerald-50/50 backdrop-blur-xs">
                        <span class="block text-xs font-extrabold tracking-widest text-emerald-700 uppercase">KOSTLY VERIFIED</span>
                        <span class="block text-2xl sm:text-3xl font-black text-emerald-600 tracking-wider">LUNAS</span>
                        <span class="block text-[9px] font-bold text-emerald-700 font-mono">{{ $invoice->paid_at?->format('d/m/Y') ?? date('d/m/Y') }}</span>
                    </div>
                </div>

                {{-- Header & Property Info --}}
                <div class="border-b-2 border-slate-900 pb-5 mb-6 pr-32 sm:pr-40">
                    <div class="flex items-center gap-2 mb-2">
                        <div class="flex h-8 w-8 items-center justify-center rounded-lg bg-[#1E3A8A] text-white font-bold text-sm">
                            K
                        </div>
                        <span class="text-lg font-black tracking-tight text-[#1E3A8A]">Kostly<span class="text-emerald-500">.</span></span>
                    </div>
                    <h1 class="text-xl sm:text-2xl font-black text-slate-900 tracking-tight">{{ $invoice->property->name }}</h1>
                    <p class="text-xs text-slate-500 mt-0.5 leading-relaxed">{{ $invoice->property->address }}</p>
                    @if ($invoice->property->user?->phone)
                        <p class="text-xs text-slate-500 mt-0.5">Kontak Pengelola: <strong>{{ $invoice->property->user->phone }}</strong></p>
                    @endif
                </div>

                {{-- Receipt Title & Meta --}}
                <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-2 pb-5 border-b border-slate-100 text-xs">
                    <div>
                        <span class="text-[10px] uppercase font-bold tracking-wider text-slate-400">Bukti Pembayaran Resmi</span>
                        <h2 class="text-base font-extrabold text-slate-800">KUITANSI SEWA KOST</h2>
                    </div>
                    <div class="sm:text-right font-mono">
                        <div class="text-slate-900 font-bold">No. Kuitansi: #KUI-{{ $invoice->invoice_code }}</div>
                        <div class="text-slate-400 text-[11px]">Waktu: {{ $invoice->paid_at?->translatedFormat('d F Y, H:i') ?? now()->translatedFormat('d F Y, H:i') }} WIB</div>
                    </div>
                </div>

                {{-- Tenant & Room Meta Table --}}
                <div class="my-6 space-y-3 text-xs sm:text-sm">
                    <div class="grid grid-cols-3 gap-2 py-1.5 border-b border-slate-100">
                        <span class="text-slate-500 font-medium">Telah Diterima Dari</span>
                        <span class="col-span-2 font-bold text-slate-900">: {{ $tenant->name }} ({{ $tenant->phone }})</span>
                    </div>

                    <div class="grid grid-cols-3 gap-2 py-1.5 border-b border-slate-100">
                        <span class="text-slate-500 font-medium">Kamar Kost</span>
                        <span class="col-span-2 font-bold text-slate-900">: Kamar {{ $tenant->room?->room_number ?? '-' }} <span class="font-normal text-slate-500">({{ $tenant->room?->type }})</span></span>
                    </div>

                    <div class="grid grid-cols-3 gap-2 py-1.5 border-b border-slate-100">
                        <span class="text-slate-500 font-medium">Periode Pembayaran</span>
                        <span class="col-span-2 font-bold text-slate-900">: Bulan {{ $invoice->due_date->translatedFormat('F Y') }} (Jatuh Tempo {{ $invoice->due_date->translatedFormat('d F Y') }})</span>
                    </div>

                    <div class="grid grid-cols-3 gap-2 py-1.5 border-b border-slate-100">
                        <span class="text-slate-500 font-medium">Metode Pembayaran</span>
                        <span class="col-span-2 font-bold text-emerald-700">: {{ $invoice->payment_method ?? 'Metode Digital / QRIS' }}</span>
                    </div>
                </div>

                {{-- Breakdown Table --}}
                <div class="rounded-2xl border border-slate-200 overflow-hidden mb-6">
                    <table class="w-full text-left text-xs sm:text-sm">
                        <thead class="bg-slate-50 border-b border-slate-200 text-slate-600 font-bold uppercase text-[11px]">
                            <tr>
                                <th class="p-3">Uraian Pembayaran</th>
                                <th class="p-3 text-right">Jumlah</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100">
                            <tr>
                                <td class="p-3 font-medium text-slate-800">
                                    Biaya Sewa Kamar 1 Bulan (Kamar {{ $tenant->room?->room_number }})
                                </td>
                                <td class="p-3 text-right font-bold text-slate-900">
                                    Rp {{ number_format((float) $invoice->amount, 0, ',', '.') }}
                                </td>
                            </tr>
                            @if ($invoice->additional_fee > 0)
                                <tr>
                                    <td class="p-3 font-medium text-slate-800">
                                        Biaya Tambahan / Fasilitas Ekstra
                                    </td>
                                    <td class="p-3 text-right font-bold text-slate-900">
                                        Rp {{ number_format((float) $invoice->additional_fee, 0, ',', '.') }}
                                    </td>
                                </tr>
                            @endif
                        </tbody>
                        <tfoot class="bg-indigo-50/50 border-t-2 border-slate-200 font-bold">
                            <tr>
                                <td class="p-3.5 text-slate-900 text-sm">TOTAL PEMBAYARAN LUNAS</td>
                                <td class="p-3.5 text-right text-base sm:text-lg font-black text-[#1E3A8A]">
                                    Rp {{ number_format((float) $invoice->total_amount, 0, ',', '.') }}
                                </td>
                            </tr>
                        </tfoot>
                    </table>
                </div>

                {{-- Signatures / Verification Footer --}}
                <div class="pt-4 border-t border-slate-100 flex flex-col sm:flex-row items-center justify-between gap-6 text-xs text-slate-500">
                    <div class="space-y-1 text-center sm:text-left">
                        <p class="text-[11px] text-slate-400">Status Validasi Dokumen:</p>
                        <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-md bg-emerald-50 text-emerald-800 font-bold text-[11px] border border-emerald-200">
                            <svg class="h-3.5 w-3.5 text-emerald-600" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/></svg>
                            Kuitansi Sah Tercatat di Database Kostly
                        </span>
                    </div>

                    <div class="text-center sm:text-right space-y-1">
                        <p class="text-slate-400 text-[11px]">Diterbitkan oleh Sistem Kostly untuk:</p>
                        <p class="font-bold text-slate-900">{{ $invoice->property->name }}</p>
                        <p class="text-[10px] text-slate-400 font-mono">{{ $invoice->property->user?->name ?? 'Pemilik Kost' }}</p>
                    </div>
                </div>
            </div>

            {{-- Footer Note on Print --}}
            <div class="no-print mt-6 text-center text-xs text-slate-400">
                <p>Kuitansi ini dapat disimpan sebagai PDF atau dicetak menggunakan printer standar.</p>
            </div>
        </div>
    </div>
</x-app>
