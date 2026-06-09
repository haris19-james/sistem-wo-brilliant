/**
 * Alpine.js component untuk dropdown notifikasi (Admin / Lapangan / Client)
 */
function notificationBell() {
    return {
        open: false,
        loading: false,
        unreadCount: 0,
        notifications: [],
        pollTimer: null,

        init(initialUnread = 0, initialNotifications = []) {
            this.unreadCount = Number(initialUnread) || 0;
            this.notifications = Array.isArray(initialNotifications) ? initialNotifications : [];
            this.bindRealtime();
            this.refreshList(false);
            this.startPolling();
        },

        toggleDropdown() {
            this.open = !this.open;
            if (this.open) {
                this.refreshList(false);
            }
        },

        bindRealtime() {
            window.addEventListener('notification.received', () => this.refreshList(false));
            window.addEventListener('notifications.count-updated', (event) => {
                if (event.detail?.unread_count !== undefined) {
                    this.unreadCount = event.detail.unread_count;
                }
            });

            if (window.NotificationConfig?.usePusher && window.Echo) {
                const channel = window.Echo.channel(window.NotificationConfig.roleChannel);
                channel.listen(window.NotificationConfig.eventName, () => this.refreshList(false));
            }
        },

        startPolling() {
            if (this.pollTimer) {
                clearInterval(this.pollTimer);
            }

            const interval = window.NotificationConfig?.pollInterval || 15000;
            this.pollTimer = setInterval(() => this.refreshList(false), interval);
        },

        async refreshList(showLoading = true) {
            if (showLoading && this.notifications.length === 0) {
                this.loading = true;
            }

            const pollUrl = window.NotificationConfig?.pollUrl || '/api/notifications/poll';

            try {
                const response = await fetch(pollUrl, {
                    headers: {
                        Accept: 'application/json',
                        'X-Requested-With': 'XMLHttpRequest',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content || '',
                    },
                    credentials: 'same-origin',
                });

                if (!response.ok) {
                    return;
                }

                const data = await response.json();
                this.unreadCount = data.unread_count || 0;
                this.notifications = data.notifications || [];
            } catch (error) {
                console.error('[NotificationBell] refresh failed', error);
            } finally {
                this.loading = false;
            }
        },

        async openItem(notification) {
            const redirectLink = notification.link_redirect;

            try {
                await fetch(`/api/notifications/${notification.id}/read`, {
                    method: 'POST',
                    headers: {
                        Accept: 'application/json',
                        'X-Requested-With': 'XMLHttpRequest',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content || '',
                    },
                    credentials: 'same-origin',
                });

                notification.is_read = true;
                this.unreadCount = Math.max(0, this.unreadCount - 1);
            } catch (error) {
                console.error('[NotificationBell] mark read failed', error);
            }

            if (redirectLink) {
                window.location.href = redirectLink;
            }
        },

        async markAllRead() {
            const readAllUrl = window.NotificationConfig?.readAllUrl || '/api/notifications/read-all';

            try {
                const response = await fetch(readAllUrl, {
                    method: 'POST',
                    headers: {
                        Accept: 'application/json',
                        'X-Requested-With': 'XMLHttpRequest',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content || '',
                    },
                    credentials: 'same-origin',
                });

                if (response.ok) {
                    this.notifications.forEach((item) => {
                        item.is_read = true;
                    });
                    this.unreadCount = 0;
                }
            } catch (error) {
                console.error('[NotificationBell] mark all read failed', error);
            }
        },
    };
}

window.notificationBell = notificationBell;
