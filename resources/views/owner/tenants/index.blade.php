<x-layouts.owner>
    <x-slot:header>
        Manajemen Penghuni Kost
    </x-slot:header>

    {{-- Page Header --}}
    <div class="mt-4 flex flex-col md:flex-row md:items-center justify-between gap-4 bg-white border border-slate-200/90 rounded-2xl p-5 sm:p-6 shadow-xs">
        <div>
            <h2 class="text-xl font-bold text-slate-900 tracking-tight">Data Penghuni Kost</h2>
            <p class="text-xs sm:text-sm text-slate-500 mt-1">
                Kelola data penyewa aktif, jadwal jatuh tempo bulanan, dan akses portal mandiri penghuni.
            </p>
        </div>

        <div>
            <button type="button" onclick="openModal('add-tenant-modal')" class="inline-flex items-center gap-2 rounded-xl bg-[#1E3A8A] px-4 py-2.5 text-xs sm:text-sm font-bold text-white shadow-md shadow-indigo-950/20 hover:bg-[#172554] transition-colors">
                <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z"/></svg>
                <span>Daftarkan Penghuni Baru</span>
            </button>
        </div>
    </div>

    {{-- Filter Bar --}}
    <div class="mt-6 flex flex-col md:flex-row md:items-center justify-between gap-4 bg-white border border-slate-200/90 rounded-2xl p-4 shadow-xs">
        {{-- Status Filter --}}
        <div class="flex items-center gap-2 text-xs font-semibold">
            <a href="{{ route('owner.tenants.index', array_merge(request()->query(), ['status' => 'active'])) }}"
                class="px-3.5 py-1.5 rounded-xl border transition-all {{ $statusFilter === 'active' ? 'bg-[#1E3A8A] text-white border-[#1E3A8A]' : 'bg-slate-50 text-slate-600 border-slate-200 hover:bg-slate-100' }}">
                Penghuni Aktif ({{ $activeCount }})
            </a>
            <a href="{{ route('owner.tenants.index', array_merge(request()->query(), ['status' => 'inactive'])) }}"
                class="px-3.5 py-1.5 rounded-xl border transition-all {{ $statusFilter === 'inactive' ? 'bg-[#1E3A8A] text-white border-[#1E3A8A]' : 'bg-slate-50 text-slate-600 border-slate-200 hover:bg-slate-100' }}">
                Riwayat / Checkout ({{ $inactiveCount }})
            </a>
            <a href="{{ route('owner.tenants.index', array_merge(request()->query(), ['status' => 'all'])) }}"
                class="px-3.5 py-1.5 rounded-xl border transition-all {{ $statusFilter === 'all' ? 'bg-[#1E3A8A] text-white border-[#1E3A8A]' : 'bg-slate-50 text-slate-600 border-slate-200 hover:bg-slate-100' }}">
                Semua
            </a>
        </div>

        {{-- Search & Property Select --}}
        <form action="{{ route('owner.tenants.index') }}" method="GET" class="flex flex-wrap items-center gap-2">
            <input type="hidden" name="status" value="{{ $statusFilter }}">

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
                <input type="text" name="search" value="{{ $search }}" placeholder="Cari nama/HP/kamar..."
                    class="rounded-xl border border-slate-300 py-1.5 pl-8 pr-3 text-xs text-slate-900 placeholder-slate-400 focus:border-[#1E3A8A] focus:outline-hidden">
                <svg class="h-4 w-4 text-slate-400 absolute left-2.5 top-2" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
            </div>

            @if ($search || $selectedPropertyId)
                <a href="{{ route('owner.tenants.index') }}" class="text-xs text-rose-600 font-semibold hover:underline">Reset</a>
            @endif
        </form>
    </div>

    {{-- Tenants Table --}}
    @if ($tenants->isEmpty())
        <div class="mt-6 bg-white border border-slate-200/90 rounded-2xl p-12 text-center shadow-xs">
            <div class="flex h-16 w-16 items-center justify-center rounded-2xl bg-indigo-50 text-[#1E3A8A] mx-auto mb-4">
                <svg class="h-8 w-8" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/></svg>
            </div>
            <h3 class="text-base font-bold text-slate-900">Belum ada data penghuni</h3>
            <p class="text-xs text-slate-500 mt-1 max-w-sm mx-auto">
                Daftarkan penghuni kost Anda untuk mulai memanfaatkan fitur penagihan otomatis via WhatsApp.
            </p>
            <button type="button" onclick="openModal('add-tenant-modal')" class="mt-5 inline-flex items-center gap-2 rounded-xl bg-[#1E3A8A] px-5 py-2.5 text-xs font-bold text-white shadow-md hover:bg-[#172554] transition-colors">
                Daftarkan Penghuni Sekarang
            </button>
        </div>
    @else
        <div class="mt-6 bg-white border border-slate-200/90 rounded-2xl overflow-hidden shadow-xs">
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-slate-200 text-left text-xs">
                    <thead class="bg-slate-50 text-slate-500 font-bold uppercase tracking-wider">
                        <tr>
                            <th class="px-5 py-3.5">Nama Penghuni</th>
                            <th class="px-5 py-3.5">Kamar</th>
                            <th class="px-5 py-3.5">Kontak WhatsApp</th>
                            <th class="px-5 py-3.5">Jatuh Tempo</th>
                            <th class="px-5 py-3.5">Mulai Sewa</th>
                            <th class="px-5 py-3.5">Portal Mandiri</th>
                            <th class="px-5 py-3.5 text-right">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 font-medium text-slate-700">
                        @foreach ($tenants as $tenant)
                            <tr class="hover:bg-slate-50/70">
                                {{-- Nama --}}
                                <td class="px-5 py-4">
                                    <div class="flex items-center gap-2.5">
                                        <div class="flex h-9 w-9 shrink-0 items-center justify-center rounded-xl bg-indigo-50 font-bold text-[#1E3A8A]">
                                            {{ substr($tenant->name, 0, 2) }}
                                        </div>
                                        <div>
                                            <span class="font-extrabold text-slate-900 text-sm block">{{ $tenant->name }}</span>
                                            @if ($tenant->emergency_contact)
                                                <span class="text-[10px] text-slate-400">Darurat: {{ $tenant->emergency_contact }}</span>
                                            @endif
                                        </div>
                                    </div>
                                </td>

                                {{-- Kamar --}}
                                <td class="px-5 py-4">
                                    @if ($tenant->room)
                                        <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-lg bg-blue-50 text-blue-800 font-bold">
                                            Kamar {{ $tenant->room->room_number }}
                                        </span>
                                        <span class="block text-[10px] text-slate-400 mt-0.5">{{ $tenant->room->type }}</span>
                                    @else
                                        <span class="text-slate-400 italic">- Tidak Ada -</span>
                                    @endif
                                </td>

                                {{-- Kontak WhatsApp --}}
                                <td class="px-5 py-4 font-mono">
                                    <a href="https://wa.me/{{ preg_replace('/[^0-9]/', '', (str_starts_with($tenant->phone, '0') ? '62' . substr($tenant->phone, 1) : $tenant->phone)) }}"
                                        target="_blank" class="inline-flex items-center gap-1 text-emerald-700 font-bold hover:underline">
                                        <span>{{ $tenant->phone }}</span>
                                        <svg class="h-3.5 w-3.5" fill="currentColor" viewBox="0 0 24 24"><path d="M12.04 2C6.58 2 2.13 6.45 2.13 11.91C2.13 13.66 2.59 15.36 3.45 16.86L2.05 22L7.3 20.62C8.75 21.41 10.38 21.83 12.04 21.83C17.5 21.83 21.95 17.38 21.95 11.92C21.95 9.27 20.92 6.78 19.05 4.91C17.18 3.03 14.69 2 12.04 2M12.05 3.67C14.25 3.67 16.31 4.53 17.87 6.09C19.42 7.65 20.28 9.72 20.28 11.92C20.28 16.46 16.58 20.15 12.04 20.15C10.56 20.15 9.11 19.76 7.85 19.02L7.55 18.84L4.42 19.66L5.26 16.61L5.06 16.29C4.24 14.99 3.81 13.47 3.81 11.91C3.81 7.37 7.5 3.67 12.05 3.67Z"/></svg>
                                    </a>
                                </td>

                                {{-- Due Day --}}
                                <td class="px-5 py-4">
                                    <span class="font-bold text-slate-900">Setiap Tgl {{ $tenant->due_day }}</span>
                                    <span class="block text-[10px] text-slate-400">Bulanan</span>
                                </td>

                                {{-- Tanggal Sewa --}}
                                <td class="px-5 py-4 text-slate-600">
                                    {{ $tenant->start_date->translatedFormat('d M Y') }}
                                    <span class="block text-[10px] text-slate-400">({{ $tenant->start_date->diffForHumans() }})</span>
                                </td>

                                {{-- Portal Mandiri Link --}}
                                <td class="px-5 py-4">
                                    <div class="flex items-center gap-1.5">
                                        <a href="{{ route('public.portal', $tenant->access_token) }}" target="_blank"
                                            class="inline-flex items-center gap-1 px-2.5 py-1 rounded-lg bg-indigo-50 text-[#1E3A8A] font-bold hover:bg-indigo-100 transition-colors">
                                            <span>Buka Portal</span>
                                            <svg class="h-3 w-3" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/></svg>
                                        </a>
                                        <button type="button" onclick="navigator.clipboard.writeText('{{ url('/portal/' . $tenant->access_token) }}'); alert('Link portal anak kost berhasil disalin ke clipboard!');"
                                            class="p-1 rounded-md text-slate-400 hover:text-slate-700 hover:bg-slate-100" title="Salin Tautan">
                                            <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z"/></svg>
                                        </button>
                                    </div>
                                </td>

                                {{-- Aksi --}}
                                <td class="px-5 py-4 text-right">
                                    <div class="inline-flex items-center gap-2">
                                        @if ($tenant->is_active)
                                            <form action="{{ route('owner.tenants.checkout', $tenant) }}" method="POST" onsubmit="return confirm('Proses checkout untuk {{ $tenant->name }}? Kamar akan dikembalikan ke status Tersedia.');">
                                                @csrf
                                                @method('PATCH')
                                                <button type="submit" class="font-bold text-amber-600 hover:underline" title="Checkout">
                                                    Checkout
                                                </button>
                                            </form>
                                        @endif

                                        <button type="button" onclick="openEditTenantModal({{ json_encode($tenant) }})" class="font-bold text-[#1E3A8A] hover:underline">
                                            Edit
                                        </button>

                                        <form action="{{ route('owner.tenants.destroy', $tenant) }}" method="POST" onsubmit="return confirm('Hapus data penghuni {{ $tenant->name }}?');">
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

    {{-- Modal Tambah Penghuni --}}
    <div id="add-tenant-modal" class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-900/50 backdrop-blur-xs hidden">
        <div class="w-full max-w-lg rounded-2xl bg-white p-6 shadow-2xl max-h-[90vh] overflow-y-auto">
            <div class="flex items-center justify-between border-b border-slate-100 pb-3 mb-4">
                <h3 class="text-lg font-bold text-slate-900">Daftarkan Penghuni Baru</h3>
                <button type="button" onclick="closeModal('add-tenant-modal')" class="text-slate-400 hover:text-slate-600 font-bold text-lg">✕</button>
            </div>

            <form action="{{ route('owner.tenants.store') }}" method="POST" class="space-y-4">
                @csrf

                <div>
                    <label class="block text-xs font-semibold uppercase tracking-wider text-slate-700">Pilih Properti Kost</label>
                    <select name="property_id" id="tenant-property-id" required class="mt-1 block w-full rounded-xl border border-slate-300 px-3.5 py-2 text-sm focus:border-[#1E3A8A] focus:outline-hidden">
                        @foreach ($properties as $prop)
                            <option value="{{ $prop->id }}" {{ $selectedPropertyId == $prop->id ? 'selected' : '' }}>
                                {{ $prop->name }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label class="block text-xs font-semibold uppercase tracking-wider text-slate-700">Pilih Kamar yang Tersedia</label>
                    <select name="room_id" required class="mt-1 block w-full rounded-xl border border-slate-300 px-3.5 py-2 text-sm focus:border-[#1E3A8A] focus:outline-hidden">
                        @if ($availableRooms->isEmpty())
                            <option value="" disabled selected>Tidak ada kamar berstatus 'Tersedia'. Ubah status kamar terlebih dahulu.</option>
                        @else
                            <option value="">-- Pilih Kamar --</option>
                            @foreach ($availableRooms as $availRoom)
                                <option value="{{ $availRoom->id }}">
                                    Kamar {{ $availRoom->room_number }} ({{ $availRoom->type }}) - Rp {{ number_format((float) $availRoom->price, 0, ',', '.') }}/bln
                                </option>
                            @endforeach
                        @endif
                    </select>
                </div>

                <div>
                    <label class="block text-xs font-semibold uppercase tracking-wider text-slate-700">Nama Lengkap Penghuni</label>
                    <input type="text" name="name" required class="mt-1 block w-full rounded-xl border border-slate-300 px-3.5 py-2 text-sm focus:border-[#1E3A8A] focus:outline-hidden" placeholder="Misal: Andika Pratama">
                </div>

                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label class="block text-xs font-semibold uppercase tracking-wider text-slate-700">No. WhatsApp</label>
                        <input type="text" name="phone" required class="mt-1 block w-full rounded-xl border border-slate-300 px-3.5 py-2 text-sm focus:border-[#1E3A8A] focus:outline-hidden" placeholder="081234567890">
                    </div>

                    <div>
                        <label class="block text-xs font-semibold uppercase tracking-wider text-slate-700">Kontak Darurat</label>
                        <input type="text" name="emergency_contact" class="mt-1 block w-full rounded-xl border border-slate-300 px-3.5 py-2 text-sm focus:border-[#1E3A8A] focus:outline-hidden" placeholder="081xxx (Ayah)">
                    </div>
                </div>

                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label class="block text-xs font-semibold uppercase tracking-wider text-slate-700">Tanggal Mulai Sewa</label>
                        <input type="date" name="start_date" value="{{ now()->toDateString() }}" required class="mt-1 block w-full rounded-xl border border-slate-300 px-3.5 py-2 text-sm focus:border-[#1E3A8A] focus:outline-hidden">
                    </div>

                    <div>
                        <label class="block text-xs font-semibold uppercase tracking-wider text-slate-700">Tgl Jatuh Tempo Bulanan</label>
                        <input type="number" name="due_day" min="1" max="28" value="1" required class="mt-1 block w-full rounded-xl border border-slate-300 px-3.5 py-2 text-sm focus:border-[#1E3A8A] focus:outline-hidden" placeholder="1-28">
                        <span class="text-[10px] text-slate-400">Misal tanggal 5 setiap bulannya</span>
                    </div>
                </div>

                <div class="flex items-center justify-end gap-3 pt-3 border-t border-slate-100">
                    <button type="button" onclick="closeModal('add-tenant-modal')" class="rounded-xl border border-slate-300 px-4 py-2 text-xs font-semibold text-slate-700 hover:bg-slate-50">Batal</button>
                    <button type="submit" class="rounded-xl bg-[#1E3A8A] px-5 py-2 text-xs font-bold text-white hover:bg-[#172554]">Simpan Penghuni</button>
                </div>
            </form>
        </div>
    </div>

    {{-- Modal Edit Penghuni --}}
    <div id="edit-tenant-modal" class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-900/50 backdrop-blur-xs hidden">
        <div class="w-full max-w-lg rounded-2xl bg-white p-6 shadow-2xl max-h-[90vh] overflow-y-auto">
            <div class="flex items-center justify-between border-b border-slate-100 pb-3 mb-4">
                <h3 class="text-lg font-bold text-slate-900">Ubah Data Penghuni</h3>
                <button type="button" onclick="closeModal('edit-tenant-modal')" class="text-slate-400 hover:text-slate-600 font-bold text-lg">✕</button>
            </div>

            <form id="edit-tenant-form" method="POST" class="space-y-4">
                @csrf
                @method('PUT')

                <div>
                    <label class="block text-xs font-semibold uppercase tracking-wider text-slate-700">Pilih Kamar</label>
                    <select id="edit-room-id" name="room_id" required class="mt-1 block w-full rounded-xl border border-slate-300 px-3.5 py-2 text-sm focus:border-[#1E3A8A] focus:outline-hidden">
                        @foreach ($user->rooms as $r)
                            <option value="{{ $r->id }}">Kamar {{ $r->room_number }} ({{ $r->type }})</option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label class="block text-xs font-semibold uppercase tracking-wider text-slate-700">Nama Lengkap</label>
                    <input type="text" id="edit-name" name="name" required class="mt-1 block w-full rounded-xl border border-slate-300 px-3.5 py-2 text-sm focus:border-[#1E3A8A] focus:outline-hidden">
                </div>

                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label class="block text-xs font-semibold uppercase tracking-wider text-slate-700">No. WhatsApp</label>
                        <input type="text" id="edit-phone" name="phone" required class="mt-1 block w-full rounded-xl border border-slate-300 px-3.5 py-2 text-sm focus:border-[#1E3A8A] focus:outline-hidden">
                    </div>

                    <div>
                        <label class="block text-xs font-semibold uppercase tracking-wider text-slate-700">Kontak Darurat</label>
                        <input type="text" id="edit-emergency-contact" name="emergency_contact" class="mt-1 block w-full rounded-xl border border-slate-300 px-3.5 py-2 text-sm focus:border-[#1E3A8A] focus:outline-hidden">
                    </div>
                </div>

                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label class="block text-xs font-semibold uppercase tracking-wider text-slate-700">Tanggal Mulai Sewa</label>
                        <input type="date" id="edit-start-date" name="start_date" required class="mt-1 block w-full rounded-xl border border-slate-300 px-3.5 py-2 text-sm focus:border-[#1E3A8A] focus:outline-hidden">
                    </div>

                    <div>
                        <label class="block text-xs font-semibold uppercase tracking-wider text-slate-700">Tgl Jatuh Tempo</label>
                        <input type="number" id="edit-due-day" name="due_day" min="1" max="28" required class="mt-1 block w-full rounded-xl border border-slate-300 px-3.5 py-2 text-sm focus:border-[#1E3A8A] focus:outline-hidden">
                    </div>
                </div>

                <div>
                    <label class="block text-xs font-semibold uppercase tracking-wider text-slate-700">Status Keaktifan</label>
                    <select id="edit-is-active" name="is_active" class="mt-1 block w-full rounded-xl border border-slate-300 px-3.5 py-2 text-sm focus:border-[#1E3A8A] focus:outline-hidden">
                        <option value="1">Aktif Menghuni</option>
                        <option value="0">Sudah Checkout / Selesai Sewa</option>
                    </select>
                </div>

                <div class="flex items-center justify-end gap-3 pt-3 border-t border-slate-100">
                    <button type="button" onclick="closeModal('edit-tenant-modal')" class="rounded-xl border border-slate-300 px-4 py-2 text-xs font-semibold text-slate-700 hover:bg-slate-50">Batal</button>
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

        function openEditTenantModal(tenant) {
            const form = document.getElementById('edit-tenant-form');
            form.action = `/dashboard/tenants/${tenant.id}`;
            document.getElementById('edit-name').value = tenant.name;
            document.getElementById('edit-phone').value = tenant.phone;
            document.getElementById('edit-emergency-contact').value = tenant.emergency_contact || '';
            document.getElementById('edit-start-date').value = tenant.start_date.split('T')[0];
            document.getElementById('edit-due-day').value = tenant.due_day;
            document.getElementById('edit-is-active').value = tenant.is_active ? '1' : '0';
            if (tenant.room_id) {
                document.getElementById('edit-room-id').value = tenant.room_id;
            }
            openModal('edit-tenant-modal');
        }
    </script>
</x-layouts.owner>
