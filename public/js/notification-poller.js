/**
 * Notification System - Real-time Polling Implementation
 * 
 * Sistem ini melakukan polling ke server setiap N detik untuk mengambil notifikasi terbaru.
 * Ketika ada notifikasi baru, sistem akan:
 * 1. Menampilkan toast notification
 * 2. Update badge count
 * 3. Menambah notifikasi ke notification panel
 * 4. Trigger custom event untuk aplikasi lain
 */

class NotificationPoller {
    constructor(options = {}) {
        this.pollInterval = options.pollInterval || 5000; // 5 detik default
        this.pollUrl = options.pollUrl || '/api/notifications/poll';
        this.countUrl = options.countUrl || '/api/notifications/count';
        this.markReadUrl = options.markReadUrl || (id) => `/api/notifications/${id}/read`;
        this.deleteUrl = options.deleteUrl || (id) => `/api/notifications/${id}`;
        
        this.isPolling = false;
        this.pollTimer = null;
        this.lastPollTime = null;
        this.notificationBadge = document.querySelector('[data-notification-badge]');
        this.notificationPanel = document.querySelector('[data-notification-panel]');
        this.notificationContainer = this.notificationPanel?.querySelector('[data-notification-list]');
        
        this.toastContainer = options.toastContainer || null;
        this.showUrgentSounds = options.showUrgentSounds || true;
    }

    /**
     * Mulai polling notifikasi
     */
    start() {
        if (this.isPolling) return;
        
        this.isPolling = true;
        console.log('[NotificationPoller] Starting polling every ' + this.pollInterval + 'ms');
        
        // Poll immediately on start
        this.poll();
        
        // Then poll at interval
        this.pollTimer = setInterval(() => this.poll(), this.pollInterval);
    }

    /**
     * Stop polling
     */
    stop() {
        if (!this.isPolling) return;
        
        this.isPolling = false;
        if (this.pollTimer) {
            clearInterval(this.pollTimer);
            this.pollTimer = null;
        }
        console.log('[NotificationPoller] Stopped polling');
    }

    /**
     * Perform single poll request
     */
    async poll() {
        try {
            const response = await fetch(this.pollUrl, {
                method: 'GET',
                headers: {
                    'Accept': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest',
                }
            });

            if (!response.ok) {
                console.error('[NotificationPoller] Poll failed:', response.status);
                return;
            }

            const data = await response.json();
            
            if (data.success && data.notifications && data.notifications.length > 0) {
                console.log('[NotificationPoller] Received', data.notifications.length, 'new notifications');
                
                // Proses setiap notifikasi
                data.notifications.forEach(notification => {
                    this.handleNewNotification(notification);
                });
            }

            // Update badge count
            if (data.unread_count !== undefined) {
                this.updateBadgeCount(data.unread_count);
            }

        } catch (error) {
            console.error('[NotificationPoller] Poll error:', error);
        }
    }

    /**
     * Handle notifikasi baru yang diterima
     */
    handleNewNotification(notification) {
        // Tampilkan toast
        this.showToast(notification);
        
        // Play sound jika urgent
        if (notification.is_urgent && this.showUrgentSounds) {
            this.playNotificationSound();
        }
        
        // Add ke notification panel jika ada
        if (this.notificationContainer) {
            this.addToNotificationPanel(notification);
        }
        
        // Trigger custom event
        window.dispatchEvent(new CustomEvent('notification.received', { 
            detail: notification 
        }));
    }

    /**
     * Tampilkan toast notification
     */
    showToast(notification) {
        if (!this.toastContainer) {
            // Create default toast container jika tidak ada
            this.createDefaultToastContainer();
        }

        const toast = document.createElement('div');
        toast.className = `notification-toast ${notification.is_urgent ? 'urgent' : 'normal'}`;
        toast.innerHTML = `
            <div class="toast-content">
                <div class="toast-header">
                    <strong>${this.getCategoryLabel(notification.category)}</strong>
                    <button class="toast-close" data-notification-id="${notification.id}">&times;</button>
                </div>
                <div class="toast-body">${notification.message}</div>
                ${notification.link_redirect ? `<a href="${notification.link_redirect}" class="toast-link">Lihat Detail →</a>` : ''}
            </div>
        `;

        // Add close handler
        toast.querySelector('.toast-close').addEventListener('click', (e) => {
            e.preventDefault();
            const notifId = e.target.dataset.notificationId;
            this.markAsRead(notifId);
            toast.remove();
        });

        // Add click handler to open link
        if (notification.link_redirect) {
            toast.querySelector('.toast-link').addEventListener('click', () => {
                this.markAsRead(notification.id);
            });
        }

        this.toastContainer.appendChild(toast);

        // Auto-remove after 5 seconds (urgent) or 8 seconds (normal)
        const duration = notification.is_urgent ? 5000 : 8000;
        setTimeout(() => {
            if (toast.parentNode) {
                toast.remove();
            }
        }, duration);
    }

    /**
     * Create default toast container
     */
    createDefaultToastContainer() {
        this.toastContainer = document.createElement('div');
        this.toastContainer.id = 'notification-toast-container';
        this.toastContainer.style.cssText = `
            position: fixed;
            top: 20px;
            right: 20px;
            z-index: 9999;
            max-width: 400px;
            display: flex;
            flex-direction: column;
            gap: 10px;
        `;
        document.body.appendChild(this.toastContainer);
    }

    /**
     * Add notifikasi ke notification panel
     */
    addToNotificationPanel(notification) {
        // Remove empty state jika ada
        const emptyState = this.notificationContainer.querySelector('[data-empty-state]');
        if (emptyState) {
            emptyState.remove();
        }

        const item = document.createElement('div');
        item.className = 'notification-item';
        item.dataset.notificationId = notification.id;
        item.innerHTML = `
            <div class="notification-item-header">
                <span class="notification-category ${notification.category}">${this.getCategoryLabel(notification.category)}</span>
                <time class="notification-time">${this.formatTime(new Date(notification.created_at))}</time>
            </div>
            <div class="notification-item-message">${notification.message}</div>
            <div class="notification-item-actions">
                ${notification.link_redirect ? `<a href="${notification.link_redirect}" class="btn-small">Lihat</a>` : ''}
                <button class="btn-small btn-delete" data-notification-id="${notification.id}">Hapus</button>
            </div>
        `;

        // Add event handlers
        item.querySelector('.btn-delete').addEventListener('click', (e) => {
            e.preventDefault();
            this.deleteNotification(notification.id, item);
        });

        if (notification.link_redirect) {
            item.querySelector('.btn-small:not(.btn-delete)').addEventListener('click', () => {
                this.markAsRead(notification.id);
            });
        }

        // Prepend ke panel
        this.notificationContainer.insertBefore(item, this.notificationContainer.firstChild);
    }

    /**
     * Update badge count
     */
    updateBadgeCount(count) {
        if (!this.notificationBadge) return;

        if (count > 0) {
            this.notificationBadge.textContent = count > 99 ? '99+' : count;
            this.notificationBadge.style.display = 'block';
        } else {
            this.notificationBadge.style.display = 'none';
        }
    }

    /**
     * Mark notifikasi sebagai read
     */
    async markAsRead(notificationId) {
        try {
            await fetch(this.markReadUrl(notificationId), {
                method: 'POST',
                headers: {
                    'Accept': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest',
                    'X-CSRF-TOKEN': this.getCsrfToken(),
                }
            });

            // Remove dari panel
            const item = this.notificationContainer?.querySelector(`[data-notification-id="${notificationId}"]`);
            if (item) {
                item.remove();
            }

            // Update count
            this.updateCountBadge();

        } catch (error) {
            console.error('[NotificationPoller] Mark read error:', error);
        }
    }

    /**
     * Delete notifikasi
     */
    async deleteNotification(notificationId, element) {
        try {
            await fetch(this.deleteUrl(notificationId), {
                method: 'DELETE',
                headers: {
                    'Accept': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest',
                    'X-CSRF-TOKEN': this.getCsrfToken(),
                }
            });

            if (element) {
                element.remove();
            }

            // Update count
            this.updateCountBadge();

        } catch (error) {
            console.error('[NotificationPoller] Delete error:', error);
        }
    }

    /**
     * Update count badge dari server
     */
    async updateCountBadge() {
        try {
            const response = await fetch(this.countUrl, {
                headers: { 'Accept': 'application/json' }
            });
            const data = await response.json();
            if (data.success) {
                this.updateBadgeCount(data.unread_count);
            }
        } catch (error) {
            console.error('[NotificationPoller] Update count error:', error);
        }
    }

    /**
     * Play notification sound
     */
    playNotificationSound() {
        // Bisa menggunakan audio element atau Web Audio API
        const sound = new Audio('/sounds/notification.mp3');
        sound.volume = 0.3;
        sound.play().catch(() => {
            // Silent fail jika browser block autoplay
        });
    }

    /**
     * Get category label dari category code
     */
    getCategoryLabel(category) {
        const labels = {
            'booking': '📅 Booking',
            'payment': '💳 Pembayaran',
            'chat': '💬 Chat',
            'vendor': '👥 Vendor',
            'task': '✓ Tugas',
            'issue': '⚠️ Kendala',
            'rundown': '📋 Rundown',
            'reminder': '🔔 Pengingat',
            'review': '⭐ Review',
            'general': '📢 Umum',
        };
        return labels[category] || category;
    }

    /**
     * Format waktu untuk display
     */
    formatTime(date) {
        const now = new Date();
        const diff = now - date;
        const seconds = Math.floor(diff / 1000);
        const minutes = Math.floor(seconds / 60);
        const hours = Math.floor(minutes / 60);
        const days = Math.floor(hours / 24);

        if (seconds < 60) return 'Baru saja';
        if (minutes < 60) return minutes + ' menit lalu';
        if (hours < 24) return hours + ' jam lalu';
        if (days < 7) return days + ' hari lalu';
        
        return date.toLocaleDateString('id-ID');
    }

    /**
     * Get CSRF token dari meta tag
     */
    getCsrfToken() {
        return document.querySelector('meta[name="csrf-token"]')?.content || '';
    }
}

// ===== GLOBAL INITIALIZATION =====
// Auto-initialize jika ada data-notification-auto-poll di html
document.addEventListener('DOMContentLoaded', () => {
    if (document.querySelector('[data-notification-auto-poll]')) {
        const poller = new NotificationPoller({
            pollInterval: parseInt(document.querySelector('[data-notification-auto-poll]')?.dataset.pollInterval || 5000),
            showUrgentSounds: true,
        });
        poller.start();
        
        // Store ke window untuk akses global
        window.notificationPoller = poller;
    }
});

// Export untuk digunakan di modul lain
if (typeof module !== 'undefined' && module.exports) {
    module.exports = NotificationPoller;
}
