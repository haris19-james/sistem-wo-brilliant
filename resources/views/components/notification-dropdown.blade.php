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
            <p class="text-gray-500 text-sm">Belum ada notifikasi</p>
        </div>

        <!-- Notifications List -->
        <div x-show="!isLoading && groupedNotifications.length > 0" class="max-h-96 overflow-y-auto space-y-4 px-2 py-2">
            <template x-for="group in groupedNotifications" :key="group.key">
                <div class="rounded-2xl border border-gray-100 bg-white shadow-sm">
                    <div class="px-5 py-4 border-b border-gray-100 flex items-center justify-between gap-3">
                        <div class="flex items-center gap-3">
                            <span class="text-xl" x-text="group.icon"></span>
                            <div>
                                <p class="text-sm font-semibold text-gray-900" x-text="group.title"></p>
                                <p class="text-xs text-gray-500" x-text="group.items.length + ' notifikasi'"></p>
                            </div>
                        </div>
                        <span x-show="group.unreadCount > 0" class="inline-flex items-center gap-1 rounded-full bg-gray-100 px-3 py-1 text-xs font-semibold text-gray-600">
                            <span x-text="group.unreadCount"></span> baru
                        </span>
                    </div>

                    <template x-for="notification in group.items.slice(0, 4)" :key="notification.id">
                        <div 
                            class="px-5 py-4 hover:bg-gray-50 transition border-b border-gray-100"
                            :class="notification.is_read ? 'bg-white opacity-75' : 'bg-blue-50/80 border-l-2 border-l-blue-400'">
                            <div class="flex items-start gap-3">
                                <div class="mt-0.5 flex h-9 w-9 items-center justify-center rounded-xl bg-white border border-gray-200 text-sm font-semibold text-gray-700">
                                    <span x-text="notification.icon"></span>
                                </div>
                                <div class="flex-1 min-w-0">
                                    <div class="flex items-center justify-between gap-3">
                                        <p class="text-sm font-medium text-gray-900" :class="notification.is_read ? '' : 'font-bold'" x-text="notification.display_message"></p>
                                        <button 
                                            type="button"
                                            x-show="notification.link_redirect"
                                            @click.stop="handleNotificationClick(notification)"
                                            class="rounded-full bg-bottle px-3 py-1 text-xs font-semibold text-white hover:bg-bottleHover transition">
                                            Lihat
                                        </button>
                                    </div>
                                    <div class="mt-2 flex items-center gap-2 text-xs text-gray-500">
                                        <span x-text="notification.groupLabel"></span>
                                        <span>•</span>
                                        <span x-text="formatTime(notification.created_at)"></span>
                                        <template x-if="!notification.is_read">
                                            <span class="ml-auto inline-flex h-2 w-2 rounded-full bg-blue-500"></span>
                                        </template>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </template>

                    <div x-show="group.items.length > 4" class="px-5 py-3 text-xs text-gray-500 bg-gray-50">
                        Menampilkan 4 dari <span x-text="group.items.length"></span> notifikasi. Buka halaman notifikasi untuk melihat semuanya.
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
        groupedNotifications: [],
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
                this.notifications = (data.notifications || []).map(notification => ({
                    ...notification,
                    display_message: this.formatMessage(notification),
                    groupLabel: this.getGroupLabel(notification.category),
                    icon: this.getNotificationIcon(notification.category),
                }));
                this.unreadCount = data.unread_count || 0;
                this.groupedNotifications = this.buildGroupedNotifications(this.notifications);
                
            } catch (error) {
                console.error('Notification error:', error);
                this.errorMessage = 'Gagal memuat notifikasi. Silakan coba lagi.';
                this.notifications = [];
                this.groupedNotifications = [];
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
                    this.groupedNotifications = this.buildGroupedNotifications(this.notifications);
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
                    this.groupedNotifications = this.buildGroupedNotifications(this.notifications);
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
                    this.groupedNotifications = this.buildGroupedNotifications(this.notifications);
                }
            } catch (error) {
                console.error('Error deleting notification:', error);
            }
        },
        
        buildGroupedNotifications(notifications) {
            const buckets = {};
            const order = ['payment', 'task', 'system'];

            // Tampilkan semua notifikasi (baca & belum baca), tanpa filter
            notifications.forEach(notification => {
                const key = this.getGroupKey(notification.category);
                if (!buckets[key]) {
                    buckets[key] = {
                        key,
                        title: this.getGroupLabel(notification.category),
                        icon: this.getGroupIcon(notification.category),
                        items: [],
                        unreadCount: 0,
                    };
                }

                buckets[key].items.push(notification);
                if (!notification.is_read) {
                    buckets[key].unreadCount += 1;
                }
            });

            return order
                .filter(key => buckets[key])
                .map(key => buckets[key])
                .concat(Object.keys(buckets)
                    .filter(key => !order.includes(key))
                    .map(key => buckets[key])
                );
        },
        
        getGroupKey(category) {
            if (category === 'payment') {
                return 'payment';
            }
            if (category === 'task') {
                return 'task';
            }
            return 'system';
        },
        
        getGroupLabel(category) {
            if (category === 'payment') {
                return 'Pembayaran';
            }
            if (category === 'task') {
                return 'Tugas Lapangan';
            }
            return 'Sistem';
        },
        
        getGroupIcon(category) {
            if (category === 'payment') {
                return '💰';
            }
            if (category === 'task') {
                return '👷';
            }
            return '⚙️';
        },
        
        getNotificationIcon(category) {
            if (category === 'payment') {
                return '💵';
            }
            if (category === 'task') {
                return '📝';
            }
            return '🔔';
        },
        
        formatMessage(notification) {
            const message = notification.message || '';
            const category = notification.category || '';

            if (category === 'task') {
                const forcedFinish = message.match(/Tugas (.+?) dipaksa selesai oleh admin/i);
                if (forcedFinish) {
                    return `Tugas ${forcedFinish[1]} telah diselesaikan oleh Admin.`;
                }

                const verifiedFinished = message.match(/Tugas (.+?) diverifikasi selesai/i);
                if (verifiedFinished) {
                    return `Tugas ${verifiedFinished[1]} telah diverifikasi selesai.`;
                }
            }

            if (category === 'payment') {
                const paymentConfirmed = message.match(/konfirmasi pembayaran baru/i);
                if (paymentConfirmed) {
                    return 'Ada konfirmasi pembayaran baru. Periksa detail pembayaran untuk aksi selanjutnya.';
                }
            }

            return message;
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
