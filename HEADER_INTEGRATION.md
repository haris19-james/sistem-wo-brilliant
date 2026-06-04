# Integrasi Komponen Header Dashboard (Admin / Klien / Korlap)

Panduan singkat untuk memanggil komponen header yang baru dibuat: `resources/views/components/dashboard/header.blade.php`.

## 1) Lokasi file komponen
- `resources/views/components/dashboard/header.blade.php`

Komponen ini bersifat reusable dan menerima props:
- `title` (string) — judul tampilan, mis. "Dashboard Admin"
- `notificationRoute` (string) — URL untuk halaman notifikasi (default `/notifications`)
- `unreadCount` (int) — jumlah notifikasi belum dibaca (opsional)

Slot `{{ $slot }}` tersedia untuk menaruh avatar user atau tombol tambahan.

## 2) Cara memanggil di view Blade
Contoh penggunaan sederhana (Admin dashboard):

```blade
{{-- Di file resources/views/admin/dashboard.blade.php --}}
@include('components.dashboard.header', [
    'title' => 'Dashboard Admin',
    'notificationRoute' => route('notifications.index'),
    'unreadCount' => $unreadCount ?? 0
])

{{-- Jika ingin menambahkan avatar di sebelah kanan: --}}
@include('components.dashboard.header', [
    'title' => 'Dashboard Admin',
    'notificationRoute' => route('notifications.index'),
    'unreadCount' => $unreadCount ?? 0
])
<div class="hidden">
    {{-- contoh slot: --}}
    @slot('slot')
        <img src="/path/to/avatar.jpg" class="h-8 w-8 rounded-full" alt="Avatar">
    @endslot
</div>
```

Atau gunakan Blade component tag (opsional) jika Anda mendaftarkan komponen di `AppServiceProvider`:

```php
// Di App\Providers\AppServiceProvider::boot()
Blade::component('components.dashboard.header', 'dashboard-header');
```

Lalu di Blade:

```blade
<x-dashboard-header :title="'Dashboard Korlap'" :notificationRoute="route('notifications.index')" :unreadCount="$unreadCount" />
```

## 3) Mengambil jumlah notifikasi belum dibaca (Controller)
Di masing-masing controller dashboard (Admin/Customer/Korlap), tambahkan:

```php
// contoh di controller
public function index()
{
    $user = auth()->user();
    $unreadCount = $user ? $user->notifications()->where('is_read', 0)->count() : 0;

    return view('admin.dashboard', compact('unreadCount'));
}
```

Jika Anda menggunakan Laravel Notifications (built-in):
```php
$unreadCount = auth()->user()->unreadNotifications->count();
```

## 4) Integrasi dengan `notification-poller.js`
Komponen sudah menambahkan atribut `data-notification-auto-poll` pada wrapper notifikasi. Pastikan file `public/js/notification-poller.js` sudah disertakan di layout utama (mis. `resources/views/layouts/app.blade.php`). Poller akan otomatis mendeteksi wrapper ini dan meng-update badge `#notification-badge`.

Contoh include JS di layout:

```blade
<script src="{{ mix('js/notification-poller.js') }}"></script>
```

atau

```blade
<script src="/js/notification-poller.js"></script>
```

## 5) Styling & Responsiveness
- Tanggal akan tersembunyi di layar HP (class `hidden sm:inline-block`), sehingga header tetap rapi di layar kecil.
- Gunakan Tailwind utilities (sudah dipakai di komponen) untuk konsistensi UI.

## 6) Shortcut pemanggilan untuk ketiga role
- Admin: `resources/views/admin/dashboard.blade.php`
- Klien: `resources/views/customer/dashboard.blade.php`
- Korlap: `resources/views/korlap/dashboard.blade.php`

Jika semua dashboard memanggil sama satu include, tidak ada duplicate code.

## 7) Catatan tambahan
- Komponen memakai JavaScript `toLocaleDateString('id-ID', ...)` seperti diminta untuk format tanggal lokal.
- Badge notifikasi diupdate oleh `notification-poller.js`; jika Anda ingin menginisialisasi nilai awal dari backend, kirim `unreadCount` melalui view data.

