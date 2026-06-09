@props([
    'title' => null,
    'notificationRoute' => null,
    'unreadCount' => 0,
])

<div class="w-full bg-white border-b border-gray-100">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex items-center justify-between h-16">
            <div class="flex items-center gap-4 min-w-0">
                @if($title)
                    <h1 class="text-lg font-semibold text-gray-900 truncate">{{ $title }}</h1>
                @endif
                <div class="text-sm text-gray-500 hidden sm:inline-block">
                    {{ \Carbon\Carbon::now()->locale('id')->translatedFormat('l, j F Y') }}
                </div>
            </div>

            <div class="flex items-center gap-3">
                <x-notification-bell />
                <div class="flex items-center">
                    {{ $slot }}
                </div>
            </div>
        </div>
    </div>
</div>
