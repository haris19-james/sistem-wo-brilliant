# 🚀 Notification Dropdown - Quick Reference Card

> Cheat sheet untuk developer tentang implementasi notifikasi dropdown

---

## 📦 Integrasi ke Halaman Baru

```blade
<!-- Di dalam layout -->
<div class="flex items-center space-x-6">
    @include('components.notification-dropdown')
    <!-- Konten lainnya -->
</div>
```

**Requirement:**
- ✅ Alpine.js sudah loaded
- ✅ Meta CSRF token ada
- ✅ User sudah authenticated

---

## 🛠️ Setup Model & Migration

```php
// Model: app/Models/UserNotification.php
class UserNotification extends Model {
    protected $fillable = [
        'user_id', 'message', 'category', 'priority',
        'link_redirect', 'reference_type', 'reference_id', 'is_read'
    ];
}

// Migration fields:
// id, user_id, message, category, priority, link_redirect,
// reference_type, reference_id, is_read, created_at, updated_at
```

---

## 📡 API Endpoints Reference

| Endpoint | Method | Purpose | CSRF |
|----------|--------|---------|------|
| `/api/notifications/poll` | GET | Fetch unread | ✅ |
| `/api/notifications/{id}/read` | POST | Mark as read | ✅ |
| `/api/notifications/read-all` | POST | Mark all read | ✅ |
| `/api/notifications/{id}` | DELETE | Delete notif | ✅ |
| `/api/notifications/count` | GET | Get count | ✅ |

---

## 💾 Create Notification Examples

### Via Factory (Testing)
```php
$notification = \App\Models\UserNotification::factory()->create([
    'user_id' => $user->id,
    'priority' => 'urgent'
]);
```

### Via Model
```php
\App\Models\UserNotification::create([
    'user_id' => auth()->id(),
    'message' => 'Your message here',
    'category' => 'Booking',
    'priority' => 'normal',
    'link_redirect' => '/admin/booking/123',
    'reference_type' => 'booking',
    'reference_id' => 123,
    'is_read' => false,
]);
```

### Via Job/Event
```php
// Dispatch notification creation
class SendNotificationJob implements ShouldQueue {
    public function handle() {
        UserNotification::create([...]);
    }
}

// Or use Observer
class UserObserver {
    public function created(User $user) {
        UserNotification::create([...]);
    }
}
```

---

## 🎨 Styling Reference

### State Colors
```
Unread:    bg-blue-50 + blue dot
Read:      bg-white
Urgent:    bg-red-50 + border-l-4 border-red-500
Normal:    bg-white
```

### Icons
```
Urgent:    Warning triangle icon (red)
Normal:    Info circle icon (blue)
Category:  Colored badge background
```

---

## ⚡ JavaScript API

```javascript
// Access Alpine component data
const dropdown = Alpine.store('notificationDropdown');

// Manual load
dropdown.loadNotifications();

// Close dropdown
dropdown.closeDropdown();

// Access notifications array
console.log(dropdown.notifications);
console.log(dropdown.unreadCount);
```

---

## 🔑 Key Features

| Feature | Details |
|---------|---------|
| **Auto-polling** | Every 15 sec when dropdown closed |
| **Real-time badge** | Shows unread count |
| **Smooth animations** | Alpine.js transitions |
| **Error handling** | User-friendly messages |
| **CSRF protected** | All POST/DELETE requests |
| **Responsive** | w-96, max-h-96 |
| **Touch-friendly** | Mobile optimized |

---

## 🧪 Quick Test Command

```bash
# Open tinker
php artisan tinker

# Create test notification
\App\Models\UserNotification::create([
    'user_id' => 1,
    'message' => 'Test notification',
    'category' => 'Test',
    'priority' => 'normal',
    'link_redirect' => '/admin/dashboard',
    'is_read' => false,
]);
```

---

## 📋 Config File Path

Component location:
```
resources/views/components/notification-dropdown.blade.php
```

Include in views:
```blade
@include('components.notification-dropdown')
```

---

## 🐛 Debug Tips

```javascript
// In browser console:

// Check component
console.log(Alpine.components);

// Check data
console.log(document.getElementById('notification-wrapper').__x);

// Test fetch
fetch('/api/notifications/poll').then(r => r.json()).then(console.log);

// Check CSRF token
console.log(document.querySelector('meta[name="csrf-token"]').content);
```

---

## 📊 Files Modified

```
Created:
- resources/views/components/notification-dropdown.blade.php
- NOTIFICATION_DROPDOWN_GUIDE.md
- NOTIFICATION_TESTING_GUIDE.md

Modified (added @include):
- resources/views/admin/vendor/index.blade.php
- resources/views/admin/pembayaran/index.blade.php
- resources/views/admin/paket/index.blade.php
- resources/views/admin/booking/index.blade.php
- resources/views/admin/chat/index.blade.php
- resources/views/admin/jadwal/index.blade.php
- resources/views/customer/pesanan_detail.blade.php
- resources/views/customer/pesanan.blade.php
- resources/views/customer/invoice.blade.php
```

---

## 🎯 Common Tasks

### Task 1: Add Custom Priority
```php
// In migration, update enum:
$table->enum('priority', ['low', 'normal', 'high', 'urgent']);

// In component, add styling for 'high':
x-show="notification.priority === 'high'" → bg-orange-100
```

### Task 2: Add Sound Notification
```javascript
// In loadNotifications() method, add:
if (this.notifications.length > this.previousCount) {
    new Audio('/sounds/notification.mp3').play();
}
```

### Task 3: Disable Auto-polling
```javascript
// Comment out in init():
// this.pollInterval = setInterval(() => { ... });
```

### Task 4: Add Read Receipt
```php
// Create migration for read_at timestamp
$table->timestamp('read_at')->nullable();

// Update in markAsRead():
$notification->update(['is_read' => true, 'read_at' => now()]);
```

---

## 🔐 Security Checklist

- ✅ CSRF token required on all mutations
- ✅ User ID validated on backend
- ✅ HTML escaped via Alpine x-text
- ✅ No direct SQL queries
- ✅ Authorization checks in Controller
- ✅ Rate limiting on API (optional)

---

## 📈 Performance Tips

| Optimization | How |
|--------------|-----|
| Reduce polling | Increase interval from 15s to 30s |
| Pagination | Limit notifications to 20 per request |
| Caching | Cache unread count for 5 sec |
| Lazy load | Load notifications on first click only |

---

## 🎓 Related Routes

```php
// In routes/web.php
Route::prefix('api/notifications')->group(function () {
    Route::get('/poll', 'NotificationController@pollNotifications');
    Route::post('/{notification}/read', 'NotificationController@markRead');
    Route::post('/read-all', 'NotificationController@markAllRead');
    Route::delete('/{notification}', 'NotificationController@delete');
    Route::get('/count', 'NotificationController@getUnreadCount');
});
```

---

**🎉 Ready to use! Copy-paste the @include() and you're done.**
