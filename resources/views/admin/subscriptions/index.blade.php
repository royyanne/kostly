<x-layouts.admin>
    <x-slot:header>
        Verifikasi Langganan Juragan PRO
    </x-slot:header>

    {{-- Header Banner & Metric Stats --}}
    <div class="mt-4 grid grid-cols-1 sm:grid-cols-4 gap-4 sm:gap-6">
        <div class="rounded-2xl border border-slate-200 bg-white p-5 shadow-xs">
            <span class="text-xs text-slate-500 font-semibold uppercase">Total Pengajuan</span>
            <div class="mt-2 text-2xl font-black text-slate-900">{{ $counts['total'] }}</div>
        </div>

        <div class="rounded-2xl border border-amber-200 bg-amber-50/50 p-5 shadow-xs">
            <span class="text-xs text-amber-800 font-semibold uppercase">Menunggu Verifikasi</span>
            <div class="mt-2 text-2xl font-black text-amber-600">{{ $counts['pending'] }}</div>
        </div>

        <div class="rounded-2xl border border-emerald-200 bg-emerald-50/50 p-5 shadow-xs">
            <span class="text-xs text-emerald-800 font-semibold uppercase">Langganan Aktif</span>
            <div class="mt-2 text-2xl font-black text-emerald-600">{{ $counts['active'] }}</div>
        </div>

        <div class="rounded-2xl border border-rose-200 bg-rose-50/50 p-5 shadow-xs">
            <span class="text-xs text-rose-800 font-semibold uppercase">Ditolak</span>
            <div class="mt-2 text-2xl font-black text-rose-600">{{ $counts['rejected'] }}</div>
        </div>
    </div>

    {{-- Filter Bar --}}
    <div class="mt-6 flex flex-wrap items-center gap-2">
        <a href="{{ route('admin.subscriptions.index') }}"
            class="px-3.5 py-1.5 rounded-xl border text-xs font-bold transition-all {{ empty($statusFilter) ? 'bg-indigo-600 text-white border-indigo-600' : 'bg-white text-slate-700 border-slate-200 hover:bg-slate-50' }}">
            Semua ({{ $counts['total'] }})
        </a>
        <a href="{{ route('admin.subscriptions.index', ['status' => 'pending']) }}"
            class="px-3.5 py-1.5 rounded-xl border text-xs font-bold transition-all {{ $statusFilter === 'pending' ? 'bg-amber-600 text-white border-amber-600' : 'bg-amber-50 text-amber-800 border-amber-200 hover:bg-amber-100' }}">
            Menunggu Verifikasi ({{ $counts['pending'] }})
        </a>
        <a href="{{ route('admin.subscriptions.index', ['status' => 'active']) }}"
            class="px-3.5 py-1.5 rounded-xl border text-xs font-bold transition-all {{ $statusFilter === 'active' ? 'bg-emerald-600 text-white border-emerald-600' : 'bg-emerald-50 text-emerald-800 border-emerald-200 hover:bg-emerald-100' }}">
            Aktif ({{ $counts['active'] }})
        </a>
        <a href="{{ route('admin.subscriptions.index', ['status' => 'rejected']) }}"
            class="px-3.5 py-1.5 rounded-xl border text-xs font-bold transition-all {{ $statusFilter === 'rejected' ? 'bg-rose-600 text-white border-rose-600' : 'bg-rose-50 text-rose-800 border-rose-200 hover:bg-rose-100' }}">
            Ditolak ({{ $counts['rejected'] }})
        </a>
    </div>

    {{-- Subscriptions Table --}}
    <div class="mt-6 bg-white border border-slate-200 rounded-2xl overflow-hidden shadow-xs">
        @if ($subscriptions->isEmpty())
            <div class="p-12 text-center text-xs text-slate-400">
                Tidak ada data langganan yang ditemukan.
            </div>
        @else
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-slate-200 text-left text-xs">
                    <thead class="bg-slate-50 text-slate-500 font-bold uppercase tracking-wider">
                        <tr>
                            <th class="px-5 py-3.5">Owner Pemilik</th>
                            <th class="px-5 py-3.5">Paket & Siklus</th>
                            <th class="px-5 py-3.5">Nominal Bayar</th>
                            <th class="px-5 py-3.5">Bukti Transfer</th>
                            <th class="px-5 py-3.5">Status</th>
                            <th class="px-5 py-3.5">Masa Berlaku</th>
                            <th class="px-5 py-3.5 text-right">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 font-medium text-slate-700">
                        @foreach ($subscriptions as $sub)
                            <tr class="hover:bg-slate-50/70">
                                <td class="px-5 py-4">
                                    <span class="font-bold text-slate-900 block text-sm">{{ $sub->user->name }}</span>
                                    <span class="text-[11px] text-slate-400 block">{{ $sub->user->email }} &middot; {{ $sub->user->phone ?? '-' }}</span>
                                    <span class="text-[10px] text-slate-400">Diajukan: {{ $sub->created_at->translatedFormat('d M Y, H:i') }}</span>
                                </td>
                                <td class="px-5 py-4">
                                    <span class="font-bold text-slate-900 block uppercase">Paket {{ $sub->plan }}</span>
                                    <span class="text-[11px] text-slate-500 font-semibold">{{ $sub->billing_cycle === 'yearly' ? 'Tahunan (1 Tahun)' : 'Bulanan (1 Bulan)' }}</span>
                                </td>
                                <td class="px-5 py-4 font-black text-slate-900 text-sm">
                                    Rp {{ number_format((float) $sub->amount, 0, ',', '.') }}
                                </td>
                                <td class="px-5 py-4">
                                    @if ($sub->payment_proof)
                                        <button type="button" onclick="openProofModal('{{ asset('storage/' . $sub->payment_proof) }}', '{{ $sub->user->name }}')"
                                            class="inline-flex items-center gap-1 text-xs font-bold text-indigo-600 hover:text-indigo-800 underline">
                                            <svg class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                                            <span>Lihat Bukti</span>
                                        </button>
                                    @else
                                        <span class="text-slate-400 italic">Tidak ada bukti</span>
                                    @endif
                                </td>
                                <td class="px-5 py-4">
                                    <span class="inline-flex items-center px-2.5 py-1 rounded-full text-[11px] font-bold {{ $sub->badgeClass() }}">
                                        {{ $sub->statusLabel() }}
                                    </span>
                                    @if ($sub->rejected_reason)
                                        <p class="text-[10px] text-rose-500 mt-0.5 line-clamp-1" title="{{ $sub->rejected_reason }}">
                                            Ditolak: {{ $sub->rejected_reason }}
                                        </p>
                                    @endif
                                </td>
                                <td class="px-5 py-4 text-xs">
                                    @if ($sub->starts_at && $sub->ends_at)
                                        <span class="font-semibold text-slate-800 block">{{ $sub->starts_at->translatedFormat('d M Y') }} - {{ $sub->ends_at->translatedFormat('d M Y') }}</span>
                                        <span class="text-[10px] text-slate-400">{{ $sub->ends_at->diffForHumans() }}</span>
                                    @else
                                        <span class="text-slate-400 italic">-</span>
                                    @endif
                                </td>
                                <td class="px-5 py-4 text-right">
                                    @if ($sub->status === 'pending')
                                        <div class="inline-flex items-center gap-2">
                                            {{-- Tombol Setujui --}}
                                            <form action="{{ route('admin.subscriptions.approve', $sub) }}" method="POST" onsubmit="return confirm('Setujui pembayaran dan aktifkan paket Pro untuk {{ $sub->user->name }}?');">
                                                @csrf
                                                @method('PATCH')
                                                <button type="submit" class="px-3 py-1.5 rounded-xl bg-emerald-600 hover:bg-emerald-700 text-white font-bold transition-colors">
                                                    Setujui
                                                </button>
                                            </form>

                                            {{-- Tombol Tolak --}}
                                            <button type="button" onclick="openRejectModal('{{ $sub->id }}', '{{ $sub->user->name }}')"
                                                class="px-3 py-1.5 rounded-xl bg-rose-50 hover:bg-rose-100 text-rose-700 border border-rose-200 font-bold transition-colors">
                                                Tolak
                                            </button>
                                        </div>
                                    @else
                                        <span class="text-slate-400 text-xs italic">Selesai</span>
                                    @endif
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <div class="p-4 border-t border-slate-100">
                {{ $subscriptions->links() }}
            </div>
        @endif
    </div>

    {{-- Modal Preview Bukti Transfer --}}
    <div id="proof-modal" class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-900/60 backdrop-blur-xs hidden">
        <div class="w-full max-w-lg rounded-2xl bg-white p-6 shadow-2xl">
            <div class="flex items-center justify-between border-b border-slate-100 pb-3 mb-4">
                <div>
                    <h3 class="text-base font-bold text-slate-900">Bukti Transfer Upgrade PRO</h3>
                    <p class="text-xs text-slate-500" id="proof-modal-subtitle">-</p>
                </div>
                <button type="button" onclick="closeModal('proof-modal')" class="text-slate-400 hover:text-slate-600 font-bold text-lg">✕</button>
            </div>

            <div class="rounded-xl border border-slate-200 bg-slate-50 p-2 flex items-center justify-center max-h-96 overflow-hidden">
                <img id="proof-modal-img" src="" alt="Bukti Transfer" class="max-h-88 max-w-full object-contain rounded-lg">
            </div>

            <div class="flex items-center justify-between gap-3 pt-4 border-t border-slate-100 mt-4">
                <a id="proof-modal-link" href="" target="_blank" class="text-xs text-indigo-600 font-bold hover:underline inline-flex items-center gap-1">
                    <span>Buka Ukuran Penuh</span>
                    <svg class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/></svg>
                </a>
                <button type="button" onclick="closeModal('proof-modal')" class="rounded-xl bg-slate-100 hover:bg-slate-200 px-4 py-2 text-xs font-semibold text-slate-700">Tutup</button>
            </div>
        </div>
    </div>

    {{-- Modal Tolak Bukti Transfer --}}
    <div id="reject-modal" class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-900/60 backdrop-blur-xs hidden">
        <div class="w-full max-w-md rounded-2xl bg-white p-6 shadow-2xl">
            <div class="flex items-center justify-between border-b border-slate-100 pb-3 mb-4">
                <h3 class="text-base font-bold text-slate-900">Tolak Bukti Pembayaran Pro</h3>
                <button type="button" onclick="closeModal('reject-modal')" class="text-slate-400 hover:text-slate-600 font-bold text-lg">✕</button>
            </div>

            <form id="reject-form" method="POST" class="space-y-4">
                @csrf
                @method('PATCH')

                <p class="text-xs text-slate-600">
                    Pengajuan langganan dari <strong id="reject-owner-name">-</strong> akan ditolak. Berikan alasan penolakan yang jelas.
                </p>

                <div>
                    <label class="block text-xs font-semibold uppercase tracking-wider text-slate-700 mb-1">Alasan Penolakan <span class="text-rose-500">*</span></label>
                    <textarea name="rejected_reason" rows="3" required placeholder="Contoh: Bukti transfer tidak valid, mutasi rekening belum masuk..."
                        class="w-full rounded-xl border border-slate-300 px-3.5 py-2 text-xs focus:border-rose-600 focus:outline-hidden"></textarea>
                </div>

                <div class="flex items-center justify-end gap-3 pt-3 border-t border-slate-100">
                    <button type="button" onclick="closeModal('reject-modal')" class="rounded-xl border border-slate-300 px-4 py-2 text-xs font-semibold text-slate-700 hover:bg-slate-50">Batal</button>
                    <button type="submit" class="rounded-xl bg-rose-600 px-5 py-2 text-xs font-bold text-white hover:bg-rose-700">Tolak Pengajuan</button>
                </div>
            </form>
        </div>
    </div>

    <script>
        function openModal(id) {
            document.getElementById(id).classList.remove('hidden');
        }

        function closeModal(id) {
            document.getElementById(id).classList.add('hidden');
        }

        function openProofModal(imgUrl, ownerName) {
            document.getElementById('proof-modal-img').src = imgUrl;
            document.getElementById('proof-modal-link').href = imgUrl;
            document.getElementById('proof-modal-subtitle').textContent = `Pengajuan atas nama: ${ownerName}`;
            openModal('proof-modal');
        }

        function openRejectModal(subId, ownerName) {
            const form = document.getElementById('reject-form');
            form.action = `/admin/subscriptions/${subId}/reject`;
            document.getElementById('reject-owner-name').textContent = ownerName;
            openModal('reject-modal');
        }
    </script>
</x-layouts.admin>
