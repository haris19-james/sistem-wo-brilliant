@extends('layouts.admin')

@section('title', 'Notifications')

@section('content')
<div class="max-w-4xl mx-auto">
    <div class="flex items-center justify-between mb-6">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">All Notifications</h1>
            <p class="text-sm text-gray-500 mt-1">{{ $unreadCount }} unread notification{{ $unreadCount === 1 ? '' : 's' }}</p>
        </div>
        @if($unreadCount > 0)
        <form method="POST" action="{{ route('admin.notifications.read-all') }}">
            @csrf
            <button type="submit" class="text-sm font-semibold text-blue-600 hover:text-blue-700 transition">
                Mark all as read
            </button>
        </form>
        @endif
    </div>

    <div class="bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden divide-y divide-gray-50">
        @forelse($notifications as $notification)
        <a href="{{ route('admin.notifications.open', $notification['id']) }}"
           class="flex items-start gap-4 px-6 py-4 transition {{ $notification['is_read'] ? 'bg-white hover:bg-gray-50' : 'bg-blue-50/50 hover:bg-blue-50/70' }}">
            @if(! $notification['is_read'])
            <span class="mt-2 h-2.5 w-2.5 shrink-0 rounded-full bg-blue-500"></span>
            @else
            <span class="mt-2 h-2.5 w-2.5 shrink-0"></span>
            @endif

            <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-lg bg-gray-50 text-gray-600">
                @include('components.partials.notification-icon', ['icon' => $notification['category_icon']])
            </div>

            <div class="flex-1 min-w-0">
                <p class="text-sm text-gray-800 leading-relaxed">
                    {!! $notification['display_message'] !!}
                    <span class="text-gray-400">— {{ $notification['formatted_time'] }}</span>
                </p>
            </div>
        </a>
        @empty
        <div class="px-6 py-16 text-center text-gray-500 text-sm">Belum ada notifikasi.</div>
        @endforelse
    </div>

    @if($notifications->hasPages())
    <div class="mt-6">
        {{ $notifications->links() }}
    </div>
    @endif
</div>
@endsection
