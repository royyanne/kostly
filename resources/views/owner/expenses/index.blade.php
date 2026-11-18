<x-layouts.owner>
    <x-slot:header>
        Pengeluaran Operasional
    </x-slot:header>

    {{-- Page Header & Actions --}}
    <div class="mt-4 flex flex-col md:flex-row md:items-center justify-between gap-4 bg-white border border-slate-200/90 rounded-2xl p-5 sm:p-6 shadow-xs">
        <div>
            <h2 class="text-xl font-bold text-slate-900 tracking-tight">Pencatatan Biaya Operasional</h2>
            <p class="text-xs sm:text-sm text-slate-500 mt-1">
                Pantau seluruh pengeluaran rutin kost (listrik, WiFi, kebersihan, servis AC) untuk kalkulasi laba bersih.
            </p>
        </div>

        <div>
            <button type="button" onclick="openModal('add-expense-modal')" class="inline-flex items-center gap-2 rounded-xl bg-[#1E3A8A] px-4 py-2.5 text-xs sm:text-sm font-bold text-white shadow-md shadow-indigo-950/20 hover:bg-[#172554] transition-colors">
                <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                <span>Catat Pengeluaran Baru</span>
            </button>
        </div>
    </div>

    {{-- Summary Cards --}}
    <div class="mt-6 grid grid-cols-1 sm:grid-cols-2 gap-4 sm:gap-6">
        <div class="rounded-2xl border border-slate-200 bg-white p-5 shadow-xs">
            <span class="text-xs font-semibold uppercase tracking-wider text-slate-500">Total Pengeluaran Bulan Ini ({{ now()->translatedFormat('F Y') }})</span>
            <div class="mt-2 text-3xl font-extrabold text-rose-600">
                Rp {{ number_format((float) $totalThisMonth, 0, ',', '.') }}
            </div>
            <p class="mt-1 text-[11px] text-slate-400">Termasuk tagihan listrik, internet, dan pemeliharaan</p>
        </div>

        <div class="rounded-2xl border border-slate-200 bg-white p-5 shadow-xs">
            <span class="text-xs font-semibold uppercase tracking-wider text-slate-500">Total Pengeluaran Sepanjang Masa</span>
            <div class="mt-2 text-3xl font-extrabold text-slate-900">
                Rp {{ number_format((float) $totalAllTime, 0, ',', '.') }}
            </div>
            <p class="mt-1 text-[11px] text-slate-400">Total akumulasi seluruh biaya operasional tercatat</p>
        </div>
    </div>

    {{-- Filter Bar --}}
    <div class="mt-6 flex flex-col md:flex-row md:items-center justify-between gap-4 bg-white border border-slate-200/90 rounded-2xl p-4 shadow-xs">
        <div class="flex flex-wrap items-center gap-1.5 text-xs font-semibold">
            <a href="{{ route('owner.expenses.index', array_merge(request()->query(), ['category' => ''])) }}"
                class="px-3 py-1.5 rounded-xl border transition-all {{ empty($categoryFilter) ? 'bg-[#1E3A8A] text-white border-[#1E3A8A]' : 'bg-slate-50 text-slate-600 border-slate-200 hover:bg-slate-100' }}">
                Semua
            </a>
            @foreach ($categories as $cat)
                <a href="{{ route('owner.expenses.index', array_merge(request()->query(), ['category' => $cat])) }}"
                    class="px-3 py-1.5 rounded-xl border transition-all {{ $categoryFilter === $cat ? 'bg-[#1E3A8A] text-white border-[#1E3A8A]' : 'bg-slate-50 text-slate-600 border-slate-200 hover:bg-slate-100' }}">
                    {{ $cat }}
                </a>
            @endforeach
        </div>

        <form action="{{ route('owner.expenses.index') }}" method="GET" class="flex flex-wrap items-center gap-2">
            @if ($categoryFilter)
                <input type="hidden" name="category" value="{{ $categoryFilter }}">
            @endif

            <div class="relative">
                <input type="text" name="search" value="{{ $search }}" placeholder="Cari judul biaya..."
                    class="rounded-xl border border-slate-300 py-1.5 pl-8 pr-3 text-xs text-slate-900 placeholder-slate-400 focus:border-[#1E3A8A] focus:outline-hidden">
                <svg class="h-4 w-4 text-slate-400 absolute left-2.5 top-2" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
            </div>

            @if ($search || $categoryFilter)
                <a href="{{ route('owner.expenses.index') }}" class="text-xs text-rose-600 font-semibold hover:underline">Reset</a>
            @endif
        </form>
    </div>

    {{-- Expenses Table --}}
    @if ($expenses->isEmpty())
        <div class="mt-6 bg-white border border-slate-200/90 rounded-2xl p-12 text-center shadow-xs">
            <div class="flex h-16 w-16 items-center justify-center rounded-2xl bg-indigo-50 text-[#1E3A8A] mx-auto mb-4">
                <svg class="h-8 w-8" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"/></svg>
            </div>
            <h3 class="text-base font-bold text-slate-900">Belum ada catatan pengeluaran</h3>
            <p class="text-xs text-slate-500 mt-1 max-w-sm mx-auto">
                Catat pengeluaran rutin Anda untuk memantau neraca laba bersih properti kosan.
            </p>
            <button type="button" onclick="openModal('add-expense-modal')" class="mt-5 inline-flex items-center gap-2 rounded-xl bg-[#1E3A8A] px-5 py-2.5 text-xs font-bold text-white shadow-md hover:bg-[#172554] transition-colors">
                Catat Biaya Sekarang
            </button>
        </div>
    @else
        <div class="mt-6 bg-white border border-slate-200/90 rounded-2xl overflow-hidden shadow-xs">
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-slate-200 text-left text-xs">
                    <thead class="bg-slate-50 text-slate-500 font-bold uppercase tracking-wider">
                        <tr>
                            <th class="px-5 py-3.5">Judul Pengeluaran</th>
                            <th class="px-5 py-3.5">Properti</th>
                            <th class="px-5 py-3.5">Kategori</th>
                            <th class="px-5 py-3.5">Nominal (Rp)</th>
                            <th class="px-5 py-3.5">Tanggal</th>
                            <th class="px-5 py-3.5">Catatan</th>
                            <th class="px-5 py-3.5 text-right">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 font-medium text-slate-700">
                        @foreach ($expenses as $expense)
                            <tr class="hover:bg-slate-50/70">
                                <td class="px-5 py-4 font-bold text-slate-900 text-sm">
                                    {{ $expense->title }}
                                </td>
                                <td class="px-5 py-4 text-slate-500">
                                    {{ $expense->property->name }}
                                </td>
                                <td class="px-5 py-4">
                                    <span class="inline-block px-2.5 py-0.5 rounded-full bg-slate-100 font-semibold text-slate-700">
                                        {{ $expense->category }}
                                    </span>
                                </td>
                                <td class="px-5 py-4 font-extrabold text-rose-600 text-sm">
                                    Rp {{ number_format((float) $expense->amount, 0, ',', '.') }}
                                </td>
                                <td class="px-5 py-4 text-slate-600">
                                    {{ $expense->expense_date->translatedFormat('d M Y') }}
                                </td>
                                <td class="px-5 py-4 text-slate-500 max-w-xs truncate">
                                    {{ $expense->notes ?? '-' }}
                                </td>
                                <td class="px-5 py-4 text-right">
                                    <div class="inline-flex items-center gap-2">
                                        <button type="button" onclick="openEditExpenseModal({{ json_encode($expense) }})" class="font-bold text-[#1E3A8A] hover:underline">
                                            Edit
                                        </button>
                                        <form action="{{ route('owner.expenses.destroy', $expense) }}" method="POST" onsubmit="return confirm('Hapus pengeluaran ini?');" class="inline">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="font-bold text-rose-600 hover:underline">
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

    {{-- Modal Tambah Pengeluaran --}}
    <div id="add-expense-modal" class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-900/50 backdrop-blur-xs hidden">
        <div class="w-full max-w-lg rounded-2xl bg-white p-6 shadow-2xl">
            <div class="flex items-center justify-between border-b border-slate-100 pb-3 mb-4">
                <h3 class="text-lg font-bold text-slate-900">Catat Pengeluaran Baru</h3>
                <button type="button" onclick="closeModal('add-expense-modal')" class="text-slate-400 hover:text-slate-600 font-bold text-lg">✕</button>
            </div>

            <form action="{{ route('owner.expenses.store') }}" method="POST" class="space-y-4">
                @csrf

                <div>
                    <label class="block text-xs font-semibold uppercase tracking-wider text-slate-700">Pilih Properti Kost</label>
                    <select name="property_id" required class="mt-1 block w-full rounded-xl border border-slate-300 px-3.5 py-2 text-sm focus:border-[#1E3A8A] focus:outline-hidden">
                        @foreach ($properties as $prop)
                            <option value="{{ $prop->id }}" {{ $selectedPropertyId == $prop->id ? 'selected' : '' }}>
                                {{ $prop->name }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label class="block text-xs font-semibold uppercase tracking-wider text-slate-700">Judul Pengeluaran</label>
                    <input type="text" name="title" required class="mt-1 block w-full rounded-xl border border-slate-300 px-3.5 py-2 text-sm focus:border-[#1E3A8A] focus:outline-hidden" placeholder="Misal: Tagihan Listrik PLN, Servis AC Kamar 102">
                </div>

                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label class="block text-xs font-semibold uppercase tracking-wider text-slate-700">Kategori</label>
                        <select name="category" required class="mt-1 block w-full rounded-xl border border-slate-300 px-3.5 py-2 text-sm focus:border-[#1E3A8A] focus:outline-hidden">
                            @foreach ($categories as $c)
                                <option value="{{ $c }}">{{ $c }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <label class="block text-xs font-semibold uppercase tracking-wider text-slate-700">Nominal Biaya (Rp)</label>
                        <input type="number" name="amount" min="0" step="1000" required class="mt-1 block w-full rounded-xl border border-slate-300 px-3.5 py-2 text-sm focus:border-[#1E3A8A] focus:outline-hidden" placeholder="250000">
                    </div>
                </div>

                <div>
                    <label class="block text-xs font-semibold uppercase tracking-wider text-slate-700">Tanggal Pengeluaran</label>
                    <input type="date" name="expense_date" value="{{ now()->toDateString() }}" required class="mt-1 block w-full rounded-xl border border-slate-300 px-3.5 py-2 text-sm focus:border-[#1E3A8A] focus:outline-hidden">
                </div>

                <div>
                    <label class="block text-xs font-semibold uppercase tracking-wider text-slate-700">Catatan / Rincian Tambahan</label>
                    <textarea name="notes" rows="2" class="mt-1 block w-full rounded-xl border border-slate-300 px-3.5 py-2 text-sm focus:border-[#1E3A8A] focus:outline-hidden" placeholder="Nomor invoice vendor, teknisi yang mengerjakan, dll"></textarea>
                </div>

                <div class="flex items-center justify-end gap-3 pt-3 border-t border-slate-100">
                    <button type="button" onclick="closeModal('add-expense-modal')" class="rounded-xl border border-slate-300 px-4 py-2 text-xs font-semibold text-slate-700 hover:bg-slate-50">Batal</button>
                    <button type="submit" class="rounded-xl bg-[#1E3A8A] px-5 py-2 text-xs font-bold text-white hover:bg-[#172554]">Simpan Biaya</button>
                </div>
            </form>
        </div>
    </div>

    {{-- Modal Edit Pengeluaran --}}
    <div id="edit-expense-modal" class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-900/50 backdrop-blur-xs hidden">
        <div class="w-full max-w-lg rounded-2xl bg-white p-6 shadow-2xl">
            <div class="flex items-center justify-between border-b border-slate-100 pb-3 mb-4">
                <h3 class="text-lg font-bold text-slate-900">Ubah Data Pengeluaran</h3>
                <button type="button" onclick="closeModal('edit-expense-modal')" class="text-slate-400 hover:text-slate-600 font-bold text-lg">✕</button>
            </div>

            <form id="edit-expense-form" method="POST" class="space-y-4">
                @csrf
                @method('PUT')

                <div>
                    <label class="block text-xs font-semibold uppercase tracking-wider text-slate-700">Pilih Properti Kost</label>
                    <select id="edit-expense-property" name="property_id" required class="mt-1 block w-full rounded-xl border border-slate-300 px-3.5 py-2 text-sm focus:border-[#1E3A8A] focus:outline-hidden">
                        @foreach ($properties as $prop)
                            <option value="{{ $prop->id }}">{{ $prop->name }}</option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label class="block text-xs font-semibold uppercase tracking-wider text-slate-700">Judul Pengeluaran</label>
                    <input type="text" id="edit-expense-title" name="title" required class="mt-1 block w-full rounded-xl border border-slate-300 px-3.5 py-2 text-sm focus:border-[#1E3A8A] focus:outline-hidden">
                </div>

                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label class="block text-xs font-semibold uppercase tracking-wider text-slate-700">Kategori</label>
                        <select id="edit-expense-category" name="category" required class="mt-1 block w-full rounded-xl border border-slate-300 px-3.5 py-2 text-sm focus:border-[#1E3A8A] focus:outline-hidden">
                            @foreach ($categories as $c)
                                <option value="{{ $c }}">{{ $c }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <label class="block text-xs font-semibold uppercase tracking-wider text-slate-700">Nominal Biaya (Rp)</label>
                        <input type="number" id="edit-expense-amount" name="amount" min="0" step="1000" required class="mt-1 block w-full rounded-xl border border-slate-300 px-3.5 py-2 text-sm focus:border-[#1E3A8A] focus:outline-hidden">
                    </div>
                </div>

                <div>
                    <label class="block text-xs font-semibold uppercase tracking-wider text-slate-700">Tanggal Pengeluaran</label>
                    <input type="date" id="edit-expense-date" name="expense_date" required class="mt-1 block w-full rounded-xl border border-slate-300 px-3.5 py-2 text-sm focus:border-[#1E3A8A] focus:outline-hidden">
                </div>

                <div>
                    <label class="block text-xs font-semibold uppercase tracking-wider text-slate-700">Catatan Tambahan</label>
                    <textarea id="edit-expense-notes" name="notes" rows="2" class="mt-1 block w-full rounded-xl border border-slate-300 px-3.5 py-2 text-sm focus:border-[#1E3A8A] focus:outline-hidden"></textarea>
                </div>

                <div class="flex items-center justify-end gap-3 pt-3 border-t border-slate-100">
                    <button type="button" onclick="closeModal('edit-expense-modal')" class="rounded-xl border border-slate-300 px-4 py-2 text-xs font-semibold text-slate-700 hover:bg-slate-50">Batal</button>
                    <button type="submit" class="rounded-xl bg-[#1E3A8A] px-5 py-2 text-xs font-bold text-white hover:bg-[#172554]">Simpan Perubahan</button>
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

        function openEditExpenseModal(exp) {
            const form = document.getElementById('edit-expense-form');
            form.action = `/dashboard/expenses/${exp.id}`;
            document.getElementById('edit-expense-property').value = exp.property_id;
            document.getElementById('edit-expense-title').value = exp.title;
            document.getElementById('edit-expense-category').value = exp.category;
            document.getElementById('edit-expense-amount').value = parseInt(exp.amount);
            document.getElementById('edit-expense-date').value = exp.expense_date.split('T')[0];
            document.getElementById('edit-expense-notes').value = exp.notes || '';
            openModal('edit-expense-modal');
        }
    </script>
</x-layouts.owner>
