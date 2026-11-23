<x-layouts.owner>
    <x-slot:header>
        Pengajuan Sewa
    </x-slot:header>

    {{-- Page Header --}}
    <div class="mt-4 flex flex-col md:flex-row md:items-center justify-between gap-4 bg-white border border-slate-200/90 rounded-2xl p-5 sm:p-6 shadow-xs">
        <div>
            <div class="flex items-center gap-2.5">
                <h2 class="text-xl font-bold text-slate-900 tracking-tight">Pengajuan Sewa Masuk</h2>
                @if($pendingCount > 0)
                    <span class="inline-flex items-center rounded-full bg-amber-100 border border-amber-300 px-2.5 py-0.5 text-xs font-extrabold text-amber-800">
                        {{ $pendingCount }} Menunggu
                    </span>
                @endif
            </div>
            <p class="text-xs sm:text-sm text-slate-500 mt-1">
                Tinjau calon penyewa yang mengajukan pemesanan kamar melalui halaman publik properti Anda.
            </p>
        </div>

        {{-- Filter Status Pills --}}
        <div class="flex items-center gap-2 overflow-x-auto pb-1">
            <a href="{{ route('owner.bookings.index') }}" class="rounded-xl px-3.5 py-2 text-xs font-bold transition-all {{ empty($statusFilter) ? 'bg-[#1E3A8A] text-white shadow-xs' : 'bg-slate-100 text-slate-600 hover:bg-slate-200' }}">
                Semua ({{ $bookings->count() }})
            </a>
            <a href="{{ route('owner.bookings.index', ['status' => 'pending']) }}" class="rounded-xl px-3.5 py-2 text-xs font-bold transition-all {{ $statusFilter === 'pending' ? 'bg-amber-600 text-white shadow-xs' : 'bg-slate-100 text-slate-600 hover:bg-slate-200' }}">
                Menunggu ({{ $pendingCount }})
            </a>
            <a href="{{ route('owner.bookings.index', ['status' => 'approved']) }}" class="rounded-xl px-3.5 py-2 text-xs font-bold transition-all {{ $statusFilter === 'approved' ? 'bg-emerald-600 text-white shadow-xs' : 'bg-slate-100 text-slate-600 hover:bg-slate-200' }}">
                Disetujui ({{ $approvedCount }})
            </a>
            <a href="{{ route('owner.bookings.index', ['status' => 'rejected']) }}" class="rounded-xl px-3.5 py-2 text-xs font-bold transition-all {{ $statusFilter === 'rejected' ? 'bg-rose-600 text-white shadow-xs' : 'bg-slate-100 text-slate-600 hover:bg-slate-200' }}">
                Ditolak ({{ $rejectedCount }})
            </a>
        </div>
    </div>

    {{-- Daftar Pengajuan Sewa --}}
    <div class="mt-6 bg-white border border-slate-200 rounded-2xl shadow-xs overflow-hidden">
        @if($bookings->isEmpty())
            <div class="py-20 text-center">
                <svg class="mx-auto h-12 w-12 text-slate-300" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/></svg>
                <p class="mt-3 text-sm font-semibold text-slate-600">Belum ada pengajuan sewa</p>
                <p class="text-xs text-slate-400 mt-1">Calon penyewa yang memesan lewat katalog online akan muncul di sini.</p>
            </div>
        @else
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-slate-100">
                    <thead class="bg-slate-50/80">
                        <tr>
                            <th class="px-5 py-3.5 text-left text-xs font-semibold uppercase tracking-wider text-slate-500">Calon Penyewa</th>
                            <th class="px-5 py-3.5 text-left text-xs font-semibold uppercase tracking-wider text-slate-500">Kamar & Properti</th>
                            <th class="px-5 py-3.5 text-left text-xs font-semibold uppercase tracking-wider text-slate-500">Mulai Masuk & Durasi</th>
                            <th class="px-5 py-3.5 text-right text-xs font-semibold uppercase tracking-wider text-slate-500">Total Biaya</th>
                            <th class="px-5 py-3.5 text-center text-xs font-semibold uppercase tracking-wider text-slate-500">Status</th>
                            <th class="px-5 py-3.5 text-center text-xs font-semibold uppercase tracking-wider text-slate-500">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-50">
                        @foreach($bookings as $booking)
                            <tr class="hover:bg-slate-50/60 transition-colors">
                                <td class="px-5 py-4">
                                    <div class="font-bold text-slate-900 text-sm">{{ $booking->name }}</div>
                                    <div class="text-xs text-slate-500 mt-0.5">{{ $booking->email }}</div>
                                    <a href="https://wa.me/{{ preg_replace('/[^0-9]/', '', $booking->phone) }}" target="_blank" class="inline-flex items-center gap-1 text-[11px] font-bold text-emerald-600 hover:text-emerald-700 mt-1">
                                        <span>💬 {{ $booking->phone }}</span>
                                    </a>
                                </td>
                                <td class="px-5 py-4">
                                    <div class="font-bold text-indigo-900 text-sm">
                                        Kamar {{ $booking->room->room_number }}
                                        <span class="text-xs font-normal text-slate-500">({{ $booking->room->type }})</span>
                                    </div>
                                    <div class="text-xs text-slate-500 mt-0.5">
                                        {{ $booking->property->name }}
                                    </div>
                                    @if($booking->notes)
                                        <p class="text-[11px] text-slate-400 italic mt-1 max-w-xs truncate" title="{{ $booking->notes }}">
                                            "{{ $booking->notes }}"
                                        </p>
                                    @endif
                                </td>
                                <td class="px-5 py-4 text-xs text-slate-600">
                                    <div><strong>Masuk:</strong> {{ $booking->start_date->translatedFormat('d F Y') }}</div>
                                    <div class="text-slate-500 mt-0.5">Durasi: <strong>{{ $booking->duration_months }} Bulan</strong></div>
                                </td>
                                <td class="px-5 py-4 text-right">
                                    <div class="font-extrabold text-sm text-[#1E3A8A]">
                                        Rp {{ number_format((float) $booking->total_price, 0, ',', '.') }}
                                    </div>
                                    <div class="text-[10px] text-slate-400">Rp {{ number_format((float) $booking->room->price, 0, ',', '.') }} / bln</div>
                                </td>
                                <td class="px-5 py-4 text-center">
                                    @if($booking->status === \App\Enums\BookingStatus::Pending)
                                        <span class="inline-flex items-center gap-1 rounded-full bg-amber-50 border border-amber-200 px-2.5 py-1 text-xs font-bold text-amber-700">
                                            <span class="h-1.5 w-1.5 rounded-full bg-amber-500 animate-pulse"></span>
                                            Menunggu
                                        </span>
                                    @elseif($booking->status === \App\Enums\BookingStatus::Approved)
                                        <span class="inline-flex items-center gap-1 rounded-full bg-emerald-50 border border-emerald-200 px-2.5 py-1 text-xs font-bold text-emerald-700">
                                            ✓ Disetujui
                                        </span>
                                    @else
                                        <span class="inline-flex items-center gap-1 rounded-full bg-rose-50 border border-rose-200 px-2.5 py-1 text-xs font-bold text-rose-700" title="{{ $booking->rejection_reason }}">
                                            ✕ Ditolak
                                        </span>
                                    @endif
                                </td>
                                <td class="px-5 py-4 text-center">
                                    @if($booking->status === \App\Enums\BookingStatus::Pending)
                                        <div class="flex items-center justify-center gap-2">
                                            {{-- Approve Form --}}
                                            <form action="{{ route('owner.bookings.approve', $booking) }}" method="POST" onsubmit="return confirm('Setujui pengajuan sewa ini? Status kamar akan otomatis berubah menjadi Occupied (Terisi).')">
                                                @csrf
                                                @method('PATCH')
                                                <button type="submit" class="inline-flex items-center gap-1 rounded-xl bg-emerald-600 hover:bg-emerald-700 px-3 py-1.5 text-xs font-bold text-white transition-colors shadow-xs">
                                                    <svg class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/></svg>
                                                    Setujui
                                                </button>
                                            </form>

                                            {{-- Reject Form --}}
                                            <form action="{{ route('owner.bookings.reject', $booking) }}" method="POST" onsubmit="return confirm('Apakah Anda yakin ingin menolak pengajuan sewa ini?')">
                                                @csrf
                                                @method('PATCH')
                                                <button type="submit" class="inline-flex items-center gap-1 rounded-xl border border-rose-200 bg-rose-50 hover:bg-rose-100 px-3 py-1.5 text-xs font-bold text-rose-700 transition-colors">
                                                    Tolak
                                                </button>
                                            </form>
                                        </div>
                                    @elseif($booking->status === \App\Enums\BookingStatus::Approved)
                                        <a href="{{ route('owner.tenants.index') }}" class="text-xs font-bold text-indigo-600 hover:text-indigo-800 underline">
                                            Lihat Penghuni &rarr;
                                        </a>
                                    @else
                                        <span class="text-xs text-slate-400 italic">Selesai</span>
                                    @endif
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endif
    </div>
</x-layouts.owner>
