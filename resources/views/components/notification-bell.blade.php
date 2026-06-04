@props(['align' => 'right'])

@php
    $unread = $bellUnreadCount ?? 0;
    $items = $bellNotifications ?? collect();
@endphp

<div class="relative" x-data="notificationBell()" @click.outside="open = false">
    <button type="button"
        @click="open = !open; if(open) refreshList()"
        class="relative p-2 bg-white border border-gray-200 rounded-lg hover:bg-gray-50 transition"
        aria-label="Notifikasi">
        <svg class="w-5 h-5 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/>
        </svg>
        <span x-show="unreadCount > 0" x-cloak
            class="absolute -top-1 -right-1 inline-flex items-center justify-center min-w-[1.25rem] h-5 px-1 text-[10px] font-bold text-white bg-green-600 border-2 border-white rounded-full"
            x-text="unreadCount > 99 ? '99+' : unreadCount"></span>
    </button>

    <div x-show="open" x-cloak x-transition
        class="absolute z-50 mt-2 w-80 sm:w-96 max-h-[28rem] overflow-hidden bg-white rounded-xl border border-gray-200 shadow-xl {{ $align === 'left' ? 'left-0' : 'right-0' }}">
        <div class="flex items-center justify-between px-4 py-3 border-b border-gray-100 bg-green-50/50">
            <h3 class="text-sm font-bold text-gray-900">Notifikasi</h3>
            <button type="button" @click="markAllRead()" class="text-xs font-semibold text-green-600 hover:text-green-700">
                Tandai Semua Sudah Dibaca
            </button>
        </div>
        <div class="overflow-y-auto max-h-72 divide-y divide-gray-50" id="notificationDropdownList">
            @forelse($items as $n)
            <a href="{{ $n->link_redirect ?: '#' }}"
                @click.prevent="openItem({{ $n->id }}, @js($n->link_redirect))"
                class="block px-4 py-3 hover:bg-green-50/50 transition {{ $n->is_read ? 'opacity-75' : 'bg-green-50/30' }}">
                <div class="flex gap-2">
                    @if(!$n->is_read)
                    <span class="w-2 h-2 mt-1.5 rounded-full bg-green-600 shrink-0"></span>
                    @else
                    <span class="w-2 shrink-0"></span>
                    @endif
                    <div class="min-w-0 flex-1">
                        <p class="text-sm text-gray-900 leading-snug">{{ $n->message }}</p>
                        <p class="text-[10px] text-gray-400 mt-1">{{ $n->created_at->diffForHumans() }}
                            @if($n->priority === 'urgent') · <span class="text-red-600 font-semibold">Penting</span>@endif
                        </p>
                    </div>
                </div>
            </a>
            @empty
            <p class="px-4 py-8 text-sm text-gray-500 text-center">Belum ada notifikasi.</p>
            @endforelse
        </div>
    </div>
</div>

@once
@push('scripts')
<script>
function notificationBell() {
    return {
        open: false,
        unreadCount: {{ (int) ($bellUnreadCount ?? 0) }},
        async refreshList() {
            try {
                const r = await fetch('{{ route('notifications.index') }}', { headers: { Accept: 'application/json' } });
                const data = await r.json();
                this.unreadCount = data.unread_count ?? 0;
            } catch (e) { /* ignore */ }
        },
        async openItem(id, link) {
            try {
                await fetch(`{{ url('/notifications') }}/${id}/read`, {
                    method: 'PATCH',
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content,
                        Accept: 'application/json',
                    },
                });
                this.unreadCount = Math.max(0, this.unreadCount - 1);
            } catch (e) { /* ignore */ }
            if (link) window.location.href = link;
        },
        async markAllRead() {
            try {
                const r = await fetch('{{ route('notifications.read-all') }}', {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content,
                        Accept: 'application/json',
                    },
                });
                const data = await r.json();
                this.unreadCount = data.unread_count ?? 0;
                window.location.reload();
            } catch (e) { alert('Gagal memperbarui notifikasi'); }
        },
    };
}
</script>
@endpush
@endonce
