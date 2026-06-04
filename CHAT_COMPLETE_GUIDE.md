# 📖 Complete Chat System Implementation Guide

## ✨ Overview

Sistem chat terintegrasi untuk Korlap (Tim Lapangan) yang memungkinkan komunikasi real-time dengan Admin dan Vendor. Fitur utama:

- ✅ **Dashboard Chat Terbaru** - Preview 5 konversasi terbaru dengan unread badge
- ✅ **Full Chat Interface** - Layout profesional dengan AJAX message loading
- ✅ **Auto Mark as Read** - Pesan otomatis ditandai baca saat dibuka
- ✅ **Avatar Inisial** - 2 huruf pertama dari nama user (AD, FD, MU)
- ✅ **Responsive Design** - Mobile, tablet, desktop siap pakai
- ✅ **Floral Theme** - Konsisten dengan emerald palette
- ✅ **Timestamp Smart** - Format dinamis (09:15, Kemarin, 3 Hari lalu, dll)

---

## 🗂️ Struktur File

```
/app
  /Http/Controllers/Lapangan
    - ChatController.php (DIPERBAIKI)
    - DashboardController.php (OPTIMIZED)
    
/database/migrations
  - 2026_05_26_120001_create_chat_messages_table.php
  - 2026_05_30_create_messages_table.php (adds sender_id, receiver_id, is_read, booking_id)

/resources/views/lapangan/modules
  - dashboard.blade.php (sudah ada, lines 293-327 = Chat Terbaru section)
  - chat/
    - index.blade.php (BARU - full chat interface)

/app/Models
  - ChatMessage.php (sudah ada dengan relation & scopes)
  
/routes
  - web.php (sudah ada routes untuk chat)
```

---

## 📊 Database Schema

### Tabel: `chat_messages`

| Kolom | Tipe | Nullable | Keterangan |
|-------|------|----------|-----------|
| id | BIGINT | ❌ | Primary Key |
| pesanan_id | BIGINT | ✅ | FK to pesanans |
| booking_id | BIGINT | ✅ | FK to pesanans (alias) |
| user_id | BIGINT | ✅ | FK to users (legacy) |
| sender_id | BIGINT | ❌ | FK to users (pengirim) |
| receiver_id | BIGINT | ❌ | FK to users (penerima) |
| pesan | TEXT | ❌ | Isi pesan |
| dari_admin | BOOLEAN | ❌ | Default: false |
| is_read | BOOLEAN | ❌ | Default: false |
| created_at | TIMESTAMP | ❌ | Auto |
| updated_at | TIMESTAMP | ❌ | Auto |

**Indices:**
- `sender_id, receiver_id` (search conversations)
- `receiver_id, is_read` (find unread messages)
- `created_at` (sort by latest)

**Foreign Keys:**
- sender_id → users(id) CASCADE DELETE
- receiver_id → users(id) CASCADE DELETE
- pesanan_id → pesanans(id) CASCADE DELETE
- booking_id → pesanans(id) CASCADE DELETE

---

## 🎯 Controller Methods

### `ChatController@index()`
**Purpose**: Load chat page dengan list konversasi
```php
// URL: lapangan/chat
// Returns: view('lapangan.modules.chat.index')
// Data passed:
//   - conversations (Collection of contacts with last message)
//   - activeConversation (First contact)
//   - messages (Messages with first contact)
//   - currentUser (Auth user)
```

### `ChatController@sendMessage()`
**Purpose**: Kirim pesan baru
```php
// URL: lapangan/chat/send (POST)
// Request: { receiver_id, pesan, pesanan_id? }
// Returns: JSON
// {
//   "success": true,
//   "message": "Pesan terkirim",
//   "data": {
//     "id": 1,
//     "text": "...",
//     "time": "09:15",
//     "type": "sent",
//     "is_read": false
//   }
// }

$message = ChatMessage::create([
    'sender_id' => auth()->id(),       // Auto-filled
    'receiver_id' => $validated['receiver_id'],
    'pesan' => $validated['pesan'],
    'is_read' => false
]);
```

### `ChatController@getConversation()`
**Purpose**: Load pesan dengan contact tertentu
```php
// URL: lapangan/chat/conversation/{contact} (GET)
// Returns: JSON
// {
//   "success": true,
//   "messages": [...],
//   "contact": {
//     "id": 1,
//     "name": "Adi Admin",
//     "role": "Admin",
//     "avatar": "AA"
//   }
// }

// Also: Mark as read automatically
ChatMessage::where('sender_id', $contact->id)
    ->where('receiver_id', auth()->id())
    ->where('is_read', false)
    ->update(['is_read' => true]);
```

### `ChatController@markAsRead()`
**Purpose**: Manually mark messages as read
```php
// URL: lapangan/chat/mark-as-read (POST)
// Request: { contact_id }
// Returns: JSON { "success": true }
```

### `ChatController@deleteMessage()`
**Purpose**: Delete pesan (hanya pengirim)
```php
// URL: lapangan/chat/message/{message} (DELETE)
// Returns: JSON { "success": true }

if ($message->sender_id !== auth()->id()) {
    return response()->json(['success' => false], 403);
}
```

### `DashboardController@getLatestConversations()`
**Purpose**: Optimized query untuk 5 konversasi terbaru
```php
// Called in: DashboardController@index()
// Returns: Collection

// Optimized dengan groupBy
$contactIds = ChatMessage::where(...)
    ->selectRaw('CASE WHEN sender_id = ? THEN receiver_id ELSE sender_id END as contact_id, MAX(id) as last_message_id')
    ->groupBy('contact_id')
    ->orderByDesc('last_message_id')
    ->limit(5)
    ->pluck('contact_id');

// Build conversation data
foreach ($contactIds as $contactId) {
    // Get contact, last message, unread count
}
```

---

## 🎨 UI Components

### 1. Dashboard "CHAT TERBARU" Section

**Location**: `resources/views/lapangan/modules/dashboard.blade.php` lines 286-328

```html
<div class="col-span-12 lg:col-span-4">
    <div class="rounded-[28px] border border-white/60 bg-white/85 p-6 ...">
        <div class="flex items-center justify-between mb-5">
            <h2 class="text-base font-semibold text-slate-900">Chat Terbaru</h2>
            <a href="{{ route('lapangan.chat') }}" class="text-xs font-semibold text-emerald-700">
                Lihat semua
            </a>
        </div>
        
        <div class="space-y-3">
            @forelse($chatTerbaru as $chat)
            <a href="{{ route('lapangan.chat') }}" class="flex items-start gap-3 rounded-3xl ...">
                <!-- Avatar -->
                <div class="flex h-12 w-12 items-center justify-center rounded-full 
                           bg-gradient-to-br from-emerald-100 to-emerald-50 
                           text-emerald-700 font-bold text-sm shadow-sm">
                    {{ $chat['avatar_initials'] }}  <!-- AD, FD, etc -->
                </div>
                
                <!-- Content -->
                <div class="min-w-0 flex-1">
                    <div class="flex items-center justify-between gap-2 mb-1">
                        <p class="truncate text-sm font-semibold">{{ $chat['nama'] }}</p>
                        @if($chat['unread_count'] > 0)
                        <span class="inline-flex h-6 min-w-[1.5rem] items-center justify-center 
                                   rounded-full bg-emerald-600 px-2 
                                   text-[11px] font-semibold text-white">
                            {{ $chat['unread_count'] }}
                        </span>
                        @endif
                    </div>
                    <p class="text-xs text-slate-400 mb-2">{{ $chat['waktu_terakhir'] }}</p>
                    <p class="text-sm text-slate-600 line-clamp-2">{{ $chat['pesan_terakhir'] }}</p>
                </div>
            </a>
            @empty
            <p class="text-center text-sm text-slate-500 py-10">Belum ada pesan.</p>
            @endforelse
        </div>
    </div>
</div>
```

**Data yang dikirim dari controller**:
```php
$chatTerbaru = collect([
    [
        'id' => 1,
        'contact_id' => 5,
        'nama' => 'Adi Admin',
        'role' => 'Admin',
        'avatar_initials' => 'AA',
        'pesan_terakhir' => 'Mohon update progress persiapan acara...',
        'waktu_terakhir' => '09:15',
        'unread_count' => 2,
        'is_online' => true
    ],
    // ... more contacts
])
```

### 2. Full Chat Page

**Location**: `resources/views/lapangan/modules/chat/index.blade.php`

**Layout Grid**:
```
┌─ Mobile ─────────┐  ┌─ Tablet (md+) ────┐  ┌─ Desktop (xl+) ────────┐
│                  │  │ Sidebar  │ Chat   │  │ Sidebar │     Chat      │
│      Chat        │  │ (25%)    │ (75%)  │  │ (25%)   │     (75%)     │
│                  │  │          │        │  │         │               │
└──────────────────┘  └──────────┴────────┘  └─────────┴───────────────┘
```

**Key Features**:
- Left sidebar: Conversations list dengan search
- Right main: Messages dan input form
- Auto-scroll saat pesan baru
- Responsive message bubbles
- Floral theme dengan emerald colors

---

## 🔄 Request/Response Flow

### Scenario 1: Korlap Buka Dashboard

```
1. GET /lapangan/dashboard
   ↓
2. DashboardController@index()
   ├─ $chatTerbaru = $this->getLatestConversations()
   │  ├─ Query contact_id dengan MAX(id) grouping
   │  ├─ Limit 5 conversations
   │  ├─ Count unread per contact
   │  └─ Format timestamp
   │
   └─ return view('lapangan.modules.dashboard', [
        'chatTerbaru' => $chatTerbaru,
        ...
      ])

3. Dashboard renders with Chat Terbaru section
   - Avatar: AD, FD, MU, etc (2 letters)
   - Badge: Green #10b981 if unread_count > 0
   - Time: 09:15, Kemarin, 3 Hari lalu, etc
   - Preview: Last message truncated to 2 lines
```

### Scenario 2: Korlap Klik "Chat Terbaru" di Dashboard

```
1. Click "Lihat semua" link
   ↓
2. GET /lapangan/chat
   ↓
3. ChatController@index()
   ├─ $conversations = $this->getConversationsForCurrentUser()
   │  └─ Return all conversations with last message & unread count
   │
   ├─ $activeConversation = $conversations->first() ?? null
   │
   ├─ $messages = $activeConversation ? $this->getMessagesWithContact(...) : []
   │
   └─ return view('lapangan.modules.chat.index', [
        'conversations' => $conversations,
        'activeConversation' => $activeConversation,
        'messages' => $messages
      ])

4. Chat page renders with:
   - Left sidebar: All conversations
   - Right main: First conversation messages
   - Input box ready to type
```

### Scenario 3: Korlap Kirim Pesan

```
1. User type message in textarea
   ↓
2. Click send button OR press Ctrl+Enter
   ↓
3. AJAX POST /lapangan/chat/send
   {
     "receiver_id": 5,
     "pesan": "Mohon update status vendor...",
     "pesanan_id": null
   }
   ↓
4. ChatController@sendMessage()
   ├─ Validate input
   ├─ Create ChatMessage:
   │  {
   │    sender_id: auth()->id(),         // Korlap ID
   │    receiver_id: 5,                   // Admin ID
   │    pesan: "...",
   │    is_read: false,
   │    created_at: now()
   │  }
   │
   └─ return response()->json([
        'success' => true,
        'data' => [
          'id' => 1,
          'text' => '...',
          'time' => '14:30',
          'type' => 'sent'
        ]
      ])

5. JavaScript append message to UI
   ├─ Create message bubble
   ├─ Add to messagesContainer
   ├─ Auto-scroll to bottom
   └─ Clear textarea & reset height
```

### Scenario 4: Korlap Klik Contact di Sidebar

```
1. Click contact button
   ↓
2. AJAX GET /lapangan/chat/conversation/{contact_id}
   ↓
3. ChatController@getConversation()
   ├─ Mark ALL messages from {contact_id} to current user as read:
   │  ChatMessage::where('sender_id', contact_id)
   │    ->where('receiver_id', auth()->id())
   │    ->update(['is_read' => true])
   │
   ├─ Query all messages between current user & contact
   │
   └─ return response()->json([
        'success' => true,
        'messages' => [
          { id, text, time, type, is_read },
          ...
        ],
        'contact' => {
          id, name, role, avatar
        }
      ])

4. JavaScript update UI
   ├─ Clear messagesContainer
   ├─ Append all messages
   ├─ Auto-scroll to bottom
   └─ Update receiver_id hidden field
```

### Scenario 5: Admin Kirim Pesan ke Korlap (Other Panel)

```
1. Admin di panel admin buka chat Korlap
   ↓
2. Admin kirim pesan:
   {
     sender_id: 1 (admin),
     receiver_id: 3 (korlap),
     pesan: "Status vendor sudah dikonfirmasi",
     is_read: false
   }
   ↓
3. Message saved ke DB
   ↓
4. Korlap di dashboard polling (jika enable)
   OR
   Korlap di chat page polling
   ↓
5. New message detected
   - If dashboard: Badge count update
   - If chat page: Message auto-appear
```

---

## 🛠️ Installation & Setup

### Step 1: Ensure Database Migrations Ran

```bash
# Check if chat_messages table exists
php artisan migrate

# If kolom sender_id, receiver_id, is_read belum ada, migration akan menambahkannya
```

### Step 2: Verify Model Relationships

`app/Models/ChatMessage.php` sudah memiliki:
- `sender()` - BelongsTo User
- `receiver()` - BelongsTo User
- `pesanan()` - BelongsTo Pesanan
- `scopeUnread()` - Get unread messages
- `scopeBetweenUsers()` - Get messages between two users
- `scopeLatestPerContact()` - Get latest message per contact

### Step 3: Test Routes

```bash
# Test dashboard
GET /lapangan/dashboard

# Test chat page
GET /lapangan/chat

# Test API endpoints
POST /lapangan/chat/send
GET /lapangan/chat/conversation/{contact_id}
POST /lapangan/chat/mark-as-read
```

### Step 4: Test in Browser

1. **Login as Korlap** (role = 'lapangan')
2. **Go to Dashboard** - Check Chat Terbaru section
3. **Click "Lihat semua"** - Open chat page
4. **Send message** - Test AJAX functionality
5. **Check unread badge** - Should update
6. **Test with another account** - Send message back to Korlap

---

## 🧪 Testing Scenarios

### Test 1: Avatar Initials Generation
```php
// Run in tinker: php artisan tinker

// Test getInitials() method
app(ChatController::class)->getInitials('Adi Dharmawan')      // AD
app(ChatController::class)->getInitials('Muhammad Usaid')     // MU
app(ChatController::class)->getInitials('Feni Dwisaputro')   // FD
```

### Test 2: Timestamp Formatting
```php
// Test at different times
$now = now();
$oneHourAgo = now()->subHour();
$yesterday = now()->subDay();
$threeDaysAgo = now()->subDays(3);
$monthAgo = now()->subMonth();

// Should output:
// 09:15 (today)
// Kemarin (yesterday)
// 3 Hari lalu (3 days ago)
// 25 May (older)
```

### Test 3: Unread Count Accuracy
```php
// Scenario:
// 1. Korlap (ID 3) receives 2 messages from Admin (ID 1)
// 2. Mark message 1 as read (is_read = true)
// 3. Message 2 still unread (is_read = false)
// 4. Expected unread_count = 1

$unreadCount = ChatMessage::where('sender_id', 1)
    ->where('receiver_id', 3)
    ->where('is_read', false)
    ->count();
// Should be 1
```

### Test 4: Message Ordering
```php
// Messages should be ordered by created_at ASC
$messages = ChatMessage::where(...)
    ->orderBy('created_at')
    ->get();
// First message = oldest
// Last message = newest
```

### Test 5: Conversation Deduplication
```php
// If both sent and received messages from same contact,
// Should appear only ONCE in conversations list
// With latest message timestamp, not duplicated

$conversations = $this->getConversationsForCurrentUser();
// Should not have duplicate contact_id
```

---

## 🚨 Troubleshooting

### Issue: Avatar shows "??" instead of initials
**Cause**: User name is empty or null
**Fix**:
```php
// In controller
if (!$contact || !trim($contact->name)) {
    continue; // Skip invalid contacts
}

$initials = $this->getInitials($contact->name ?? 'Unknown');
```

### Issue: Badge shows wrong unread count
**Cause**: Not checking is_read correctly or not marking as read
**Fix**:
```php
// When loading conversation, auto-mark as read
ChatMessage::where('sender_id', $contact->id)
    ->where('receiver_id', auth()->id())
    ->where('is_read', false)          // IMPORTANT: check is_read = false
    ->update(['is_read' => true]);
```

### Issue: Messages not appearing in order
**Cause**: OrderBy not used in query
**Fix**:
```php
$messages = ChatMessage::where(...)
    ->orderBy('created_at')            // IMPORTANT: sort by created_at
    ->get();
```

### Issue: Same message appears twice
**Cause**: Appending message in AJAX before response confirmed
**Fix**:
```js
// Send message
fetch(...).then(response => {
    if (response.success) {
        // ONLY append after confirmation
        const messageEl = document.createElement('div');
        messageEl.innerHTML = response.data.text;
        container.appendChild(messageEl);
    }
});
```

### Issue: Chat slow / laggy
**Cause**: Loading too many messages at once
**Fix**:
```php
// Add pagination or limit
$messages = ChatMessage::where(...)
    ->latest()
    ->limit(50)              // Load only 50 latest
    ->get()
    ->reverse();             // Reverse for proper order
```

---

## 📱 Browser Compatibility

| Browser | Mobile | Tablet | Desktop |
|---------|--------|--------|---------|
| Chrome | ✅ | ✅ | ✅ |
| Firefox | ✅ | ✅ | ✅ |
| Safari | ✅ | ✅ | ✅ |
| Edge | ✅ | ✅ | ✅ |

All modern browsers with ES6 support.

---

## 🎯 Performance Metrics

| Operation | Expected Time |
|-----------|--------------|
| Load dashboard | < 100ms |
| Load chat page | < 150ms |
| Send message | < 200ms |
| Load conversation | < 100ms |
| Search contacts | < 50ms |

Use DevTools Network tab to verify.

---

## 📚 Additional Resources

- **Laravel Eloquent**: https://laravel.com/docs/eloquent
- **Blade Templates**: https://laravel.com/docs/blade
- **Tailwind CSS**: https://tailwindcss.com
- **AJAX Best Practices**: https://mdn.io/fetch

---

**Status**: ✅ PRODUCTION READY
**Last Updated**: 2026-05-30
**Version**: 1.0
