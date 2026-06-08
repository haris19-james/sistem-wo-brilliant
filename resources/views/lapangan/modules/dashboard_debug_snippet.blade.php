<!-- Debug snippet: logs active booking_id and meeting booking ids -->
<script>
    document.addEventListener('DOMContentLoaded', function () {
        try {
            const sessionBookingId = {!! json_encode(session()->get('booking_id')) !!};
            const pageBookingIds = {!! json_encode(($bookingsWithMeetings ?? collect())->pluck('id')->filter()->values()->all()) !!};
            const meetingBookingIds = {!! json_encode(($vendorMeetingsUpcoming ?? collect())->pluck('booking_id')->filter()->unique()->values()->all()) !!};

            console.log('[Lapangan Debug] session_booking_id:', sessionBookingId);
            console.log('[Lapangan Debug] page_booking_ids:', pageBookingIds);
            console.log('[Lapangan Debug] meeting_booking_ids:', meetingBookingIds);
        } catch (e) {
            console.warn('[Lapangan Debug] failed to output booking debug info', e);
        }
    });
</script>
