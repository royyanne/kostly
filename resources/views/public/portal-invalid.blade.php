<x-app>
    <x-slot:title>
        {{ $isInactive ? 'Akses Portal Nonaktif' : 'Tautan Portal Tidak Ditemukan' }} - Kostly
    </x-slot:title>

    <div class="min-h-screen bg-slate-50 flex items-center justify-center py-12 px-4 sm:px-6 lg:px-8">
        <div class="max-w-md w-full bg-white border border-slate-200/90 rounded-3xl p-8 sm:p-10 shadow-xl shadow-slate-900/5 text-center">
            {{-- Icon Indicator --}}
            <div class="mx-auto flex h-16 w-16 items-center justify-center rounded-2xl {{ $isInactive ? 'bg-amber-50 text-amber-600' : 'bg-rose-50 text-rose-600' }} mb-5 shadow-inner">
                @if ($isInactive)
                    <svg class="h-8 w-8" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />
                    </svg>
                @else
                    <svg class="h-8 w-8" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                    </svg>
                @endif
            </div>

            {{-- Title & Friendly Message --}}
            @if ($isInactive)
                <h1 class="text-xl sm:text-2xl font-black text-slate-900 tracking-tight">Status Sewa Sudah Tidak Aktif</h1>
                <p class="mt-2 text-xs sm:text-sm text-slate-500 leading-relaxed">
                    Halo <strong>{{ $tenant->name ?? 'Penghuni' }}</strong>, masa sewa Anda di properti ini telah selesai atau Anda telah melakukan proses checkout. Akses portal mandiri dinonaktifkan.
                </p>
                <div class="mt-4 p-3 bg-amber-50/60 rounded-xl border border-amber-200/50 text-[11px] text-amber-800">
                    Terima kasih telah menjadi bagian dari keluarga penghuni kost kami! Semoga sukses di tempat tinggal yang baru. ✨
                </div>
            @else
                <h1 class="text-xl sm:text-2xl font-black text-slate-900 tracking-tight">Tautan Portal Tidak Valid</h1>
                <p class="mt-2 text-xs sm:text-sm text-slate-500 leading-relaxed">
                    Tautan atau kode akses portal penghuni tidak ditemukan di dalam sistem. Pastikan Anda mengklik tautan lengkap yang dikirimkan oleh pemilik kost melalui WhatsApp.
                </p>
            @endif

            {{-- Actions --}}
            <div class="mt-8 space-y-3">
                @if (isset($tenant) && $tenant?->property?->user?->phone)
                    <a href="https://wa.me/{{ preg_replace('/[^0-9]/', '', (str_starts_with($tenant->property->user->phone, '0') ? '62' . substr($tenant->property->user->phone, 1) : $tenant->property->user->phone)) }}"
                        target="_blank"
                        class="w-full inline-flex items-center justify-center gap-2 rounded-2xl bg-emerald-600 px-5 py-3 text-xs sm:text-sm font-bold text-white shadow-md shadow-emerald-950/20 hover:bg-emerald-700 transition-colors">
                        <svg class="h-4 w-4" fill="currentColor" viewBox="0 0 24 24"><path d="M12.04 2C6.58 2 2.13 6.45 2.13 11.91C2.13 13.66 2.59 15.36 3.45 16.86L2.05 22L7.3 20.62C8.75 21.41 10.38 21.83 12.04 21.83C17.5 21.83 21.95 17.38 21.95 11.92C21.95 9.27 20.92 6.78 19.05 4.91C17.18 3.03 14.69 2 12.04 2M12.05 3.67C14.25 3.67 16.31 4.53 17.87 6.09C19.42 7.65 20.28 9.72 20.28 11.92C20.28 16.46 16.58 20.15 12.04 20.15C10.56 20.15 9.11 19.76 7.85 19.02L7.55 18.84L4.42 19.66L5.26 16.61L5.06 16.29C4.24 14.99 3.81 13.47 3.81 11.91C3.81 7.37 7.5 3.67 12.05 3.67Z"/></svg>
                        <span>Hubungi Pemilik Kost via WhatsApp</span>
                    </a>
                @endif

                <a href="{{ route('landing') }}"
                    class="w-full inline-flex items-center justify-center gap-2 rounded-2xl border border-slate-200 bg-slate-50 px-5 py-3 text-xs sm:text-sm font-bold text-slate-700 hover:bg-slate-100 transition-colors">
                    <span>Kembali ke Halaman Utama</span>
                </a>
            </div>
        </div>
    </div>
</x-app>
