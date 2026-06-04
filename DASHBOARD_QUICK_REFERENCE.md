# Dashboard Header & Notification Dropdown - Quick Reference

## 🎯 Component Structure

### Header Component Location
```
resources/views/components/dashboard-header.blade.php
```

### Usage di Page
```blade
<x-dashboard-header 
    title="Dashboard Admin"
    :unreadCount="$notificationCount"
    notificationRoute="{{ route('notifications.index') }}">
    
    <!-- User Profile Slot -->
    <div class="flex items-center gap-2">
        <img src="..." alt="User" class="w-8 h-8 rounded-full">
        <span class="text-sm">{{ auth()->user()->name }}</span>
    </div>
</x-dashboard-header>
```

---

## 📱 Responsive Breakpoints

### Header Layout Behavior

| Screen Size | Title | Left Date | Right Date | Bell | User Profile |
|------------|-------|-----------|------------|------|--------------|
| **xs-sm** | ✅ | ✅ | ❌ | ✅ | ❌ |
| **md** | ✅ | ✅ | ❌ | ✅ | ✅ |
| **lg+** | ✅ | ✅ | ✅ | ✅ | ✅ |

**Legend:**
- ✅ = Visible
- ❌ = Hidden

### CSS Classes
```blade
<!-- Left Title & Subtitle (semua screen) -->
<div class="flex items-center min-w-0">
    <!-- Toggle sidebar pada mobile -->
    <button class="lg:hidden">...</button>
    <!-- Judul -->
    <h2 class="text-xl font-bold">...</h2>
    <!-- Date pada md down -->
    <p class="hidden md:block">...</p>
</div>

<!-- Right Section (lg+ screens) -->
<div class="flex items-center gap-4 ml-auto">
    <!-- Date hanya di lg+ -->
    <span class="hidden lg:inline">...</span>
    <!-- Notification (semua screen) -->
    @include('components.notification-dropdown')
    <!-- User profile hanya md+ -->
    <div class="hidden md:flex">...</div>
</div>
```

---

## 🔔 Notification Dropdown Reference

### Component Signature
```blade
@include('components.notification-dropdown')
```

### Alpine.js Methods
```javascript
// State Management
notificationDropdown.isOpen              // Boolean
notificationDropdown.isLoading           // Boolean
notificationDropdown.notifications[]     // Array
notificationDropdown.unreadCount         // Number

// Methods
toggleDropdown()                         // Toggle open/close
openDropdown()                          // Force open
closeDropdown()                         // Force close
loadNotifications()                     // Fetch dari API
handleNotificationClick(notification)   // Mark read + redirect
markAsRead(notificationId)              // Mark single read
markAllAsRead()                         // Mark all read
deleteNotification(notificationId)      // Delete with confirm
formatTime(dateString)                  // Format relative time
```

### API Endpoints Required
```
GET  /api/notifications/poll           # Fetch notifications
POST /api/notifications/{id}/read       # Mark single read
POST /api/notifications/read-all        # Mark all read
DELETE /api/notifications/{id}          # Delete notification
```

### Notification Object Structure
```json
{
  "id": 1,
  "message": "New booking received",
  "category": "Booking",
  "priority": "normal|urgent",
  "is_read": false|true,
  "link_redirect": "/path/to/page",
  "created_at": "2024-01-15 10:30:00"
}
```

---

## 🎨 Styling Reference

### Button Styling
```blade
<button class="relative inline-flex items-center justify-center w-10 h-10 rounded-md text-gray-600 hover:text-bottle hover:bg-gray-100 transition">
```

### Dropdown Container
```blade
<div class="absolute right-0 mt-2 w-96 bg-white rounded-2xl shadow-2xl border border-gray-100 z-50">
```

### Header Background
```blade
<header class="bg-gradient-to-r from-leafSoft to-leafSoft px-6 py-4 border-b border-gray-100">
```

### Notification Item States
```blade
<!-- Unread notification -->
<div class="bg-leafSoft border-l-4 border-l-transparent">

<!-- Read notification -->
<div class="bg-white border-l-4 border-l-transparent">

<!-- Urgent notification -->
<div class="bg-leafSoft border-l-4 border-l-red-500">
```

---

## 💡 Common Customizations

### Change Dropdown Width
```javascript
// Current
class="... w-96 ..."

// Options
w-80   = 320px (smaller)
w-96   = 384px (current)
w-[420px] = 420px (custom)
```

### Adjust Auto-Poll Interval
```javascript
// Current: 15 seconds
this.pollInterval = setInterval(() => {
    this.loadNotifications();
}, 15000);  // ← Change this value in milliseconds
```

### Change Colors
```blade
<!-- Current theme -->
<!-- Header: leafSoft background -->
<!-- Button: bottle hover, leafSoft gradient -->
<!-- Unread: leafSoft bg, bottle dot -->
<!-- Urgent: red border + red icon -->

<!-- To customize, update Tailwind colors in tailwind.config.js -->
```

---

## 🔐 Security Considerations

### CSRF Protection
```javascript
'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
```
✅ Already implemented in all fetch requests

### Authentication
All API endpoints should check:
```php
// In controller
$this->authorize('view', auth()->user());
```

### Rate Limiting
Consider adding rate limiting to API endpoints:
```php
Route::middleware('throttle:60,1')->post('/notifications/{id}/read');
```

---

## 🐛 Troubleshooting

### Dropdown not appearing
- [ ] Check if Alpine.js is loaded
- [ ] Verify z-50 class not hidden by parent overflow
- [ ] Open browser console for JavaScript errors

### Notifications not loading
- [ ] Check API endpoint: `/api/notifications/poll`
- [ ] Verify CSRF token in meta tag
- [ ] Check network tab for 401/403 responses

### Badge count wrong
- [ ] API should return correct `unread_count`
- [ ] Check if `markAsRead()` reducing count correctly
- [ ] Clear browser cache and reload

### Styling inconsistent
- [ ] Verify Tailwind CSS custom colors loaded
- [ ] Check if bottle/leafSoft colors in config
- [ ] Verify no CSS conflicts with other components

---

## 📋 Integration Checklist

When integrating header into new page:

- [ ] Include `x-dashboard-header` component
- [ ] Pass `title` prop
- [ ] Include user profile in slot
- [ ] Verify Alpine.js loaded
- [ ] Verify Tailwind config has custom colors
- [ ] Verify CSRF token in layout
- [ ] Test on mobile/tablet/desktop
- [ ] Check notification API endpoints exist
- [ ] Test dropdown open/close
- [ ] Test mark as read functionality
- [ ] Test delete notification
- [ ] Test redirect on click
- [ ] Verify no console errors

---

## 📞 Support Information

**Component Versions:**
- Alpine.js: v3.13.3
- Chart.js: Latest (via CDN)
- Tailwind CSS: v3.x

**Brand Colors:**
- Bottle Green: `#00A32A`
- Bottle Hover: `#008F24`
- Leaf Soft: `#EDFCF0`
- Gray BG: `#F8FAFC`
- Gray Text: `#64748B`

**Deployment Requirements:**
- Laravel 10+
- Blade templating
- Alpine.js script
- Tailwind CSS build
- API endpoints configured
