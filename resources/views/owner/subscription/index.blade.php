<x-layouts.owner>
    <x-slot:header>
        Paket & Langganan
    </x-slot:header>

    {{-- Title Heading --}}
    <div class="mt-4 mb-2">
        <h1 class="text-xl font-black text-slate-900 tracking-tight">Manajemen Langganan Owner</h1>
        <p class="text-xs text-slate-500">Pilih paket langganan terbaik untuk kemudahan pengelolaan bisnis kost Anda.</p>
    </div>

    {{-- Top Current Plan Status Banner --}}
    <div class="mt-4 rounded-3xl border border-slate-200 bg-white p-6 sm:p-8 shadow-xs">
        <div class="flex flex-col md:flex-row md:items-center justify-between gap-6">
            <div class="flex items-start gap-4">
                <div class="flex h-14 w-14 shrink-0 items-center justify-center rounded-2xl {{ $user->isPro() ? 'bg-emerald-50 text-emerald-600 border border-emerald-200' : 'bg-slate-100 text-slate-600' }}">
                    <svg class="h-7 w-7" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 3v4M3 5h4M6 17v4m-2-2h4m5-16l2.286 6.857L21 12l-5.714 2.143L13 21l-2.286-6.857L5 12l5.714-2.143L13 3z"/></svg>
                </div>
                <div>
                    <div class="flex items-center gap-2.5">
                        <h2 class="text-xl sm:text-2xl font-black text-slate-900">
                            Paket Anda Saat Ini: <span class="{{ $user->isPro() ? 'text-emerald-600' : 'text-[#1E3A8A]' }}">{{ $user->subscription_plan->label() }}</span>
                        </h2>
                        <span class="px-2.5 py-0.5 rounded-full text-xs font-black uppercase {{ $user->isPro() ? 'bg-emerald-100 text-emerald-800' : 'bg-slate-100 text-slate-600' }}">
                            {{ $user->isPro() ? 'PRO' : 'FREE' }}
                        </span>
                    </div>

                    <p class="text-xs sm:text-sm text-slate-500 mt-1">
                        @if ($user->isPro())
                            @if ($user->plan_expires_at)
                                Masa aktif paket Pro Anda berlaku sampai <strong>{{ $user->plan_expires_at->translatedFormat('d F Y, H:i') }} WIB</strong> ({{ $user->plan_expires_at->diffForHumans() }}).
                            @else
                                Akun Anda memiliki akses tak terbatas ke seluruh fitur Pro Kostly.
                            @endif
                        @else
                            Paket Free memiliki batas maksimal 1 Properti Kost dan 10 Kamar Kost. Tingkatkan ke Pro untuk properti & kamar tanpa batas!
                        @endif
                    </p>
                </div>
            </div>

            {{-- Action CTA --}}
            <div class="shrink-0">
                @if ($pendingSubscription)
                    <div class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl bg-amber-50 border border-amber-200 text-amber-800 text-xs font-bold">
                        <span class="h-2 w-2 rounded-full bg-amber-500 animate-pulse"></span>
                        <span>Upgrade Sedang Diverifikasi</span>
                    </div>
                @elseif (!$user->isPro())
                    <button type="button" onclick="openUpgradeModal()" class="inline-flex items-center gap-2 rounded-xl bg-emerald-600 px-5 py-3 text-xs sm:text-sm font-bold text-white shadow-lg shadow-emerald-900/20 hover:bg-emerald-700 transition-colors">
                        <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/></svg>
                        <span>Upgrade ke Paket PRO</span>
                    </button>
                @else
                    <button type="button" onclick="openUpgradeModal()" class="inline-flex items-center gap-2 rounded-xl bg-[#1E3A8A] px-4 py-2.5 text-xs sm:text-sm font-bold text-white hover:bg-[#172554] transition-colors">
                        <span>Perpanjang Masa Aktif Pro</span>
                    </button>
                @endif
            </div>
        </div>
    </div>

    {{-- Plan Comparison Cards --}}
    <div class="mt-8">
        <h3 class="text-base sm:text-lg font-bold text-slate-900 mb-4">Pilihan Paket Langganan Kostly</h3>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            {{-- Free Plan Card --}}
            <div class="rounded-3xl border border-slate-200 bg-white p-6 sm:p-8 flex flex-col justify-between shadow-xs">
                <div>
                    <div class="flex items-center justify-between">
                        <span class="text-xs font-bold uppercase tracking-wider text-slate-400">Pemula</span>
                        <span class="px-2.5 py-0.5 rounded-md bg-slate-100 text-slate-600 text-[10px] font-black uppercase">Gratis</span>
                    </div>
                    <h4 class="text-2xl font-black text-slate-900 mt-2">Paket Free</h4>
                    <div class="mt-3 flex items-baseline gap-1">
                        <span class="text-3xl font-black text-slate-900">Rp 0</span>
                        <span class="text-xs text-slate-400">/ selamanya</span>
                    </div>
                    <p class="text-xs text-slate-500 mt-2">Cocok untuk pemilik yang baru mulai mengelola 1 properti kost skala kecil.</p>

                    <div class="mt-6 pt-6 border-t border-slate-100 space-y-3 text-xs text-slate-700 font-medium">
                        <div class="flex items-center gap-2.5">
                            <svg class="h-4 w-4 text-emerald-500 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/></svg>
                            <span>Maksimal <strong>1 Properti Kost</strong></span>
                        </div>
                        <div class="flex items-center gap-2.5">
                            <svg class="h-4 w-4 text-emerald-500 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/></svg>
                            <span>Maksimal <strong>10 Kamar Kost</strong></span>
                        </div>
                        <div class="flex items-center gap-2.5">
                            <svg class="h-4 w-4 text-emerald-500 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/></svg>
                            <span>Catat Tagihan & Pengeluaran</span>
                        </div>
                        <div class="flex items-center gap-2.5">
                            <svg class="h-4 w-4 text-emerald-500 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/></svg>
                            <span>Portal Mandiri Anak Kost</span>
                        </div>
                    </div>
                </div>

                <div class="mt-8 pt-4">
                    @if (!$user->isPro())
                        <div class="w-full text-center py-2.5 text-xs font-bold text-slate-400 bg-slate-100 rounded-xl">
                            Paket Aktif Anda Saat Ini
                        </div>
                    @endif
                </div>
            </div>

            {{-- Pro Plan Card (Highlighted) --}}
            <div class="rounded-3xl border-2 border-emerald-500 bg-linear-to-b from-white to-emerald-50/20 p-6 sm:p-8 flex flex-col justify-between shadow-xl relative overflow-hidden">
                <div class="absolute top-0 right-0 bg-emerald-600 text-white text-[10px] uppercase font-black px-4 py-1 rounded-bl-xl shadow-xs">
                    Rekomendasi Juragan
                </div>

                <div>
                    <div class="flex items-center justify-between">
                        <span class="text-xs font-bold uppercase tracking-wider text-emerald-700">Skala Penuh</span>
                        <span class="px-2.5 py-0.5 rounded-md bg-emerald-100 text-emerald-800 text-[10px] font-black uppercase">Tanpa Batas</span>
                    </div>
                    <h4 class="text-2xl font-black text-slate-900 mt-2">Paket Juragan PRO</h4>
                    <div class="mt-3 flex items-baseline gap-1">
                        <span class="text-3xl font-black text-emerald-600">Rp 99.000</span>
                        <span class="text-xs text-slate-500">/ bulan (atau Rp 999.000 / tahun)</span>
                    </div>
                    <p class="text-xs text-slate-500 mt-2">Untuk juragan kost yang ingin mengelola banyak cabang kost tanpa batas kamar.</p>

                    <div class="mt-6 pt-6 border-t border-slate-100 space-y-3 text-xs text-slate-700 font-medium">
                        <div class="flex items-center gap-2.5">
                            <svg class="h-4 w-4 text-emerald-600 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/></svg>
                            <span><strong>Unlimited</strong> Properti Kost (Bebas Cabang)</span>
                        </div>
                        <div class="flex items-center gap-2.5">
                            <svg class="h-4 w-4 text-emerald-600 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/></svg>
                            <span><strong>Unlimited</strong> Kamar Kost (Bebas Tambah)</span>
                        </div>
                        <div class="flex items-center gap-2.5">
                            <svg class="h-4 w-4 text-emerald-600 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/></svg>
                            <span>Pengingat Otomatis Tagihan WhatsApp & Notifikasi</span>
                        </div>
                        <div class="flex items-center gap-2.5">
                            <svg class="h-4 w-4 text-emerald-600 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/></svg>
                            <span>Laporan Keuangan & Ekspor CSV Excel Tanpa Batas</span>
                        </div>
                        <div class="flex items-center gap-2.5">
                            <svg class="h-4 w-4 text-emerald-600 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/></svg>
                            <span>Prioritas Verifikasi Pembayaran & Dukungan Tim</span>
                        </div>
                    </div>
                </div>

                <div class="mt-8 pt-4">
                    @if ($pendingSubscription)
                        <div class="w-full text-center py-2.5 text-xs font-bold text-amber-700 bg-amber-50 border border-amber-200 rounded-xl">
                            Pembayaran sedang diverifikasi admin
                        </div>
                    @elseif ($user->isPro())
                        <div class="w-full text-center py-2.5 text-xs font-bold text-emerald-700 bg-emerald-100 rounded-xl flex items-center justify-center gap-1.5">
                            <span>✓ Paket Pro Aktif</span>
                        </div>
                    @else
                        <button type="button" onclick="openUpgradeModal()" class="w-full py-3 rounded-xl bg-emerald-600 hover:bg-emerald-700 text-xs sm:text-sm font-bold text-white transition-colors shadow-md shadow-emerald-950/20 text-center">
                            Pilih Paket PRO Sekarang
                        </button>
                    @endif
                </div>
            </div>
        </div>
    </div>

    {{-- Subscription History Table --}}
    <div class="mt-10">
        <h3 class="text-base sm:text-lg font-bold text-slate-900 mb-4">Riwayat Transaksi Langganan</h3>

        <div class="bg-white border border-slate-200 rounded-2xl overflow-hidden shadow-xs">
            @if ($subscriptions->isEmpty())
                <div class="p-8 text-center text-xs text-slate-400">
                    Belum ada riwayat transaksi upgrade langganan.
                </div>
            @else
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-slate-100 text-left text-xs">
                        <thead class="bg-slate-50 text-slate-500 font-bold uppercase tracking-wider">
                            <tr>
                                <th class="px-5 py-3.5">Tanggal</th>
                                <th class="px-5 py-3.5">Paket & Siklus</th>
                                <th class="px-5 py-3.5">Nominal</th>
                                <th class="px-5 py-3.5">Status</th>
                                <th class="px-5 py-3.5">Masa Berlaku</th>
                                <th class="px-5 py-3.5">Bukti Transfer</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100 font-medium text-slate-700">
                            @foreach ($subscriptions as $sub)
                                <tr class="hover:bg-slate-50/70">
                                    <td class="px-5 py-4">
                                        <span class="font-bold text-slate-900 block">{{ $sub->created_at->translatedFormat('d M Y') }}</span>
                                        <span class="text-[10px] text-slate-400">{{ $sub->created_at->format('H:i') }} WIB</span>
                                    </td>
                                    <td class="px-5 py-4">
                                        <span class="font-bold text-slate-900 block uppercase">Paket {{ $sub->plan }}</span>
                                        <span class="text-[11px] text-slate-500">{{ $sub->billing_cycle === 'yearly' ? 'Tahunan (1 Tahun)' : 'Bulanan (1 Bulan)' }}</span>
                                    </td>
                                    <td class="px-5 py-4 font-bold text-slate-900">
                                        Rp {{ number_format((float) $sub->amount, 0, ',', '.') }}
                                    </td>
                                    <td class="px-5 py-4">
                                        <span class="inline-flex items-center px-2.5 py-1 rounded-full text-[11px] font-bold {{ $sub->badgeClass() }}">
                                            {{ $sub->statusLabel() }}
                                        </span>
                                        @if ($sub->rejected_reason)
                                            <p class="text-[10px] text-rose-600 mt-1">Alasan: {{ $sub->rejected_reason }}</p>
                                        @endif
                                    </td>
                                    <td class="px-5 py-4">
                                        @if ($sub->starts_at && $sub->ends_at)
                                            <span class="block font-semibold text-slate-800">{{ $sub->starts_at->translatedFormat('d M Y') }} - {{ $sub->ends_at->translatedFormat('d M Y') }}</span>
                                        @else
                                            <span class="text-slate-400 italic">-</span>
                                        @endif
                                    </td>
                                    <td class="px-5 py-4">
                                        @if ($sub->payment_proof)
                                            <a href="{{ asset('storage/' . $sub->payment_proof) }}" target="_blank" class="text-xs text-blue-600 hover:underline font-bold inline-flex items-center gap-1">
                                                <span>Lihat Foto</span>
                                                <svg class="h-3 w-3" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/></svg>
                                            </a>
                                        @else
                                            <span class="text-slate-400">-</span>
                                        @endif
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @endif
        </div>
    </div>

    {{-- Modal Pengajuan Upgrade --}}
    <div id="upgrade-modal" class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-900/60 backdrop-blur-xs hidden">
        <div class="w-full max-w-lg rounded-3xl bg-white p-6 sm:p-8 shadow-2xl max-h-[90vh] overflow-y-auto">
            <div class="flex items-center justify-between border-b border-slate-100 pb-3 mb-5">
                <div>
                    <h3 class="text-lg font-bold text-slate-900">Upgrade ke Paket Juragan PRO</h3>
                    <p class="text-xs text-slate-500">Buka akses properti dan kamar tanpa batas</p>
                </div>
                <button type="button" onclick="closeUpgradeModal()" class="text-slate-400 hover:text-slate-600 font-bold text-lg">✕</button>
            </div>

            <form action="{{ route('owner.subscription.store') }}" method="POST" enctype="multipart/form-data" class="space-y-5">
                @csrf

                {{-- Pilih Siklus --}}
                <div>
                    <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-2">Pilih Siklus Berlangganan</label>
                    <div class="grid grid-cols-2 gap-3">
                        <label class="flex flex-col p-3 rounded-2xl border-2 border-slate-200 cursor-pointer hover:border-emerald-500 has-checked:border-emerald-600 has-checked:bg-emerald-50/30 transition-all">
                            <input type="radio" name="billing_cycle" value="monthly" checked onchange="updatePriceDisplay('monthly')" class="sr-only">
                            <span class="text-xs font-bold text-slate-800">Bulanan</span>
                            <span class="text-base font-black text-emerald-600 mt-1">Rp 99.000</span>
                            <span class="text-[10px] text-slate-400">/ 1 bulan</span>
                        </label>

                        <label class="flex flex-col p-3 rounded-2xl border-2 border-slate-200 cursor-pointer hover:border-emerald-500 has-checked:border-emerald-600 has-checked:bg-emerald-50/30 transition-all relative overflow-hidden">
                            <span class="absolute top-0 right-0 bg-emerald-600 text-[9px] font-black text-white px-2 py-0.5 rounded-bl-lg">Hemat 16%</span>
                            <input type="radio" name="billing_cycle" value="yearly" onchange="updatePriceDisplay('yearly')" class="sr-only">
                            <span class="text-xs font-bold text-slate-800">Tahunan</span>
                            <span class="text-base font-black text-emerald-600 mt-1">Rp 999.000</span>
                            <span class="text-[10px] text-slate-400">/ 1 tahun</span>
                        </label>
                    </div>
                </div>

                {{-- Instruksi Pembayaran --}}
                <div class="rounded-2xl border border-indigo-100 bg-indigo-50/50 p-4 text-xs text-slate-700">
                    <span class="font-bold text-[#1E3A8A] block mb-1">Instruksi Pembayaran Transfer / QRIS:</span>
                    <ul class="list-disc list-inside space-y-1 text-slate-600 text-[11px]">
                        <li>BCA: <strong>8830-192-888</strong> a.n. PT Kostly Manajemen Indonesia</li>
                        <li>Mandiri: <strong>137-00-9921-888</strong> a.n. PT Kostly Manajemen</li>
                        <li>Total yang harus ditransfer: <strong id="modal-total-price" class="text-emerald-700 text-xs">Rp 99.000</strong></li>
                    </ul>
                </div>

                {{-- Upload Bukti Transfer --}}
                <div>
                    <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-1">Foto Bukti Transfer (Maks. 2MB) <span class="text-rose-500">*</span></label>
                    <input type="file" name="payment_proof" accept="image/*" required class="w-full rounded-xl border border-slate-200 bg-slate-50 px-3.5 py-2 text-xs text-slate-700 file:mr-3 file:py-1 file:px-3 file:rounded-lg file:border-0 file:text-xs file:font-bold file:bg-[#1E3A8A] file:text-white hover:file:bg-[#172554] focus:outline-none">
                    <span class="text-[10px] text-slate-400 mt-1 block">Format file: JPG, PNG, atau WEBP.</span>
                </div>

                <div class="flex items-center justify-end gap-3 pt-3 border-t border-slate-100">
                    <button type="button" onclick="closeUpgradeModal()" class="rounded-xl border border-slate-300 px-4 py-2 text-xs font-semibold text-slate-700 hover:bg-slate-50">Batal</button>
                    <button type="submit" class="rounded-xl bg-emerald-600 px-5 py-2 text-xs font-bold text-white hover:bg-emerald-700 shadow-md">Kirim Bukti Pembayaran</button>
                </div>
            </form>
        </div>
    </div>

    <script>
        function openUpgradeModal() {
            document.getElementById('upgrade-modal').classList.remove('hidden');
        }

        function closeUpgradeModal() {
            document.getElementById('upgrade-modal').classList.add('hidden');
        }

        function updatePriceDisplay(cycle) {
            const priceLabel = cycle === 'yearly' ? 'Rp 999.000' : 'Rp 99.000';
            document.getElementById('modal-total-price').textContent = priceLabel;
        }
    </script>
</x-layouts.owner>
