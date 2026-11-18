<x-layouts.owner>
    <x-slot:header>
        Tagihan & Invoice Bulanan
    </x-slot:header>

    {{-- Page Header & Actions --}}
    <div class="mt-4 flex flex-col md:flex-row md:items-center justify-between gap-4 bg-white border border-slate-200/90 rounded-2xl p-5 sm:p-6 shadow-xs">
        <div>
            <h2 class="text-xl font-bold text-slate-900 tracking-tight">Tagihan & Penagihan Otomatis</h2>
            <p class="text-xs sm:text-sm text-slate-500 mt-1">
                Kelola status pembayaran sewa, buat tagihan bulanan otomatis, dan kirim pengingat WhatsApp.
            </p>
        </div>

        <div class="flex flex-wrap items-center gap-2.5">
            {{-- Tombol Generate Tagihan Otomatis --}}
            <form action="{{ route('owner.invoices.generateMonthly') }}" method="POST" onsubmit="return confirm('Generate tagihan bulan ini untuk seluruh penghuni aktif yang belum ditagih?');">
                @csrf
                <button type="submit" class="inline-flex items-center gap-2 rounded-xl bg-emerald-600 px-4 py-2.5 text-xs sm:text-sm font-bold text-white shadow-md shadow-emerald-950/20 hover:bg-emerald-700 transition-colors">
                    <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/></svg>
                    <span>Generate Tagihan Bulan Ini</span>
                </button>
            </form>

            {{-- Tombol Buat Tagihan Manual --}}
            <button type="button" onclick="openModal('add-invoice-modal')" class="inline-flex items-center gap-2 rounded-xl bg-[#1E3A8A] px-4 py-2.5 text-xs sm:text-sm font-bold text-white shadow-md shadow-indigo-950/20 hover:bg-[#172554] transition-colors">
                <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                <span>Buat Tagihan Manual</span>
            </button>
        </div>
    </div>

    {{-- Summary Cards --}}
    <div class="mt-6 grid grid-cols-1 sm:grid-cols-3 gap-4 sm:gap-6">
        {{-- Belum Lunas --}}
        <div class="rounded-2xl border border-amber-200 bg-white p-5 shadow-xs">
            <div class="flex items-center justify-between text-xs text-amber-800 font-semibold uppercase tracking-wider">
                <span>Belum Lunas</span>
                <span class="flex h-7 w-7 items-center justify-center rounded-lg bg-amber-50 text-amber-700 font-bold">
                    {{ $unpaidCount }}
                </span>
            </div>
            <div class="mt-3 text-2xl font-black text-amber-600">
                Rp {{ number_format((float) $unpaidSum, 0, ',', '.') }}
            </div>
            <p class="mt-1 text-[11px] text-slate-400">Menunggu pembayaran penghuni</p>
        </div>

        {{-- Lunas --}}
        <div class="rounded-2xl border border-emerald-200 bg-white p-5 shadow-xs">
            <div class="flex items-center justify-between text-xs text-emerald-800 font-semibold uppercase tracking-wider">
                <span>Sudah Lunas</span>
                <span class="flex h-7 w-7 items-center justify-center rounded-lg bg-emerald-50 text-emerald-700 font-bold">
                    {{ $paidCount }}
                </span>
            </div>
            <div class="mt-3 text-2xl font-black text-emerald-600">
                Rp {{ number_format((float) $paidSum, 0, ',', '.') }}
            </div>
            <p class="mt-1 text-[11px] text-slate-400">Pembayaran terverifikasi masuk</p>
        </div>

        {{-- Overdue --}}
        <div class="rounded-2xl border border-rose-200 bg-white p-5 shadow-xs">
            <div class="flex items-center justify-between text-xs text-rose-800 font-semibold uppercase tracking-wider">
                <span>Lewat Jatuh Tempo</span>
                <span class="flex h-7 w-7 items-center justify-center rounded-lg bg-rose-50 text-rose-700 font-bold">
                    {{ $overdueCount }}
                </span>
            </div>
            <div class="mt-3 text-2xl font-black text-rose-600">
                Rp {{ number_format((float) $overdueSum, 0, ',', '.') }}
            </div>
            <p class="mt-1 text-[11px] text-slate-400">Butuh pengingat WhatsApp lanjutan</p>
        </div>
    </div>

    {{-- Filter Bar --}}
    <div class="mt-6 flex flex-col md:flex-row md:items-center justify-between gap-4 bg-white border border-slate-200/90 rounded-2xl p-4 shadow-xs">
        <div class="flex flex-wrap items-center gap-2 text-xs font-semibold">
            <a href="{{ route('owner.invoices.index', array_merge(request()->query(), ['status' => ''])) }}"
                class="px-3.5 py-1.5 rounded-xl border transition-all {{ empty($statusFilter) ? 'bg-[#1E3A8A] text-white border-[#1E3A8A]' : 'bg-slate-50 text-slate-600 border-slate-200 hover:bg-slate-100' }}">
                Semua ({{ $totalCount }})
            </a>
            <a href="{{ route('owner.invoices.index', array_merge(request()->query(), ['status' => 'unpaid'])) }}"
                class="inline-flex items-center gap-1.5 px-3.5 py-1.5 rounded-xl border transition-all {{ $statusFilter === 'unpaid' ? 'bg-amber-600 text-white border-amber-600' : 'bg-amber-50 text-amber-800 border-amber-200 hover:bg-amber-100' }}">
                <span class="h-2 w-2 rounded-full {{ $statusFilter === 'unpaid' ? 'bg-white' : 'bg-amber-500' }}"></span>
                Belum Lunas ({{ $unpaidCount }})
            </a>
            <a href="{{ route('owner.invoices.index', array_merge(request()->query(), ['status' => 'paid'])) }}"
                class="inline-flex items-center gap-1.5 px-3.5 py-1.5 rounded-xl border transition-all {{ $statusFilter === 'paid' ? 'bg-emerald-600 text-white border-emerald-600' : 'bg-emerald-50 text-emerald-800 border-emerald-200 hover:bg-emerald-100' }}">
                <span class="h-2 w-2 rounded-full {{ $statusFilter === 'paid' ? 'bg-white' : 'bg-emerald-500' }}"></span>
                Lunas ({{ $paidCount }})
            </a>
            <a href="{{ route('owner.invoices.index', array_merge(request()->query(), ['status' => 'overdue'])) }}"
                class="inline-flex items-center gap-1.5 px-3.5 py-1.5 rounded-xl border transition-all {{ $statusFilter === 'overdue' ? 'bg-rose-600 text-white border-rose-600' : 'bg-rose-50 text-rose-800 border-rose-200 hover:bg-rose-100' }}">
                <span class="h-2 w-2 rounded-full {{ $statusFilter === 'overdue' ? 'bg-white' : 'bg-rose-500' }}"></span>
                Lewat Tempo ({{ $overdueCount }})
            </a>
        </div>

        <form action="{{ route('owner.invoices.index') }}" method="GET" class="flex flex-wrap items-center gap-2">
            @if ($statusFilter)
                <input type="hidden" name="status" value="{{ $statusFilter }}">
            @endif

            <div class="relative">
                <input type="text" name="search" value="{{ $search }}" placeholder="Cari kode/penghuni..."
                    class="rounded-xl border border-slate-300 py-1.5 pl-8 pr-3 text-xs text-slate-900 placeholder-slate-400 focus:border-[#1E3A8A] focus:outline-hidden">
                <svg class="h-4 w-4 text-slate-400 absolute left-2.5 top-2" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
            </div>

            @if ($search || $statusFilter)
                <a href="{{ route('owner.invoices.index') }}" class="text-xs text-rose-600 font-semibold hover:underline">Reset</a>
            @endif
        </form>
    </div>

    {{-- Invoices Table --}}
    @if ($invoices->isEmpty())
        <div class="mt-6 bg-white border border-slate-200/90 rounded-2xl p-12 text-center shadow-xs">
            <div class="flex h-16 w-16 items-center justify-center rounded-2xl bg-indigo-50 text-[#1E3A8A] mx-auto mb-4">
                <svg class="h-8 w-8" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 14l6-6m-5.5.5h.01m4.99 5h.01M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16l3.5-2 3.5 2 3.5-2 3.5 2zM10 8.5a.5.5 0 11-1 0 .5.5 0 011 0zm5 5a.5.5 0 11-1 0 .5.5 0 011 0z"/></svg>
            </div>
            <h3 class="text-base font-bold text-slate-900">Belum ada data tagihan</h3>
            <p class="text-xs text-slate-500 mt-1 max-w-sm mx-auto">
                Buat tagihan pertama Anda atau klik "Generate Tagihan Bulan Ini" untuk membuat tagihan otomatis seluruh penghuni aktif.
            </p>
        </div>
    @else
        <div class="mt-6 bg-white border border-slate-200/90 rounded-2xl overflow-hidden shadow-xs">
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-slate-200 text-left text-xs">
                    <thead class="bg-slate-50 text-slate-500 font-bold uppercase tracking-wider">
                        <tr>
                            <th class="px-5 py-3.5">Kode Tagihan</th>
                            <th class="px-5 py-3.5">Penghuni & Kamar</th>
                            <th class="px-5 py-3.5">Total Tagihan</th>
                            <th class="px-5 py-3.5">Jatuh Tempo</th>
                            <th class="px-5 py-3.5">Status</th>
                            <th class="px-5 py-3.5">Metode Bayar</th>
                            <th class="px-5 py-3.5 text-right">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 font-medium text-slate-700">
                        @foreach ($invoices as $invoice)
                            <tr class="hover:bg-slate-50/70">
                                {{-- Kode Invoice --}}
                                <td class="px-5 py-4">
                                    <a href="{{ route('public.pay', $invoice->invoice_code) }}" target="_blank"
                                        class="font-mono font-bold text-[#1E3A8A] hover:underline inline-flex items-center gap-1">
                                        <span>#{{ $invoice->invoice_code }}</span>
                                        <svg class="h-3 w-3" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/></svg>
                                    </a>
                                </td>

                                {{-- Penghuni & Kamar --}}
                                <td class="px-5 py-4">
                                    <span class="font-bold text-slate-900 block text-sm">{{ $invoice->tenant->name }}</span>
                                    <span class="inline-block text-[11px] text-slate-500 font-semibold">
                                        Kamar {{ $invoice->tenant->room?->room_number ?? '-' }}
                                    </span>
                                </td>

                                {{-- Total Nominal --}}
                                <td class="px-5 py-4">
                                    <span class="font-extrabold text-slate-900 text-sm block">
                                        Rp {{ number_format((float) $invoice->total_amount, 0, ',', '.') }}
                                    </span>
                                    @if ($invoice->additional_fee > 0)
                                        <span class="text-[10px] text-slate-400">
                                            Pokok: {{ number_format((float) $invoice->amount, 0, ',', '.') }} + Tambahan: {{ number_format((float) $invoice->additional_fee, 0, ',', '.') }}
                                        </span>
                                    @endif
                                </td>

                                {{-- Jatuh Tempo --}}
                                <td class="px-5 py-4">
                                    <span class="font-semibold text-slate-800 block">{{ $invoice->due_date->translatedFormat('d M Y') }}</span>
                                    <span class="text-[10px] {{ $invoice->due_date->isPast() && $invoice->status !== App\Enums\InvoiceStatus::Paid ? 'text-rose-600 font-bold' : 'text-slate-400' }}">
                                        {{ $invoice->due_date->diffForHumans() }}
                                    </span>
                                </td>

                                {{-- Status --}}
                                <td class="px-5 py-4">
                                    <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-[11px] font-bold {{ $invoice->status->badgeClass() }}">
                                        {{ $invoice->status->label() }}
                                    </span>
                                </td>

                                {{-- Metode Bayar --}}
                                <td class="px-5 py-4">
                                    @if ($invoice->status === App\Enums\InvoiceStatus::Paid)
                                        <span class="font-bold text-emerald-800">{{ $invoice->payment_method ?? 'Lunas' }}</span>
                                        <span class="block text-[10px] text-slate-400">{{ $invoice->paid_at?->translatedFormat('d/m/Y H:i') }}</span>
                                    @else
                                        <span class="text-slate-400 italic">Belum Dibayar</span>
                                    @endif
                                </td>

                                {{-- Aksi --}}
                                <td class="px-5 py-4 text-right">
                                    <div class="inline-flex items-center gap-2">
                                        {{-- Tandai Lunas --}}
                                        @if ($invoice->status !== App\Enums\InvoiceStatus::Paid)
                                            <button type="button" onclick="openMarkPaidModal({{ json_encode($invoice) }})"
                                                class="px-2.5 py-1 rounded-lg bg-emerald-600 text-white font-bold hover:bg-emerald-700 transition-colors" title="Tandai Lunas">
                                                Lunas
                                            </button>

                                            {{-- Kirim Pengingat WhatsApp --}}
                                            <form action="{{ route('owner.invoices.sendReminder', $invoice) }}" method="POST" class="inline">
                                                @csrf
                                                <button type="submit" class="px-2.5 py-1 rounded-lg border border-slate-300 font-bold text-slate-700 hover:bg-slate-50 transition-colors" title="Kirim Pengingat WA">
                                                    Kirim WA
                                                </button>
                                            </form>
                                        @endif

                                        <form action="{{ route('owner.invoices.destroy', $invoice) }}" method="POST" onsubmit="return confirm('Hapus tagihan #{{ $invoice->invoice_code }}?');" class="inline">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="text-rose-600 font-bold hover:underline p-1" title="Hapus Tagihan">
                                                Hapus
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    @endif

    {{-- Modal Buat Tagihan Manual --}}
    <div id="add-invoice-modal" class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-900/50 backdrop-blur-xs hidden">
        <div class="w-full max-w-lg rounded-2xl bg-white p-6 shadow-2xl">
            <div class="flex items-center justify-between border-b border-slate-100 pb-3 mb-4">
                <h3 class="text-lg font-bold text-slate-900">Buat Tagihan Sewa Baru</h3>
                <button type="button" onclick="closeModal('add-invoice-modal')" class="text-slate-400 hover:text-slate-600 font-bold text-lg">✕</button>
            </div>

            <form action="{{ route('owner.invoices.store') }}" method="POST" class="space-y-4">
                @csrf

                <div>
                    <label class="block text-xs font-semibold uppercase tracking-wider text-slate-700">Pilih Penghuni yang Ditagih</label>
                    <select name="tenant_id" id="invoice-tenant-id" onchange="autoFillInvoiceAmount(this)" required class="mt-1 block w-full rounded-xl border border-slate-300 px-3.5 py-2 text-sm focus:border-[#1E3A8A] focus:outline-hidden">
                        <option value="">-- Pilih Penghuni Aktif --</option>
                        @foreach ($activeTenants as $t)
                            <option value="{{ $t->id }}" data-price="{{ $t->room?->price ?? 0 }}">
                                {{ $t->name }} (Kamar {{ $t->room?->room_number ?? '-' }} - {{ $t->property->name }})
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label class="block text-xs font-semibold uppercase tracking-wider text-slate-700">Biaya Pokok Sewa (Rp)</label>
                        <input type="number" id="invoice-amount" name="amount" min="0" step="10000" required class="mt-1 block w-full rounded-xl border border-slate-300 px-3.5 py-2 text-sm focus:border-[#1E3A8A] focus:outline-hidden" placeholder="1500000">
                    </div>

                    <div>
                        <label class="block text-xs font-semibold uppercase tracking-wider text-slate-700">Biaya Tambahan (Rp)</label>
                        <input type="number" name="additional_fee" min="0" step="10000" value="0" class="mt-1 block w-full rounded-xl border border-slate-300 px-3.5 py-2 text-sm focus:border-[#1E3A8A] focus:outline-hidden" placeholder="0">
                    </div>
                </div>

                <div>
                    <label class="block text-xs font-semibold uppercase tracking-wider text-slate-700">Batas Tanggal Jatuh Tempo</label>
                    <input type="date" name="due_date" value="{{ now()->addDays(5)->toDateString() }}" required class="mt-1 block w-full rounded-xl border border-slate-300 px-3.5 py-2 text-sm focus:border-[#1E3A8A] focus:outline-hidden">
                </div>

                <div class="flex items-center justify-end gap-3 pt-3 border-t border-slate-100">
                    <button type="button" onclick="closeModal('add-invoice-modal')" class="rounded-xl border border-slate-300 px-4 py-2 text-xs font-semibold text-slate-700 hover:bg-slate-50">Batal</button>
                    <button type="submit" class="rounded-xl bg-[#1E3A8A] px-5 py-2 text-xs font-bold text-white hover:bg-[#172554]">Buat Tagihan</button>
                </div>
            </form>
        </div>
    </div>

    {{-- Modal Tandai Lunas --}}
    <div id="mark-paid-modal" class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-900/50 backdrop-blur-xs hidden">
        <div class="w-full max-w-md rounded-2xl bg-white p-6 shadow-2xl">
            <div class="flex items-center justify-between border-b border-slate-100 pb-3 mb-4">
                <h3 class="text-lg font-bold text-slate-900">Konfirmasi Pelunasan Tagihan</h3>
                <button type="button" onclick="closeModal('mark-paid-modal')" class="text-slate-400 hover:text-slate-600 font-bold text-lg">✕</button>
            </div>

            <form id="mark-paid-form" method="POST" class="space-y-4">
                @csrf
                @method('PATCH')

                <div>
                    <p class="text-xs text-slate-600 mb-3">
                        Tandai tagihan <strong id="paid-invoice-code">#INV</strong> sebesar <strong id="paid-total-amount">Rp 0</strong> sebagai Lunas.
                    </p>

                    <label class="block text-xs font-semibold uppercase tracking-wider text-slate-700">Pilih Metode Pembayaran</label>
                    <select name="payment_method" required class="mt-1 block w-full rounded-xl border border-slate-300 px-3.5 py-2 text-sm focus:border-emerald-600 focus:outline-hidden">
                        <option value="Transfer BCA">Transfer Bank BCA</option>
                        <option value="Transfer Mandiri">Transfer Bank Mandiri</option>
                        <option value="Transfer BNI / BRI">Transfer Bank BNI / BRI</option>
                        <option value="QRIS">QRIS / E-Wallet</option>
                        <option value="Uang Tunai / Cash">Uang Tunai / Cash</option>
                    </select>
                </div>

                <div class="flex items-center justify-end gap-3 pt-3 border-t border-slate-100">
                    <button type="button" onclick="closeModal('mark-paid-modal')" class="rounded-xl border border-slate-300 px-4 py-2 text-xs font-semibold text-slate-700 hover:bg-slate-50">Batal</button>
                    <button type="submit" class="rounded-xl bg-emerald-600 px-5 py-2 text-xs font-bold text-white hover:bg-emerald-700">Simpan Sebagai Lunas</button>
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

        function autoFillInvoiceAmount(select) {
            const selectedOption = select.options[select.selectedIndex];
            const price = selectedOption.getAttribute('data-price');
            if (price) {
                document.getElementById('invoice-amount').value = parseInt(price);
            }
        }

        function openMarkPaidModal(invoice) {
            const form = document.getElementById('mark-paid-form');
            form.action = `/dashboard/invoices/${invoice.id}/mark-paid`;
            document.getElementById('paid-invoice-code').textContent = '#' + invoice.invoice_code;
            document.getElementById('paid-total-amount').textContent = 'Rp ' + new Intl.NumberFormat('id-ID').format(invoice.total_amount);
            openModal('mark-paid-modal');
        }
    </script>
</x-layouts.owner>
