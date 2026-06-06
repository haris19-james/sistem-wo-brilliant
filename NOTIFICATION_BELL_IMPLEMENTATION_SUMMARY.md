# Notification Bell Implementation - Codebase Overview

## 1. NOTIFICATION BELL COMPONENT

### File Location
**[resources/views/components/notification-bell.blade.php](resources/views/components/notification-bell.blade.php)**

### Key Features
- **Alpine.js Integration**: Uses `x-data="notificationBell()"` for state management
- **Props**: 
  - `align` (default: 'right') - Controls dropdown position
  - `$bellUnreadCount` - Number of unread notifications
  - `$bellNotifications` - Collection of notification items
- **Visual Elements**:
  - Bell icon with red unread count badge
  - Dropdown panel with max height (`max-h-72`)
  - Divider between notification items (`divide-y`)
  - Shows "Belum ada notifikasi" when empty
  - Visual indicator (green dot) for unread items

### Alpine.js Functions
```javascript
notificationBell() {
  - open (boolean): controls dropdown visibility
  - unreadCount (number): tracks unread count
  - refreshList(): fetches fresh notifications from API
  - openItem(id, link): marks notification as read and redirects
  - markAllRead(): marks all notifications as read and reloads page
}
```

### API Calls Made
1. `GET {{ route('notifications.index') }}` - Fetch notifications list
2. `PATCH /notifications/{id}/read` - Mark single notification as read
3. `POST {{ route('notifications.read-all') }}` - Mark all as read

---

## 2. NOTIFICATION API ENDPOINTS

### NotificationController (Real-time Polling API)
**File**: [app/Http/Controllers/NotificationController.php](app/Http/Controllers/NotificationController.php)

#### Endpoints
| Method | Route | Handler | Purpose |
|--------|-------|---------|---------|
| GET | `/api/notifications/poll` | `pollNotifications()` | Fetch latest unread notifications (up to 50) |
| GET | `/api/notifications/count` | `getUnreadCount()` | Quick badge count check (includes urgent count) |
| POST | `/api/notifications/{notification}/read` | `markRead()` | Mark single notification as read |
| POST | `/api/notifications/read-all` | `markAllRead()` | Mark all notifications as read |
| DELETE | `/api/notifications/{notification}` | `delete()` | Delete a notification |

#### Response Structure (pollNotifications)
```json
{
  "success": true,
  "unread_count": 5,
  "notifications": [
    {
      "id": 123,
      "message": "Notification message",
      "category": "booking|payment|refund|system",
      "priority": "normal|urgent",
      "link_redirect": "/path/to/resource",
      "reference_id": 1,
      "reference_type": "pesanan|payment|refund",
      "created_at": "2026-06-04T10:30:00Z",
      "is_urgent": false
    }
  ],
  "timestamp": "2026-06-04T10:31:00Z"
}
```

### NotificationCenterController (Main API)
**File**: [app/Http/Controllers/NotificationCenterController.php](app/Http/Controllers/NotificationCenterController.php)

Uses `NotificationCenterService` for business logic

#### Endpoints
| Method | Route | Handler | Purpose |
|--------|-------|---------|---------|
| GET | `/notifications` | `index()` | Fetch 20 latest notifications + unread count |
| PATCH | `/notifications/{notification}/read` | `markRead()` | Mark single notification as read |
| POST | `/notifications/read-all` | `markAllRead()` | Mark all notifications as read |

#### Response Structure (index)
```json
{
  "unread_count": 3,
  "notifications": [
    {
      "id": 123,
      "message": "Your booking has been confirmed",
      "is_read": false,
      "link_redirect": "/pesanan/123",
      "priority": "normal|urgent",
      "category": "booking|payment|refund|system",
      "time": "5 minutes ago"
    }
  ]
}
```

---

## 3. NOTIFICATION MODEL

**File**: [app/Models/UserNotification.php](app/Models/UserNotification.php)

### Database Fields
```php
protected $fillable = [
    'user_id',        // Foreign key to users table
    'role',           // User role
    'message',        // Notification text
    'is_read',        // Boolean flag
    'link_redirect',  // URL to redirect to
    'priority',       // 'normal' or 'urgent'
    'category',       // 'booking', 'payment', 'refund', 'system'
    'reference_id',   // ID of related resource
    'reference_type', // Type of related resource
];
```

### Methods
- `user()`: BelongsTo relationship to User
- `scopeUnread()`: Query scope for unread notifications
- `isUrgent()`: Returns true if priority === 'urgent'

### Casts
```php
'is_read' => 'boolean'
```

---

## 4. EXISTING DASHBOARD HEADER COMPONENT

**File**: [resources/views/components/dashboard-header.blade.php](resources/views/components/dashboard-header.blade.php)

### Props
```php
'title' => null
'notificationRoute' => route('notifications.index')
'unreadCount' => 0
```

### Integration Pattern
- Uses `data-notification-auto-poll` attribute for auto-polling
- Displays notification badge (red background)
- Integrates bell icon with notification route link
- Badge only shows when `unreadCount > 0`

### Structure
```blade
<div data-notification-auto-poll data-notification-route="{{ $notificationRoute }}">
  <a href="{{ $notificationRoute }}" class="relative">
    <!-- Bell Icon SVG -->
    <span id="notification-badge">{{ $unreadCount }}</span>
  </a>
</div>
```

---

## 5. MODAL/DROPDOWN PATTERNS IN CODEBASE

### Modal Component Example
**File**: [resources/views/components/booking/cancel-modal.blade.php](resources/views/components/booking/cancel-modal.blade.php)

#### Structure Pattern
```blade
<div id="{{ $modalId }}"
     class="fixed inset-0 z-[85] hidden items-center justify-center p-4"
     role="dialog"
     aria-modal="true"
     aria-labelledby="{{ $modalId }}-title">
  
  <!-- Backdrop -->
  <div class="absolute inset-0 bg-slate-900/45 backdrop-blur-sm"></div>
  
  <!-- Modal Content -->
  <div class="relative w-full max-w-lg bg-white rounded-2xl shadow-2xl">
    <!-- Header, Body, Footer -->
  </div>
</div>
```

#### Show/Hide Logic (Vanilla JS)
```javascript
// Show modal
document.querySelectorAll('[data-open-cancel-modal]').forEach(btn => {
  btn.addEventListener('click', () => {
    const id = btn.getAttribute('data-open-cancel-modal');
    const modal = document.getElementById('modal-cancel-booking-' + id);
    if (modal) {
      modal.classList.remove('hidden');
      modal.classList.add('flex');
    }
  });
});

// Hide modal
document.querySelectorAll('[data-close-cancel-modal]').forEach(el => {
  el.addEventListener('click', () => {
    const id = el.getAttribute('data-close-cancel-modal');
    const modal = document.getElementById('modal-cancel-booking-' + id);
    if (modal) {
      modal.classList.add('hidden');
      modal.classList.remove('flex');
    }
  });
});
```

---

## 6. ROUTE DEFINITIONS

**File**: [routes/web.php](routes/web.php) (lines 65-80)

```php
Route::middleware('auth')->group(function () {
    // Web Routes
    Route::get('/notifications', [NotificationCenterController::class, 'index'])
        ->name('notifications.index');
    Route::patch('/notifications/{notification}/read', [NotificationCenterController::class, 'markRead'])
        ->name('notifications.read');
    Route::post('/notifications/read-all', [NotificationCenterController::class, 'markAllRead'])
        ->name('notifications.read-all');

    // API Routes (Polling)
    Route::prefix('api/notifications')->group(function () {
        Route::get('/poll', [NotificationController::class, 'pollNotifications'])
            ->name('api.notifications.poll');
        Route::get('/count', [NotificationController::class, 'getUnreadCount'])
            ->name('api.notifications.count');
        Route::post('/{notification}/read', [NotificationController::class, 'markRead'])
            ->name('api.notifications.mark-read');
        Route::post('/read-all', [NotificationController::class, 'markAllRead'])
            ->name('api.notifications.read-all');
        Route::delete('/{notification}', [NotificationController::class, 'delete'])
            ->name('api.notifications.delete');
    });
});
```

---

## 7. TOAST/NOTIFICATION DISPLAY PATTERNS

**Files**: `resources/js/kanban-checklist.js`, `resources/js/jadwal-interactive.js`

### Basic Toast Pattern
```javascript
function showNotification(message, type = 'info') {
    const notification = document.createElement('div');
    
    // Set classes based on type
    const bgColor = type === 'success' ? 'bg-green-100 border-green-400 text-green-700'
                  : type === 'error'   ? 'bg-red-100 border-red-400 text-red-700'
                  : 'bg-blue-100 border-blue-400 text-blue-700';
    
    notification.className = `fixed top-4 right-4 ${bgColor} border px-4 py-3 rounded shadow-lg`;
    notification.textContent = message;
    document.body.appendChild(notification);
    
    // Auto-remove after 3 seconds
    setTimeout(() => {
        notification.style.opacity = '0';
        notification.style.transition = 'opacity 0.3s ease-out';
        setTimeout(() => notification.remove(), 300);
    }, 3000);
}
```

---

## 8. KEY INTEGRATION POINTS

### Notification Bell Component Used In:
- Dashboard header component
- Main dashboard layout

### Auto-Polling Attributes:
- `data-notification-auto-poll` - Triggers automatic polling
- `data-notification-route` - Sets the API endpoint

### Service Layer:
- `App\Services\NotificationCenterService` - Encapsulates notification logic

---

## SUMMARY

The codebase has a **mature notification system** with:

✅ **Notification Bell Component** - Alpine.js-based dropdown with badge
✅ **Dual API Approach** - Both JSON and HTML-based endpoints
✅ **Real-time Polling** - Built-in support via `/api/notifications/poll`
✅ **Modal Patterns** - Reusable backdrop + modal structure
✅ **Service Layer** - NotificationCenterService handles business logic
✅ **Toast Notifications** - Basic vanilla JS toast for inline feedback

This foundation is ready for **enhancement with dropdown modals** for notification detail views or bulk actions.
