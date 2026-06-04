# 🌸 Ethereal Floral Theme - CSS Implementation Examples

## Siap Copy-Paste untuk Berbagai Komponen

---

## 1. DASHBOARD STAT CARDS

### Full Example
```html
<div class="grid-glass-4">
  <!-- Card 1: Blue Icon -->
  <div class="stat-card">
    <div class="flex items-start justify-between mb-3">
      <div class="icon-container-blue">
        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
        </svg>
      </div>
    </div>
    <p class="text-label mb-1">Acara Hari Ini</p>
    <p class="text-4xl font-bold text-primary mb-4">42</p>
    <a href="#" class="text-sm font-semibold text-field hover:text-field/80 transition inline-flex items-center gap-1">
      Lihat detail <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
    </a>
  </div>

  <!-- Card 2: Green Icon -->
  <div class="stat-card">
    <div class="flex items-start justify-between mb-3">
      <div class="icon-container-green">
        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
        </svg>
      </div>
    </div>
    <p class="text-label mb-1">Vendor Aktif</p>
    <p class="text-4xl font-bold text-primary mb-4">28</p>
    <a href="#" class="text-sm font-semibold text-field hover:text-field/80 transition inline-flex items-center gap-1">
      Lihat detail <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
    </a>
  </div>

  <!-- Card 3: Orange Icon -->
  <div class="stat-card">
    <div class="flex items-start justify-between mb-3">
      <div class="icon-container-orange">
        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6V4m0 2a2 2 0 100 4m0-4a2 2 0 110 4m-6 8a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4m6 6v10m6-2a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4"/>
        </svg>
      </div>
    </div>
    <p class="text-label mb-1">Tugas Pending</p>
    <p class="text-4xl font-bold text-primary mb-4">5</p>
    <a href="#" class="text-sm font-semibold text-field hover:text-field/80 transition inline-flex items-center gap-1">
      Lihat detail <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
    </a>
  </div>

  <!-- Card 4: Purple Icon -->
  <div class="stat-card">
    <div class="flex items-start justify-between mb-3">
      <div class="icon-container-purple">
        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"/>
        </svg>
      </div>
    </div>
    <p class="text-label mb-1">Pesan Belum Dibaca</p>
    <p class="text-4xl font-bold text-primary mb-4">3</p>
    <a href="#" class="text-sm font-semibold text-field hover:text-field/80 transition inline-flex items-center gap-1">
      Lihat pesan <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
    </a>
  </div>
</div>
```

---

## 2. CONTENT CARDS - 3 COLUMN LAYOUT

```html
<div class="grid-glass-3">
  <!-- Left Column -->
  <div class="content-card card-ornament">
    <div class="content-card-header">
      <div class="flex items-center justify-between">
        <h3 class="text-label">Acara Hari Ini</h3>
        <a href="#" class="text-xs font-semibold text-secondary hover:text-field transition">Lihat semua</a>
      </div>
    </div>
    <div class="content-card-body max-h-96 overflow-y-auto">
      <a href="#" class="card-item block">
        <div class="mb-3 w-full h-32 bg-white/40 backdrop-blur-sm rounded-lg overflow-hidden flex-shrink-0 border border-white/20">
          <svg class="w-full h-full text-field/20 p-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
          </svg>
        </div>
        <div class="flex items-start justify-between gap-2 mb-3">
          <div class="flex-1">
            <p class="font-semibold text-primary text-sm">Dewa & Merry</p>
            <p class="text-xs text-secondary mt-1 flex items-center gap-1">
              <svg class="w-3 h-3 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
              Garut Convention Center
            </p>
            <p class="text-xs text-secondary flex items-center gap-1">
              <svg class="w-3 h-3 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
              14:00 WIB
            </p>
          </div>
          <span class="badge-success whitespace-nowrap flex-shrink-0">Berlangsung</span>
        </div>
      </a>
    </div>
  </div>

  <!-- Middle Column -->
  <div class="content-card card-ornament">
    <div class="content-card-header">
      <div class="flex items-center justify-between">
        <h3 class="text-label">Jadwal Acara</h3>
        <a href="#" class="text-xs font-semibold text-secondary hover:text-field transition">Lihat semua</a>
      </div>
    </div>
    <div class="content-card-body max-h-96 overflow-y-auto">
      <div class="flex gap-4">
        <div class="flex flex-col items-center">
          <div class="timeline-dot mt-1.5"></div>
          <div class="timeline-line my-1"></div>
        </div>
        <div class="pb-2 flex-1">
          <p class="text-sm font-bold text-primary">09:00</p>
          <p class="text-xs text-secondary mt-1">Pembukaan & Pemberkatan</p>
          <span class="inline-block mt-2 px-2 py-1 bg-white/40 backdrop-blur-sm text-tertiary text-xs rounded-full border border-white/20">Upacara</span>
        </div>
      </div>
      <div class="flex gap-4">
        <div class="flex flex-col items-center">
          <div class="timeline-dot mt-1.5"></div>
          <div class="timeline-line my-1"></div>
        </div>
        <div class="pb-2 flex-1">
          <p class="text-sm font-bold text-primary">11:30</p>
          <p class="text-xs text-secondary mt-1">Resepsi & Makan Bersama</p>
          <span class="inline-block mt-2 px-2 py-1 bg-white/40 backdrop-blur-sm text-tertiary text-xs rounded-full border border-white/20">Seremonial</span>
        </div>
      </div>
    </div>
  </div>

  <!-- Right Column -->
  <div class="content-card card-ornament">
    <div class="content-card-header">
      <div class="flex items-center justify-between">
        <h3 class="text-label">Vendor Hari Ini</h3>
        <a href="#" class="text-xs font-semibold text-secondary hover:text-field transition">Lihat semua</a>
      </div>
    </div>
    <div class="content-card-body max-h-96 overflow-y-auto">
      <div class="card-item flex items-center justify-between">
        <div class="flex items-center gap-3 flex-1">
          <div class="icon-container-orange">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6V4m0 2a2 2 0 100 4m0-4a2 2 0 110 4m-6 8a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4m6 6v10m6-2a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4"/>
            </svg>
          </div>
          <div>
            <p class="font-semibold text-primary text-sm truncate">Catering Sasmita</p>
            <p class="text-xs text-secondary truncate">Catering</p>
          </div>
        </div>
        <span class="badge-success whitespace-nowrap flex-shrink-0">Hadir</span>
      </div>
      <div class="card-item flex items-center justify-between">
        <div class="flex items-center gap-3 flex-1">
          <div class="icon-container-blue">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 9a2 2 0 012-2h.93a2 2 0 001.664-.89l.812-1.22A2 2 0 0110.07 4h3.86a2 2 0 011.664.89l.812 1.22A2 2 0 0018.07 7H19a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V9z"/>
            </svg>
          </div>
          <div>
            <p class="font-semibold text-primary text-sm truncate">Studio Foto Brilliant</p>
            <p class="text-xs text-secondary truncate">Dokumentasi</p>
          </div>
        </div>
        <span class="badge-success whitespace-nowrap flex-shrink-0">Hadir</span>
      </div>
    </div>
  </div>
</div>
```

---

## 3. TASKS & CHECKLIST

```html
<div class="content-card">
  <div class="flex items-center justify-between mb-4">
    <h3 class="text-label">Tugas Hari Ini</h3>
    <a href="#" class="text-xs font-semibold text-secondary hover:text-field transition">Lihat semua</a>
  </div>
  <div class="space-y-2">
    <label class="flex items-center gap-3 p-3 bg-white/40 backdrop-blur-sm rounded-lg hover:bg-white/50 cursor-pointer transition border border-white/20">
      <input type="checkbox" checked class="checkbox-floral" />
      <span class="text-sm text-primary font-medium">Cek dekorasi dan perlengkapan</span>
    </label>
    <label class="flex items-center gap-3 p-3 bg-white/40 backdrop-blur-sm rounded-lg hover:bg-white/50 cursor-pointer transition border border-white/20">
      <input type="checkbox" checked class="checkbox-floral" />
      <span class="text-sm text-primary font-medium">Briefing dengan vendor</span>
    </label>
    <label class="flex items-center gap-3 p-3 bg-white/40 backdrop-blur-sm rounded-lg hover:bg-white/50 cursor-pointer transition border border-white/20">
      <input type="checkbox" class="checkbox-floral" />
      <span class="text-sm text-secondary font-medium">Cek rundown acara</span>
    </label>
  </div>
</div>
```

---

## 4. PROGRESS TRACKING

```html
<div class="content-card">
  <h3 class="text-label mb-6">Progress Acara</h3>
  
  <!-- Progress Item 1 -->
  <div class="mb-6">
    <div class="flex items-center justify-between mb-2">
      <p class="text-sm font-medium text-primary">Persiapan Ruang</p>
      <span class="text-sm font-bold text-field">85%</span>
    </div>
    <div class="progress-bar-glass">
      <div class="progress-fill" style="width: 85%"></div>
    </div>
  </div>

  <!-- Progress Item 2 -->
  <div class="mb-6">
    <div class="flex items-center justify-between mb-2">
      <p class="text-sm font-medium text-primary">Vendor Tiba</p>
      <span class="text-sm font-bold text-field">60%</span>
    </div>
    <div class="progress-bar-glass">
      <div class="progress-fill" style="width: 60%"></div>
    </div>
  </div>

  <!-- Progress Item 3 -->
  <div>
    <div class="flex items-center justify-between mb-2">
      <p class="text-sm font-medium text-primary">Cek Sound & Lighting</p>
      <span class="text-sm font-bold text-field">40%</span>
    </div>
    <div class="progress-bar-glass">
      <div class="progress-fill" style="width: 40%"></div>
    </div>
  </div>
</div>
```

---

## 5. MESSAGING / CHAT

```html
<div class="content-card">
  <div class="flex items-center justify-between mb-4">
    <h3 class="text-label">Chat Terbaru</h3>
    <a href="#" class="text-xs font-semibold text-secondary hover:text-field transition">Lihat semua</a>
  </div>
  <div class="space-y-3">
    <div class="card-item flex items-start gap-3">
      <div class="w-10 h-10 bg-field/20 rounded-full flex items-center justify-center flex-shrink-0 text-field font-bold text-sm border border-field/30">
        K
      </div>
      <div class="flex-1">
        <div class="flex items-start justify-between gap-2">
          <p class="font-semibold text-primary text-sm">Karina - Vendor Dekorasi</p>
          <span class="inline-flex items-center justify-center w-5 h-5 bg-field text-white text-xs font-bold rounded-full flex-shrink-0">2</span>
        </div>
        <p class="text-xs text-secondary line-clamp-2 mt-1">Sudah siap dengan dekorasi bunga. Tinggal tunggu instruksi untuk setup.</p>
        <p class="text-xs text-tertiary mt-1">14:30</p>
      </div>
    </div>
  </div>
</div>
```

---

## 6. ALERTS & NOTICES

```html
<!-- Info Alert -->
<div class="bg-pastel-blue/20 border border-pastel-blue/30 rounded-lg p-4 backdrop-blur-sm">
  <div class="flex items-start gap-3">
    <div class="w-5 h-5 text-pastel-blue-dark flex-shrink-0">
      <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
      </svg>
    </div>
    <div class="flex-1">
      <p class="font-semibold text-pastel-blue-dark text-sm">Informasi</p>
      <p class="text-xs text-secondary mt-1">Setiap vendor harus check-in 30 menit sebelum acara dimulai.</p>
    </div>
  </div>
</div>

<!-- Warning Alert -->
<div class="bg-pastel-orange/20 border border-pastel-orange/30 rounded-lg p-4 backdrop-blur-sm mt-4">
  <div class="flex items-start gap-3">
    <div class="w-5 h-5 text-pastel-orange-dark flex-shrink-0">
      <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4v2m0 4v2M7.08 6.47a9 9 0 1117.84 0m-17.84 0a9 9 0 0117.84 0"/>
      </svg>
    </div>
    <div class="flex-1">
      <p class="font-semibold text-pastel-orange-dark text-sm">Peringatan</p>
      <p class="text-xs text-secondary mt-1">Catering belum tiba. Hubungi vendor segera untuk update.</p>
    </div>
  </div>
</div>

<!-- Success Alert -->
<div class="bg-field/20 border border-field/30 rounded-lg p-4 backdrop-blur-sm mt-4">
  <div class="flex items-start gap-3">
    <div class="w-5 h-5 text-field flex-shrink-0">
      <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
      </svg>
    </div>
    <div class="flex-1">
      <p class="font-semibold text-field text-sm">Berhasil</p>
      <p class="text-xs text-secondary mt-1">Semua vendor telah check-in dan siap melaksanakan tugas.</p>
    </div>
  </div>
</div>
```

---

## 7. VENDOR LIST WITH FILTERS

```html
<div class="content-card">
  <div class="flex items-center justify-between mb-4">
    <h3 class="text-label">Daftar Vendor</h3>
    <div class="flex gap-2">
      <button class="btn-soft-primary">Semua</button>
      <button class="btn-soft-outline">Hadir</button>
      <button class="btn-soft-outline">Belum</button>
    </div>
  </div>
  <div class="space-y-2">
    <div class="card-item flex items-center justify-between">
      <div class="flex items-center gap-3 flex-1">
        <div class="icon-container-orange">
          <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6V4m0 2a2 2 0 100 4m0-4a2 2 0 110 4m-6 8a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4m6 6v10m6-2a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4"/>
          </svg>
        </div>
        <div class="flex-1">
          <p class="font-semibold text-primary">Catering Sasmita</p>
          <p class="text-xs text-secondary">Catering</p>
        </div>
      </div>
      <span class="badge-success">Hadir</span>
    </div>
  </div>
</div>
```

---

## 8. FORM SECTION

```html
<div class="content-card">
  <h3 class="text-label mb-4">Form Laporan</h3>
  <div class="space-y-4">
    <!-- Text Input -->
    <div>
      <label class="block text-sm font-semibold text-primary mb-2">Nama Vendor</label>
      <input type="text" placeholder="Masukkan nama vendor" 
             class="w-full px-4 py-2 bg-white/40 backdrop-blur-sm border border-white/20 rounded-lg text-primary placeholder-tertiary focus:outline-none focus:border-field/50 focus:bg-white/50 transition" />
    </div>

    <!-- Select Input -->
    <div>
      <label class="block text-sm font-semibold text-primary mb-2">Kategori</label>
      <select class="w-full px-4 py-2 bg-white/40 backdrop-blur-sm border border-white/20 rounded-lg text-primary focus:outline-none focus:border-field/50 focus:bg-white/50 transition">
        <option>Pilih Kategori</option>
        <option>Catering</option>
        <option>Dekorasi</option>
        <option>Dokumentasi</option>
      </select>
    </div>

    <!-- Textarea -->
    <div>
      <label class="block text-sm font-semibold text-primary mb-2">Catatan</label>
      <textarea rows="4" placeholder="Tambahkan catatan..." 
                class="w-full px-4 py-2 bg-white/40 backdrop-blur-sm border border-white/20 rounded-lg text-primary placeholder-tertiary focus:outline-none focus:border-field/50 focus:bg-white/50 transition resize-none"></textarea>
    </div>

    <!-- Buttons -->
    <div class="flex gap-3 pt-2">
      <button class="btn-soft-primary flex-1">Simpan</button>
      <button class="btn-soft-outline flex-1">Batal</button>
    </div>
  </div>
</div>
```

---

## 9. TABLE WITH GLASS STYLING

```html
<div class="content-card">
  <div class="overflow-x-auto">
    <table class="w-full">
      <thead>
        <tr class="border-b border-white/20">
          <th class="px-4 py-3 text-left text-xs font-bold text-label">Vendor</th>
          <th class="px-4 py-3 text-left text-xs font-bold text-label">Kategori</th>
          <th class="px-4 py-3 text-left text-xs font-bold text-label">Status</th>
          <th class="px-4 py-3 text-center text-xs font-bold text-label">Aksi</th>
        </tr>
      </thead>
      <tbody class="divide-y divide-white/10">
        <tr class="hover:bg-white/20 transition">
          <td class="px-4 py-3 text-sm text-primary font-semibold">Catering Sasmita</td>
          <td class="px-4 py-3 text-sm text-secondary">Catering</td>
          <td class="px-4 py-3 text-sm">
            <span class="badge-success">Hadir</span>
          </td>
          <td class="px-4 py-3 text-center">
            <button class="text-field hover:text-field/80 transition text-sm font-semibold">Edit</button>
          </td>
        </tr>
      </tbody>
    </table>
  </div>
</div>
```

---

## 10. MODAL / DIALOG BACKDROP

```html
<!-- Backdrop -->
<div class="fixed inset-0 bg-black/40 backdrop-blur-sm z-40"></div>

<!-- Modal -->
<div class="fixed inset-0 flex items-center justify-center z-50">
  <div class="glass-card w-full max-w-md mx-4 shadow-glass-lg rounded-2xl">
    <div class="content-card-header">
      <h3 class="text-lg font-bold text-primary">Konfirmasi Aksi</h3>
    </div>
    <div class="p-6">
      <p class="text-secondary mb-6">Apakah Anda yakin ingin menyelesaikan tugas ini?</p>
      <div class="flex gap-3">
        <button class="btn-soft-outline flex-1">Batal</button>
        <button class="btn-soft-primary flex-1">Yakin</button>
      </div>
    </div>
  </div>
</div>
```

---

## Tips Penggunaan

### Copy-Paste Strategy
1. Pilih komponen yang sesuai dengan kebutuhan
2. Copy seluruh HTML block
3. Paste di template Blade Anda
4. Sesuaikan data/variable sebagai kebutuhan
5. Tidak perlu menambah CSS lagi - semua sudah di-define

### Customization
- Ubah `icon-container-*` untuk warna ikon berbeda
- Ubah `badge-*` untuk status berbeda
- Gunakan `text-primary/secondary/tertiary` untuk kontrol text
- Gunakan `grid-glass-*` untuk layout berbeda

### Performance Tips
- Use `max-h-96 overflow-y-auto` untuk long lists
- Use `line-clamp-*` untuk truncate text
- Use `flex-shrink-0` untuk prevent icon squeeze
- Use `whitespace-nowrap` untuk prevent line break

---

**Ready to use! Copy-paste langsung ke project Anda.** 🚀
