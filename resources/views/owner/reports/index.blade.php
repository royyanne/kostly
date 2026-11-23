<x-layouts.owner>
    <x-slot:header>
        Laporan Keuangan
    </x-slot:header>

    {{-- Page Header --}}
    <div class="mt-4 flex flex-col md:flex-row md:items-center justify-between gap-4 bg-white border border-slate-200/90 rounded-2xl p-5 sm:p-6 shadow-xs">
        <div>
            <h2 class="text-xl font-bold text-slate-900 tracking-tight">Rekap Laporan Keuangan Bulanan</h2>
            <p class="text-xs sm:text-sm text-slate-500 mt-1">
                Pantau pemasukan, pengeluaran, dan laba bersih kost Anda dalam satu laporan terintegrasi.
            </p>
        </div>

        {{-- Export Buttons --}}
        <div class="flex flex-wrap items-center gap-2 shrink-0">
            <a href="{{ route('owner.reports.exportCsv', array_filter(['month' => $month, 'year' => $year, 'property_id' => $selectedPropertyId])) }}"
               class="inline-flex items-center gap-2 rounded-xl border border-emerald-200 bg-emerald-50 px-4 py-2.5 text-xs sm:text-sm font-bold text-emerald-700 hover:bg-emerald-100 transition-colors">
                <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                Export Excel (CSV)
            </a>
            <a href="{{ route('owner.reports.print', array_filter(['month' => $month, 'year' => $year, 'property_id' => $selectedPropertyId])) }}"
               target="_blank"
               class="inline-flex items-center gap-2 rounded-xl bg-[#1E3A8A] px-4 py-2.5 text-xs sm:text-sm font-bold text-white shadow-md shadow-indigo-950/20 hover:bg-[#172554] transition-colors">
                <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/></svg>
                Cetak Laporan (PDF)
            </a>
        </div>
    </div>

    {{-- Filter Form --}}
    <div class="mt-6 bg-white border border-slate-200 rounded-2xl p-5 sm:p-6 shadow-xs">
        <h3 class="text-sm font-bold text-slate-700 mb-4">Filter Periode Laporan</h3>
        <form method="GET" action="{{ route('owner.reports.index') }}" class="flex flex-wrap items-end gap-3">
            {{-- Bulan --}}
            <div class="flex flex-col gap-1">
                <label for="month" class="text-xs font-semibold text-slate-600">Bulan</label>
                <select id="month" name="month" class="rounded-xl border border-slate-200 bg-slate-50 px-3 py-2 text-sm text-slate-700 focus:border-indigo-400 focus:ring-2 focus:ring-indigo-100 focus:outline-none">
                    @foreach(range(1, 12) as $m)
                        <option value="{{ $m }}" @selected($m === $month)>
                            {{ \Carbon\Carbon::create(null, $m, 1)->translatedFormat('F') }}
                        </option>
                    @endforeach
                </select>
            </div>

            {{-- Tahun --}}
            <div class="flex flex-col gap-1">
                <label for="year" class="text-xs font-semibold text-slate-600">Tahun</label>
                <select id="year" name="year" class="rounded-xl border border-slate-200 bg-slate-50 px-3 py-2 text-sm text-slate-700 focus:border-indigo-400 focus:ring-2 focus:ring-indigo-100 focus:outline-none">
                    @foreach(range(now()->year - 2, now()->year + 1) as $y)
                        <option value="{{ $y }}" @selected($y === $year)>{{ $y }}</option>
                    @endforeach
                </select>
            </div>

            {{-- Properti --}}
            @if($properties->count() > 1)
                <div class="flex flex-col gap-1">
                    <label for="property_id" class="text-xs font-semibold text-slate-600">Properti</label>
                    <select id="property_id" name="property_id" class="rounded-xl border border-slate-200 bg-slate-50 px-3 py-2 text-sm text-slate-700 focus:border-indigo-400 focus:ring-2 focus:ring-indigo-100 focus:outline-none">
                        <option value="">Semua Properti</option>
                        @foreach($properties as $property)
                            <option value="{{ $property->id }}" @selected($property->id === $selectedPropertyId)>{{ $property->name }}</option>
                        @endforeach
                    </select>
                </div>
            @endif

            <button type="submit" class="inline-flex items-center gap-2 rounded-xl bg-[#1E3A8A] px-4 py-2.5 text-sm font-bold text-white hover:bg-[#172554] transition-colors">
                <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z"/></svg>
                Tampilkan
            </button>
        </form>
    </div>

    {{-- Ringkasan Keuangan --}}
    <div class="mt-6 grid grid-cols-1 sm:grid-cols-3 gap-4 sm:gap-6">
        {{-- Total Pemasukan --}}
        <div class="rounded-2xl border border-emerald-100 bg-emerald-50 p-5 shadow-xs">
            <div class="flex items-center gap-3 mb-3">
                <div class="flex h-10 w-10 items-center justify-center rounded-xl bg-emerald-500/10">
                    <svg class="h-5 w-5 text-emerald-600" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 11l5-5m0 0l5 5m-5-5v12"/></svg>
                </div>
                <span class="text-xs font-semibold uppercase tracking-wider text-emerald-700">Total Pemasukan</span>
            </div>
            <div class="text-2xl sm:text-3xl font-extrabold text-emerald-700">
                Rp {{ number_format($totalIncome, 0, ',', '.') }}
            </div>
            <p class="mt-1 text-[11px] text-emerald-600">Tagihan lunas bulan {{ $monthName }} {{ $year }}</p>
        </div>

        {{-- Total Pengeluaran --}}
        <div class="rounded-2xl border border-rose-100 bg-rose-50 p-5 shadow-xs">
            <div class="flex items-center gap-3 mb-3">
                <div class="flex h-10 w-10 items-center justify-center rounded-xl bg-rose-500/10">
                    <svg class="h-5 w-5 text-rose-600" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 13l-5 5m0 0l-5-5m5 5V6"/></svg>
                </div>
                <span class="text-xs font-semibold uppercase tracking-wider text-rose-700">Total Pengeluaran</span>
            </div>
            <div class="text-2xl sm:text-3xl font-extrabold text-rose-700">
                Rp {{ number_format($totalExpense, 0, ',', '.') }}
            </div>
            <p class="mt-1 text-[11px] text-rose-600">Biaya operasional bulan {{ $monthName }} {{ $year }}</p>
        </div>

        {{-- Laba Bersih --}}
        <div class="rounded-2xl border p-5 shadow-xs {{ $netProfit >= 0 ? 'border-indigo-100 bg-indigo-50' : 'border-rose-200 bg-rose-100' }}">
            <div class="flex items-center gap-3 mb-3">
                <div class="flex h-10 w-10 items-center justify-center rounded-xl {{ $netProfit >= 0 ? 'bg-indigo-500/10' : 'bg-rose-500/10' }}">
                    <svg class="h-5 w-5 {{ $netProfit >= 0 ? 'text-indigo-600' : 'text-rose-700' }}" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/></svg>
                </div>
                <span class="text-xs font-semibold uppercase tracking-wider {{ $netProfit >= 0 ? 'text-indigo-700' : 'text-rose-800' }}">Laba Bersih</span>
            </div>
            <div class="text-2xl sm:text-3xl font-extrabold {{ $netProfit >= 0 ? 'text-indigo-700' : 'text-rose-800' }}">
                {{ $netProfit < 0 ? '-' : '' }}Rp {{ number_format(abs($netProfit), 0, ',', '.') }}
            </div>
            <p class="mt-1 text-[11px] {{ $netProfit >= 0 ? 'text-indigo-600' : 'text-rose-700' }}">Pemasukan dikurangi pengeluaran</p>
        </div>
    </div>

    {{-- Tabel Rincian Transaksi --}}
    <div class="mt-6 bg-white border border-slate-200 rounded-2xl shadow-xs overflow-hidden">
        <div class="px-5 sm:px-6 py-4 border-b border-slate-100 flex items-center justify-between">
            <div>
                <h3 class="text-base font-bold text-slate-900">Rincian Transaksi</h3>
                <p class="text-xs text-slate-500 mt-0.5">
                    {{ $monthName }} {{ $year }}
                    @if($selectedProperty) — {{ $selectedProperty->name }} @endif
                    &middot; {{ $transactions->count() }} transaksi
                </p>
            </div>
        </div>

        @if($transactions->isEmpty())
            <div class="py-20 text-center">
                <svg class="mx-auto h-12 w-12 text-slate-300" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                <p class="mt-3 text-sm font-semibold text-slate-500">Tidak ada transaksi</p>
                <p class="text-xs text-slate-400 mt-1">Belum ada pemasukan atau pengeluaran pada periode ini.</p>
            </div>
        @else
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-slate-100">
                    <thead class="bg-slate-50/80">
                        <tr>
                            <th class="px-5 py-3 text-left text-xs font-semibold uppercase tracking-wider text-slate-500">Tanggal</th>
                            <th class="px-5 py-3 text-left text-xs font-semibold uppercase tracking-wider text-slate-500">Jenis</th>
                            <th class="px-5 py-3 text-left text-xs font-semibold uppercase tracking-wider text-slate-500">Uraian</th>
                            <th class="px-5 py-3 text-left text-xs font-semibold uppercase tracking-wider text-slate-500 hidden md:table-cell">Properti</th>
                            <th class="px-5 py-3 text-right text-xs font-semibold uppercase tracking-wider text-emerald-600">Pemasukan</th>
                            <th class="px-5 py-3 text-right text-xs font-semibold uppercase tracking-wider text-rose-600">Pengeluaran</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-50">
                        @foreach($transactions as $tx)
                            <tr class="hover:bg-slate-50/60 transition-colors">
                                <td class="px-5 py-3.5 text-xs text-slate-500 whitespace-nowrap">
                                    {{ $tx['date']->translatedFormat('d M Y') }}
                                </td>
                                <td class="px-5 py-3.5 whitespace-nowrap">
                                    @if($tx['type'] === 'income')
                                        <span class="inline-flex items-center gap-1 rounded-full bg-emerald-50 px-2.5 py-0.5 text-xs font-bold text-emerald-700">
                                            <svg class="h-3 w-3" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 10l7-7m0 0l7 7m-7-7v18"/></svg>
                                            Pemasukan
                                        </span>
                                    @else
                                        <span class="inline-flex items-center gap-1 rounded-full bg-rose-50 px-2.5 py-0.5 text-xs font-bold text-rose-700">
                                            <svg class="h-3 w-3" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M19 14l-7 7m0 0l-7-7m7 7V3"/></svg>
                                            Pengeluaran
                                        </span>
                                    @endif
                                </td>
                                <td class="px-5 py-3.5">
                                    <p class="text-sm font-semibold text-slate-800">{{ $tx['title'] }}</p>
                                    <p class="text-xs text-slate-400">{{ $tx['category'] }}</p>
                                </td>
                                <td class="px-5 py-3.5 text-xs text-slate-500 hidden md:table-cell">{{ $tx['property_name'] }}</td>
                                <td class="px-5 py-3.5 text-right text-sm font-bold whitespace-nowrap">
                                    @if($tx['income'] > 0)
                                        <span class="text-emerald-600">Rp {{ number_format($tx['income'], 0, ',', '.') }}</span>
                                    @else
                                        <span class="text-slate-300">—</span>
                                    @endif
                                </td>
                                <td class="px-5 py-3.5 text-right text-sm font-bold whitespace-nowrap">
                                    @if($tx['expense'] > 0)
                                        <span class="text-rose-600">Rp {{ number_format($tx['expense'], 0, ',', '.') }}</span>
                                    @else
                                        <span class="text-slate-300">—</span>
                                    @endif
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                    {{-- Footer Totals --}}
                    <tfoot class="bg-slate-50 border-t-2 border-slate-200">
                        <tr>
                            <td colspan="4" class="px-5 py-3.5 text-sm font-bold text-slate-700">Total</td>
                            <td class="px-5 py-3.5 text-right text-sm font-extrabold text-emerald-700">
                                Rp {{ number_format($totalIncome, 0, ',', '.') }}
                            </td>
                            <td class="px-5 py-3.5 text-right text-sm font-extrabold text-rose-700">
                                Rp {{ number_format($totalExpense, 0, ',', '.') }}
                            </td>
                        </tr>
                    </tfoot>
                </table>
            </div>
        @endif
    </div>
</x-layouts.owner>
