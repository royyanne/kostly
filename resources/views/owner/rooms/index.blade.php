<x-layouts.owner>
    <x-slot:header>
        Manajemen Kamar Kost
    </x-slot:header>

    {{-- Page Header & Actions --}}
    <div class="mt-4 flex flex-col md:flex-row md:items-center justify-between gap-4 bg-white border border-slate-200/90 rounded-2xl p-5 sm:p-6 shadow-xs">
        <div>
            <h2 class="text-xl font-bold text-slate-900 tracking-tight">Kamar & Visual Grid</h2>
            <p class="text-xs sm:text-sm text-slate-500 mt-1">
                Monitor ketersediaan kamar secara visual, ubah status realtime, dan kelola harga sewa.
            </p>
        </div>

        <div class="flex flex-wrap items-center gap-2.5">
            {{-- View Mode Toggle --}}
            <div class="inline-flex rounded-xl border border-slate-200 bg-slate-50 p-1 text-xs font-semibold">
                <button type="button" onclick="switchView('grid')" id="btn-view-grid" class="flex items-center gap-1.5 rounded-lg bg-white px-3 py-1.5 text-slate-900 shadow-xs">
                    <svg class="h-4 w-4 text-[#1E3A8A]" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z"/></svg>
                    <span>Visual Grid</span>
                </button>
                <button type="button" onclick="switchView('table')" id="btn-view-table" class="flex items-center gap-1.5 rounded-lg px-3 py-1.5 text-slate-500 hover:text-slate-900">
                    <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 10h16M4 14h16M4 18h16"/></svg>
                    <span>Tabel</span>
                </button>
            </div>

            <button type="button" onclick="openModal('add-room-modal')" class="inline-flex items-center gap-2 rounded-xl bg-[#1E3A8A] px-4 py-2.5 text-xs sm:text-sm font-bold text-white shadow-md shadow-indigo-950/20 hover:bg-[#172554] transition-colors">
                <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                <span>Tambah Kamar Baru</span>
            </button>
        </div>
    </div>

    {{-- Filter Bar --}}
    <div class="mt-6 flex flex-col md:flex-row md:items-center justify-between gap-4 bg-white border border-slate-200/90 rounded-2xl p-4 shadow-xs">
        {{-- Status Filter Pills --}}
        <div class="flex flex-wrap items-center gap-2 text-xs font-semibold">
            <a href="{{ route('owner.rooms.index', array_merge(request()->query(), ['status' => ''])) }}"
                class="px-3 py-1.5 rounded-xl border transition-all {{ empty($statusFilter) ? 'bg-[#1E3A8A] text-white border-[#1E3A8A]' : 'bg-slate-50 text-slate-600 border-slate-200 hover:bg-slate-100' }}">
                Semua ({{ $totalCount }})
            </a>
            <a href="{{ route('owner.rooms.index', array_merge(request()->query(), ['status' => 'available'])) }}"
                class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-xl border transition-all {{ $statusFilter === 'available' ? 'bg-emerald-600 text-white border-emerald-600' : 'bg-emerald-50 text-emerald-800 border-emerald-200 hover:bg-emerald-100' }}">
                <span class="h-2 w-2 rounded-full {{ $statusFilter === 'available' ? 'bg-white' : 'bg-emerald-500' }}"></span>
                Tersedia ({{ $availableCount }})
            </a>
            <a href="{{ route('owner.rooms.index', array_merge(request()->query(), ['status' => 'occupied'])) }}"
                class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-xl border transition-all {{ $statusFilter === 'occupied' ? 'bg-blue-600 text-white border-blue-600' : 'bg-blue-50 text-blue-800 border-blue-200 hover:bg-blue-100' }}">
                <span class="h-2 w-2 rounded-full {{ $statusFilter === 'occupied' ? 'bg-white' : 'bg-blue-500' }}"></span>
                Terisi ({{ $occupiedCount }})
            </a>
            <a href="{{ route('owner.rooms.index', array_merge(request()->query(), ['status' => 'maintenance'])) }}"
                class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-xl border transition-all {{ $statusFilter === 'maintenance' ? 'bg-amber-600 text-white border-amber-600' : 'bg-amber-50 text-amber-800 border-amber-200 hover:bg-amber-100' }}">
                <span class="h-2 w-2 rounded-full {{ $statusFilter === 'maintenance' ? 'bg-white' : 'bg-amber-500' }}"></span>
                Perbaikan ({{ $maintenanceCount }})
            </a>
        </div>

        {{-- Search & Property Select --}}
        <form action="{{ route('owner.rooms.index') }}" method="GET" class="flex flex-wrap items-center gap-2">
            @if ($statusFilter)
                <input type="hidden" name="status" value="{{ $statusFilter }}">
            @endif

            @if ($properties->count() > 1)
                <select name="property_id" onchange="this.form.submit()" class="rounded-xl border border-slate-300 py-1.5 px-3 text-xs font-semibold text-slate-700 focus:border-[#1E3A8A] focus:outline-hidden">
                    <option value="">Semua Properti</option>
                    @foreach ($properties as $prop)
                        <option value="{{ $prop->id }}" {{ $selectedPropertyId == $prop->id ? 'selected' : '' }}>
                            {{ $prop->name }}
                        </option>
                    @endforeach
                </select>
            @endif

            <div class="relative">
                <input type="text" name="search" value="{{ $search }}" placeholder="Cari no/tipe kamar..."
                    class="rounded-xl border border-slate-300 py-1.5 pl-8 pr-3 text-xs text-slate-900 placeholder-slate-400 focus:border-[#1E3A8A] focus:outline-hidden">
                <svg class="h-4 w-4 text-slate-400 absolute left-2.5 top-2" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
            </div>

            @if ($search || $selectedPropertyId || $statusFilter)
                <a href="{{ route('owner.rooms.index') }}" class="text-xs text-rose-600 font-semibold hover:underline">Reset</a>
            @endif
        </form>
    </div>

    {{-- Empty State --}}
    @if ($rooms->isEmpty())
        <div class="mt-6 bg-white border border-slate-200/90 rounded-2xl p-12 text-center shadow-xs">
            <div class="flex h-16 w-16 items-center justify-center rounded-2xl bg-indigo-50 text-[#1E3A8A] mx-auto mb-4">
                <svg class="h-8 w-8" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/></svg>
            </div>
            <h3 class="text-base font-bold text-slate-900">Tidak ada kamar ditemukan</h3>
            <p class="text-xs text-slate-500 mt-1 max-w-sm mx-auto">
                {{ $search ? 'Tidak ada kamar yang cocok dengan kata kunci pencarian.' : 'Belum ada data kamar kost. Daftarkan kamar pertama Anda sekarang!' }}
            </p>
            <button type="button" onclick="openModal('add-room-modal')" class="mt-5 inline-flex items-center gap-2 rounded-xl bg-[#1E3A8A] px-5 py-2.5 text-xs font-bold text-white shadow-md hover:bg-[#172554] transition-colors">
                Tambah Kamar Baru
            </button>
        </div>
    @else
        {{-- VIEW 1: Interactive Visual Room Grid --}}
        <div id="grid-view-container" class="mt-6 grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-4 sm:gap-5">
            @foreach ($rooms as $room)
                <div class="group relative rounded-2xl border-2 bg-white p-5 shadow-xs transition-all hover:shadow-lg flex flex-col justify-between
                    {{ $room->status === App\Enums\RoomStatus::Available ? 'border-emerald-300/80 hover:border-emerald-500' : '' }}
                    {{ $room->status === App\Enums\RoomStatus::Occupied ? 'border-blue-300/80 hover:border-blue-500' : '' }}
                    {{ $room->status === App\Enums\RoomStatus::Maintenance ? 'border-amber-300/80 hover:border-amber-500' : '' }}
                ">
                    <div>
                        {{-- Top Header: Room Number & Status Badge --}}
                        <div class="flex items-center justify-between mb-3">
                            <div class="flex items-baseline gap-1.5">
                                <span class="text-xs font-bold uppercase text-slate-400">Kamar</span>
                                <span class="text-2xl font-black text-slate-900 tracking-tight">{{ $room->room_number }}</span>
                            </div>

                            <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-xs font-bold {{ $room->status->badgeClass() }}">
                                <span class="h-2 w-2 rounded-full {{ $room->status->dotClass() }}"></span>
                                {{ $room->status->label() }}
                            </span>
                        </div>

                        {{-- Room Type & Monthly Price --}}
                        <div class="space-y-1">
                            <span class="inline-block px-2 py-0.5 rounded-md bg-slate-100 text-[11px] font-semibold text-slate-700">
                                {{ $room->type }}
                            </span>
                            <div class="pt-1">
                                <span class="text-lg font-extrabold text-[#1E3A8A]">
                                    Rp {{ number_format((float) $room->price, 0, ',', '.') }}
                                </span>
                                <span class="text-xs text-slate-500 font-medium">/ bulan</span>
                            </div>
                        </div>

                        {{-- Tenant Section --}}
                        <div class="mt-4 pt-3.5 border-t border-slate-100">
                            @if ($room->currentTenant)
                                <div class="rounded-xl bg-blue-50/60 p-2.5 text-xs">
                                    <div class="flex items-center gap-1.5 font-bold text-blue-950 truncate">
                                        <svg class="h-4 w-4 text-blue-600 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
                                        <span class="truncate">{{ $room->currentTenant->name }}</span>
                                    </div>
                                    <div class="mt-1 flex items-center justify-between text-[11px] text-blue-800">
                                        <span>Jatuh Tempo: <strong>Tgl {{ $room->currentTenant->due_day }}</strong></span>
                                        <span class="font-mono text-slate-500">{{ $room->currentTenant->phone }}</span>
                                    </div>
                                </div>
                            @elseif ($room->status === App\Enums\RoomStatus::Maintenance)
                                <div class="rounded-xl bg-amber-50/60 p-2.5 text-xs text-amber-900 flex items-center gap-2">
                                    <svg class="h-4 w-4 text-amber-600 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
                                    <span class="text-[11px] font-medium">Dalam perawatan teknisi / perbaikan</span>
                                </div>
                            @else
                                <div class="rounded-xl bg-emerald-50/60 p-2.5 text-xs text-emerald-900 flex items-center gap-2">
                                    <svg class="h-4 w-4 text-emerald-600 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                    <span class="text-[11px] font-semibold">Kamar kosong siap ditawarkan</span>
                                </div>
                            @endif
                        </div>
                    </div>

                    {{-- Actions: Quick Status Changer & Controls --}}
                    <div class="mt-4 pt-3 border-t border-slate-100 flex items-center justify-between gap-2">
                        {{-- Quick status change form --}}
                        <form action="{{ route('owner.rooms.updateStatus', $room) }}" method="POST" class="inline-flex">
                            @csrf
                            @method('PATCH')
                            <select name="status" onchange="this.form.submit()" class="rounded-lg border border-slate-200 bg-slate-50 py-1 px-2 text-[11px] font-semibold text-slate-700 hover:border-slate-300 focus:outline-hidden">
                                <option value="available" {{ $room->status === App\Enums\RoomStatus::Available ? 'selected' : '' }}>Set: Tersedia</option>
                                <option value="occupied" {{ $room->status === App\Enums\RoomStatus::Occupied ? 'selected' : '' }}>Set: Terisi</option>
                                <option value="maintenance" {{ $room->status === App\Enums\RoomStatus::Maintenance ? 'selected' : '' }}>Set: Perbaikan</option>
                            </select>
                        </form>

                        <div class="flex items-center gap-1">
                            <button type="button" onclick="openEditRoomModal({{ json_encode($room) }})" class="p-1.5 rounded-lg text-slate-400 hover:text-indigo-600 hover:bg-slate-50 transition-colors" title="Edit Kamar">
                                <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"/></svg>
                            </button>

                            <form action="{{ route('owner.rooms.destroy', $room) }}" method="POST" onsubmit="return confirm('Hapus kamar {{ $room->room_number }}?');">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="p-1.5 rounded-lg text-slate-400 hover:text-rose-600 hover:bg-slate-50 transition-colors" title="Hapus Kamar">
                                    <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>

        {{-- VIEW 2: Data Table List --}}
        <div id="table-view-container" class="mt-6 bg-white border border-slate-200/90 rounded-2xl overflow-hidden shadow-xs hidden">
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-slate-200 text-left text-xs">
                    <thead class="bg-slate-50 text-slate-500 font-bold uppercase tracking-wider">
                        <tr>
                            <th class="px-5 py-3.5">No. Kamar</th>
                            <th class="px-5 py-3.5">Properti</th>
                            <th class="px-5 py-3.5">Tipe Kamar</th>
                            <th class="px-5 py-3.5">Harga Sewa</th>
                            <th class="px-5 py-3.5">Status</th>
                            <th class="px-5 py-3.5">Penghuni Saat Ini</th>
                            <th class="px-5 py-3.5 text-right">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 font-medium text-slate-700">
                        @foreach ($rooms as $room)
                            <tr class="hover:bg-slate-50/70">
                                <td class="px-5 py-4 font-extrabold text-slate-900 text-sm">
                                    {{ $room->room_number }}
                                </td>
                                <td class="px-5 py-4 text-slate-500">
                                    {{ $room->property->name }}
                                </td>
                                <td class="px-5 py-4">
                                    <span class="px-2 py-0.5 rounded-md bg-slate-100 font-semibold">{{ $room->type }}</span>
                                </td>
                                <td class="px-5 py-4 font-bold text-slate-900">
                                    Rp {{ number_format((float) $room->price, 0, ',', '.') }}
                                </td>
                                <td class="px-5 py-4">
                                    <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full font-bold {{ $room->status->badgeClass() }}">
                                        <span class="h-1.5 w-1.5 rounded-full {{ $room->status->dotClass() }}"></span>
                                        {{ $room->status->label() }}
                                    </span>
                                </td>
                                <td class="px-5 py-4">
                                    @if ($room->currentTenant)
                                        <span class="font-bold text-blue-900">{{ $room->currentTenant->name }}</span>
                                        <span class="block text-[10px] text-slate-400">Jatuh Tempo: Tgl {{ $room->currentTenant->due_day }}</span>
                                    @else
                                        <span class="text-slate-400 italic">- Kosong -</span>
                                    @endif
                                </td>
                                <td class="px-5 py-4 text-right">
                                    <div class="inline-flex items-center gap-2">
                                        <button type="button" onclick="openEditRoomModal({{ json_encode($room) }})" class="font-bold text-[#1E3A8A] hover:underline">
                                            Edit
                                        </button>
                                        <form action="{{ route('owner.rooms.destroy', $room) }}" method="POST" onsubmit="return confirm('Hapus kamar {{ $room->room_number }}?');" class="inline">
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

    {{-- Modal Tambah Kamar --}}
    <div id="add-room-modal" class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-900/50 backdrop-blur-xs hidden">
        <div class="w-full max-w-lg rounded-2xl bg-white p-6 shadow-2xl">
            <div class="flex items-center justify-between border-b border-slate-100 pb-3 mb-4">
                <h3 class="text-lg font-bold text-slate-900">Tambah Kamar Kost Baru</h3>
                <button type="button" onclick="closeModal('add-room-modal')" class="text-slate-400 hover:text-slate-600 font-bold text-lg">✕</button>
            </div>

            <form action="{{ route('owner.rooms.store') }}" method="POST" class="space-y-4">
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

                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label class="block text-xs font-semibold uppercase tracking-wider text-slate-700">Nomor / Kode Kamar</label>
                        <input type="text" name="room_number" required class="mt-1 block w-full rounded-xl border border-slate-300 px-3.5 py-2 text-sm focus:border-[#1E3A8A] focus:outline-hidden" placeholder="Misal: 101 atau A-01">
                    </div>

                    <div>
                        <label class="block text-xs font-semibold uppercase tracking-wider text-slate-700">Status Awal</label>
                        <select name="status" required class="mt-1 block w-full rounded-xl border border-slate-300 px-3.5 py-2 text-sm focus:border-[#1E3A8A] focus:outline-hidden">
                            <option value="available">Tersedia (Kosong)</option>
                            <option value="occupied">Terisi (Dihuni)</option>
                            <option value="maintenance">Perbaikan (Renovasi)</option>
                        </select>
                    </div>
                </div>

                <div>
                    <label class="block text-xs font-semibold uppercase tracking-wider text-slate-700">Tipe Kamar</label>
                    <input type="text" name="type" required class="mt-1 block w-full rounded-xl border border-slate-300 px-3.5 py-2 text-sm focus:border-[#1E3A8A] focus:outline-hidden" placeholder="Misal: Deluxe AC, VIP Eksklusif, Standard Fan">
                </div>

                <div>
                    <label class="block text-xs font-semibold uppercase tracking-wider text-slate-700">Harga Sewa Bulanan (Rp)</label>
                    <input type="number" name="price" step="10000" min="0" required class="mt-1 block w-full rounded-xl border border-slate-300 px-3.5 py-2 text-sm focus:border-[#1E3A8A] focus:outline-hidden" placeholder="1500000">
                </div>

                <div class="flex items-center justify-end gap-3 pt-3 border-t border-slate-100">
                    <button type="button" onclick="closeModal('add-room-modal')" class="rounded-xl border border-slate-300 px-4 py-2 text-xs font-semibold text-slate-700 hover:bg-slate-50">Batal</button>
                    <button type="submit" class="rounded-xl bg-[#1E3A8A] px-5 py-2 text-xs font-bold text-white hover:bg-[#172554]">Simpan Kamar</button>
                </div>
            </form>
        </div>
    </div>

    {{-- Modal Edit Kamar --}}
    <div id="edit-room-modal" class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-900/50 backdrop-blur-xs hidden">
        <div class="w-full max-w-lg rounded-2xl bg-white p-6 shadow-2xl">
            <div class="flex items-center justify-between border-b border-slate-100 pb-3 mb-4">
                <h3 class="text-lg font-bold text-slate-900">Ubah Data Kamar</h3>
                <button type="button" onclick="closeModal('edit-room-modal')" class="text-slate-400 hover:text-slate-600 font-bold text-lg">✕</button>
            </div>

            <form id="edit-room-form" method="POST" class="space-y-4">
                @csrf
                @method('PUT')

                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label class="block text-xs font-semibold uppercase tracking-wider text-slate-700">Nomor / Kode Kamar</label>
                        <input type="text" id="edit-room-number" name="room_number" required class="mt-1 block w-full rounded-xl border border-slate-300 px-3.5 py-2 text-sm focus:border-[#1E3A8A] focus:outline-hidden">
                    </div>

                    <div>
                        <label class="block text-xs font-semibold uppercase tracking-wider text-slate-700">Status Kamar</label>
                        <select id="edit-status" name="status" required class="mt-1 block w-full rounded-xl border border-slate-300 px-3.5 py-2 text-sm focus:border-[#1E3A8A] focus:outline-hidden">
                            <option value="available">Tersedia</option>
                            <option value="occupied">Terisi</option>
                            <option value="maintenance">Perbaikan</option>
                        </select>
                    </div>
                </div>

                <div>
                    <label class="block text-xs font-semibold uppercase tracking-wider text-slate-700">Tipe Kamar</label>
                    <input type="text" id="edit-type" name="type" required class="mt-1 block w-full rounded-xl border border-slate-300 px-3.5 py-2 text-sm focus:border-[#1E3A8A] focus:outline-hidden">
                </div>

                <div>
                    <label class="block text-xs font-semibold uppercase tracking-wider text-slate-700">Harga Sewa Bulanan (Rp)</label>
                    <input type="number" id="edit-price" name="price" step="10000" min="0" required class="mt-1 block w-full rounded-xl border border-slate-300 px-3.5 py-2 text-sm focus:border-[#1E3A8A] focus:outline-hidden">
                </div>

                <div class="flex items-center justify-end gap-3 pt-3 border-t border-slate-100">
                    <button type="button" onclick="closeModal('edit-room-modal')" class="rounded-xl border border-slate-300 px-4 py-2 text-xs font-semibold text-slate-700 hover:bg-slate-50">Batal</button>
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

        function openEditRoomModal(room) {
            const form = document.getElementById('edit-room-form');
            form.action = `/dashboard/rooms/${room.id}`;
            document.getElementById('edit-room-number').value = room.room_number;
            document.getElementById('edit-type').value = room.type;
            document.getElementById('edit-price').value = parseInt(room.price);
            document.getElementById('edit-status').value = room.status;
            openModal('edit-room-modal');
        }

        function switchView(mode) {
            const gridContainer = document.getElementById('grid-view-container');
            const tableContainer = document.getElementById('table-view-container');
            const btnGrid = document.getElementById('btn-view-grid');
            const btnTable = document.getElementById('btn-view-table');

            if (mode === 'grid') {
                gridContainer.classList.remove('hidden');
                tableContainer.classList.add('hidden');
                btnGrid.classList.add('bg-white', 'text-slate-900', 'shadow-xs');
                btnGrid.classList.remove('text-slate-500');
                btnTable.classList.remove('bg-white', 'text-slate-900', 'shadow-xs');
                btnTable.classList.add('text-slate-500');
            } else {
                gridContainer.classList.add('hidden');
                tableContainer.classList.remove('hidden');
                btnTable.classList.add('bg-white', 'text-slate-900', 'shadow-xs');
                btnTable.classList.remove('text-slate-500');
                btnGrid.classList.remove('bg-white', 'text-slate-900', 'shadow-xs');
                btnGrid.classList.add('text-slate-500');
            }
        }
    </script>
</x-layouts.owner>

