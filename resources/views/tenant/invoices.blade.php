<x-layouts.tenant>
    {{-- Header --}}
    <div class="mt-2 mb-6 flex flex-col sm:flex-row sm:items-center justify-between gap-4 bg-white border border-slate-200/90 rounded-2xl p-5 sm:p-6 shadow-xs">
        <div>
            <h1 class="text-xl font-bold text-slate-900 tracking-tight">Riwayat Tagihan Sewa</h1>
            <p class="text-xs sm:text-sm text-slate-500 mt-1">
                Pantau tagihan sewa bulanan, bayar online, atau unggah bukti transfer langsung ke pemilik kost.
            </p>
        </div>
        @if($tenant)
            <div class="text-xs text-slate-600 sm:text-right">
                <div>Properti: <strong>{{ $tenant->property->name }}</strong></div>
                <div>Kamar: <strong>Kamar {{ $tenant->room?->room_number ?? '-' }}</strong></div>
            </div>
        @endif
    </div>

    {{-- Error messages --}}
    @if($errors->any())
        <div class="mb-5 rounded-2xl border border-rose-200 bg-rose-50 p-4 text-xs text-rose-700">
            <strong class="font-bold">Gagal mengunggah bukti:</strong>
            <ul class="list-disc list-inside mt-1 space-y-0.5">
                @foreach($errors->all() as $err)
                    <li>{{ $err }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    {{-- Invoice Table --}}
    <div class="bg-white border border-slate-200 rounded-3xl shadow-xs overflow-hidden">
        @if($invoices->isEmpty())
            <div class="py-16 text-center">
                <svg class="mx-auto h-12 w-12 text-slate-300" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 14l6-6m-5.5.5h.01m4.99 5h.01M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16l3.5-2 3.5 2 3.5-2 3.5 2zM10 8.5a.5.5 0 11-1 0 .5.5 0 011 0zm5 5a.5.5 0 11-1 0 .5.5 0 011 0z"/></svg>
                <h3 class="mt-3 text-sm font-bold text-slate-700">Belum Ada Tagihan</h3>
                <p class="text-xs text-slate-400 mt-1">Tagihan bulanan akan otomatis muncul di sini saat diterbitkan oleh pemilik kost.</p>
            </div>
        @else
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-slate-100 text-xs sm:text-sm">
                    <thead class="bg-slate-50/80">
                        <tr>
                            <th class="px-5 py-3.5 text-left text-xs font-semibold uppercase tracking-wider text-slate-500">Kode Tagihan</th>
                            <th class="px-5 py-3.5 text-left text-xs font-semibold uppercase tracking-wider text-slate-500">Jatuh Tempo</th>
                            <th class="px-5 py-3.5 text-right text-xs font-semibold uppercase tracking-wider text-slate-500">Total Tagihan</th>
                            <th class="px-5 py-3.5 text-center text-xs font-semibold uppercase tracking-wider text-slate-500">Status</th>
                            <th class="px-5 py-3.5 text-center text-xs font-semibold uppercase tracking-wider text-slate-500">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-50">
                        @foreach($invoices as $inv)
                            <tr class="hover:bg-slate-50/60 transition-colors">
                                <td class="px-5 py-4">
                                    <span class="font-mono font-bold text-slate-800">#{{ $inv->invoice_code }}</span>
                                    @if($inv->paid_at)
                                        <div class="text-[10px] font-sans font-normal text-emerald-600 mt-0.5">
                                            Lunas: {{ \Carbon\Carbon::parse($inv->paid_at)->translatedFormat('d M Y, H:i') }}
                                        </div>
                                    @endif

                                    @if($inv->rejected_reason)
                                        <div class="mt-1.5 p-2 rounded-lg bg-rose-50 border border-rose-200 text-rose-700 text-[11px]">
                                            <strong>Bukti Ditolak:</strong> {{ $inv->rejected_reason }}
                                        </div>
                                    @endif
                                </td>
                                <td class="px-5 py-4 text-slate-600">
                                    {{ \Carbon\Carbon::parse($inv->due_date)->translatedFormat('d F Y') }}
                                </td>
                                <td class="px-5 py-4 text-right font-extrabold text-sm text-[#1E3A8A]">
                                    Rp {{ number_format((float) $inv->total_amount, 0, ',', '.') }}
                                </td>
                                <td class="px-5 py-4 text-center">
                                    @if($inv->status === \App\Enums\InvoiceStatus::Paid)
                                        <span class="inline-flex items-center gap-1 rounded-full bg-emerald-50 border border-emerald-200 px-2.5 py-0.5 text-xs font-bold text-emerald-700">
                                            ✓ Lunas
                                        </span>
                                    @elseif($inv->status === \App\Enums\InvoiceStatus::PendingVerification)
                                        <span class="inline-flex items-center gap-1 rounded-full bg-blue-50 border border-blue-200 px-2.5 py-0.5 text-xs font-bold text-blue-700">
                                            <span class="h-1.5 w-1.5 rounded-full bg-blue-500 animate-pulse"></span>
                                            Menunggu Verifikasi
                                        </span>
                                    @elseif($inv->status === \App\Enums\InvoiceStatus::Overdue)
                                        <span class="inline-flex items-center gap-1 rounded-full bg-rose-50 border border-rose-200 px-2.5 py-0.5 text-xs font-bold text-rose-700">
                                            Lewat Jatuh Tempo
                                        </span>
                                    @else
                                        <span class="inline-flex items-center gap-1 rounded-full bg-amber-50 border border-amber-200 px-2.5 py-0.5 text-xs font-bold text-amber-700">
                                            Belum Lunas
                                        </span>
                                    @endif
                                </td>
                                <td class="px-5 py-4 text-center">
                                    <div class="flex items-center justify-center gap-2 flex-wrap">
                                        @if($inv->status === \App\Enums\InvoiceStatus::Paid)
                                            @if($tenant)
                                                <a href="{{ route('public.portal.receipt', ['access_token' => $tenant->access_token, 'invoice_code' => $inv->invoice_code]) }}" target="_blank" class="inline-flex items-center gap-1 rounded-xl border border-slate-200 bg-white hover:bg-slate-50 px-3 py-1.5 text-xs font-bold text-slate-700 transition-colors shadow-xs">
                                                    <span>Cetak Kuitansi</span>
                                                </a>
                                            @else
                                                <span class="text-xs text-slate-400">Lunas</span>
                                            @endif
                                        @elseif($inv->status === \App\Enums\InvoiceStatus::PendingVerification)
                                            @if($inv->payment_proof)
                                                <a href="{{ asset('storage/' . $inv->payment_proof) }}" target="_blank" class="inline-flex items-center gap-1 rounded-xl border border-blue-200 bg-blue-50 hover:bg-blue-100 px-3 py-1.5 text-xs font-bold text-blue-700 transition-colors">
                                                    <span>Bukti Diunggah</span>
                                                </a>
                                            @endif
                                        @else
                                            {{-- Tombol Bayar Online --}}
                                            <a href="{{ route('public.pay', $inv->invoice_code) }}" class="inline-flex items-center gap-1 rounded-xl bg-emerald-600 hover:bg-emerald-700 px-3 py-1.5 text-xs font-bold text-white transition-colors shadow-xs">
                                                <span>Bayar Online</span>
                                            </a>

                                            {{-- Tombol Unggah Bukti Manual --}}
                                            <button type="button" onclick="openUploadModal('{{ $inv->id }}', '{{ $inv->invoice_code }}', '{{ number_format((float) $inv->total_amount, 0, ',', '.') }}')" class="inline-flex items-center gap-1 rounded-xl border border-indigo-200 bg-indigo-50 hover:bg-indigo-100 px-3 py-1.5 text-xs font-bold text-[#1E3A8A] transition-colors">
                                                <span>Upload Bukti</span>
                                            </button>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endif
    </div>

    {{-- Upload Modal --}}
    <div id="upload-modal" class="fixed inset-0 z-50 hidden bg-slate-900/60 backdrop-blur-xs overflow-y-auto">
        <div class="flex min-h-full items-center justify-center p-4">
            <div class="relative w-full max-w-md rounded-3xl bg-white p-6 sm:p-8 shadow-2xl">
                <div class="flex items-center justify-between pb-3 border-b border-slate-100 mb-4">
                    <h3 class="text-base font-bold text-slate-900">Upload Bukti Pembayaran</h3>
                    <button type="button" onclick="closeUploadModal()" class="rounded-lg p-1 text-slate-400 hover:text-slate-600">
                        <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                    </button>
                </div>

                <form id="upload-form" action="" method="POST" enctype="multipart/form-data" class="space-y-4">
                    @csrf

                    <div>
                        <span class="text-xs text-slate-500">Kode Tagihan:</span>
                        <div class="font-mono font-bold text-slate-800" id="modal-invoice-code">-</div>
                        <div class="text-xs text-[#1E3A8A] font-extrabold mt-0.5" id="modal-invoice-amount">-</div>
                    </div>

                    {{-- Informasi Rekening & QRIS Pemilik Kost --}}
                    @php
                        $ownerUser = $tenant?->property?->user;
                    @endphp
                    @if($ownerUser && ($ownerUser->bank_account_number || $ownerUser->qris_image_path))
                        <div class="rounded-2xl border border-indigo-100 bg-indigo-50/60 p-3.5 text-xs text-slate-700 space-y-2">
                            <span class="font-bold text-[#1E3A8A] block">Tujuan Pembayaran Pemilik Kost:</span>
                            @if($ownerUser->bank_account_number)
                                <div class="bg-white p-2.5 rounded-xl border border-indigo-100">
                                    <span class="text-[10px] text-slate-400 block uppercase font-bold">{{ $ownerUser->bank_name ?? 'Bank Transfer' }}</span>
                                    <span class="font-mono font-black text-slate-900 text-sm block">{{ $ownerUser->bank_account_number }}</span>
                                    <span class="text-[11px] text-slate-600">a.n. {{ $ownerUser->bank_account_name ?? $ownerUser->name }}</span>
                                </div>
                            @endif

                            @if($ownerUser->qris_image_path)
                                <div class="bg-white p-2.5 rounded-xl border border-indigo-100 flex items-center justify-between gap-3">
                                    <div class="flex items-center gap-2.5">
                                        <img src="{{ asset('storage/' . $ownerUser->qris_image_path) }}" alt="QRIS Owner" class="h-10 w-10 object-contain rounded-lg border border-slate-200">
                                        <span class="font-bold text-slate-800 text-[11px]">QRIS Kost Tersedia</span>
                                    </div>
                                    <a href="{{ asset('storage/' . $ownerUser->qris_image_path) }}" target="_blank" class="text-[11px] font-bold text-[#1E3A8A] hover:underline">
                                        Buka QRIS &rarr;
                                    </a>
                                </div>
                            @endif
                        </div>
                    @endif

                    <div>
                        <label for="payment_method" class="block text-xs font-bold text-slate-700 mb-1">Metode Pembayaran</label>
                        <select id="payment_method" name="payment_method" class="w-full rounded-xl border border-slate-200 bg-slate-50 px-3.5 py-2 text-xs text-slate-800 focus:border-indigo-400 focus:bg-white focus:outline-none">
                            <option value="Transfer Bank BCA">Transfer Bank BCA</option>
                            <option value="Transfer Bank Mandiri">Transfer Bank Mandiri</option>
                            <option value="Transfer Bank BRI">Transfer Bank BRI</option>
                            <option value="Transfer Bank BNI">Transfer Bank BNI</option>
                            <option value="QRIS / E-Wallet">QRIS / E-Wallet</option>
                        </select>
                    </div>

                    <div>
                        <label for="payment_proof" class="block text-xs font-bold text-slate-700 mb-1">Foto Bukti Transfer (Maks. 2MB) <span class="text-rose-500">*</span></label>
                        <input type="file" id="payment_proof" name="payment_proof" accept="image/*" required class="w-full rounded-xl border border-slate-200 bg-slate-50 px-3.5 py-2 text-xs text-slate-700 file:mr-3 file:py-1 file:px-3 file:rounded-lg file:border-0 file:text-xs file:font-bold file:bg-[#1E3A8A] file:text-white hover:file:bg-[#172554] focus:outline-none">
                        <span class="text-[10px] text-slate-400 mt-1 block">Format: JPG, PNG, atau WEBP.</span>
                    </div>

                    <div class="pt-2 flex items-center justify-end gap-2">
                        <button type="button" onclick="closeUploadModal()" class="rounded-xl px-4 py-2 text-xs font-bold text-slate-600 hover:bg-slate-100">
                            Batal
                        </button>
                        <button type="submit" class="rounded-xl bg-[#1E3A8A] hover:bg-[#172554] px-5 py-2 text-xs font-bold text-white shadow-xs">
                            Unggah Bukti
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script>
        function openUploadModal(invoiceId, invoiceCode, amount) {
            const form = document.getElementById('upload-form');
            form.action = `/tenant/invoices/${invoiceId}/upload-proof`;
            document.getElementById('modal-invoice-code').innerText = '#' + invoiceCode;
            document.getElementById('modal-invoice-amount').innerText = 'Rp ' + amount;
            document.getElementById('upload-modal').classList.remove('hidden');
        }

        function closeUploadModal() {
            document.getElementById('upload-modal').classList.add('hidden');
        }
    </script>
</x-layouts.tenant>
