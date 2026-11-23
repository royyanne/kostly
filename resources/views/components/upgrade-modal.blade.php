{{-- Modal Upgrade Paket Pro --}}
<div id="upgrade-pro-modal" class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-900/60 backdrop-blur-xs hidden">
    <div class="relative w-full max-w-lg rounded-3xl bg-white p-6 sm:p-8 shadow-2xl border border-slate-100 animate-in fade-in zoom-in-95 duration-150">
        {{-- Close Button --}}
        <button type="button" onclick="closeUpgradeModal()" class="absolute right-5 top-5 p-2 rounded-xl text-slate-400 hover:text-slate-600 hover:bg-slate-100 transition-colors">
            <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
        </button>

        {{-- Badge & Header --}}
        <div class="text-center pb-5 border-b border-slate-100">
            <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-bold bg-amber-50 text-amber-800 border border-amber-200/80 mb-3 shadow-2xs">
                <span>⭐</span>
                <span>Kostly Juragan Pro</span>
            </span>
            <h3 class="text-xl sm:text-2xl font-black text-slate-900 tracking-tight">Upgrade ke Paket Pro</h3>
            <p class="text-xs sm:text-sm text-slate-500 mt-1 max-w-sm mx-auto">
                Kelola bisnis kost Anda tanpa batas kuota dengan fitur otomasi premium terlengkap.
            </p>
        </div>

        {{-- Feature List Comparison --}}
        <div class="py-5 space-y-3 text-xs sm:text-sm">
            <div class="flex items-start gap-3 rounded-2xl bg-slate-50 p-3.5">
                <div class="flex h-6 w-6 shrink-0 items-center justify-center rounded-full bg-emerald-100 text-emerald-700 font-bold text-xs">
                    ✓
                </div>
                <div>
                    <strong class="text-slate-900 block font-bold">Properti Kost Tanpa Batas</strong>
                    <span class="text-slate-500 text-xs">Kelola lebih dari 1 lokasi kost atau kontrakan sekaligus dalam satu akun juragan.</span>
                </div>
            </div>

            <div class="flex items-start gap-3 rounded-2xl bg-slate-50 p-3.5">
                <div class="flex h-6 w-6 shrink-0 items-center justify-center rounded-full bg-emerald-100 text-emerald-700 font-bold text-xs">
                    ✓
                </div>
                <div>
                    <strong class="text-slate-900 block font-bold">Kamar Kost Tanpa Batas</strong>
                    <span class="text-slate-500 text-xs">Bebas mendaftarkan puluhan hingga ratusan kamar tanpa batasan kuota 10 kamar.</span>
                </div>
            </div>

            <div class="flex items-start gap-3 rounded-2xl bg-slate-50 p-3.5">
                <div class="flex h-6 w-6 shrink-0 items-center justify-center rounded-full bg-emerald-100 text-emerald-700 font-bold text-xs">
                    ✓
                </div>
                <div>
                    <strong class="text-slate-900 block font-bold">Pengingat Tagihan Otomatis via WhatsApp</strong>
                    <span class="text-slate-500 text-xs">Kirim invoice dan notifikasi H-3 & Hari-H langsung ke nomor WhatsApp anak kost.</span>
                </div>
            </div>

            <div class="flex items-start gap-3 rounded-2xl bg-slate-50 p-3.5">
                <div class="flex h-6 w-6 shrink-0 items-center justify-center rounded-full bg-emerald-100 text-emerald-700 font-bold text-xs">
                    ✓
                </div>
                <div>
                    <strong class="text-slate-900 block font-bold">Laporan Keuangan & Export Excel (CSV)</strong>
                    <span class="text-slate-500 text-xs">Rekap pemasukan, pengeluaran, laba bersih, serta cetak laporan PDF resmi siap print.</span>
                </div>
            </div>
        </div>

        {{-- CTA Buttons --}}
        <div class="pt-2 space-y-2.5">
            <a href="{{ route('owner.subscription.index') }}"
                class="w-full inline-flex items-center justify-center gap-2 rounded-2xl bg-linear-to-r from-emerald-600 to-emerald-700 px-5 py-3.5 text-xs sm:text-sm font-bold text-white shadow-lg shadow-emerald-700/25 hover:from-emerald-700 hover:to-emerald-800 transition-all">
                <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/></svg>
                <span>Buka Halaman Upgrade Langganan PRO</span>
            </a>

            <button type="button" onclick="closeUpgradeModal()"
                class="w-full inline-flex items-center justify-center rounded-2xl border border-slate-200 bg-white py-2.5 text-xs font-semibold text-slate-600 hover:bg-slate-50 transition-colors">
                Nanti Saja
            </button>
        </div>
    </div>
</div>

<script>
    function openUpgradeModal() {
        document.getElementById('upgrade-pro-modal').classList.remove('hidden');
    }
    function closeUpgradeModal() {
        document.getElementById('upgrade-pro-modal').classList.add('hidden');
    }
</script>
