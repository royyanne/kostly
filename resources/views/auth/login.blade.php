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
                Masuk ke Akun Pemilik
            </h2>
            <p class="mt-2 text-sm text-slate-600">
                Kelola properti, kamar, dan tagihan kost Anda dalam satu pintu.
            </p>
        </div>

        <div class="mt-8 sm:mx-auto sm:w-full sm:max-w-md px-4 sm:px-0">
            {{-- Demo Credential Card --}}
            <div class="mb-5 rounded-2xl border border-indigo-100 bg-indigo-50/70 p-4 text-xs text-indigo-900 shadow-xs">
                <div class="flex items-center justify-between mb-1.5">
                    <span class="font-bold inline-flex items-center gap-1.5 text-[#1E3A8A]">
                        <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                        Akun Demo Pemilik Kost (Seeded):
                    </span>
                    <button type="button" onclick="fillDemoCredentials()" class="rounded-md bg-[#1E3A8A] px-2.5 py-1 text-[11px] font-semibold text-white hover:bg-[#172554] transition-colors">
                        Isi Otomatis
                    </button>
                </div>
                <div class="font-mono text-[11px] text-slate-700 space-y-0.5">
                    <div>Email: <span class="font-semibold text-slate-900">owner@kostly.id</span></div>
                    <div>Password: <span class="font-semibold text-slate-900">password</span></div>
                </div>
            </div>

            {{-- Main Form Card --}}
            <div class="rounded-2xl border border-slate-200/90 bg-white p-6 sm:p-8 shadow-xl shadow-slate-900/5">
                <form action="{{ route('login') }}" method="POST" class="space-y-5">
                    @csrf

                    {{-- Email Field --}}
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

                    {{-- Password Field --}}
                    <div>
                        <div class="flex items-center justify-between">
                            <label for="password" class="block text-xs font-semibold uppercase tracking-wider text-slate-700">
                                Kata Sandi
                            </label>
                            <span class="text-xs text-slate-400">Min. 8 karakter</span>
                        </div>
                        <div class="mt-1.5 relative">
                            <input id="password" name="password" type="password" autocomplete="current-password" required
                                class="block w-full rounded-xl border border-slate-300 px-3.5 py-2.5 text-sm text-slate-900 placeholder-slate-400 focus:border-[#1E3A8A] focus:outline-hidden focus:ring-2 focus:ring-[#1E3A8A]/20 transition-all @error('password') border-rose-500 focus:border-rose-500 focus:ring-rose-500/20 @enderror"
                                placeholder="••••••••">
                            <button type="button" onclick="togglePasswordVisibility('password', this)" class="absolute inset-y-0 right-0 flex items-center pr-3 text-slate-400 hover:text-slate-600">
                                <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                            </button>
                        </div>
                        @error('password')
                            <p class="mt-1.5 text-xs text-rose-600 font-medium">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- Remember Me --}}
                    <div class="flex items-center justify-between">
                        <label class="flex items-center gap-2 cursor-pointer">
                            <input type="checkbox" name="remember" class="h-4 w-4 rounded border-slate-300 text-[#1E3A8A] focus:ring-[#1E3A8A]">
                            <span class="text-xs text-slate-600">Ingat sesi saya</span>
                        </label>
                    </div>

                    {{-- Submit Button --}}
                    <div>
                        <button type="submit" class="w-full inline-flex items-center justify-center gap-2 rounded-xl bg-[#1E3A8A] py-3 text-sm font-bold text-white shadow-md shadow-indigo-950/20 hover:bg-[#172554] focus:outline-hidden focus:ring-2 focus:ring-[#1E3A8A] focus:ring-offset-2 transition-all">
                            <span>Masuk ke Dashboard</span>
                            <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"/></svg>
                        </button>
                    </div>
                </form>

                {{-- Register Link --}}
                <div class="mt-6 border-t border-slate-100 pt-5 text-center">
                    <p class="text-xs text-slate-600">
                        Belum punya akun juragan kost?
                        <a href="{{ route('register') }}" class="font-bold text-[#1E3A8A] hover:underline">
                            Daftar gratis di sini
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

    <script>
        function fillDemoCredentials() {
            document.getElementById('email').value = 'owner@kostly.id';
            document.getElementById('password').value = 'password';
        }

        function togglePasswordVisibility(fieldId, btn) {
            const input = document.getElementById(fieldId);
            if (input.type === 'password') {
                input.type = 'text';
            } else {
                input.type = 'password';
            }
        }
    </script>
</x-app>

