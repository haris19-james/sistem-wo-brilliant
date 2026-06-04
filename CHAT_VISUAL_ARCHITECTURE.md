# 📊 VISUAL CHAT SYSTEM ARCHITECTURE

## System Overview Diagram

```
┌─────────────────────────────────────────────────────────────────┐
│                      KORLAP CHAT SYSTEM                         │
├─────────────────────────────────────────────────────────────────┤
│                                                                  │
│  ┌───────────────────┐              ┌───────────────────┐      │
│  │  FRONTEND (UI)    │              │   BACKEND (API)   │      │
│  ├───────────────────┤              ├───────────────────┤      │
│  │ ┌───────────────┐ │              │ ┌───────────────┐ │      │
│  │ │  Dashboard    │ │──AJAX GET──→ │ │  Dashboard    │ │      │
│  │ │  Chat Terbaru │ │              │ │  Controller   │ │      │
│  │ └───────────────┘ │              │ └───────────────┘ │      │
│  │                   │              │         ↓          │      │
│  │ ┌───────────────┐ │              │ getLatest         │      │
│  │ │  Chat Page    │ │──AJAX POST→ │ Conversations()   │      │
│  │ │  Full View    │ │              │         ↓          │      │
│  │ └───────────────┘ │              │ ┌───────────────┐ │      │
│  │    │              │              │ │  Chat         │ │      │
│  │    ├─ Sidebar     │              │ │  Controller   │ │      │
│  │    │  (Contacts)  │              │ └───────────────┘ │      │
│  │    │              │              │  │        │       │      │
│  │    ├─ Main        │              │  ├─ send         │      │
│  │    │  (Messages)  │              │  ├─ get          │      │
│  │    │              │              │  ├─ markRead     │      │
│  │    └─ Input       │              │  └─ delete       │      │
│  │       (Textarea)  │              │                  │      │
│  │                   │              └──────────────────┘      │
│  └───────────────────┘                                         │
│         ↓                                                       │
│  ┌───────────────────────────────────────────────────────────┐│
│  │         JAVASCRIPT (AJAX, Event Listeners)                ││
│  │                                                           ││
│  │  Functions:                                             ││
│  │  - loadConversation(contactId)                          ││
│  │  - sendMessage(receiverId, pesan)                       ││
│  │  - markAsRead(contactId)                                ││
│  │  - scrollToBottom()                                     ││
│  │  - searchContacts(term)                                 ││
│  └───────────────────────────────────────────────────────────┘
│                          ↓
│  ┌───────────────────────────────────────────────────────────┐
│  │            DATABASE (MySQL)                              │
│  │                                                           │
│  │  chat_messages TABLE:                                    │
│  │  ┌────────────────────────────────────────────────┐     │
│  │  │ id  │sender_id│receiver_id│pesan│is_read│created_at││
│  │  ├────┼─────────┼───────────┼──────┼────────┼──────────┤│
│  │  │ 1  │   3     │     1     │ ...  │   0   │ 2026-05-30││
│  │  │ 2  │   1     │     3     │ ...  │   1   │ 2026-05-30││
│  │  │ 3  │   3     │     5     │ ...  │   0   │ 2026-05-30││
│  │  └────┴─────────┴───────────┴──────┴────────┴──────────┘│
│  │                                                           │
│  │  Indexes:                                               │
│  │  - (sender_id, receiver_id)                            │
│  │  - (receiver_id, is_read)                              │
│  │  - (created_at)                                        │
│  └───────────────────────────────────────────────────────────┘
│
└─────────────────────────────────────────────────────────────────┘
```

## Data Flow Diagram

### Message Send Flow
```
┌─────────────────────────────────────────────────────────────┐
│               USER SENDS MESSAGE                            │
├─────────────────────────────────────────────────────────────┤
│                                                             │
│  1. Type in textarea                                       │
│     └─ max 2000 chars                                     │
│                                                             │
│  2. Click send button                                      │
│     └─ textarea value captured                            │
│                                                             │
│  3. AJAX POST /lapangan/chat/send                         │
│     │                                                       │
│     └─ JSON payload:                                       │
│        {                                                   │
│          "receiver_id": 5,                                │
│          "pesan": "Mohon update status...",              │
│          "_token": "CSRF_TOKEN"                          │
│        }                                                   │
│                                                             │
│  4. ChatController@sendMessage()                          │
│     │                                                       │
│     ├─ Validate input                                      │
│     │  └─ receiver_id exists in users                     │
│     │  └─ pesan not empty, max 2000                       │
│     │  └─ auth check (sender !== receiver)               │
│     │                                                       │
│     ├─ Create ChatMessage                                  │
│     │  └─ INSERT into chat_messages:                      │
│     │     ├─ sender_id = auth()->id() (Korlap)           │
│     │     ├─ receiver_id = 5 (Admin)                      │
│     │     ├─ pesan = input message                        │
│     │     ├─ is_read = false                              │
│     │     ├─ created_at = now()                           │
│     │     └─ updated_at = now()                           │
│     │                                                       │
│     └─ Return JSON:                                        │
│        {                                                   │
│          "success": true,                                 │
│          "data": {                                        │
│            "id": 42,                                      │
│            "text": "Mohon update...",                     │
│            "time": "14:30",                               │
│            "type": "sent",                                │
│            "is_read": false                               │
│          }                                                │
│        }                                                   │
│                                                             │
│  5. JavaScript receives response                           │
│     │                                                       │
│     ├─ Create message element                              │
│     │  └─ <div class="flex justify-end">                 │
│     │     └─ <div class="bg-emerald-600 rounded-3xl...">│
│     │        └─ <p>Mohon update...</p>                   │
│     │        └─ <p class="text-xs">14:30</p>             │
│     │                                                       │
│     ├─ Append to messagesContainer                        │
│     ├─ Auto-scroll to bottom                              │
│     ├─ Clear textarea                                      │
│     └─ Reset height to 1 row                               │
│                                                             │
│  6. Message visible to user                               │
│     └─ Appears on right side (sent bubble)                │
│                                                             │
│  7. Admin/Vendor sees message                             │
│     └─ In their panel (at next polling/refresh)           │
│                                                             │
└─────────────────────────────────────────────────────────────┘
```

### Message Receive & Read Flow
```
┌──────────────────────────────────────────────────────────┐
│          ADMIN SENDS MESSAGE TO KORLAP                 │
├──────────────────────────────────────────────────────────┤
│                                                          │
│  1. Admin sends message in their panel                 │
│     └─ ChatMessage created:                            │
│        {                                               │
│          sender_id: 1,    (Admin)                      │
│          receiver_id: 3,  (Korlap)                     │
│          pesan: "Status vendor sudah...",             │
│          is_read: false                                │
│        }                                               │
│                                                          │
│  2. Korlap opens dashboard                             │
│     └─ GET /lapangan/dashboard                        │
│                                                          │
│  3. DashboardController@index()                        │
│     └─ getLatestConversations()                        │
│        │                                                │
│        ├─ Query latest message per contact             │
│        ├─ Count unread (is_read = false)               │
│        │  └─ SELECT COUNT(*) FROM chat_messages       │
│        │     WHERE sender_id = 1 AND               │
│        │     receiver_id = 3 AND is_read = false     │
│        │     Result: 1 (one unread)                   │
│        │                                                │
│        ├─ Generate avatar initials (AA)               │
│        ├─ Format timestamp (09:15, Kemarin, etc)      │
│        └─ Return Collection:                          │
│           {                                            │
│             contact_id: 1,                            │
│             nama: "Adi Admin",                         │
│             avatar_initials: "AA",                    │
│             pesan_terakhir: "Status vendor...",       │
│             waktu_terakhir: "09:15",                  │
│             unread_count: 1                           │
│           }                                            │
│                                                          │
│  4. Dashboard renders Chat Terbaru                    │
│     └─ Show Admin with:                               │
│        ├─ Avatar: AA (emerald gradient)                │
│        ├─ Badge: 1 (green bg-emerald-600)             │
│        ├─ Time: 09:15                                  │
│        └─ Preview: Status vendor sudah...             │
│                                                          │
│  5. Korlap sees badge and clicks chat                │
│     └─ Click on Admin contact or "Lihat semua"       │
│                                                          │
│  6. GET /lapangan/chat/conversation/{admin_id}        │
│                                                          │
│  7. ChatController@getConversation()                  │
│     │                                                   │
│     ├─ Mark messages as read:                         │
│     │  └─ UPDATE chat_messages                        │
│     │     SET is_read = true                          │
│     │     WHERE sender_id = 1 AND                     │
│     │     receiver_id = 3                             │
│     │     Result: 1 row updated                       │
│     │                                                   │
│     ├─ Query all messages between them               │
│     │  └─ ORDER BY created_at ASC                     │
│     │     Result: all 10 messages                     │
│     │                                                   │
│     └─ Return JSON with messages array                │
│                                                          │
│  8. JavaScript displays messages                      │
│     ├─ Old sent messages (right, emerald)             │
│     ├─ Received message from Admin (left, gray)       │
│     ├─ Auto-scroll to latest                          │
│     └─ Message marked as read in DB                   │
│                                                          │
│  9. Next dashboard refresh                            │
│     └─ Badge disappears (unread_count = 0)            │
│                                                          │
└──────────────────────────────────────────────────────────┘
```

## Component Structure

### Dashboard Chat Terbaru Card
```html
<div class="col-span-12 lg:col-span-4">
  <div class="rounded-[28px] border border-white/60 
              bg-white/85 p-6 shadow-xl">
    
    <!-- Header -->
    <div class="flex items-center justify-between mb-5">
      <h2 class="text-base font-semibold text-slate-900">
        Chat Terbaru
      </h2>
      <a href="{{ route('lapangan.chat') }}" 
         class="text-xs font-semibold text-emerald-700">
        Lihat semua →
      </a>
    </div>
    
    <!-- Conversation Items -->
    <div class="space-y-3">
      @foreach($chatTerbaru as $chat)
      
      <!-- Chat Item -->
      <a href="{{ route('lapangan.chat') }}" 
         class="flex items-start gap-3 rounded-3xl 
                border border-slate-200/70 
                bg-white p-4 hover:bg-emerald-50">
        
        <!-- Avatar with Initials -->
        <div class="flex h-12 w-12 items-center 
                    justify-center rounded-full 
                    bg-gradient-to-br from-emerald-100 to-emerald-50 
                    text-emerald-700 font-bold text-sm shadow-sm">
          {{ $chat['avatar_initials'] }}
          <!-- AD, FD, MU, etc -->
        </div>
        
        <!-- Chat Info -->
        <div class="min-w-0 flex-1">
          
          <!-- Header Row: Name + Badge -->
          <div class="flex items-center justify-between gap-2 mb-1">
            <p class="truncate text-sm font-semibold 
                      text-slate-900">
              {{ $chat['nama'] }}
            </p>
            
            <!-- Unread Badge (if exists) -->
            @if($chat['unread_count'] > 0)
            <span class="inline-flex h-6 min-w-[1.5rem] 
                         items-center justify-center 
                         rounded-full bg-emerald-600 px-2 
                         text-[11px] font-semibold text-white 
                         shadow-sm">
              {{ $chat['unread_count'] }}
            </span>
            @endif
          </div>
          
          <!-- Role -->
          <p class="text-xs text-slate-500 font-medium 
                    whitespace-nowrap">
            {{ $chat['role'] }}
          </p>
          
          <!-- Time -->
          <p class="text-xs text-slate-400 mb-2">
            {{ $chat['waktu_terakhir'] }}
          </p>
          
          <!-- Message Preview (2 lines max) -->
          <p class="text-sm text-slate-600 line-clamp-2">
            {{ $chat['pesan_terakhir'] }}
          </p>
        </div>
      </a>
      
      @endforeach
    </div>
  </div>
</div>
```

### Chat Page Layout
```html
<div class="grid gap-4 xl:grid-cols-4 h-[calc(100vh-200px)]">
  
  <!-- Sidebar: Conversations (1 column = 25%) -->
  <div class="xl:col-span-1 rounded-[28px] 
              border border-white/60 bg-white/85 
              p-4 flex flex-col">
    
    <!-- Search -->
    <input type="text" id="searchContact" 
           placeholder="Cari kontak..." 
           class="w-full px-4 py-2 rounded-3xl border 
                  border-slate-200/70 mb-4">
    
    <!-- Conversation List (scrollable) -->
    <div class="space-y-2 overflow-y-auto flex-1" 
         id="conversationList">
      
      <!-- Each Contact -->
      <button onclick="loadConversation(5, 'Admin')">
        <div class="flex h-12 w-12 items-center 
                    justify-center rounded-full 
                    bg-gradient-to-br from-emerald-100 
                    to-emerald-50 text-emerald-700 
                    font-bold text-sm">
          AA  <!-- Avatar -->
        </div>
        <div>
          <p>Adi Admin</p>
          <p class="text-xs text-slate-500">Admin</p>
          <p class="text-xs text-slate-400 truncate">
            Last message preview...
          </p>
          <!-- Badge if unread -->
          <span class="bg-emerald-600 text-white 
                       rounded-full text-[10px]">2</span>
        </div>
      </button>
      
    </div>
  </div>
  
  <!-- Main: Messages (3 columns = 75%) -->
  <div class="xl:col-span-3 rounded-[28px] 
              border border-white/60 bg-white/85 
              flex flex-col">
    
    <!-- Header -->
    <div class="border-b border-slate-200/70 p-6 
                bg-gradient-to-r from-white to-emerald-50">
      <div class="flex items-center gap-4">
        <div class="flex h-14 w-14 items-center 
                    justify-center rounded-full 
                    bg-gradient-to-br from-emerald-100 
                    to-emerald-50 text-emerald-700 
                    font-bold">
          AA
        </div>
        <div>
          <h3 class="text-base font-semibold 
                     text-slate-900">Adi Admin</h3>
          <p class="text-xs text-slate-500">Admin</p>
        </div>
      </div>
    </div>
    
    <!-- Messages (scrollable) -->
    <div class="flex-1 overflow-y-auto p-6 space-y-4 
                bg-gradient-to-b from-white 
                to-emerald-50/30" id="messagesContainer">
      
      <!-- Sent Message (Right) -->
      <div class="flex justify-end">
        <div class="rounded-3xl rounded-tr-lg 
                    bg-emerald-600 text-white 
                    shadow-lg shadow-emerald-200 
                    px-5 py-3 max-w-xs">
          <p class="text-sm">Mohon update status vendor...</p>
          <p class="mt-1 text-xs text-emerald-100">14:30</p>
        </div>
      </div>
      
      <!-- Received Message (Left) -->
      <div class="flex justify-start">
        <div class="rounded-3xl rounded-tl-lg 
                    bg-slate-100 text-slate-900 
                    shadow-sm px-5 py-3 max-w-xs">
          <p class="text-sm">Status sudah dikonfirmasi</p>
          <p class="mt-1 text-xs text-slate-500">15:45</p>
        </div>
      </div>
      
    </div>
    
    <!-- Input -->
    <div class="border-t border-slate-200/70 p-4 
                bg-white">
      <form id="messageForm" class="flex gap-3 items-end">
        <textarea id="messageInput" 
                  name="pesan" 
                  maxlength="2000"
                  placeholder="Ketik pesan..." 
                  class="flex-1 px-4 py-3 rounded-3xl 
                         border border-slate-200/70 
                         bg-white resize-none"></textarea>
        <button type="submit" 
                class="flex h-12 w-12 items-center 
                       justify-center rounded-full 
                       bg-emerald-600 text-white 
                       shadow-lg shadow-emerald-200">
          ➤
        </button>
      </form>
    </div>
  </div>
  
</div>
```

## Query Performance

### Dashboard Latest Conversations
```sql
-- Optimized query untuk 5 konversasi terbaru
SELECT 
  CASE 
    WHEN sender_id = 3 THEN receiver_id
    ELSE sender_id 
  END as contact_id,
  MAX(id) as last_message_id
FROM chat_messages
WHERE sender_id = 3 OR receiver_id = 3
GROUP BY contact_id
ORDER BY last_message_id DESC
LIMIT 5;

-- Result:
-- contact_id | last_message_id
-- 1          | 42
-- 5          | 39
-- 8          | 35
-- 2          | 32
-- 7          | 28

-- Then for each contact:
SELECT COUNT(*) FROM chat_messages
WHERE sender_id = {contact_id} AND receiver_id = 3 AND is_read = 0;
-- Result: unread_count
```

### Conversation Messages
```sql
-- Load all messages between Korlap (3) and Admin (1)
SELECT * FROM chat_messages
WHERE (sender_id = 3 AND receiver_id = 1)
   OR (sender_id = 1 AND receiver_id = 3)
ORDER BY created_at ASC;

-- Result:
-- id | sender_id | receiver_id | pesan           | is_read | created_at
-- 1  | 1         | 3           | Welcome...      | 1       | 2026-05-30 09:00
-- 2  | 3         | 1           | Thanks...       | 1       | 2026-05-30 09:15
-- 3  | 1         | 3           | Status?         | 0       | 2026-05-30 10:00
-- 4  | 3         | 1           | Will update...  | 0       | 2026-05-30 10:30
-- ...
```

## State Management

### Message State in UI
```javascript
// Current state of messages in container
const messages = [
  {
    id: 1,
    text: "Mohon update status...",
    time: "14:30",
    type: "sent",      // 'sent' or 'received'
    is_read: false
  },
  {
    id: 2,
    text: "Status sudah dikonfirmasi",
    time: "15:45",
    type: "received",
    is_read: false     // Will update after page load
  },
  // ...
];

// After mark as read
messages[1].is_read = true;  // But UI doesn't show this
```

### Conversation State
```javascript
// Selected contact
const activeContact = {
  id: 1,
  name: "Adi Admin",
  role: "Admin",
  avatar: "AA",
  unreadCount: 1
};

// Before clicking: badge shows "1"
// After clicking: mark as read AJAX fires
// Result: next refresh, badge is gone
```

---

## CSS Classes Reference

### Colors (Emerald Palette)
```
emerald-50      #f0fdf4
emerald-100     #dcfce7
emerald-600     #16a34a (badge background)
emerald-700     #15803d (text color)
emerald-200     #bbf7d0 (subtle backgrounds)
```

### Border Radius
```
rounded-[28px]  Card borders
rounded-3xl     Buttons, inputs, message bubbles
rounded-full    Avatar circles, badges
rounded-tr-lg   Sent message corner (no top-right)
rounded-tl-lg   Received message corner (no top-left)
```

### Shadows
```
shadow-sm       Light shadows (dashboard items)
shadow-lg       Medium shadows (message bubbles)
shadow-xl       Heavy shadows (cards)
shadow-emerald-200  Emerald tinted shadow (badges)
```

### Responsive Prefixes
```
xl:col-span-1   Desktop: 1 column width
lg:col-span-4   Laptop: 4 column width
md:              Tablet and up
sm:              Mobile and up
```

---

**Architecture Version**: 1.0
**Last Updated**: 2026-05-30
**Status**: ✅ PRODUCTION READY
