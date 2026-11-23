<x-layouts.owner>
    <x-slot:header>
        Laporan Komplain Penghuni
    </x-slot:header>

    {{-- Page Header & Actions --}}
    <div class="mt-4 flex flex-col md:flex-row md:items-center justify-between gap-4 bg-white border border-slate-200/90 rounded-2xl p-5 sm:p-6 shadow-xs">
        <div>
            <h2 class="text-xl font-bold text-slate-900 tracking-tight">Pusat Komplain & Perbaikan Fasilitas</h2>
            <p class="text-xs sm:text-sm text-slate-500 mt-1">
                Laporan kerusakan dari portal penghuni atau dicatat manual. Pantau status penanganan dari pending hingga selesai.
            </p>
        </div>

        <div>
            <button type="button" onclick="openModal('add-complaint-modal')" class="inline-flex items-center gap-2 rounded-xl bg-[#1E3A8A] px-4 py-2.5 text-xs sm:text-sm font-bold text-white shadow-md shadow-indigo-950/20 hover:bg-[#172554] transition-colors">
                <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                <span>Catat Komplain Manual</span>
            </button>
        </div>
    </div>

    {{-- Filter Bar --}}
    <div class="mt-6 flex flex-col md:flex-row md:items-center justify-between gap-4 bg-white border border-slate-200/90 rounded-2xl p-4 shadow-xs">
        <div class="flex flex-wrap items-center gap-2 text-xs font-semibold">
            <a href="{{ route('owner.complaints.index', array_merge(request()->query(), ['status' => ''])) }}"
                class="px-3.5 py-1.5 rounded-xl border transition-all {{ empty($statusFilter) ? 'bg-[#1E3A8A] text-white border-[#1E3A8A]' : 'bg-slate-50 text-slate-600 border-slate-200 hover:bg-slate-100' }}">
                Semua ({{ $totalCount }})
            </a>
            <a href="{{ route('owner.complaints.index', array_merge(request()->query(), ['status' => 'pending'])) }}"
                class="inline-flex items-center gap-1.5 px-3.5 py-1.5 rounded-xl border transition-all {{ $statusFilter === 'pending' ? 'bg-amber-600 text-white border-amber-600' : 'bg-amber-50 text-amber-800 border-amber-200 hover:bg-amber-100' }}">
                <span class="h-2 w-2 rounded-full {{ $statusFilter === 'pending' ? 'bg-white' : 'bg-amber-500' }}"></span>
                Menunggu ({{ $pendingCount }})
            </a>
            <a href="{{ route('owner.complaints.index', array_merge(request()->query(), ['status' => 'in_progress'])) }}"
                class="inline-flex items-center gap-1.5 px-3.5 py-1.5 rounded-xl border transition-all {{ $statusFilter === 'in_progress' ? 'bg-blue-600 text-white border-blue-600' : 'bg-blue-50 text-blue-800 border-blue-200 hover:bg-blue-100' }}">
                <span class="h-2 w-2 rounded-full {{ $statusFilter === 'in_progress' ? 'bg-white' : 'bg-blue-500' }}"></span>
                Diproses ({{ $inProgressCount }})
            </a>
            <a href="{{ route('owner.complaints.index', array_merge(request()->query(), ['status' => 'resolved'])) }}"
                class="inline-flex items-center gap-1.5 px-3.5 py-1.5 rounded-xl border transition-all {{ $statusFilter === 'resolved' ? 'bg-emerald-600 text-white border-emerald-600' : 'bg-emerald-50 text-emerald-800 border-emerald-200 hover:bg-emerald-100' }}">
                <span class="h-2 w-2 rounded-full {{ $statusFilter === 'resolved' ? 'bg-white' : 'bg-emerald-500' }}"></span>
                Selesai ({{ $resolvedCount }})
            </a>
        </div>

        <form action="{{ route('owner.complaints.index') }}" method="GET" class="flex flex-wrap items-center gap-2">
            @if ($statusFilter)
                <input type="hidden" name="status" value="{{ $statusFilter }}">
            @endif

            <div class="relative">
                <input type="text" name="search" value="{{ $search }}" placeholder="Cari masalah/penghuni..."
                    class="rounded-xl border border-slate-300 py-1.5 pl-8 pr-3 text-xs text-slate-900 placeholder-slate-400 focus:border-[#1E3A8A] focus:outline-hidden">
                <svg class="h-4 w-4 text-slate-400 absolute left-2.5 top-2" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
            </div>

            @if ($search || $statusFilter)
                <a href="{{ route('owner.complaints.index') }}" class="text-xs text-rose-600 font-semibold hover:underline">Reset</a>
            @endif
        </form>
    </div>

    {{-- Complaints Grid --}}
    @if ($complaints->isEmpty())
        <div class="mt-6 bg-white border border-slate-200/90 rounded-2xl p-12 text-center shadow-xs">
            <div class="flex h-16 w-16 items-center justify-center rounded-2xl bg-indigo-50 text-[#1E3A8A] mx-auto mb-4">
                <svg class="h-8 w-8" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
            </div>
            <h3 class="text-base font-bold text-slate-900">Tidak ada komplain ditemukan</h3>
            <p class="text-xs text-slate-500 mt-1 max-w-sm mx-auto">
                Semua fasilitas properti kost Anda berada dalam kondisi prima tanpa kendala aktif.
            </p>
        </div>
    @else
        <div class="mt-6 grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            @foreach ($complaints as $complaint)
                <div class="rounded-2xl border border-slate-200/90 bg-white p-5 shadow-xs flex flex-col justify-between hover:border-slate-300 transition-all">
                    <div>
                        {{-- Top Badge & Date --}}
                        <div class="flex items-center justify-between mb-3">
                            <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-[11px] font-bold {{ $complaint->status->badgeClass() }}">
                                {{ $complaint->status->label() }}
                            </span>
                            <span class="text-[11px] text-slate-400 font-medium">
                                {{ $complaint->created_at->diffForHumans() }}
                            </span>
                        </div>

                        {{-- Title & Description --}}
                        <h3 class="text-base font-bold text-slate-900 tracking-tight">{{ $complaint->title }}</h3>
                        <p class="mt-2 text-xs text-slate-600 leading-relaxed bg-slate-50/70 p-3 rounded-xl border border-slate-100">
                            {{ $complaint->description }}
                        </p>

                        @if ($complaint->photo_path)
                            <div class="mt-2.5">
                                <a href="{{ asset('storage/' . $complaint->photo_path) }}" target="_blank"
                                    class="inline-flex items-center gap-1.5 text-[11px] font-bold text-indigo-700 bg-indigo-50 hover:bg-indigo-100 px-2.5 py-1 rounded-lg border border-indigo-100 transition-colors">
                                    <svg class="h-3.5 w-3.5 text-indigo-600" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                                    <span>Lihat Foto Lampiran Kerusakan</span>
                                </a>
                            </div>
                        @endif

                        {{-- Balasan Owner / Catatan Penanganan --}}
                        @if ($complaint->owner_response)
                            <div class="mt-3 p-3 rounded-xl bg-emerald-50/70 border border-emerald-200/80 text-xs text-emerald-900">
                                <div class="flex items-center gap-1 font-bold text-emerald-800 mb-1">
                                    <svg class="h-3.5 w-3.5 text-emerald-600" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                    <span>Tindak Lanjut Pemilik:</span>
                                </div>
                                <p class="text-slate-700 leading-relaxed">{{ $complaint->owner_response }}</p>
                            </div>
                        @endif

                        {{-- Reporter Info --}}
                        <div class="mt-4 flex items-center justify-between text-xs pt-3 border-t border-slate-100">
                            <div>
                                <span class="text-slate-400 block text-[10px]">Pelapor:</span>
                                <span class="font-bold text-slate-900">{{ $complaint->tenant->name }}</span>
                            </div>
                            <div class="text-right">
                                <span class="text-slate-400 block text-[10px]">Lokasi:</span>
                                <span class="font-bold text-[#1E3A8A]">Kamar {{ $complaint->room->room_number }}</span>
                            </div>
                        </div>
                    </div>

                    {{-- Actions: Quick Status Changer & Delete --}}
                    <div class="mt-5 pt-3 border-t border-slate-100 flex items-center justify-between gap-2">
                        <button type="button" onclick="openRespondModal({{ json_encode($complaint) }})"
                            class="inline-flex items-center gap-1 rounded-lg bg-indigo-50 hover:bg-indigo-100 border border-indigo-200 px-2.5 py-1 text-[11px] font-bold text-[#1E3A8A] transition-colors">
                            <svg class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                            <span>Tindak Lanjut & Balas</span>
                        </button>

                        <form action="{{ route('owner.complaints.destroy', $complaint) }}" method="POST" onsubmit="return confirm('Hapus tiket komplain ini?');" class="inline">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="p-1.5 rounded-lg text-slate-400 hover:text-rose-600 hover:bg-slate-50 transition-colors" title="Hapus Tiket">
                                <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                            </button>
                        </form>
                    </div>
                </div>
            @endforeach
        </div>
    @endif

    {{-- Modal Catat Komplain Baru --}}
    <div id="add-complaint-modal" class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-900/50 backdrop-blur-xs hidden">
        <div class="w-full max-w-lg rounded-2xl bg-white p-6 shadow-2xl">
            <div class="flex items-center justify-between border-b border-slate-100 pb-3 mb-4">
                <h3 class="text-lg font-bold text-slate-900">Catat Laporan Komplain Baru</h3>
                <button type="button" onclick="closeModal('add-complaint-modal')" class="text-slate-400 hover:text-slate-600 font-bold text-lg">✕</button>
            </div>

            <form action="{{ route('owner.complaints.store') }}" method="POST" class="space-y-4">
                @csrf

                <div>
                    <label class="block text-xs font-semibold uppercase tracking-wider text-slate-700">Pilih Penghuni Pelapor</label>
                    <select name="tenant_id" required class="mt-1 block w-full rounded-xl border border-slate-300 px-3.5 py-2 text-sm focus:border-[#1E3A8A] focus:outline-hidden">
                        <option value="">-- Pilih Penghuni Aktif --</option>
                        @foreach ($activeTenants as $t)
                            <option value="{{ $t->id }}">
                                {{ $t->name }} (Kamar {{ $t->room?->room_number ?? '-' }} - {{ $t->property->name }})
                            </option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label class="block text-xs font-semibold uppercase tracking-wider text-slate-700">Judul Kerusakan / Masalah</label>
                    <input type="text" name="title" required class="mt-1 block w-full rounded-xl border border-slate-300 px-3.5 py-2 text-sm focus:border-[#1E3A8A] focus:outline-hidden" placeholder="Misal: AC bocor menetes, Lampu kamar mandi mati">
                </div>

                <div>
                    <label class="block text-xs font-semibold uppercase tracking-wider text-slate-700">Rincian Kendala</label>
                    <textarea name="description" rows="3" required class="mt-1 block w-full rounded-xl border border-slate-300 px-3.5 py-2 text-sm focus:border-[#1E3A8A] focus:outline-hidden" placeholder="Jelaskan detail masalah fasilitas yang dilaporkan"></textarea>
                </div>

                <div>
                    <label class="block text-xs font-semibold uppercase tracking-wider text-slate-700">Status Awal</label>
                    <select name="status" required class="mt-1 block w-full rounded-xl border border-slate-300 px-3.5 py-2 text-sm focus:border-[#1E3A8A] focus:outline-hidden">
                        <option value="pending">Menunggu Tindak Lanjut</option>
                        <option value="in_progress">Sedang Diproses Teknisi</option>
                        <option value="resolved">Selesai Diperbaiki</option>
                    </select>
                </div>

                <div class="flex items-center justify-end gap-3 pt-3 border-t border-slate-100">
                    <button type="button" onclick="closeModal('add-complaint-modal')" class="rounded-xl border border-slate-300 px-4 py-2 text-xs font-semibold text-slate-700 hover:bg-slate-50">Batal</button>
                    <button type="submit" class="rounded-xl bg-[#1E3A8A] px-5 py-2 text-xs font-bold text-white hover:bg-[#172554]">Simpan Laporan</button>
                </div>
            </form>
        </div>
    </div>

    {{-- Modal Tindak Lanjut & Balas Komplain --}}
    <div id="respond-complaint-modal" class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-900/60 backdrop-blur-xs hidden">
        <div class="w-full max-w-lg rounded-2xl bg-white p-6 shadow-2xl">
            <div class="flex items-center justify-between border-b border-slate-100 pb-3 mb-4">
                <div>
                    <h3 class="text-base font-bold text-slate-900">Tindak Lanjut & Balas Komplain</h3>
                    <p class="text-xs text-slate-500" id="respond-complaint-subtitle">-</p>
                </div>
                <button type="button" onclick="closeModal('respond-complaint-modal')" class="text-slate-400 hover:text-slate-600 font-bold text-lg">✕</button>
            </div>

            <form id="respond-complaint-form" method="POST" class="space-y-4">
                @csrf
                @method('PATCH')

                <div>
                    <label class="block text-xs font-semibold uppercase tracking-wider text-slate-700">Status Penanganan</label>
                    <select id="respond-status" name="status" required class="mt-1 block w-full rounded-xl border border-slate-300 px-3.5 py-2 text-sm focus:border-[#1E3A8A] focus:outline-hidden">
                        <option value="pending">Menunggu Tindak Lanjut</option>
                        <option value="in_progress">Sedang Diproses Teknisi</option>
                        <option value="resolved">Selesai Diperbaiki</option>
                    </select>
                </div>

                <div>
                    <label class="block text-xs font-semibold uppercase tracking-wider text-slate-700">Catatan Tindak Lanjut / Pesan untuk Penghuni</label>
                    <textarea id="respond-owner-response" name="owner_response" rows="4" placeholder="Misal: Teknisi AC sudah dijadwalkan datang besok jam 10 pagi, mohon ada orang di kamar..."
                        class="mt-1 block w-full rounded-xl border border-slate-300 px-3.5 py-2 text-xs focus:border-[#1E3A8A] focus:outline-hidden"></textarea>
                    <span class="text-[10px] text-slate-400 mt-1 block">Catatan ini akan langsung terlihat oleh anak kost di portal mandiri mereka.</span>
                </div>

                <div class="flex items-center justify-end gap-3 pt-3 border-t border-slate-100">
                    <button type="button" onclick="closeModal('respond-complaint-modal')" class="rounded-xl border border-slate-300 px-4 py-2 text-xs font-semibold text-slate-700 hover:bg-slate-50">Batal</button>
                    <button type="submit" class="rounded-xl bg-[#1E3A8A] px-5 py-2 text-xs font-bold text-white hover:bg-[#172554]">Simpan Tindak Lanjut</button>
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

        function openRespondModal(complaint) {
            const form = document.getElementById('respond-complaint-form');
            form.action = `/dashboard/complaints/${complaint.id}/status`;
            document.getElementById('respond-complaint-subtitle').textContent = `Tiket: ${complaint.title} (${complaint.tenant ? complaint.tenant.name : '-'})`;
            document.getElementById('respond-status').value = complaint.status;
            document.getElementById('respond-owner-response').value = complaint.owner_response || '';
            openModal('respond-complaint-modal');
        }
    </script>
</x-layouts.owner>
