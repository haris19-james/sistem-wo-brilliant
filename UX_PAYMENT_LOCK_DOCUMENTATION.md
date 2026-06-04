# UX Improvement: Payment Lock Information Banner

## Overview
Implementasi UX improvement untuk memberikan informasi yang jelas kepada customer mengapa jadwal meeting vendor terkunci saat belum membayar DP, dan memberikan clear call-to-action untuk membayar.

## Changes Made

### 1. **Info Banner - Conditional Rendering**
**File**: `resources/views/customer/modules/pesanan/show.blade.php`

**Logic**:
```php
$isPaymentLocked = in_array($pesanan->status_pembayaran, ['unpaid', 'rejected'], true) 
    && $pesanan->status_booking === 'pending';
```

**Conditions**:
- Muncul ketika status pembayaran = `unpaid` atau `rejected`
- DAN status booking = `pending`
- Banner tidak muncul jika sudah `dp_paid` atau `fully_paid`

### 2. **Banner Design & Content**
Banner ditampilkan dengan:
- **Icon**: Warning/Lock icon (amber color)
- **Title**: "Jadwal Meeting Vendor Terkunci"
- **Description**: Pesan informatif yang menjelaskan bahwa customer perlu bayar minimal DP untuk membuka jadwal meeting
- **CTA Buttons**: 
  - Primary: "Bayar DP Sekarang" (mengarah ke halaman pembayaran)
  - Secondary: "Lihat Detail Pembayaran" (mengarah ke invoice)
- **Styling**: Amber/warning theme untuk visibility tanpa terlalu mencolok

**Visual**:
```
┌─────────────────────────────────────────────────────────┐
│ ⚠️  Jadwal Meeting Vendor Terkunci                      │
│                                                         │
│ Untuk melanjutkan proses booking dan membuka akses    │
│ penjadwalan meeting dengan vendor, Anda perlu         │
│ menyelesaikan pembayaran minimal DP (Down Payment).   │
│                                                         │
│ [💳 Bayar DP Sekarang] [📄 Lihat Detail Pembayaran]   │
└─────────────────────────────────────────────────────────┘
```

### 3. **Jadwal Meeting Section - Lock Overlay**
**File**: `resources/views/customer/modules/pesanan/show.blade.php`

**Features**:
- Section tetap visible (tidak hidden)
- Ditampilkan dengan **opacity-50** (semi-transparent)
- **pointer-events-none** - mencegah interaksi
- Overlay dengan backdrop blur ditampilkan di atas section
- Overlay menampilkan lock icon dan pesan "Terkunci - Selesaikan pembayaran DP untuk membuka"

**Implementation**:
```blade
<div class="space-y-3 max-h-96 overflow-y-auto pr-1 {{ $isPaymentLocked ? 'opacity-50 pointer-events-none' : '' }}">
    <!-- Meeting items rendered here -->
</div>

@if($isPaymentLocked)
<div class="absolute inset-0 rounded-xl bg-black/10 backdrop-blur-[2px] flex items-center justify-center cursor-not-allowed">
    <!-- Lock overlay content -->
</div>
@endif
```

### 4. **Success Notification - Auto-Refresh Logic**
**File**: `resources/views/customer/modules/pesanan/show.blade.php`

**Feature**: Automatic success notification muncul saat DP payment verified
```php
@if($pesanan->status_pembayaran === 'dp_paid' && $pesanan->status_booking === 'approved_dp')
<div class="mb-6 p-4 bg-green-50 border border-green-200 rounded-xl text-green-800 text-sm">
    <strong>✓ Pembayaran DP Berhasil!</strong>
    <p class="mt-1">Jadwal meeting vendor kini sudah dibuka. Anda dapat menjadwalkan pertemuan dengan vendor sesuai kebutuhan.</p>
</div>
@endif
```

**Flow**:
1. Customer bayar DP dan upload bukti
2. Admin verifikasi dan approve pembayaran
3. `status_pembayaran` diubah ke `dp_paid` via `PaymentWorkflowService`
4. `status_booking` diubah ke `approved_dp`
5. Ketika customer kembali ke halaman pesanan (refresh atau redirect), kondisi di atas true
6. Success banner tampil otomatis

### 5. **Backend Status Updates**
**File**: `app/Services/PaymentWorkflowService.php`

Status fields yang di-update setelah pembayaran DP disetujui:
```php
// DP dibayar
'status_pembayaran' => 'dp_paid',
'status_booking' => 'approved_dp',
'akses_jadwal' => 'partial'

// Lunas
'status_pembayaran' => 'fully_paid',
'status_booking' => 'approved_lunas',
'akses_jadwal' => 'full'
```

## User Experience Flow

### Scenario 1: First Visit (Before Payment)
```
Customer opens booking detail
↓
status_pembayaran = 'unpaid' && status_booking = 'pending'
↓
[INFO BANNER] Jadwal Meeting Vendor Terkunci
[LOCK OVERLAY] on jadwal meeting section
↓
Customer clicks "Bayar DP Sekarang"
↓
Redirected to payment page
```

### Scenario 2: After DP Payment
```
Customer uploads payment proof
↓
Admin verifies & approves
↓
status_pembayaran = 'dp_paid'
status_booking = 'approved_dp'
↓
Customer refreshes/revisits page
↓
[SUCCESS BANNER] Pembayaran DP Berhasil!
Banner HIDDEN (conditions not met)
LOCK OVERLAY REMOVED
↓
Jadwal meeting fully accessible
```

## Database States

| State | status_pembayaran | status_booking | Banner | Lock | Access |
|-------|-------------------|----------------|--------|------|--------|
| No Payment | unpaid | pending | ✓ Show | ✓ Locked | ✗ None |
| Payment Rejected | rejected | pending | ✓ Show | ✓ Locked | ✗ None |
| DP Paid | dp_paid | approved_dp | ✗ Hide | ✗ Unlocked | ✓ Partial |
| Fully Paid | fully_paid | approved_lunas | ✗ Hide | ✗ Unlocked | ✓ Full |

## CSS Classes Used

- **Banner**: `bg-amber-50 border-2 border-amber-200` (warning theme)
- **Lock Overlay**: `bg-black/10 backdrop-blur-[2px] absolute inset-0` (semi-transparent)
- **Locked Content**: `opacity-50 pointer-events-none` (disabled state)
- **Success Banner**: `bg-green-50 border border-green-200` (success theme)

## Icons Used

- **Info Banner**: SVG exclamation triangle (amber-600)
- **Lock Overlay**: SVG lock icon (amber-600)
- **CTA Buttons**: 💳 (credit card) and 📄 (document)
- **Success Banner**: ✓ checkmark

## Responsive Design

- **Mobile (< 640px)**: Buttons stack vertically (`flex-col`)
- **Tablet+ (≥ 640px)**: Buttons side-by-side (`flex-row`)
- **Banner text**: Responsive padding and font sizing
- **Lock overlay**: Full screen with centered message

## Testing Checklist

- [ ] New booking with unpaid status shows both banner and lock overlay
- [ ] Rejected payment shows both banner and lock overlay
- [ ] After DP payment, banner disappears when page reloads
- [ ] After DP payment, lock overlay removed from jadwal section
- [ ] Success notification appears after DP verification
- [ ] CTA buttons redirect to correct pages
- [ ] Lock overlay centered properly on all screen sizes
- [ ] Banner styling matches design system (amber/warning theme)
- [ ] Success notification only shows when conditions met

## Browser Compatibility

- Chrome/Edge: ✓ Full support
- Firefox: ✓ Full support
- Safari: ✓ Full support
- Mobile browsers: ✓ Full support

## Performance Notes

- No additional database queries (uses existing data)
- All conditions evaluated server-side (Blade)
- No JavaScript required for functionality
- CSS only (no animation libraries needed)

## Future Enhancements

1. **Real-time Updates**: Add WebSocket notification when admin approves payment
2. **Auto-redirect**: Redirect customer to vendor meetings page after payment approval
3. **Payment Reminder**: Send email/SMS notification with link to payment page
4. **Progress Indicator**: Show payment progress (X% of DP paid)
5. **Payment Plan**: Display visual timeline for payment deadlines

## Notes

- Lock overlay uses `cursor-not-allowed` to indicate disabled state
- Backdrop blur creates visual hierarchy without hiding content
- Banner colors follow Tailwind's amber theme (warning/caution level)
- Success notification only shows on page reload (not real-time yet)
