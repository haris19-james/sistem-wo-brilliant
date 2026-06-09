<!-- Debug snippet: logs active booking_id and meeting booking ids -->
<script>
    document.addEventListener('DOMContentLoaded', function () {
        try {
            const sessionBookingId = <?php echo json_encode(session()->get('booking_id')); ?>;
            const pageBookingIds = <?php echo json_encode(($bookingsWithMeetings ?? collect())->pluck('id')->filter()->values()->all()); ?>;
            const meetingBookingIds = <?php echo json_encode(($vendorMeetingsUpcoming ?? collect())->pluck('booking_id')->filter()->unique()->values()->all()); ?>;

            console.log('[Lapangan Debug] session_booking_id:', sessionBookingId);
            console.log('[Lapangan Debug] page_booking_ids:', pageBookingIds);
            console.log('[Lapangan Debug] meeting_booking_ids:', meetingBookingIds);
        } catch (e) {
            console.warn('[Lapangan Debug] failed to output booking debug info', e);
        }
    });
</script>
<?php /**PATH C:\laragon\www\sistem-wo-brilliant2\resources\views/lapangan/modules/dashboard_debug_snippet.blade.php ENDPATH**/ ?>