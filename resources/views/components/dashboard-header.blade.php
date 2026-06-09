@props([
    'title' => null,
    'notificationRoute' => route('notifications.index') ?? url('/notifications'),
    'unreadCount' => 0,
])

<header class="bg-white border-b border-gray-100 h-20 px-6 flex items-center gap-6 shrink-0" x-data="headerComponent()">
    {{-- Left: Title & Subtitle --}}
    <div class="flex items-center min-w-0">
        <button @click="sidebarOpen = true" class="lg:hidden text-gray-500 hover:text-bottle mr-4 shrink-0">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h7"/></svg>
        </button>
        <div>
            <h2 class="text-xl font-bold text-gray-900 leading-tight">{{ $title ?? 'Dashboard' }}</h2>
            <p class="text-xs text-gray-500 mt-0.5 hidden md:block">{{ \Carbon\Carbon::now()->locale('id')->translatedFormat('l, j F Y') }}</p>
        </div>
    </div>

    {{-- Right: Search + Notification + User Profile --}}
    <div class="flex items-center gap-3 ml-auto">
        <div class="hidden sm:flex items-center">
            <label for="admin-global-search" class="sr-only">Search</label>
            <div class="relative">
                <svg class="absolute left-3 top-1/2 -translate-y-1/2 h-4 w-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                </svg>
                <input id="admin-global-search"
                       type="search"
                       placeholder="Search..."
                       class="w-56 lg:w-64 rounded-full border border-gray-200 bg-gray-50 py-2 pl-10 pr-4 text-sm text-gray-700 placeholder:text-gray-400 focus:border-bottle focus:bg-white focus:outline-none focus:ring-2 focus:ring-bottle/20 transition" />
            </div>
        </div>

        <x-notification-bell />

        {{-- User Profile Avatar + Dropdown (visible on md+) --}}
        <div class="hidden sm:flex items-center gap-3 pl-4 border-l border-gray-100 relative" x-data="profileMenu()">
            <!-- Avatar (Clickable) -->
            <button @click="toggleMenu()"
                    class="relative inline-flex items-center justify-center w-10 h-10 rounded-full border-2 border-gray-200 hover:border-bottle transition overflow-hidden shrink-0 bg-gray-100"
                    :title="userName"
                    aria-label="Profile menu">
                <img :src="userAvatar" 
                     :alt="userName"
                     class="w-full h-full object-cover"
                     @load="onImageLoad">
                <div x-show="!imageLoaded" class="absolute inset-0 flex items-center justify-center bg-gray-200">
                    <svg class="w-5 h-5 text-gray-400" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M10 9a3 3 0 100-6 3 3 0 000 6zm-7 9a7 7 0 1114 0H3z" clip-rule="evenodd"/>
                    </svg>
                </div>
            </button>

            <!-- Profile Menu Dropdown -->
            <div x-show="menuOpen"
                 @click.away="menuOpen = false"
                 class="absolute right-0 top-full mt-2 w-56 bg-white rounded-lg shadow-xl border border-gray-100 z-50 overflow-hidden"
                 x-transition:enter="transition ease-out duration-100"
                 x-transition:enter-start="opacity-0 scale-95 translate-y-0"
                 x-transition:enter-end="opacity-100 scale-100 translate-y-0">

                <!-- Profile Header -->
                <div class="px-4 py-4 bg-gradient-to-r from-leafSoft to-leafSoft border-b border-gray-100">
                    <div class="flex items-center gap-3">
                        <img :src="userAvatar" 
                             :alt="userName"
                             class="w-12 h-12 rounded-full border-2 border-white object-cover">
                        <div class="flex-1 min-w-0">
                            <p class="text-sm font-bold text-gray-900 truncate" x-text="userName"></p>
                            <p class="text-xs text-gray-600 truncate" x-text="userEmail"></p>
                        </div>
                    </div>
                </div>

                <!-- Menu Items -->
                <div class="py-2">
                    <a href="{{ route('admin.profile.show') }}"
                       class="flex items-center gap-3 px-4 py-2.5 text-gray-700 hover:bg-gray-50 transition"
                       @click="menuOpen = false">
                        <svg class="w-4 h-4 text-gray-500" fill="currentColor" viewBox="0 0 20 20">
                            <path d="M10.5 1.5H4a2.5 2.5 0 00-2.5 2.5v10A2.5 2.5 0 004 16.5h12a2.5 2.5 0 002.5-2.5V9.5"></path>
                            <circle cx="15" cy="4" r="3"></circle>
                        </svg>
                        <span class="text-sm font-medium">Profil Akun</span>
                    </a>

                    <a href="#"
                       class="flex items-center gap-3 px-4 py-2.5 text-gray-700 hover:bg-gray-50 transition"
                       @click.prevent="menuOpen = false">
                        <svg class="w-4 h-4 text-gray-500" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M11.49 3.17c-.38-1.56-2.6-1.56-2.98 0a1.532 1.532 0 01-2.286.948c-1.372-.836-2.942.734-2.106 2.106.54.886.061 2.042-.947 2.287-1.561.379-1.561 2.6 0 2.978a1.532 1.532 0 01.947 2.287c-.836 1.372.734 2.942 2.106 2.106a1.532 1.532 0 012.287.947c.379 1.561 2.6 1.561 2.978 0a1.533 1.533 0 012.287-.947c1.372.836 2.942-.734 2.106-2.106a1.533 1.533 0 01.947-2.287c1.561-.379 1.561-2.6 0-2.978a1.532 1.532 0 01-.947-2.287c.836-1.372-.734-2.942-2.106-2.106a1.532 1.532 0 01-2.287-.947zM10 13a3 3 0 100-6 3 3 0 000 6z" clip-rule="evenodd"/>
                        </svg>
                        <span class="text-sm font-medium">Pengaturan</span>
                    </a>
                </div>

                <!-- Logout -->
                <div class="border-t border-gray-100 py-2">
                    <form method="POST" action="{{ route('logout') }}" class="m-0">
                        @csrf
                        <button type="submit"
                                class="w-full flex items-center gap-3 px-4 py-2.5 text-red-600 hover:bg-red-50 transition text-left"
                                @click="menuOpen = false">
                            <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M3 3a1 1 0 00-1 1v12a1 1 0 102 0V4a1 1 0 00-1-1zm10.293 9.293a1 1 0 001.414 1.414l3-3a1 1 0 000-1.414l-3-3a1 1 0 10-1.414 1.414L14.586 9H7a1 1 0 100 2h7.586l-1.293 1.293z" clip-rule="evenodd"/>
                            </svg>
                            <span class="text-sm font-medium">Keluar</span>
                        </button>
                    </form>
                </div>
            </div>

            {{ $slot }}
        </div>
    </div>
</header>

<script>
function headerComponent() {
    return {
        init() {
            // Listen for profile updates
            window.addEventListener('profile-updated', (e) => {
                // Refresh profile data
                this.refreshProfileData();
            });
        },

        refreshProfileData() {
            fetch('{{ route("admin.profile.current") }}')
                .then(response => response.json())
                .then(data => {
                    window.dispatchEvent(new CustomEvent('profile-data-changed', {
                        detail: data.user
                    }));
                })
                .catch(error => console.error('Error refreshing profile:', error));
        }
    };
}

function profileMenu() {
    return {
        menuOpen: false,
        userName: '{{ auth()->user()->name }}',
        userEmail: '{{ auth()->user()->email }}',
        userAvatar: '{{ auth()->user()->getAvatarUrlAttribute() }}',
        imageLoaded: false,

        init() {
            // Listen for profile updates
            window.addEventListener('profile-data-changed', (e) => {
                this.userName = e.detail.name;
                this.userEmail = e.detail.email;
                this.userAvatar = e.detail.avatar_url;
                this.imageLoaded = false;
            });
        },

        toggleMenu() {
            this.menuOpen = !this.menuOpen;
        },

        onImageLoad() {
            this.imageLoaded = true;
        }
    };
}
</script>


