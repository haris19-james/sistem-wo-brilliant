/**
 * Drawer tambah jadwal meeting vendor — /lapangan/jadwal
 */
function lapanganVendorMeetingDrawer(bookings) {
    const oldInput = window.__meetingDrawerOld || {};

    return {
        drawerOpen: false,
        comboOpen: false,
        comboQuery: '',
        submitting: false,
        bookings: Array.isArray(bookings) ? bookings : [],
        form: {
            booking_id: oldInput.booking_id || '',
            nomor_pesanan: '',
            client_name: '',
            title: oldInput.title || '',
            meeting_date: oldInput.meeting_date || '',
            meeting_time: oldInput.meeting_time || '',
            location: oldInput.location || '',
            notes: oldInput.notes || '',
        },

        get filteredBookings() {
            const q = (this.comboQuery || '').toLowerCase().trim();
            if (!q) {
                return this.bookings;
            }

            return this.bookings.filter((item) => {
                const haystack = [
                    item.nomor_pesanan,
                    item.client_name,
                    item.nama_pasangan,
                    item.payment_label,
                ].join(' ').toLowerCase();

                return haystack.includes(q);
            });
        },

        init() {
            if (this.form.booking_id) {
                const selected = this.bookings.find((b) => String(b.id) === String(this.form.booking_id));
                if (selected) {
                    this.selectBooking(selected, false);
                }
            }

            if (window.location.hash === '#vendor-meetings' && (oldInput.booking_id || oldInput.title)) {
                this.openDrawer();
            }
        },

        openDrawer(bookingId = null) {
            if (bookingId) {
                const selected = this.bookings.find((b) => String(b.id) === String(bookingId));
                if (selected) {
                    this.selectBooking(selected);
                }
            }
            this.drawerOpen = true;
            document.body.classList.add('overflow-hidden');
        },

        closeDrawer() {
            this.drawerOpen = false;
            this.comboOpen = false;
            document.body.classList.remove('overflow-hidden');
        },

        selectBooking(item, closeCombo = true) {
            this.form.booking_id = item.id;
            this.form.nomor_pesanan = item.nomor_pesanan;
            this.form.client_name = item.client_name;
            this.comboQuery = `${item.nomor_pesanan} — ${item.client_name}`;

            if (!this.form.title) {
                this.form.title = 'Technical Meeting Vendor';
            }

            if (closeCombo) {
                this.comboOpen = false;
            }
        },

        clearBooking() {
            this.form.booking_id = '';
            this.form.nomor_pesanan = '';
            this.form.client_name = '';
        },
    };
}

document.addEventListener('alpine:init', () => {
    if (typeof Alpine !== 'undefined') {
        Alpine.data('lapanganVendorMeetingDrawer', lapanganVendorMeetingDrawer);
    }
});
