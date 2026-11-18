<x-app>
    <div class="min-h-full flex flex-col justify-center py-12 sm:px-6 lg:px-8 bg-slate-50">
        {{-- Brand Header --}}
        <div class="sm:mx-auto sm:w-full sm:max-w-md text-center">
            <a href="{{ route('landing') }}" class="inline-flex items-center gap-2.5 group">
                <div class="flex h-12 w-12 items-center justify-center rounded-2xl bg-[#1E3A8A] text-white shadow-lg shadow-indigo-950/20 group-hover:scale-105 transition-transform">
                    <svg class="h-7 w-7" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                    </svg>
                </div>
                <div class="text-left">
                    <span class="text-2xl font-black tracking-tight text-[#1E3A8A]">Kostly<span class="text-emerald-500">.</span></span>
                    <span class="block -mt-1 text-[10px] font-bold tracking-wider text-slate-500 uppercase">Manajemen Kost</span>
                </div>
            </a>
            <h2 class="mt-6 text-2xl font-extrabold tracking-tight text-slate-900 sm:text-3xl">
                Buat Akun Pemilik Kost
            </h2>
            <p class="mt-2 text-sm text-slate-600">
                Mulai uji coba gratis 14 hari seluruh fitur Pro tanpa ikatan.
            </p>
        </div>

        <div class="mt-8 sm:mx-auto sm:w-full sm:max-w-md px-4 sm:px-0">
            {{-- Benefit Badge --}}
            <div class="mb-5 rounded-2xl border border-emerald-200 bg-emerald-50/70 p-3.5 text-xs text-emerald-900 shadow-xs flex items-center gap-2.5">
                <div class="flex h-8 w-8 shrink-0 items-center justify-center rounded-xl bg-emerald-500 text-white font-bold">
                    ✓
                </div>
                <div>
                    <span class="font-bold">Gratis 14 Hari Akses Lengkap:</span>
                    <p class="text-[11px] text-emerald-800">Otomasi WhatsApp, QRIS, dan portal mandiri langsung aktif begitu mendaftar.</p>
                </div>
            </div>

            {{-- Main Form Card --}}
            <div class="rounded-2xl border border-slate-200/90 bg-white p-6 sm:p-8 shadow-xl shadow-slate-900/5">
                <form action="{{ route('register') }}" method="POST" class="space-y-4">
                    @csrf

                    {{-- Nama Lengkap --}}
                    <div>
                        <label for="name" class="block text-xs font-semibold uppercase tracking-wider text-slate-700">
                            Nama Lengkap
                        </label>
                        <div class="mt-1.5">
                            <input id="name" name="name" type="text" autocomplete="name" required value="{{ old('name') }}"
                                class="block w-full rounded-xl border border-slate-300 px-3.5 py-2.5 text-sm text-slate-900 placeholder-slate-400 focus:border-[#1E3A8A] focus:outline-hidden focus:ring-2 focus:ring-[#1E3A8A]/20 transition-all @error('name') border-rose-500 focus:border-rose-500 focus:ring-rose-500/20 @enderror"
                                placeholder="Misal: Budi Santoso">
                        </div>
                        @error('name')
                            <p class="mt-1.5 text-xs text-rose-600 font-medium">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- Nomor WhatsApp --}}
                    <div>
                        <label for="phone" class="block text-xs font-semibold uppercase tracking-wider text-slate-700">
                            Nomor WhatsApp / HP
                        </label>
                        <div class="mt-1.5">
                            <input id="phone" name="phone" type="text" autocomplete="tel" required value="{{ old('phone') }}"
                                class="block w-full rounded-xl border border-slate-300 px-3.5 py-2.5 text-sm text-slate-900 placeholder-slate-400 focus:border-[#1E3A8A] focus:outline-hidden focus:ring-2 focus:ring-[#1E3A8A]/20 transition-all @error('phone') border-rose-500 focus:border-rose-500 focus:ring-rose-500/20 @enderror"
                                placeholder="081234567890">
                        </div>
                        <p class="mt-1 text-[11px] text-slate-400">Digunakan untuk notifikasi sistem dan laporan berkala.</p>
                        @error('phone')
                            <p class="mt-1.5 text-xs text-rose-600 font-medium">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- Email --}}
                    <div>
                        <label for="email" class="block text-xs font-semibold uppercase tracking-wider text-slate-700">
                            Alamat Email
                        </label>
                        <div class="mt-1.5">
                            <input id="email" name="email" type="email" autocomplete="email" required value="{{ old('email') }}"
                                class="block w-full rounded-xl border border-slate-300 px-3.5 py-2.5 text-sm text-slate-900 placeholder-slate-400 focus:border-[#1E3A8A] focus:outline-hidden focus:ring-2 focus:ring-[#1E3A8A]/20 transition-all @error('email') border-rose-500 focus:border-rose-500 focus:ring-rose-500/20 @enderror"
                                placeholder="nama@email.com">
                        </div>
                        @error('email')
                            <p class="mt-1.5 text-xs text-rose-600 font-medium">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- Password --}}
                    <div>
                        <label for="password" class="block text-xs font-semibold uppercase tracking-wider text-slate-700">
                            Kata Sandi
                        </label>
                        <div class="mt-1.5 relative">
                            <input id="password" name="password" type="password" autocomplete="new-password" required
                                class="block w-full rounded-xl border border-slate-300 px-3.5 py-2.5 text-sm text-slate-900 placeholder-slate-400 focus:border-[#1E3A8A] focus:outline-hidden focus:ring-2 focus:ring-[#1E3A8A]/20 transition-all @error('password') border-rose-500 focus:border-rose-500 focus:ring-rose-500/20 @enderror"
                                placeholder="Minimal 8 karakter">
                        </div>
                        @error('password')
                            <p class="mt-1.5 text-xs text-rose-600 font-medium">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- Password Confirmation --}}
                    <div>
                        <label for="password_confirmation" class="block text-xs font-semibold uppercase tracking-wider text-slate-700">
                            Ulangi Kata Sandi
                        </label>
                        <div class="mt-1.5">
                            <input id="password_confirmation" name="password_confirmation" type="password" autocomplete="new-password" required
                                class="block w-full rounded-xl border border-slate-300 px-3.5 py-2.5 text-sm text-slate-900 placeholder-slate-400 focus:border-[#1E3A8A] focus:outline-hidden focus:ring-2 focus:ring-[#1E3A8A]/20 transition-all"
                                placeholder="Ketik ulang kata sandi">
                        </div>
                    </div>

                    {{-- Submit Button --}}
                    <div class="pt-2">
                        <button type="submit" class="w-full inline-flex items-center justify-center gap-2 rounded-xl bg-[#1E3A8A] py-3.5 text-sm font-bold text-white shadow-md shadow-indigo-950/20 hover:bg-[#172554] focus:outline-hidden focus:ring-2 focus:ring-[#1E3A8A] focus:ring-offset-2 transition-all">
                            <span>Daftar & Mulai Kelola Kost</span>
                            <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"/></svg>
                        </button>
                    </div>
                </form>

                {{-- Login Link --}}
                <div class="mt-6 border-t border-slate-100 pt-5 text-center">
                    <p class="text-xs text-slate-600">
                        Sudah memiliki akun Kostly?
                        <a href="{{ route('login') }}" class="font-bold text-[#1E3A8A] hover:underline">
                            Masuk di sini
                        </a>
                    </p>
                </div>
            </div>

            {{-- Back to Home --}}
            <div class="mt-6 text-center">
                <a href="{{ route('landing') }}" class="inline-flex items-center gap-1.5 text-xs font-semibold text-slate-500 hover:text-slate-800 transition-colors">
                    <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
                    <span>Kembali ke Halaman Utama</span>
                </a>
            </div>
        </div>
    </div>
</x-app>

