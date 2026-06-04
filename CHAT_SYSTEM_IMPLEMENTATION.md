# Implementasi Sistem Chat Korlap - Dokumentasi Lengkap

## 📋 Ringkasan
Implementasi sistem chat yang memungkinkan komunikasi terintegrasi antara Admin, Vendor, dan Korlap (Tim Lapangan) dengan fitur:
- Pesan real-time dengan polling otomatis
- Dashboard chat terbaru dengan badge unread
- Inisial avatar dinamis
- Timestamp terformat (HH:mm, Kemarin, X hari lalu)
- Status baca pesan

## 📁 File-File yang Dimodifikasi/Dibuat

### 1. Migration & Database Schema
- **File**: `database/migrations/2026_05_26_120001_create_chat_messages_table.php`
- **Status**: Sudah ada, perlu validasi kolom

### 2. Model
- **File**: `app/Models/ChatMessage.php`
- **Status**: Sudah ada dengan relasi lengkap

### 3. Controller
- **File**: `app/Http/Controllers/Lapangan/ChatController.php`
- **File**: `app/Http/Controllers/Lapangan/DashboardController.php`

### 4. Routes
- **File**: `routes/web.php`
- **Status**: Sudah ada, validasi endpoint

### 5. Views
- **File**: `resources/views/lapangan/modules/dashboard.blade.php`
- **File**: `resources/views/lapangan/modules/chat/index.blade.php` (perlu dibuat)

## 🗄️ Struktur Tabel `chat_messages`

```sql
CREATE TABLE chat_messages (
    id BIGINT PRIMARY KEY AUTO_INCREMENT,
    pesanan_id BIGINT UNSIGNED NULLABLE (FK to pesanans),
    booking_id BIGINT UNSIGNED NULLABLE (FK to pesanans),
    user_id BIGINT UNSIGNED NULLABLE (FK to users),
    sender_id BIGINT UNSIGNED (FK to users),
    receiver_id BIGINT UNSIGNED (FK to users),
    pesan TEXT,
    dari_admin BOOLEAN DEFAULT FALSE,
    is_read BOOLEAN DEFAULT FALSE,
    created_at TIMESTAMP,
    updated_at TIMESTAMP,
    
    FOREIGN KEY (pesanan_id) REFERENCES pesanans(id) ON DELETE CASCADE,
    FOREIGN KEY (booking_id) REFERENCES pesanans(id) ON DELETE CASCADE,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (sender_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (receiver_id) REFERENCES users(id) ON DELETE CASCADE,
    INDEX idx_sender_receiver (sender_id, receiver_id),
    INDEX idx_receiver_is_read (receiver_id, is_read),
    INDEX idx_created_at (created_at)
);
```

## 🔄 Alur Chat

### 1. Korlap Mengirim Pesan
```
Korlap di Dashboard → Click "Chat Terbaru" 
→ Buka ChatController@index 
→ Lihat list konversasi & pesan terakhir
→ Click kontak → Load pesan dengan contact
→ Ketik pesan → Call sendMessage()
→ Message disimpan ke DB dengan:
   - sender_id = auth()->id() (Korlap)
   - receiver_id = $contact->id
   - is_read = false
→ Return JSON response
→ AJAX append pesan ke UI
```

### 2. Dashboard Auto-Refresh
```
Dashboard load → getLatestConversations()
→ Query dengan polling (wire:poll.3s)
→ Update chat list setiap 3 detik
→ Tampilkan unread count badge
```

### 3. Pesan Masuk dari Admin/Vendor
```
Admin/Vendor kirim pesan
→ Message saved ke DB
→ Polling Korlap detects new message
→ UI update otomatis
→ Unread badge muncul
```

## ✨ Fitur Utama

### A. Badge Unread (Hijau)
- Muncul jika `is_read = false`
- Warna: `bg-emerald-600` (hijau)
- Border-radius: `rounded-full`
- Font-size: `11px` (text-[11px])
- Padding: `px-2` (minimal 1.5rem)

### B. Avatar Inisial
- Ambil 2 huruf pertama dari nama
- Contoh: "Adi Dharmawan" → "AD"
- Background: `bg-gradient-to-br from-emerald-100 to-emerald-50`
- Text: `text-emerald-700`
- Border-radius: `rounded-full`
- Size: `h-12 w-12`

### C. Timestamp Format
```php
// Today: HH:mm (09:15)
// Yesterday: "Kemarin"
// This week: "X Hari lalu"
// Older: "d M" format (25 May)
```

### D. Pesan Terakhir
- Truncate hingga 2 baris dengan `line-clamp-2`
- Text size: `text-sm`
- Color: `text-slate-600`

## 🚀 Implementasi

### Step 1: Database Migration
Sudah ada, pastikan kolom-kolom ini terdapat di tabel:
- `sender_id`, `receiver_id`, `is_read`, `booking_id`

### Step 2: Model Relationships
```php
// app/Models/ChatMessage.php
public function sender(): BelongsTo { ... }
public function receiver(): BelongsTo { ... }
public function pesanan(): BelongsTo { ... }

public function scopeUnread($query, $userId) { ... }
public function scopeBetweenUsers($query, $userId1, $userId2) { ... }
public function scopeConversationsFor($query, $userId) { ... }
```

### Step 3: Controller Logic
**DashboardController::getLatestConversations()**
- Query pesan terbaru per kontak
- Count unread messages
- Format timestamp
- Return array collection

**ChatController::index()**
- Load list konversasi
- Load pesan dengan kontak pertama
- Return view dengan data

**ChatController::sendMessage()**
- Validasi input
- Create message dengan sender_id = auth()->id()
- Return JSON response

**ChatController::getConversation()**
- Mark messages as read
- Return messages dengan contact

### Step 4: Views
- Dashboard: Tampilkan chat terbaru dengan badge
- Chat page: Full conversation interface
- Both: Responsive design dengan floral theme

## 📱 Responsive Design
- Mobile: Single column, full-width
- Tablet: 2-column layout
- Desktop: 3-column grid untuk chat list

## 🎨 Theme Integration
- Colors: Emerald palette (floral theme)
- Border-radius: `rounded-[28px]` (card), `rounded-3xl` (elements), `rounded-full` (avatar)
- Shadows: `shadow-xl shadow-slate-900/5`
- Backdrop: `backdrop-blur-sm`
- Floral SVG accents di setiap card

## ⚡ Performance Tips
1. Gunakan eager loading: `with(['sender', 'receiver'])`
2. Index foreign keys di database
3. Limit polling ke 3 detik (jangan lebih cepat)
4. Cache conversation list jika perlu
5. Lazy load messages (infinite scroll untuk chat besar)

## 🔒 Security
- Authorization: Validasi sender_id === auth()->id()
- Sanitize message text
- CSRF token di form
- Rate limiting di sendMessage endpoint
- Only load own conversations

## 📝 Testing
- Test unread count accuracy
- Test message ordering (latest first)
- Test avatar initials edge cases
- Test timestamp formatting
- Test permissions (tidak bisa lihat chat orang lain)

## 🐛 Debugging
```php
// Debug query
DB::enableQueryLog();
// ... code ...
dd(DB::getQueryLog());

// Debug conversation
dd($chatTerbaru);

// Debug message
dd($message->load(['sender', 'receiver']));
```
