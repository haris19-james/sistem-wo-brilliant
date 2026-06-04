@props([
    'title' => null,
    'notificationRoute' => null,
    'unreadCount' => 0,
])

<div class="w-full bg-white dark:bg-gray-800 border-b dark:border-gray-700">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex items-center justify-between h-16">
            <div class="flex items-center">
                @if($title)
                    <h1 class="text-lg font-semibold text-gray-900 dark:text-gray-100 mr-4">{{ $title }}</h1>
                @endif

                {{-- Tanggal: tersembunyi di layar kecil, tampil sm+ --}}
                <div class="text-sm text-gray-600 dark:text-gray-300 hidden sm:inline-block" id="dashboard-date">
                    {{-- JS akan mengisi teks ini menggunakan toLocaleDateString('id-ID', ...) --}}
                </div>
            </div>

            <div class="flex items-center space-x-4">
                {{-- Notification wrapper: notification-poller akan auto-init jika atribut ini ada --}}
                <div class="relative" data-notification-auto-poll data-notification-route="{{ $notificationRoute ?? url('/notifications') }}" aria-live="polite">
                    <a href="{{ $notificationRoute ?? url('/notifications') }}" class="relative inline-flex items-center p-2 rounded-md text-gray-600 hover:text-gray-900 dark:text-gray-300 dark:hover:text-white focus:outline-none focus:ring-2 focus:ring-indigo-500" aria-label="Notifikasi">
                        {{-- Bell Icon --}}
                        <svg class="h-6 w-6" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" aria-hidden="true">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6 6 0 10-12 0v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9" />
                        </svg>

                        {{-- Badge: updaten oleh notification-poller.js dengan id 'notification-badge' --}}
                        <span id="notification-badge" class="absolute -top-1 -right-1 inline-flex items-center justify-center px-2 py-0.5 rounded-full text-xs font-medium bg-red-600 text-white transform translate-x-1/2 -translate-y-1/2" style="display: {{ ((int) $unreadCount) > 0 ? 'inline-flex' : 'none' }};">{{ (int) $unreadCount }}</span>
                    </a>
                </div>

                {{-- Optional: user avatar or settings slot --}}
                <div class="flex items-center">
                    {{ $slot }}
                </div>
            </div>
        </div>
    </div>

    {{-- Inline script: set tanggal sesuai permintaan (pakai toLocaleDateString('id-ID', ...)) --}}
    <script>
        (function(){
            try {
                const el = document.getElementById('dashboard-date');
                if (!el) return;
                const today = new Date();
                const formatted = today.toLocaleDateString('id-ID', { weekday: 'long', day: 'numeric', month: 'long', year: 'numeric' });
                // Kapitalisasi hari pertama (opsional) — tetap sesuai locale
                el.textContent = formatted;
            } catch (e) {
                // fallback: server-side rendering using PHP date if JS error
                const phpFallback = "{{ \Carbon\Carbon::now()->locale('id')->translatedFormat('l, j F Y') }}";
                const el2 = document.getElementById('dashboard-date');
                if (el2) el2.textContent = phpFallback;
            }
        })();
    </script>
</div>
