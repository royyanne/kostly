<x-app>
    {{-- Top Navigation Bar --}}
    <header class="sticky top-0 z-50 border-b border-slate-200/80 bg-white/95 backdrop-blur-md">
        <div class="mx-auto flex max-w-7xl items-center justify-between px-4 py-3.5 sm:px-6 lg:px-8">
            <a href="{{ route('landing') }}" class="flex items-center gap-2.5 group">
                <div class="flex h-10 w-10 items-center justify-center rounded-xl bg-[#1E3A8A] text-white shadow-md shadow-indigo-900/20">
                    <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                    </svg>
                </div>
                <div class="flex flex-col">
                    <span class="text-xl font-bold tracking-tight text-[#1E3A8A]">Kostly<span class="text-emerald-500">.</span></span>
                    <span class="-mt-1 text-[10px] font-semibold tracking-wider text-slate-500 uppercase">Detail Kost</span>
                </div>
            </a>

            <div class="flex items-center gap-3">
                <a href="{{ route('landing') }}#daftar-kost" class="inline-flex items-center gap-1.5 text-xs sm:text-sm font-semibold text-slate-600 hover:text-[#1E3A8A]">
                    <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
                    <span>Cari Kost Lainnya</span>
                </a>
                @auth
                    @if(auth()->user()->isOwner())
                        <a href="{{ route('owner.dashboard') }}" class="rounded-xl bg-[#1E3A8A] px-3.5 py-1.5 text-xs font-semibold text-white shadow-xs">Dashboard Pemilik</a>
                    @elseif(auth()->user()->isTenant())
                        <a href="{{ route('tenant.dashboard') }}" class="rounded-xl bg-emerald-600 px-3.5 py-1.5 text-xs font-semibold text-white shadow-xs">Portal Penyewa</a>
                    @endif
                @else
                    <a href="{{ route('login') }}" class="rounded-xl border border-slate-200 px-3.5 py-1.5 text-xs font-semibold text-slate-700 hover:bg-slate-50">Masuk</a>
                @endauth
            </div>
        </div>
    </header>

    <main class="mx-auto max-w-7xl px-4 py-8 sm:px-6 lg:px-8">
        {{-- Flash Messages --}}
        @if(session('success'))
            <div class="mb-6 rounded-2xl border border-emerald-200 bg-emerald-50 p-4 sm:p-5 shadow-xs flex items-start gap-3">
                <svg class="h-6 w-6 text-emerald-600 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                <div>
                    <h4 class="text-sm font-bold text-emerald-900">Pengajuan Terkirim!</h4>
                    <p class="text-xs sm:text-sm text-emerald-700 mt-0.5">{{ session('success') }}</p>
                </div>
            </div>
        @endif

        @if($errors->any())
            <div class="mb-6 rounded-2xl border border-rose-200 bg-rose-50 p-4 sm:p-5 shadow-xs flex items-start gap-3">
                <svg class="h-6 w-6 text-rose-600 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                <div>
                    <h4 class="text-sm font-bold text-rose-900">Periksa Kembali Formulir</h4>
                    <ul class="text-xs text-rose-700 mt-1 list-disc list-inside space-y-0.5">
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            </div>
        @endif

        {{-- Property Header Banner --}}
        <div class="relative overflow-hidden rounded-3xl bg-gradient-to-r from-[#1E3A8A] via-indigo-900 to-slate-900 p-6 sm:p-10 text-white shadow-xl mb-8">
            <div class="relative z-10 max-w-3xl">
                <div class="flex flex-wrap items-center gap-2 mb-3">
                    <span class="inline-flex items-center gap-1.5 rounded-full bg-emerald-500/20 border border-emerald-400/30 px-3 py-1 text-xs font-bold text-emerald-300">
                        <span class="h-2 w-2 rounded-full bg-emerald-400 animate-pulse"></span>
                        {{ $availableRooms->count() }} Kamar Tersedia
                    </span>
                    @if($property->city)
                        <span class="rounded-full bg-white/10 px-3 py-1 text-xs font-semibold text-slate-200 backdrop-blur-xs">
                            📍 {{ $property->city }}
                        </span>
                    @endif
                </div>

                <h1 class="text-2xl sm:text-4xl font-extrabold tracking-tight text-white mb-2">
                    {{ $property->name }}
                </h1>

                <p class="text-sm sm:text-base text-slate-300 flex items-start gap-1.5 mb-4">
                    <svg class="h-5 w-5 shrink-0 text-slate-400 mt-0.5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                    <span>{{ $property->address }}</span>
                </p>

                <div class="flex flex-wrap items-center gap-4 text-xs sm:text-sm text-slate-300">
                    <div>
                        <span class="text-slate-400">Pengelola:</span>
                        <strong class="text-white ml-1">{{ $property->user?->name ?? 'Pemilik Kost' }}</strong>
                    </div>
                    @if($property->user?->phone)
                        <a href="https://wa.me/{{ preg_replace('/[^0-9]/', '', $property->user->phone) }}?text={{ urlencode('Halo, saya tertarik dengan kamar di ' . $property->name) }}" target="_blank" class="inline-flex items-center gap-1.5 rounded-xl bg-emerald-500 hover:bg-emerald-600 px-3 py-1.5 text-xs font-bold text-white transition-colors">
                            <span>Hubungi Pemilik via WhatsApp</span>
                        </a>
                    @endif
                </div>
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
            {{-- Main Content: Description, Facilities, Available Rooms --}}
            <div class="lg:col-span-2 space-y-8">
                {{-- Deskripsi Kost --}}
                <div class="rounded-3xl border border-slate-200 bg-white p-6 sm:p-8 shadow-xs">
                    <h2 class="text-lg font-bold text-slate-900 mb-3">Tentang Properti Ini</h2>
                    <p class="text-sm sm:text-base text-slate-600 leading-relaxed whitespace-pre-line">
                        {{ $property->description ?: 'Kost yang nyaman, tenang, dan bersih dengan akses strategis ke kampus dan pusat perkantoran.' }}
                    </p>

                    {{-- Fasilitas Kost --}}
                    <h3 class="text-sm font-bold text-slate-800 uppercase tracking-wider mt-6 mb-3">Fasilitas Properti</h3>
                    <div class="flex flex-wrap gap-2 sm:gap-2.5">
                        @php
                            $facilitiesList = $property->facilities ?? [
                                'Kamar Mandi Dalam', 'AC / Kipas Angin', 'WiFi Cepat', 'Kasur Spring Bed', 'Lemari Pakaian', 'Dapur Bersama', 'Parkir Motor / Mobil', 'CCTV 24 Jam'
                            ];
                        @endphp
                        @foreach($facilitiesList as $fac)
                            <span class="inline-flex items-center gap-1.5 rounded-xl border border-slate-200 bg-slate-50 px-3 py-1.5 text-xs font-semibold text-slate-700">
                                <svg class="h-4 w-4 text-emerald-500" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/></svg>
                                {{ $fac }}
                            </span>
                        @endforeach
                    </div>
                </div>

                {{-- Daftar Kamar Kosong (Available) --}}
                <div class="rounded-3xl border border-slate-200 bg-white p-6 sm:p-8 shadow-xs" id="kamar-tersedia">
                    <div class="flex items-center justify-between mb-6">
                        <div>
                            <h2 class="text-lg font-bold text-slate-900">Daftar Kamar Siap Huni</h2>
                            <p class="text-xs sm:text-sm text-slate-500 mt-0.5">Pilih kamar yang sesuai dengan kebutuhan Anda dan ajukan sewa langsung.</p>
                        </div>
                        <span class="rounded-full bg-emerald-50 border border-emerald-200 px-3 py-1 text-xs font-bold text-emerald-700">
                            {{ $availableRooms->count() }} Pilihan
                        </span>
                    </div>

                    @if($availableRooms->isEmpty())
                        <div class="rounded-2xl border border-dashed border-slate-200 p-8 text-center">
                            <svg class="mx-auto h-12 w-12 text-slate-300" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/></svg>
                            <h4 class="mt-3 text-sm font-bold text-slate-700">Semua Kamar Sedang Terisi</h4>
                            <p class="text-xs text-slate-500 mt-1">Saat ini belum ada kamar yang kosong di properti ini. Silakan hubungi pemilik untuk reservasi mendatang.</p>
                        </div>
                    @else
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                            @foreach($availableRooms as $room)
                                <div class="rounded-2xl border border-slate-200 p-4 sm:p-5 hover:border-indigo-300 hover:shadow-md transition-all flex flex-col justify-between bg-slate-50/50">
                                    <div>
                                        <div class="flex items-center justify-between mb-2">
                                            <span class="text-xs font-extrabold uppercase tracking-wider text-indigo-900">Kamar {{ $room->room_number }}</span>
                                            <span class="inline-flex items-center rounded-full bg-emerald-100 px-2 py-0.5 text-[11px] font-bold text-emerald-800">
                                                Tersedia
                                            </span>
                                        </div>
                                        <h3 class="text-base font-bold text-slate-900">{{ $room->type }}</h3>
                                        <div class="mt-2 text-xl font-extrabold text-[#1E3A8A]">
                                            Rp {{ number_format((float) $room->price, 0, ',', '.') }}
                                            <span class="text-xs font-normal text-slate-500">/ bulan</span>
                                        </div>
                                    </div>

                                    <div class="mt-4 pt-3 border-t border-slate-200/70">
                                        <button type="button" onclick="selectRoom('{{ $room->id }}', '{{ $room->room_number }}', '{{ $room->type }}', {{ $room->price }})" class="w-full rounded-xl bg-[#1E3A8A] hover:bg-[#172554] py-2 text-xs font-bold text-white transition-colors shadow-xs">
                                            Ajukan Sewa Kamar Ini
                                        </button>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @endif
                </div>
            </div>

            {{-- Sidebar: Booking Request Form --}}
            <div class="lg:col-span-1">
                <div class="sticky top-24 rounded-3xl border border-slate-200 bg-white p-6 sm:p-8 shadow-lg shadow-indigo-950/5" id="form-sewa">
                    <div class="flex items-center gap-2 mb-4 pb-3 border-b border-slate-100">
                        <div class="flex h-8 w-8 items-center justify-center rounded-lg bg-indigo-50 text-[#1E3A8A]">
                            <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/></svg>
                        </div>
                        <div>
                            <h2 class="text-base font-bold text-slate-900">Form Pengajuan Sewa</h2>
                            <p class="text-xs text-slate-500">Isi data untuk memesan kamar</p>
                        </div>
                    </div>

                    <form action="{{ route('public.kost.book', $property->slug ?? $property->id) }}" method="POST" class="space-y-4">
                        @csrf

                        {{-- Pilih Kamar --}}
                        <div>
                            <label for="room_id" class="block text-xs font-bold text-slate-700 mb-1">Pilih Kamar <span class="text-rose-500">*</span></label>
                            <select id="room_id" name="room_id" required onchange="onRoomChange(this)" class="w-full rounded-xl border border-slate-200 bg-slate-50 px-3.5 py-2.5 text-xs sm:text-sm text-slate-800 focus:border-indigo-400 focus:bg-white focus:ring-2 focus:ring-indigo-100 focus:outline-none">
                                <option value="">-- Pilih Kamar Kosong --</option>
                                @foreach($availableRooms as $r)
                                    <option value="{{ $r->id }}" data-price="{{ $r->price }}" data-number="{{ $r->room_number }}" data-type="{{ $r->type }}" @selected(old('room_id') == $r->id)>
                                        Kamar {{ $r->room_number }} ({{ $r->type }}) — Rp {{ number_format((float) $r->price, 0, ',', '.') }}/bln
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        {{-- Tanggal Mulai Masuk --}}
                        <div>
                            <label for="start_date" class="block text-xs font-bold text-slate-700 mb-1">Tanggal Mulai Masuk <span class="text-rose-500">*</span></label>
                            <input type="date" id="start_date" name="start_date" min="{{ now()->toDateString() }}" value="{{ old('start_date', now()->toDateString()) }}" required class="w-full rounded-xl border border-slate-200 bg-slate-50 px-3.5 py-2 text-xs sm:text-sm text-slate-800 focus:border-indigo-400 focus:bg-white focus:ring-2 focus:ring-indigo-100 focus:outline-none">
                        </div>

                        {{-- Durasi Sewa --}}
                        <div class="grid grid-cols-2 gap-2">
                            <div>
                                <label for="duration_type" class="block text-xs font-bold text-slate-700 mb-1">Skema</label>
                                <select id="duration_type" name="duration_type" onchange="onDurationTypeChange(this)" class="w-full rounded-xl border border-slate-200 bg-slate-50 px-3 py-2 text-xs text-slate-800 focus:border-indigo-400 focus:bg-white focus:outline-none">
                                    <option value="monthly" @selected(old('duration_type') === 'monthly')>Bulanan</option>
                                    <option value="yearly" @selected(old('duration_type') === 'yearly')>Tahunan (12 Bln)</option>
                                </select>
                            </div>
                            <div>
                                <label for="duration_months" class="block text-xs font-bold text-slate-700 mb-1">Durasi</label>
                                <select id="duration_months" name="duration_months" onchange="calculateTotal()" class="w-full rounded-xl border border-slate-200 bg-slate-50 px-3 py-2 text-xs text-slate-800 focus:border-indigo-400 focus:bg-white focus:outline-none">
                                    <option value="1" @selected(old('duration_months') == 1)>1 Bulan</option>
                                    <option value="3" @selected(old('duration_months') == 3)>3 Bulan</option>
                                    <option value="6" @selected(old('duration_months') == 6)>6 Bulan</option>
                                    <option value="12" @selected(old('duration_months') == 12)>12 Bulan</option>
                                </select>
                            </div>
                        </div>

                        {{-- Data Calon Penyewa --}}
                        <div class="pt-2 border-t border-slate-100 space-y-3">
                            <span class="text-xs font-bold uppercase tracking-wider text-slate-400">Data Calon Penyewa</span>

                            <div>
                                <label for="name" class="block text-xs font-bold text-slate-700 mb-1">Nama Lengkap <span class="text-rose-500">*</span></label>
                                <input type="text" id="name" name="name" value="{{ old('name', auth()->user()?->name) }}" required placeholder="Contoh: Budi Santoso" class="w-full rounded-xl border border-slate-200 bg-slate-50 px-3.5 py-2 text-xs sm:text-sm text-slate-800 focus:border-indigo-400 focus:bg-white focus:outline-none">
                            </div>

                            <div>
                                <label for="email" class="block text-xs font-bold text-slate-700 mb-1">Email Aktif <span class="text-rose-500">*</span></label>
                                <input type="email" id="email" name="email" value="{{ old('email', auth()->user()?->email) }}" required placeholder="nama@email.com" class="w-full rounded-xl border border-slate-200 bg-slate-50 px-3.5 py-2 text-xs sm:text-sm text-slate-800 focus:border-indigo-400 focus:bg-white focus:outline-none">
                            </div>

                            <div>
                                <label for="phone" class="block text-xs font-bold text-slate-700 mb-1">No. WhatsApp / HP <span class="text-rose-500">*</span></label>
                                <input type="text" id="phone" name="phone" value="{{ old('phone', auth()->user()?->phone) }}" required placeholder="081234567890" class="w-full rounded-xl border border-slate-200 bg-slate-50 px-3.5 py-2 text-xs sm:text-sm text-slate-800 focus:border-indigo-400 focus:bg-white focus:outline-none">
                            </div>

                            @guest
                                <div>
                                    <label for="password" class="block text-xs font-bold text-slate-700 mb-1">Kata Sandi Akun (Opsional)</label>
                                    <input type="password" id="password" name="password" placeholder="Minimal 6 karakter" class="w-full rounded-xl border border-slate-200 bg-slate-50 px-3.5 py-2 text-xs sm:text-sm text-slate-800 focus:border-indigo-400 focus:bg-white focus:outline-none">
                                    <span class="text-[10px] text-slate-400">Untuk login ke Dashboard Penyewa Anda nanti. Default: <code>password</code></span>
                                </div>
                            @endguest

                            <div>
                                <label for="notes" class="block text-xs font-bold text-slate-700 mb-1">Catatan Tambahan (Opsional)</label>
                                <textarea id="notes" name="notes" rows="2" placeholder="Pertanyaan atau permintaan fasilitas tambahan..." class="w-full rounded-xl border border-slate-200 bg-slate-50 px-3.5 py-2 text-xs text-slate-800 focus:border-indigo-400 focus:bg-white focus:outline-none">{{ old('notes') }}</textarea>
                            </div>
                        </div>

                        {{-- Total Kalkulasi --}}
                        <div class="rounded-2xl bg-indigo-50/70 border border-indigo-100 p-4">
                            <div class="flex items-center justify-between text-xs text-slate-600 mb-1">
                                <span>Estimasi Total Sewa:</span>
                                <span id="calc-formula" class="font-medium text-slate-500">-</span>
                            </div>
                            <div class="text-xl font-extrabold text-[#1E3A8A]" id="total-display">
                                Rp 0
                            </div>
                        </div>

                        <button type="submit" class="w-full rounded-xl bg-[#1E3A8A] hover:bg-[#172554] py-3 text-sm font-bold text-white shadow-md shadow-indigo-950/20 transition-all flex items-center justify-center gap-2">
                            <span>Ajukan Sewa Sekarang</span>
                            <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"/></svg>
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </main>

    <script>
        function selectRoom(roomId, roomNumber, roomType, price) {
            const select = document.getElementById('room_id');
            select.value = roomId;
            calculateTotal();
            document.getElementById('form-sewa').scrollIntoView({ behavior: 'smooth' });
        }

        function onRoomChange(select) {
            calculateTotal();
        }

        function onDurationTypeChange(select) {
            const durationSelect = document.getElementById('duration_months');
            if (select.value === 'yearly') {
                durationSelect.value = '12';
            } else if (durationSelect.value === '12') {
                durationSelect.value = '1';
            }
            calculateTotal();
        }

        function calculateTotal() {
            const select = document.getElementById('room_id');
            const selectedOption = select.options[select.selectedIndex];
            const durationMonths = parseInt(document.getElementById('duration_months').value) || 1;
            const price = parseFloat(selectedOption?.getAttribute('data-price')) || 0;

            const total = price * durationMonths;
            const formulaText = `${durationMonths} bulan × Rp ${price.toLocaleString('id-ID')}`;

            document.getElementById('calc-formula').innerText = price > 0 ? formulaText : '-';
            document.getElementById('total-display').innerText = 'Rp ' + total.toLocaleString('id-ID');
        }

        document.addEventListener('DOMContentLoaded', () => {
            calculateTotal();
        });
    </script>
</x-app>
