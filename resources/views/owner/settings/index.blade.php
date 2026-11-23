<x-layouts.owner>
    <x-slot:header>
        Pengaturan Pembayaran Kost
    </x-slot:header>

    <div class="mt-4 max-w-4xl">
        {{-- Header Card --}}
        <div class="rounded-3xl border border-slate-200 bg-white p-6 sm:p-8 shadow-xs mb-6">
            <h2 class="text-xl font-black text-slate-900 tracking-tight">Rekening & QRIS Pembayaran Kost</h2>
            <p class="text-xs sm:text-sm text-slate-500 mt-1 leading-relaxed">
                Informasi rekening bank dan kode QRIS ini akan otomatis ditampilkan kepada anak kost pada modal pembayaran tagihan sewa bulanan di portal mereka.
            </p>
        </div>

        <form action="{{ route('owner.settings.update') }}" method="POST" enctype="multipart/form-data" class="space-y-6">
            @csrf
            @method('PUT')

            {{-- Rekening Bank --}}
            <div class="rounded-3xl border border-slate-200 bg-white p-6 sm:p-8 shadow-xs space-y-4">
                <div class="flex items-center gap-2.5 pb-3 border-b border-slate-100">
                    <div class="flex h-9 w-9 items-center justify-center rounded-xl bg-indigo-50 text-[#1E3A8A] font-bold">
                        <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"/></svg>
                    </div>
                    <div>
                        <h3 class="text-sm font-bold text-slate-900">Rekening Transfer Bank Utama</h3>
                        <p class="text-xs text-slate-400">Rekening tujuan transfer sewa penghuni</p>
                    </div>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 pt-2">
                    <div>
                        <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-1">Nama Bank</label>
                        <input type="text" name="bank_name" value="{{ old('bank_name', $user->bank_name) }}" placeholder="Contoh: BCA / Mandiri / BRI"
                            class="w-full rounded-xl border border-slate-200 bg-slate-50 px-3.5 py-2.5 text-xs text-slate-800 focus:border-[#1E3A8A] focus:bg-white focus:outline-hidden">
                    </div>

                    <div>
                        <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-1">Nomor Rekening</label>
                        <input type="text" name="bank_account_number" value="{{ old('bank_account_number', $user->bank_account_number) }}" placeholder="Contoh: 1234567890"
                            class="w-full rounded-xl border border-slate-200 bg-slate-50 px-3.5 py-2.5 text-xs text-slate-800 font-mono focus:border-[#1E3A8A] focus:bg-white focus:outline-hidden">
                    </div>

                    <div>
                        <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-1">Atas Nama (Pemilik Rekening)</label>
                        <input type="text" name="bank_account_name" value="{{ old('bank_account_name', $user->bank_account_name) }}" placeholder="Contoh: Budi Santoso"
                            class="w-full rounded-xl border border-slate-200 bg-slate-50 px-3.5 py-2.5 text-xs text-slate-800 focus:border-[#1E3A8A] focus:bg-white focus:outline-hidden">
                    </div>
                </div>
            </div>

            {{-- QRIS --}}
            <div class="rounded-3xl border border-slate-200 bg-white p-6 sm:p-8 shadow-xs space-y-4">
                <div class="flex items-center gap-2.5 pb-3 border-b border-slate-100">
                    <div class="flex h-9 w-9 items-center justify-center rounded-xl bg-emerald-50 text-emerald-600 font-bold">
                        <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v1m6 11h2m-6 0h-2v4m0-11v3m0 0h.01M12 12h4.01M16 20h4M4 12h4m12 0h.01M5 8h2a1 1 0 001-1V5a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1zm12 0h2a1 1 0 001-1V5a1 1 0 00-1-1h-2a1 1 0 00-1 1v2a1 1 0 001 1zM5 20h2a1 1 0 001-1v-2a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1z"/></svg>
                    </div>
                    <div>
                        <h3 class="text-sm font-bold text-slate-900">Foto Barcode QRIS Kost (Opsional)</h3>
                        <p class="text-xs text-slate-400">Mudahkan penghuni bayar lewat GoPay, OVO, Dana, ShopeePay, atau BCA Mobile</p>
                    </div>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-6 items-center pt-2">
                    <div>
                        <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-1">Unggah Gambar QRIS (Maks. 2MB)</label>
                        <input type="file" name="qris_image" accept="image/*"
                            class="w-full rounded-xl border border-slate-200 bg-slate-50 px-3.5 py-2 text-xs text-slate-700 file:mr-3 file:py-1 file:px-3 file:rounded-lg file:border-0 file:text-xs file:font-bold file:bg-[#1E3A8A] file:text-white hover:file:bg-[#172554] focus:outline-hidden">
                        <span class="text-[10px] text-slate-400 mt-1 block">Format didukung: JPG, PNG, WEBP.</span>
                    </div>

                    @if ($user->qris_image_path)
                        <div class="p-3 rounded-2xl border border-slate-200 bg-slate-50 flex items-center gap-4">
                            <img src="{{ asset('storage/' . $user->qris_image_path) }}" alt="QRIS Owner" class="h-20 w-20 object-contain rounded-xl border border-slate-200 bg-white">
                            <div>
                                <span class="text-xs font-bold text-slate-800 block">QRIS Terpasang</span>
                                <a href="{{ asset('storage/' . $user->qris_image_path) }}" target="_blank" class="text-xs text-[#1E3A8A] hover:underline font-semibold mt-1 inline-block">
                                    Lihat Ukuran Penuh &rarr;
                                </a>
                            </div>
                        </div>
                    @endif
                </div>
            </div>

            {{-- Submit Button --}}
            <div class="flex items-center justify-end gap-3 pt-2">
                <button type="submit" class="rounded-xl bg-[#1E3A8A] hover:bg-[#172554] px-6 py-2.5 text-xs sm:text-sm font-bold text-white shadow-md transition-colors">
                    Simpan Pengaturan Pembayaran
                </button>
            </div>
        </form>
    </div>
</x-layouts.owner>
