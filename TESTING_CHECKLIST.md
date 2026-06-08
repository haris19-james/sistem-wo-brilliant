# Testing Checklist - Vendor Meeting Synchronization Fix

**Tanggal:** 2024  
**Objective:** Verify synchronization of vendor meetings across Admin, Korlap, and Customer dashboards  
**Special Focus:** Client "Haris dan Nilam"

---

## Pre-Test Setup

### Environment Check
- [ ] `.env` contains `FRONTEND_REVALIDATE_URL` 
- [ ] `.env` contains `FRONTEND_REVALIDATE_SECRET`
- [ ] Laravel log file exists: `storage/logs/laravel.log`
- [ ] Clear logs: `rm storage/logs/laravel.log` (optional, for clean testing)
- [ ] Enable debug mode: `APP_DEBUG=true` in `.env`

### Database Check
- [ ] `vendor_meetings` table exists
- [ ] `pesanans` table has booking data
- [ ] "Haris dan Nilam" booking exists with status "confirmed" or "on_progress"
- [ ] At least one booking has payment status "dp_paid" or "fully_paid"

### Browser Setup
- [ ] Open 3 browser windows: Admin, Korlap, Customer
- [ ] Keep developer console open in each window
- [ ] Open terminal for log monitoring: `tail -f storage/logs/laravel.log`

---

## Test Case 1: Query Service - Korlap Status Filter Default

**Objective:** Verify Korlap shows all statuses by default (not just "aktif")

### Steps:
1. Login to **Korlap dashboard**
2. Go to **Jadwal → Meetings Tab**
3. Check filters at top: **Status dropdown default value**

### Expected Results:
- [ ] Status filter shows **"semua"** by default (not "aktif")
- [ ] All meetings visible regardless of status: scheduled, ongoing, completed
- [ ] If no filter applied, shows: "Semua tanggal", "semua" status

### Verification:
```bash
# In terminal, monitor logs:
grep "groupedForKorlap" storage/logs/laravel.log | tail -1

# Output should show: "status_filter": "semua"
```

### Screenshot:
- [ ] Take screenshot of filter bar showing "semua" selected
- [ ] Take screenshot of meetings list with mixed statuses

---

## Test Case 2: Query Service - Korlap Date Range Removal

**Objective:** Verify Korlap shows meetings beyond 60-day window

### Steps:
1. In **Korlap Jadwal → Meetings Tab**
2. Go to very old booking (6+ months old)
3. Check if meetings display for this old booking

### Expected Results:
- [ ] Meetings for 6+ month old bookings are visible
- [ ] No hidden 60-day date range restriction
- [ ] Range label shows: **"Semua tanggal"** when no date filter

### Verification:
```bash
grep "range_label" storage/logs/laravel.log

# Output: "range_label": "Semua tanggal" (not limited)
```

### Screenshot:
- [ ] Show old booking with meetings displayed
- [ ] Show range label in filter bar

---

## Test Case 3: Debug Logging - Target Client "Haris dan Nilam"

**Objective:** Verify debug logging correctly identifies "Haris dan Nilam"

### Steps:
1. Go to **Korlap Jadwal → Meetings Tab**
2. Open **Terminal** and monitor logs:
   ```bash
   tail -f storage/logs/laravel.log | grep "target_client"
   ```
3. Refresh page (F5) in Korlap browser

### Expected Results:
- [ ] Log output appears within 5 seconds
- [ ] JSON contains: `"target_client_haris_nilam": {"found": true}`
- [ ] `matching_groups` array contains booking details for "Haris dan Nilam"

### Example Log Output:
```json
{
  "target_client_haris_nilam": {
    "found": true,
    "matching_groups": [
      {
        "client_name": "Haris dan Nilam",
        "booking_id": 101,
        "meeting_count": 3
      }
    ]
  }
}
```

### Screenshot:
- [ ] Terminal showing log output
- [ ] Korlap page with "Haris dan Nilam" meetings visible

### Troubleshooting if "found: false":
- [ ] Check: Does "Haris dan Nilam" booking exist?
- [ ] Check: Is payment status "dp_paid" or "fully_paid"?
- [ ] Check: Is booking status "confirmed", "on_progress", or "completed"?
- [ ] Check: Does booking have meetings attached?

---

## Test Case 4: Admin Query - Enhanced Eager Loading

**Objective:** Verify Admin displays complete meeting data with vendor info

### Steps:
1. Login to **Admin dashboard**
2. Go to **Vendor Meetings List**
3. Check a meeting record for client "Haris dan Nilam" (if exists)
4. Inspect page elements in Developer Tools → Network tab

### Expected Results:
- [ ] Meeting record shows: **Booking info** (client, paket)
- [ ] Meeting record shows: **Korlap name** (assigned team)
- [ ] Meeting record shows: **Vendor name** (meeting facilitator)
- [ ] No "N/A" or missing fields for relationships

### Verification:
```bash
grep "Admin index query results" storage/logs/laravel.log | tail -1

# Output should show:
"client_names_in_page": ["Haris dan Nilam", ...],
"target_haris_nilam_found": true
```

### Screenshot:
- [ ] Admin vendor meetings list
- [ ] Highlight "Haris dan Nilam" row showing all fields

---

## Test Case 5: Real-Time Cache Invalidation - Admin Create

**Objective:** Verify Admin create meeting immediately syncs to Korlap + Customer

### Steps:
1. **Admin browser:** Go to **Vendor Meetings → Create**
2. **Korlap browser:** Keep **Jadwal → Meetings** open
3. **Customer browser:** Keep **Jadwal tab** open
4. In Admin: **Fill form & Submit** for "Haris dan Nilam" booking
5. Monitor logs:
   ```bash
   tail -f storage/logs/laravel.log | grep "cache revalidated"
   ```

### Expected Results:
- [ ] Admin shows success message: "...berhasil dibuat...tersinkron ke Korlap..."
- [ ] Log shows: `[Admin\VendorMeeting] storeMeeting - cache revalidated`
- [ ] **Korlap page updates within 2 seconds** (without manual refresh)
- [ ] **Customer page updates within 2 seconds** (without manual refresh)
- [ ] New meeting appears in all 3 dashboards with same details

### Timing:
- [ ] Admin → Create success: < 1 second
- [ ] Cache invalidation request: < 1 second (logged)
- [ ] Korlap updates: < 2 seconds
- [ ] Customer updates: < 2 seconds

### Screenshot:
- [ ] Admin: Success message showing
- [ ] Korlap: New meeting appeared (timestamp noted)
- [ ] Customer: New meeting appeared (timestamp noted)
- [ ] Terminal: Cache revalidation log output

### Failure Scenario:
If meetings don't appear immediately:
- [ ] Check: `FRONTEND_REVALIDATE_URL` configured in .env?
- [ ] Check: Frontend cache invalidation endpoint responding?
- [ ] Fallback: Manual page refresh should still show new meeting

---

## Test Case 6: Real-Time Cache Invalidation - Korlap Update

**Objective:** Verify Korlap status update syncs to Customer immediately

### Steps:
1. **Customer browser:** Open **Jadwal tab**
2. **Korlap browser:** Open same **Jadwal → Meetings**
3. In Korlap: Find a "scheduled" meeting
4. Change status to **"ongoing"**
5. Monitor logs: `grep "updateStatus - cache revalidated"`

### Expected Results:
- [ ] Korlap shows: "...diubah menjadi 'ongoing'...tersinkron..."
- [ ] Log shows: `[LapanganVendorMeeting] updateStatus - cache revalidated`
- [ ] **Customer page updates within 2 seconds** (without refresh)
- [ ] Status change visible in Customer meeting card

### Screenshot:
- [ ] Korlap: Status change success message
- [ ] Customer: Status updated (before/after view)
- [ ] Terminal: Cache revalidation log

---

## Test Case 7: Real-Time Cache Invalidation - Admin Delete

**Objective:** Verify Admin delete invalidates cache across dashboards

### Steps:
1. **Korlap browser:** Note a meeting exists in **Jadwal → Meetings**
2. **Customer browser:** Note same meeting in their **Jadwal**
3. In **Admin:** Find same meeting and **Delete**
4. Monitor: `grep "cache revalidated" storage/logs/laravel.log`

### Expected Results:
- [ ] Admin shows: "...berhasil dihapus...tersinkron..."
- [ ] Log shows: `[Admin\VendorMeeting] destroy - cache revalidated`
- [ ] **Korlap page updates** (meeting disappears within 2 seconds)
- [ ] **Customer page updates** (meeting disappears within 2 seconds)
- [ ] No orphaned/cached meetings showing

### Screenshot:
- [ ] Admin: Delete success message
- [ ] Korlap: Meeting list (no deleted meeting)
- [ ] Customer: Meeting list (no deleted meeting)

---

## Test Case 8: Deduplication - Vendor-Specific Meetings

**Objective:** Verify different vendors' same-time meetings both display

### Setup:
- [ ] Create 2 meetings for same booking on same date/time
- [ ] Assign to **2 different vendors**
- [ ] Deploy code with vendor_id in dedup key

### Steps:
1. In **Korlap Jadwal → Meetings**
2. Look for booking with multiple meetings at same time
3. Verify **both vendor meetings display** (not deduplicated)

### Expected Results:
- [ ] Both meetings visible (not collapsed to 1)
- [ ] Each shows correct vendor name
- [ ] Log shows both in `booking_ids_in_results`

### Verification:
```bash
grep "deduped_meetings_count" storage/logs/laravel.log | tail -1

# Should show: both meetings counted, not reduced
```

### Screenshot:
- [ ] Meeting list showing both vendor meetings for same booking

---

## Test Case 9: Role-Based Filter Verification

**Objective:** Verify no inappropriate filtering based on role

### Steps:

#### Admin View:
1. Login as **Admin**
2. Check **Vendor Meetings** list
3. Count total meetings displayed

#### Korlap View:
1. Login as **Korlap**
2. Check **Jadwal → Meetings**
3. Count meetings (should be subset of Admin)

#### Customer View:
1. Login as **Customer**
2. Check **Jadwal tab**
3. Count meetings (should only be their own bookings)

### Expected Results:
- [ ] **Admin:** Shows all meetings across all Korlaps (global view)
- [ ] **Korlap:** Shows only assigned bookings (WHERE korlap_id = auth()->id())
- [ ] **Customer:** Shows only their own bookings (via user_id)
- [ ] **No unexpected filtering** (e.g., status-based role filters)

### Scopes Verified:
```
Admin:     VendorMeeting::all() [no scope]
Korlap:    VendorMeeting::forKorlap($korlapId)
Customer:  VendorMeeting via booking relationship
```

### Screenshot:
- [ ] Admin: Full meetings count
- [ ] Korlap: Subset count
- [ ] Customer: Own bookings meetings

---

## Test Case 10: Payment Status Gating

**Objective:** Verify unpaid bookings don't show meetings

### Setup:
- [ ] Have a booking with status_pembayaran = "unpaid"
- [ ] Try to create meeting for this booking

### Steps:
1. In **Admin:** Try to create meeting for unpaid booking
2. Check error message

### Expected Results:
- [ ] Admin: Shows error "belum membayar minimal DP"
- [ ] Unpaid booking **cannot** have meetings created
- [ ] Existing meetings for unpaid bookings **don't display** in Korlap

### Screenshot:
- [ ] Admin create form error message

---

## Test Case 11: Filter Interaction

**Objective:** Verify filters work together correctly

### Steps:
1. In **Korlap Jadwal → Meetings**
2. Apply multiple filters:
   - [ ] Status: "selesai" (completed only)
   - [ ] Klien: "Haris"
   - [ ] Tanggal: specific date

### Expected Results:
- [ ] Results show **only completed meetings**
- [ ] Results contain **only "Haris dan Nilam"** client
- [ ] Results on **specific date only**
- [ ] All 3 filters applied cumulatively (AND logic)

### Verification:
```bash
grep "filters_applied" storage/logs/laravel.log | tail -1

# Should show all 3 filters active
```

---

## Edge Cases Testing

### Edge Case 1: No Meetings Exist
- [ ] Korlap jadwal shows empty state gracefully
- [ ] No errors in logs
- [ ] Filters still functional

### Edge Case 2: Concurrent Updates
- [ ] Admin updates meeting while Korlap viewing same page
- [ ] Cache invalidation prevents stale data
- [ ] No inconsistent state

### Edge Case 3: Network Delay
- [ ] Cache revalidation timeout doesn't crash app
- [ ] Error logged but user experience not broken
- [ ] Manual refresh still works

### Edge Case 4: Invalid Filter Values
- [ ] Invalid date format handled gracefully
- [ ] Invalid client name doesn't break query
- [ ] Invalid status defaults to "semua"

---

## Regression Testing

### Critical Paths to Verify:
- [ ] Customer booking still works (unaffected)
- [ ] Korlap assignment logic unchanged
- [ ] Payment verification still gates access
- [ ] Rundown/timeline schedule unaffected
- [ ] Reports and statistics accurate

---

## Performance Testing (Optional)

### Baseline:
- [ ] Korlap Jadwal loads in < 2 seconds (with 100+ bookings)
- [ ] Admin Meetings list loads in < 2 seconds (with 1000+ records)
- [ ] Cache revalidation completes in < 1 second

### Load Test:
- [ ] 10 concurrent updates: no race conditions
- [ ] High-volume logging: no performance degradation

---

## Sign-Off

**Tester Name:** ________________  
**Date:** ________________  
**Overall Result:** 
- [ ] **PASS** - All tests passed, ready for production
- [ ] **PASS with notes** - Minor issues documented below
- [ ] **FAIL** - Critical issues found, not ready

### Issues Found:
```
1. ...
2. ...
```

### Notes:
```
...
```

---

## Deployment Checklist (After Passing Tests)

- [ ] Code committed and merged to main branch
- [ ] Migration run (if any - none for this change)
- [ ] Cache cleared: `php artisan cache:clear`
- [ ] Config cached: `php artisan config:cache`
- [ ] Logs monitored for 1 hour post-deploy
- [ ] Customer support notified of fix
- [ ] "Haris dan Nilam" client verified happy with results

---

**End of Testing Checklist**
