<!-- Notification Dropdown Component -->
<div class="relative shrink-0" id="notification-wrapper" x-data="notificationDropdown()">
    <!-- Notification Bell Button -->
    <button 
        @click="toggleDropdown()"
        class="relative inline-flex items-center justify-center w-10 h-10 rounded-md text-gray-600 hover:text-bottle hover:bg-gray-100 focus:outline-none transition"
        id="btn-notification"
        aria-label="Notifikasi"
        title="Notifikasi">
        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/>
        </svg>
        <!-- Notification Badge -->
        <span class="absolute -top-0.5 -right-0.5 flex items-center justify-center w-5 h-5 bg-red-500 rounded-full ring-2 ring-white"
              x-show="unreadCount > 0"
              style="display: none;">
            <span class="text-[10px] font-bold text-white" x-text="unreadCount > 99 ? '99+' : unreadCount"></span>
        </span>
    </button>

    <!-- Dropdown Menu -->
    <div 
        x-show="isOpen" 
        @click.away="closeDropdown()"
        style="display: none;"
        x-transition:enter="transition ease-out duration-100"
        x-transition:enter-start="opacity-0 scale-95 translate-y-2"
        x-transition:enter-end="opacity-100 scale-100 translate-y-0"
        x-transition:leave="transition ease-in duration-75"
        x-transition:leave-start="opacity-100 scale-100 translate-y-0"
        x-transition:leave-end="opacity-0 scale-95 translate-y-2"
        class="absolute right-0 mt-2 w-96 bg-white rounded-2xl shadow-2xl border border-gray-100 z-50 overflow-hidden">
        
        <!-- Header -->
        <div class="bg-gradient-to-r from-leafSoft to-leafSoft px-6 py-4 border-b border-gray-100 flex items-center justify-between">
            <h3 class="text-sm font-bold text-gray-900">Notifikasi</h3>
            <button 
                @click="markAllAsRead()"
                x-show="unreadCount > 0"
                class="text-xs font-medium text-bottle hover:text-bottleHover transition">
                Tandai semua telah dibaca
            </button>
        </div>

        <!-- Loading State -->
        <div x-show="isLoading" class="px-6 py-8 text-center">
            <div class="inline-block">
                <svg class="animate-spin h-6 w-6 text-bottle" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                </svg>
            </div>
            <p class="text-gray-500 text-sm mt-2">Memuat notifikasi...</p>
        </div>

        <!-- Empty State -->
        <div x-show="!isLoading && notifications.length === 0" class="px-6 py-8 text-center">
            <svg class="mx-auto h-12 w-12 text-gray-300 mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4"/>
            </svg>
            <p class="text-gray-500 text-sm">Tidak ada notifikasi</p>
        </div>

        <!-- Notifications List -->
        <div x-show="!isLoading && notifications.length > 0" class="max-h-96 overflow-y-auto divide-y divide-gray-100">
            <template x-for="notification in notifications" :key="notification.id">
                <div 
                    @click="handleNotificationClick(notification)"
                    :class="{
                        'bg-leafSoft': !notification.is_read,
                        'bg-white': notification.is_read,
                        'border-l-4 border-l-red-500': notification.priority === 'urgent'
                    }"
                    class="px-6 py-4 hover:bg-gray-50 cursor-pointer transition group">
                    
                    <div class="flex items-start gap-3">
                        <!-- Icon -->
                        <div :class="{
                            'bg-red-100 text-red-600': notification.priority === 'urgent',
                            'bg-blue-100 text-bottle': notification.priority !== 'urgent'
                        }" class="flex-shrink-0 p-2 rounded-lg mt-0.5">
                            <template x-if="notification.priority === 'urgent'">
                                <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/>
                                </svg>
                            </template>
                            <template x-if="notification.priority !== 'urgent'">
                                <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                                    <path d="M10 12a2 2 0 100-4 2 2 0 000 4z"/>
                                    <path fill-rule="evenodd" d="M.458 10C1.732 5.943 5.522 3 10 3s8.268 2.943 9.542 7c-1.274 4.057-5.064 7-9.542 7S1.732 14.057.458 10zM14 10a4 4 0 11-8 0 4 4 0 018 0z" clip-rule="evenodd"/>
                                </svg>
                            </template>
                        </div>

                        <!-- Content -->
                        <div class="flex-1 min-w-0">
                            <!-- Category Badge -->
                            <div class="flex items-center gap-2 mb-1">
                                <span :class="{
                                    'bg-red-100 text-red-700': notification.priority === 'urgent',
                                    'bg-gray-100 text-gray-600': notification.priority !== 'urgent'
                                }" class="px-2 py-0.5 text-xs font-semibold rounded">
                                    <span x-text="notification.category || 'Notifikasi'"></span>
                                </span>
                                <template x-if="!notification.is_read">
                                    <span class="w-2 h-2 bg-bottle rounded-full"></span>
                                </template>
                            </div>

                            <!-- Message -->
                            <p :class="notification.priority === 'urgent' ? 'text-red-600 font-semibold' : 'text-gray-700'"
                               class="text-sm leading-snug break-words"
                               x-text="notification.message"></p>

                            <!-- Time -->
                            <p class="text-xs text-gray-500 mt-2" x-text="formatTime(notification.created_at)"></p>
                        </div>

                        <!-- Delete Button -->
                        <button 
                            @click.stop="deleteNotification(notification.id)"
                            class="flex-shrink-0 text-gray-300 hover:text-gray-500 opacity-0 group-hover:opacity-100 transition">
                            <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd"/>
                            </svg>
                        </button>
                    </div>
                </div>
            </template>
        </div>

        <!-- Error State -->
        <div x-show="errorMessage" class="px-6 py-4 bg-red-50 border-t border-red-100">
            <p class="text-sm text-red-600" x-text="errorMessage"></p>
        </div>
    </div>
</div>

<script>
function notificationDropdown() {
    return {
        isOpen: false,
        isLoading: false,
        notifications: [],
        unreadCount: 0,
        errorMessage: '',
        pollInterval: null,
        
        init() {
            // Load notifikasi saat komponen diinisialisasi
            this.loadNotifications();
            
            // Set up polling setiap 15 detik
            this.pollInterval = setInterval(() => {
                if (!this.isOpen) {
                    this.loadNotifications();
                }
            }, 15000);
        },
        
        toggleDropdown() {
            if (this.isOpen) {
                this.closeDropdown();
            } else {
                this.openDropdown();
            }
        },
        
        openDropdown() {
            this.isOpen = true;
            this.errorMessage = '';
            this.loadNotifications();
        },
        
        closeDropdown() {
            this.isOpen = false;
        },
        
        async loadNotifications() {
            try {
                this.isLoading = true;
                this.errorMessage = '';
                
                const response = await fetch('{{ route("api.notifications.poll") }}', {
                    method: 'GET',
                    headers: {
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                    }
                });
                
                if (!response.ok) {
                    throw new Error('Gagal memuat notifikasi');
                }
                
                const data = await response.json();
                this.notifications = data.notifications || [];
                this.unreadCount = data.unread_count || 0;
                
            } catch (error) {
                console.error('Notification error:', error);
                this.errorMessage = 'Gagal memuat notifikasi. Silakan coba lagi.';
                this.notifications = [];
            } finally {
                this.isLoading = false;
            }
        },
        
        async handleNotificationClick(notification) {
            // Mark as read jika belum dibaca
            if (!notification.is_read) {
                await this.markAsRead(notification.id);
            }
            
            // Redirect ke link jika ada
            if (notification.link_redirect) {
                window.location.href = notification.link_redirect;
            }
        },
        
        async markAsRead(notificationId) {
            try {
                const response = await fetch(`{{ route('api.notifications.mark-read', ':id') }}`.replace(':id', notificationId), {
                    method: 'POST',
                    headers: {
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                    }
                });
                
                if (response.ok) {
                    // Update local state
                    const notification = this.notifications.find(n => n.id === notificationId);
                    if (notification) {
                        notification.is_read = true;
                    }
                    this.unreadCount = Math.max(0, this.unreadCount - 1);
                }
            } catch (error) {
                console.error('Error marking notification as read:', error);
            }
        },
        
        async markAllAsRead() {
            try {
                const response = await fetch('{{ route("api.notifications.read-all") }}', {
                    method: 'POST',
                    headers: {
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                    }
                });
                
                if (response.ok) {
                    // Update local state
                    this.notifications.forEach(n => n.is_read = true);
                    this.unreadCount = 0;
                }
            } catch (error) {
                console.error('Error marking all as read:', error);
            }
        },
        
        async deleteNotification(notificationId) {
            if (!confirm('Apakah Anda yakin ingin menghapus notifikasi ini?')) {
                return;
            }
            
            try {
                const response = await fetch(`{{ route('api.notifications.delete', ':id') }}`.replace(':id', notificationId), {
                    method: 'DELETE',
                    headers: {
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                    }
                });
                
                if (response.ok) {
                    // Remove from local state
                    this.notifications = this.notifications.filter(n => n.id !== notificationId);
                }
            } catch (error) {
                console.error('Error deleting notification:', error);
            }
        },
        
        formatTime(dateString) {
            const date = new Date(dateString);
            const now = new Date();
            const diffMs = now - date;
            const diffSecs = Math.floor(diffMs / 1000);
            const diffMins = Math.floor(diffSecs / 60);
            const diffHours = Math.floor(diffMins / 60);
            const diffDays = Math.floor(diffHours / 24);
            
            if (diffSecs < 60) {
                return 'Baru saja';
            } else if (diffMins < 60) {
                return `${diffMins} menit lalu`;
            } else if (diffHours < 24) {
                return `${diffHours} jam lalu`;
            } else if (diffDays < 7) {
                return `${diffDays} hari lalu`;
            } else {
                return date.toLocaleDateString('id-ID', { 
                    year: 'numeric', 
                    month: 'short', 
                    day: 'numeric' 
                });
            }
        },
        
        destroy() {
            if (this.pollInterval) {
                clearInterval(this.pollInterval);
            }
        }
    }
}
</script>
