@php
    $unreadCount = auth()->user()->unreadNotifications()->count();
    $latestNotifications = auth()->user()->notifications()->latest()->take(5)->get();
@endphp

<div class="relative" id="notification-bell-dropdown">
    {{-- Bell Button --}}
    <button type="button"
        onclick="toggleNotificationDropdown()"
        id="notification-bell-btn"
        class="relative p-2 rounded-xl border border-slate-200 bg-white hover:bg-slate-50 text-slate-600 transition-colors shadow-xs flex items-center justify-center"
        title="Notifikasi">
        <svg class="h-5 w-5 text-slate-600" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8">
            <path stroke-linecap="round" stroke-linejoin="round" d="M14.857 17.082a23.848 23.848 0 005.454-1.31A8.967 8.967 0 0118 9.75v-.7V9A6 6 0 006 9v.75a8.967 8.967 0 01-2.312 6.022c1.733.64 3.56 1.085 5.455 1.31m5.714 0a24.255 24.255 0 01-5.714 0m5.714 0a3 3 0 11-5.714 0" />
        </svg>

        @if ($unreadCount > 0)
            <span class="absolute -top-1 -right-1 flex h-5 min-w-5 items-center justify-center rounded-full bg-rose-600 px-1 text-[10px] font-black text-white ring-2 ring-white">
                {{ $unreadCount > 99 ? '99+' : $unreadCount }}
            </span>
        @endif
    </button>

    {{-- Dropdown Panel --}}
    <div id="notification-dropdown-panel"
        class="hidden absolute right-0 mt-2 w-80 sm:w-96 rounded-2xl bg-white border border-slate-200 shadow-2xl z-50 overflow-hidden animate-in fade-in slide-in-from-top-2 duration-150">
        {{-- Header --}}
        <div class="px-4 py-3 border-b border-slate-100 flex items-center justify-between bg-slate-50/70">
            <div class="flex items-center gap-2">
                <span class="text-xs font-bold text-slate-800">Notifikasi</span>
                @if ($unreadCount > 0)
                    <span class="rounded-full bg-rose-100 px-2 py-0.5 text-[10px] font-bold text-rose-700">
                        {{ $unreadCount }} baru
                    </span>
                @endif
            </div>

            @if ($unreadCount > 0)
                <form action="{{ route('notifications.markAllRead') }}" method="POST">
                    @csrf
                    @method('PATCH')
                    <button type="submit" class="text-[11px] font-bold text-[#1E3A8A] hover:underline">
                        Tandai Semua Dibaca
                    </button>
                </form>
            @endif
        </div>

        {{-- List --}}
        <div class="max-h-80 overflow-y-auto divide-y divide-slate-100">
            @forelse ($latestNotifications as $notification)
                @php
                    $data = $notification->data;
                    $isUnread = $notification->unread();
                    $type = $data['type'] ?? 'info';
                @endphp
                <div class="p-3.5 hover:bg-slate-50 transition-colors {{ $isUnread ? 'bg-indigo-50/30' : '' }}">
                    <div class="flex items-start justify-between gap-2">
                        <div class="flex items-start gap-2.5 min-w-0">
                            {{-- Type Icon --}}
                            <div class="mt-0.5 flex h-7 w-7 shrink-0 items-center justify-center rounded-lg {{ $type === 'overdue' ? 'bg-rose-100 text-rose-600' : 'bg-blue-100 text-blue-600' }}">
                                @if ($type === 'overdue')
                                    <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
                                @else
                                    <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/></svg>
                                @endif
                            </div>

                            <div class="min-w-0">
                                <h4 class="text-xs font-bold text-slate-800 {{ $isUnread ? 'text-[#1E3A8A]' : '' }}">
                                    {{ $data['title'] ?? 'Notifikasi Tagihan' }}
                                </h4>
                                <p class="text-[11px] text-slate-600 mt-0.5 line-clamp-2">
                                    {{ $data['message'] ?? '-' }}
                                </p>
                                <span class="text-[10px] text-slate-400 mt-1 block">
                                    {{ $notification->created_at->diffForHumans() }}
                                </span>
                            </div>
                        </div>

                        {{-- Action Read / View --}}
                        <div class="shrink-0 flex flex-col items-end gap-1">
                            @if ($isUnread)
                                <span class="h-2 w-2 rounded-full bg-blue-600"></span>
                            @endif

                            <form action="{{ route('notifications.markAsRead', $notification->id) }}" method="POST">
                                @csrf
                                @method('PATCH')
                                <button type="submit" class="text-[10px] font-bold text-[#1E3A8A] hover:underline whitespace-nowrap">
                                    {{ $isUnread ? 'Buka' : 'Lihat' }}
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            @empty
                <div class="py-8 text-center text-xs text-slate-400">
                    <svg class="mx-auto h-8 w-8 text-slate-300 mb-2" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/></svg>
                    Belum ada notifikasi baru
                </div>
            @endforelse
        </div>
    </div>
</div>

<script>
    function toggleNotificationDropdown() {
        const panel = document.getElementById('notification-dropdown-panel');
        if (panel) {
            panel.classList.toggle('hidden');
        }
    }

    document.addEventListener('click', function(e) {
        const wrapper = document.getElementById('notification-bell-dropdown');
        const panel = document.getElementById('notification-dropdown-panel');
        if (wrapper && panel && !wrapper.contains(e.target)) {
            panel.classList.add('hidden');
        }
    });
</script>
