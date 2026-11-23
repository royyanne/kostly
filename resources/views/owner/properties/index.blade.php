<x-layouts.owner>
    <x-slot:header>
        Manajemen Properti Kost
    </x-slot:header>

    {{-- Page Header --}}
    <div class="mt-4 flex flex-col md:flex-row md:items-center justify-between gap-4 bg-white border border-slate-200/90 rounded-2xl p-5 sm:p-6 shadow-xs">
        <div>
            <div class="flex flex-wrap items-center gap-2 mb-1.5">
                <h2 class="text-xl font-bold text-slate-900 tracking-tight">Daftar Properti Kost</h2>
                @if ($user->isFree())
                    <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-xs font-bold {{ $properties->count() >= 1 ? 'bg-amber-100 text-amber-900 border border-amber-300' : 'bg-slate-100 text-slate-700 border border-slate-200' }}">
                        Kuota Paket Free: {{ $properties->count() }}/1 Properti
                    </span>
                @else
                    <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-xs font-bold bg-amber-50 text-amber-800 border border-amber-200">
                        ⭐ Paket Pro: Unlimited Properti
                    </span>
                @endif
            </div>
            <p class="text-xs sm:text-sm text-slate-500">
                Kelola data cabang lokasi kosan atau kontrakan yang Anda miliki.
            </p>
        </div>
        <div class="flex items-center gap-2">
            @if ($user->canCreateProperty())
                <button type="button" onclick="openModal('add-property-modal')" class="inline-flex items-center gap-2 rounded-xl bg-[#1E3A8A] px-4 py-2.5 text-xs sm:text-sm font-bold text-white shadow-md shadow-indigo-950/20 hover:bg-[#172554] transition-colors">
                    <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                    <span>Tambah Properti Baru</span>
                </button>
            @else
                <button type="button" onclick="openUpgradeModal()" class="inline-flex items-center gap-2 rounded-xl bg-gradient-to-r from-amber-500 to-amber-600 px-4 py-2.5 text-xs sm:text-sm font-bold text-white shadow-md shadow-amber-500/20 hover:from-amber-600 hover:to-amber-700 transition-all">
                    <span>⭐ Kuota Penuh - Upgrade ke Pro</span>
                </button>
            @endif
        </div>
    </div>

    @if (! $user->canCreateProperty())
        {{-- Banner Ajakan Upgrade Pro --}}
        <div class="mt-4 rounded-2xl border border-amber-200 bg-gradient-to-r from-amber-50 via-white to-amber-50/50 p-4 sm:p-5 flex flex-col sm:flex-row sm:items-center justify-between gap-3 shadow-2xs">
            <div class="flex items-center gap-3">
                <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-xl bg-amber-100 text-amber-700 font-bold text-lg">
                    ⭐
                </div>
                <div>
                    <h3 class="text-sm font-bold text-slate-900">Batas Kuota Properti Paket Free Telah Penuh (1/1)</h3>
                    <p class="text-xs text-slate-500 mt-0.5">Akun Paket Free dibatasi maksimal 1 properti. Ingin membuka cabang kosan baru? Tingkatkan ke Paket Pro untuk mengelola properti tanpa batas.</p>
                </div>
            </div>
            <a href="{{ route('owner.subscription.index') }}" class="shrink-0 inline-flex items-center justify-center gap-1.5 rounded-xl bg-emerald-600 px-4 py-2 text-xs font-bold text-white hover:bg-emerald-700 transition-colors shadow-xs">
                <span>Upgrade ke Pro</span>
                <svg class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
            </a>
        </div>
    @endif

    {{-- Properties Grid --}}
    @if ($properties->isEmpty())
        <div class="mt-6 bg-white border border-slate-200/90 rounded-2xl p-12 text-center shadow-xs">
            <div class="flex h-16 w-16 items-center justify-center rounded-2xl bg-indigo-50 text-[#1E3A8A] mx-auto mb-4">
                <svg class="h-8 w-8" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/></svg>
            </div>
            <h3 class="text-base font-bold text-slate-900">Belum ada properti terdaftar</h3>
            <p class="text-xs text-slate-500 mt-1 max-w-sm mx-auto">
                Mulai kelola bisnis kost Anda dengan mendaftarkan lokasi kosan pertama Anda.
            </p>
            <button type="button" onclick="openModal('add-property-modal')" class="mt-5 inline-flex items-center gap-2 rounded-xl bg-[#1E3A8A] px-5 py-2.5 text-xs font-bold text-white shadow-md hover:bg-[#172554] transition-colors">
                Tambah Properti Sekarang
            </button>
        </div>
    @else
        <div class="mt-6 grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            @foreach ($properties as $property)
                @php
                    $total = $property->rooms_count;
                    $occupied = $property->rooms->where('status', App\Enums\RoomStatus::Occupied)->count();
                    $occupancy = $total > 0 ? (int) round(($occupied / $total) * 100) : 0;
                @endphp
                <div class="rounded-2xl border border-slate-200/90 bg-white p-6 shadow-xs flex flex-col justify-between hover:border-indigo-300 transition-all">
                    <div>
                        <div class="flex items-start justify-between gap-2">
                            <div>
                                <span class="inline-flex items-center px-2 py-0.5 rounded-md text-[10px] font-bold bg-indigo-50 text-[#1E3A8A] mb-1">
                                    Properti ID: #{{ $property->id }}
                                </span>
                                <h3 class="text-lg font-bold text-slate-900 tracking-tight">{{ $property->name }}</h3>
                            </div>
                            <div class="flex items-center gap-1">
                                <button type="button" onclick="openEditModal({{ json_encode($property) }})" class="p-1.5 rounded-lg text-slate-400 hover:text-indigo-600 hover:bg-slate-50 transition-colors" title="Edit Properti">
                                    <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"/></svg>
                                </button>
                                <form action="{{ route('owner.properties.destroy', $property) }}" method="POST" onsubmit="return confirm('Apakah Anda yakin ingin menghapus properti ini beserta semua kamar di dalamnya? Data yang dihapus tidak dapat dipulihkan.');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="p-1.5 rounded-lg text-slate-400 hover:text-rose-600 hover:bg-slate-50 transition-colors" title="Hapus Properti">
                                        <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                    </button>
                                </form>
                            </div>
                        </div>

                        <p class="mt-2 text-xs text-slate-600 flex items-start gap-1.5">
                            <svg class="h-4 w-4 text-slate-400 shrink-0 mt-0.5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                            <span>{{ $property->address }}</span>
                        </p>

                        @if ($property->description)
                            <p class="mt-2 text-xs text-slate-500 line-clamp-2">{{ $property->description }}</p>
                        @endif

                        {{-- Stats summary inside card --}}
                        <div class="mt-5 grid grid-cols-2 gap-3 pt-4 border-t border-slate-100 text-xs">
                            <div class="rounded-xl bg-slate-50 p-3">
                                <span class="text-slate-500 block text-[11px]">Jumlah Kamar</span>
                                <span class="text-base font-bold text-slate-900">{{ $property->rooms_count }} Kamar</span>
                            </div>
                            <div class="rounded-xl bg-slate-50 p-3">
                                <span class="text-slate-500 block text-[11px]">Penghuni Aktif</span>
                                <span class="text-base font-bold text-blue-700">{{ $property->tenants_count }} Orang</span>
                            </div>
                        </div>

                        {{-- Occupancy Bar --}}
                        <div class="mt-3">
                            <div class="flex items-center justify-between text-[11px] text-slate-500 mb-1">
                                <span>Tingkat Okupansi:</span>
                                <span class="font-bold text-slate-900">{{ $occupancy }}%</span>
                            </div>
                            <div class="h-2 w-full rounded-full bg-slate-100 overflow-hidden">
                                <div class="h-full bg-[#1E3A8A] rounded-full" style="width: {{ $occupancy }}%"></div>
                            </div>
                        </div>
                    </div>

                    <div class="mt-5 pt-4 border-t border-slate-100 flex items-center justify-between">
                        <a href="{{ route('owner.rooms.index', ['property_id' => $property->id]) }}" class="inline-flex items-center gap-1.5 text-xs font-bold text-[#1E3A8A] hover:underline">
                            <span>Buka Denah Kamar</span>
                            <svg class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                        </a>
                        <span class="text-[11px] text-slate-400">Dibuat {{ $property->created_at->diffForHumans() }}</span>
                    </div>
                </div>
            @endforeach
        </div>
    @endif

    {{-- Modal Tambah Properti --}}
    <div id="add-property-modal" class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-900/50 backdrop-blur-xs hidden">
        <div class="w-full max-w-lg rounded-2xl bg-white p-6 shadow-2xl">
            <div class="flex items-center justify-between border-b border-slate-100 pb-3 mb-4">
                <h3 class="text-lg font-bold text-slate-900">Tambah Properti Kost Baru</h3>
                <button type="button" onclick="closeModal('add-property-modal')" class="text-slate-400 hover:text-slate-600 font-bold text-lg">✕</button>
            </div>

            <form action="{{ route('owner.properties.store') }}" method="POST" class="space-y-4">
                @csrf
                <div>
                    <label class="block text-xs font-semibold uppercase tracking-wider text-slate-700">Nama Properti Kost</label>
                    <input type="text" name="name" required class="mt-1 block w-full rounded-xl border border-slate-300 px-3.5 py-2 text-sm focus:border-[#1E3A8A] focus:outline-hidden" placeholder="Misal: Kost Griya Nyaman Pasteur">
                </div>

                <div>
                    <label class="block text-xs font-semibold uppercase tracking-wider text-slate-700">Alamat Lengkap</label>
                    <textarea name="address" rows="3" required class="mt-1 block w-full rounded-xl border border-slate-300 px-3.5 py-2 text-sm focus:border-[#1E3A8A] focus:outline-hidden" placeholder="Jalan, nomor, kelurahan, kecamatan, kota"></textarea>
                </div>

                <div>
                    <label class="block text-xs font-semibold uppercase tracking-wider text-slate-700">Deskripsi / Fasilitas Tambahan (Opsional)</label>
                    <textarea name="description" rows="2" class="mt-1 block w-full rounded-xl border border-slate-300 px-3.5 py-2 text-sm focus:border-[#1E3A8A] focus:outline-hidden" placeholder="Fasilitas AC, WiFi 100 Mbps, Dapur, Parkir Luas, dll"></textarea>
                </div>

                <div class="flex items-center justify-end gap-3 pt-3 border-t border-slate-100">
                    <button type="button" onclick="closeModal('add-property-modal')" class="rounded-xl border border-slate-300 px-4 py-2 text-xs font-semibold text-slate-700 hover:bg-slate-50">Batal</button>
                    <button type="submit" class="rounded-xl bg-[#1E3A8A] px-5 py-2 text-xs font-bold text-white hover:bg-[#172554]">Simpan Properti</button>
                </div>
            </form>
        </div>
    </div>

    {{-- Modal Edit Properti --}}
    <div id="edit-property-modal" class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-900/50 backdrop-blur-xs hidden">
        <div class="w-full max-w-lg rounded-2xl bg-white p-6 shadow-2xl">
            <div class="flex items-center justify-between border-b border-slate-100 pb-3 mb-4">
                <h3 class="text-lg font-bold text-slate-900">Ubah Data Properti</h3>
                <button type="button" onclick="closeModal('edit-property-modal')" class="text-slate-400 hover:text-slate-600 font-bold text-lg">✕</button>
            </div>

            <form id="edit-property-form" method="POST" class="space-y-4">
                @csrf
                @method('PUT')
                <div>
                    <label class="block text-xs font-semibold uppercase tracking-wider text-slate-700">Nama Properti Kost</label>
                    <input type="text" id="edit-name" name="name" required class="mt-1 block w-full rounded-xl border border-slate-300 px-3.5 py-2 text-sm focus:border-[#1E3A8A] focus:outline-hidden">
                </div>

                <div>
                    <label class="block text-xs font-semibold uppercase tracking-wider text-slate-700">Alamat Lengkap</label>
                    <textarea id="edit-address" name="address" rows="3" required class="mt-1 block w-full rounded-xl border border-slate-300 px-3.5 py-2 text-sm focus:border-[#1E3A8A] focus:outline-hidden"></textarea>
                </div>

                <div>
                    <label class="block text-xs font-semibold uppercase tracking-wider text-slate-700">Deskripsi / Fasilitas Tambahan</label>
                    <textarea id="edit-description" name="description" rows="2" class="mt-1 block w-full rounded-xl border border-slate-300 px-3.5 py-2 text-sm focus:border-[#1E3A8A] focus:outline-hidden"></textarea>
                </div>

                <div class="flex items-center justify-end gap-3 pt-3 border-t border-slate-100">
                    <button type="button" onclick="closeModal('edit-property-modal')" class="rounded-xl border border-slate-300 px-4 py-2 text-xs font-semibold text-slate-700 hover:bg-slate-50">Batal</button>
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

        function openEditModal(property) {
            const form = document.getElementById('edit-property-form');
            form.action = `/dashboard/properties/${property.id}`;
            document.getElementById('edit-name').value = property.name;
            document.getElementById('edit-address').value = property.address;
            document.getElementById('edit-description').value = property.description || '';
            openModal('edit-property-modal');
        }
    </script>
    <x-upgrade-modal />
</x-layouts.owner>

