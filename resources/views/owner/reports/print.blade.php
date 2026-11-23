<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Laporan Keuangan {{ $monthName }} {{ $year }} - Kostly</title>
    <style>
        * { box-sizing: border-box; margin: 0; padding: 0; }

        body {
            font-family: 'Segoe UI', Arial, sans-serif;
            font-size: 13px;
            color: #1e293b;
            background: #fff;
        }

        .no-print {
            background: #1E3A8A;
            padding: 12px 24px;
            display: flex;
            align-items: center;
            gap: 12px;
        }

        .no-print a {
            color: rgba(255,255,255,0.7);
            text-decoration: none;
            font-size: 13px;
        }

        .no-print a:hover { color: #fff; }

        .print-btn {
            margin-left: auto;
            background: #10B981;
            color: white;
            border: none;
            padding: 8px 20px;
            border-radius: 8px;
            font-size: 13px;
            font-weight: 700;
            cursor: pointer;
            display: inline-flex;
            align-items: center;
            gap: 8px;
        }

        .print-btn:hover { background: #059669; }

        .container {
            max-width: 900px;
            margin: 0 auto;
            padding: 32px 24px;
        }

        .header {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            padding-bottom: 20px;
            border-bottom: 2px solid #1E3A8A;
            margin-bottom: 24px;
        }

        .logo { display: flex; align-items: center; gap: 10px; }

        .logo-icon {
            width: 40px;
            height: 40px;
            background: #1E3A8A;
            border-radius: 10px;
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-weight: 900;
            font-size: 16px;
        }

        .logo-name { font-size: 22px; font-weight: 900; color: #1E3A8A; }
        .logo-tagline { font-size: 10px; color: #64748b; margin-top: 1px; }

        .doc-title { text-align: right; }
        .doc-title h1 { font-size: 18px; font-weight: 800; color: #1e293b; }
        .doc-title p { font-size: 12px; color: #64748b; margin-top: 4px; }

        .meta-grid {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 12px;
            margin-bottom: 24px;
        }

        .meta-card {
            background: #f8fafc;
            border: 1px solid #e2e8f0;
            border-radius: 10px;
            padding: 12px 16px;
        }

        .meta-card .label { font-size: 10px; font-weight: 600; text-transform: uppercase; letter-spacing: 0.05em; color: #94a3b8; margin-bottom: 4px; }
        .meta-card .value { font-size: 14px; font-weight: 700; color: #1e293b; }

        .summary-cards {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 12px;
            margin-bottom: 28px;
        }

        .summary-card {
            border-radius: 10px;
            padding: 14px 16px;
        }

        .summary-card.income { background: #ecfdf5; border: 1px solid #a7f3d0; }
        .summary-card.expense { background: #fff1f2; border: 1px solid #fecdd3; }
        .summary-card.profit { background: #eef2ff; border: 1px solid #c7d2fe; }
        .summary-card.loss { background: #fff1f2; border: 1px solid #fecdd3; }

        .summary-card .sc-label { font-size: 10px; font-weight: 600; text-transform: uppercase; letter-spacing: 0.05em; margin-bottom: 6px; }
        .summary-card.income .sc-label { color: #065f46; }
        .summary-card.expense .sc-label { color: #9f1239; }
        .summary-card.profit .sc-label { color: #3730a3; }
        .summary-card.loss .sc-label { color: #9f1239; }

        .summary-card .sc-value { font-size: 18px; font-weight: 900; }
        .summary-card.income .sc-value { color: #047857; }
        .summary-card.expense .sc-value { color: #be123c; }
        .summary-card.profit .sc-value { color: #4338ca; }
        .summary-card.loss .sc-value { color: #be123c; }

        h2 { font-size: 14px; font-weight: 800; color: #1e293b; margin-bottom: 12px; }

        table { width: 100%; border-collapse: collapse; }
        thead th {
            background: #1E3A8A;
            color: white;
            padding: 9px 12px;
            text-align: left;
            font-size: 11px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.04em;
        }
        thead th:last-child, thead th:nth-last-child(2) { text-align: right; }

        tbody tr:nth-child(even) { background: #f8fafc; }
        tbody tr:hover { background: #f1f5f9; }

        tbody td {
            padding: 8px 12px;
            border-bottom: 1px solid #f1f5f9;
            font-size: 12px;
            vertical-align: middle;
        }

        td:last-child, td:nth-last-child(1) { text-align: right; white-space: nowrap; }

        .badge {
            display: inline-block;
            padding: 2px 8px;
            border-radius: 999px;
            font-size: 10px;
            font-weight: 700;
        }
        .badge-income { background: #d1fae5; color: #065f46; }
        .badge-expense { background: #ffe4e6; color: #9f1239; }

        tfoot tr td {
            background: #1e293b;
            color: white;
            font-weight: 800;
            font-size: 13px;
            padding: 10px 12px;
        }
        tfoot tr td:last-child, tfoot tr td:nth-last-child(1) { text-align: right; }

        .income-val { color: #10b981; font-weight: 700; }
        .expense-val { color: #f43f5e; font-weight: 700; }
        .dash { color: #cbd5e1; }

        .footer {
            margin-top: 32px;
            padding-top: 16px;
            border-top: 1px solid #e2e8f0;
            display: flex;
            justify-content: space-between;
            font-size: 11px;
            color: #94a3b8;
        }

        .empty-state {
            text-align: center;
            padding: 48px;
            color: #94a3b8;
            border: 1px dashed #cbd5e1;
            border-radius: 10px;
        }

        @media print {
            .no-print { display: none !important; }

            body { font-size: 12px; }

            .container { padding: 20px 16px; }

            thead th { background: #1E3A8A !important; color: white !important; -webkit-print-color-adjust: exact; print-color-adjust: exact; }

            tfoot tr td { background: #1e293b !important; color: white !important; -webkit-print-color-adjust: exact; print-color-adjust: exact; }

            .summary-card { -webkit-print-color-adjust: exact; print-color-adjust: exact; }

            .logo-icon { -webkit-print-color-adjust: exact; print-color-adjust: exact; }

            @page { margin: 1.5cm; size: A4 landscape; }
        }
    </style>
</head>
<body>

    {{-- Toolbar (hanya tampil di layar, tidak dicetak) --}}
    <div class="no-print">
        <a href="{{ route('owner.reports.index', array_filter(['month' => $month, 'year' => $year, 'property_id' => $selectedPropertyId])) }}">
            &larr; Kembali ke Laporan
        </a>
        <button class="print-btn" onclick="window.print()">
            <svg width="16" height="16" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/></svg>
            Cetak / Simpan PDF
        </button>
    </div>

    <div class="container">

        {{-- Header Dokumen --}}
        <div class="header">
            <div class="logo">
                <div class="logo-icon">K</div>
                <div>
                    <div class="logo-name">Kostly</div>
                    <div class="logo-tagline">Platform Manajemen Kost Digital</div>
                </div>
            </div>
            <div class="doc-title">
                <h1>LAPORAN KEUANGAN BULANAN</h1>
                <p>Dicetak: {{ now()->translatedFormat('d F Y, H:i') }} WIB</p>
            </div>
        </div>

        {{-- Meta Info --}}
        <div class="meta-grid">
            <div class="meta-card">
                <div class="label">Pemilik Kost</div>
                <div class="value">{{ $user->name }}</div>
            </div>
            <div class="meta-card">
                <div class="label">Properti</div>
                <div class="value">{{ $selectedProperty ? $selectedProperty->name : 'Semua Properti' }}</div>
            </div>
            <div class="meta-card">
                <div class="label">Periode</div>
                <div class="value">{{ $monthName }} {{ $year }}</div>
            </div>
            <div class="meta-card">
                <div class="label">Total Transaksi</div>
                <div class="value">{{ $transactions->count() }} transaksi</div>
            </div>
        </div>

        {{-- Ringkasan --}}
        <div class="summary-cards">
            <div class="summary-card income">
                <div class="sc-label">Total Pemasukan</div>
                <div class="sc-value">Rp {{ number_format($totalIncome, 0, ',', '.') }}</div>
            </div>
            <div class="summary-card expense">
                <div class="sc-label">Total Pengeluaran</div>
                <div class="sc-value">Rp {{ number_format($totalExpense, 0, ',', '.') }}</div>
            </div>
            <div class="summary-card {{ $netProfit >= 0 ? 'profit' : 'loss' }}">
                <div class="sc-label">Laba Bersih</div>
                <div class="sc-value">{{ $netProfit < 0 ? '-' : '' }}Rp {{ number_format(abs($netProfit), 0, ',', '.') }}</div>
            </div>
        </div>

        {{-- Tabel Transaksi --}}
        <h2>Rincian Transaksi</h2>

        @if($transactions->isEmpty())
            <div class="empty-state">
                <p>Tidak ada transaksi pada periode ini.</p>
            </div>
        @else
            <table>
                <thead>
                    <tr>
                        <th>Tanggal</th>
                        <th>Jenis</th>
                        <th>Uraian Transaksi</th>
                        <th>Kategori</th>
                        <th>Properti</th>
                        <th>Pemasukan (Rp)</th>
                        <th>Pengeluaran (Rp)</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($transactions as $tx)
                        <tr>
                            <td>{{ $tx['date']->format('d/m/Y') }}</td>
                            <td>
                                <span class="badge {{ $tx['type'] === 'income' ? 'badge-income' : 'badge-expense' }}">
                                    {{ $tx['type_label'] }}
                                </span>
                            </td>
                            <td>{{ $tx['title'] }}</td>
                            <td>{{ $tx['category'] }}</td>
                            <td>{{ $tx['property_name'] }}</td>
                            <td>
                                @if($tx['income'] > 0)
                                    <span class="income-val">{{ number_format($tx['income'], 0, ',', '.') }}</span>
                                @else
                                    <span class="dash">—</span>
                                @endif
                            </td>
                            <td>
                                @if($tx['expense'] > 0)
                                    <span class="expense-val">{{ number_format($tx['expense'], 0, ',', '.') }}</span>
                                @else
                                    <span class="dash">—</span>
                                @endif
                            </td>
                        </tr>
                    @endforeach
                </tbody>
                <tfoot>
                    <tr>
                        <td colspan="5">TOTAL PERIODE {{ strtoupper($monthName) }} {{ $year }}</td>
                        <td>{{ number_format($totalIncome, 0, ',', '.') }}</td>
                        <td>{{ number_format($totalExpense, 0, ',', '.') }}</td>
                    </tr>
                </tfoot>
            </table>
        @endif

        {{-- Footer --}}
        <div class="footer">
            <span>Kostly &copy; {{ now()->year }} &middot; Laporan digenerate otomatis oleh sistem.</span>
            <span>Laba Bersih: <strong>{{ $netProfit < 0 ? '-' : '' }}Rp {{ number_format(abs($netProfit), 0, ',', '.') }}</strong></span>
        </div>
    </div>
</body>
</html>
