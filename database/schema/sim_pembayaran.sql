-- =============================================================================
-- MODUL SIM PEMBAYARAN — Brilliant WO
-- Skema relasi: users ↔ pesanans ↔ invoices ↔ pembayaran ↔ jadwal ↔ operasional
-- =============================================================================

-- ---------------------------------------------------------------------------
-- 1. USERS (sudah ada — referensi role)
-- role: 'customer' | 'admin' | 'lapangan'
-- ---------------------------------------------------------------------------

-- Field deadline pelunasan (H-14 event) pada pemesanan
ALTER TABLE pesanans
    ADD COLUMN IF NOT EXISTS tanggal_jatuh_tempo DATE NULL AFTER tanggal_acara,
    ADD COLUMN IF NOT EXISTS status_deadline ENUM('safe','warning','overdue') NOT NULL DEFAULT 'safe' AFTER tanggal_jatuh_tempo;

-- Kolom kunci workflow pembayaran & akses jadwal:
--   status_pembayaran ENUM('unpaid','dp_paid','fully_paid')
--   status_pemesanan  ENUM('pending','confirmed','on_progress','success','cancelled',...)
--   akses_jadwal      ENUM('none','partial','full')
--
--   none    → belum bayar / menunggu verifikasi
--   partial → DP terverifikasi — fase persiapan awal saja
--   full    → lunas — seluruh jadwal & vendor terbuka

ALTER TABLE pesanans
    ADD COLUMN IF NOT EXISTS status_pembayaran ENUM('unpaid','dp_paid','fully_paid') NOT NULL DEFAULT 'unpaid',
    ADD COLUMN IF NOT EXISTS akses_jadwal ENUM('none','partial','full') NOT NULL DEFAULT 'none',
    ADD COLUMN IF NOT EXISTS status_pemesanan VARCHAR(50) NOT NULL DEFAULT 'pending',
    ADD COLUMN IF NOT EXISTS korlap_id BIGINT UNSIGNED NULL,
    ADD COLUMN IF NOT EXISTS verified_admin_id BIGINT UNSIGNED NULL,
    ADD COLUMN IF NOT EXISTS verified_by_admin_at TIMESTAMP NULL,
    ADD COLUMN IF NOT EXISTS fully_paid_by_admin_at TIMESTAMP NULL;

-- ---------------------------------------------------------------------------
-- 3. INVOICE / TAGIHAN
-- ---------------------------------------------------------------------------
CREATE TABLE IF NOT EXISTS invoices (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    pesanan_id BIGINT UNSIGNED NOT NULL,
    nomor_invoice VARCHAR(50) NOT NULL UNIQUE,
    total_biaya DECIMAL(15,2) NOT NULL,
    dp_dibayar DECIMAL(15,2) NOT NULL DEFAULT 0,
    sisa_pembayaran DECIMAL(15,2) NOT NULL DEFAULT 0,
    status ENUM('Belum Bayar','DP Lunas','Lunas') NOT NULL DEFAULT 'Belum Bayar',
    metode_pembayaran VARCHAR(100) NULL,
    tanggal_invoice DATE NOT NULL,
    jatuh_tempo DATE NULL,
    jatuh_tempo_dp DATE NULL,
    jatuh_tempo_pelunasan DATE NULL,
    created_at TIMESTAMP NULL,
    updated_at TIMESTAMP NULL,
    FOREIGN KEY (pesanan_id) REFERENCES pesanans(id) ON DELETE CASCADE
);

-- ---------------------------------------------------------------------------
-- 4. TRANSAKSI PEMBAYARAN (bukti transfer customer)
-- ---------------------------------------------------------------------------
CREATE TABLE IF NOT EXISTS pembayaran_konfirmasis (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    invoice_id BIGINT UNSIGNED NOT NULL,
    user_id BIGINT UNSIGNED NOT NULL,
    jenis_pembayaran ENUM('DP','Pelunasan','Cicilan') NOT NULL,
    jumlah DECIMAL(15,2) NOT NULL,
    bank_pengirim VARCHAR(100) NOT NULL,
    nama_pengirim VARCHAR(150) NOT NULL,
    tanggal_transfer DATE NOT NULL,
    bukti_transfer VARCHAR(500) NOT NULL,
    catatan TEXT NULL,
    status ENUM('Menunggu Konfirmasi','Disetujui','Ditolak') NOT NULL DEFAULT 'Menunggu Konfirmasi',
    status_verifikasi ENUM('pending','approved_dp','approved_lunas','rejected') NOT NULL DEFAULT 'pending',
    catatan_admin TEXT NULL,
    alasan_penolakan TEXT NULL,
    confirmed_by BIGINT UNSIGNED NULL,
    confirmed_at TIMESTAMP NULL,
    created_at TIMESTAMP NULL,
    updated_at TIMESTAMP NULL,
    FOREIGN KEY (invoice_id) REFERENCES invoices(id) ON DELETE CASCADE,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
);

-- ---------------------------------------------------------------------------
-- 5. JADWAL EVENT (rundown + meeting vendor)
-- ---------------------------------------------------------------------------
CREATE TABLE IF NOT EXISTS rundowns (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    pesanan_id BIGINT UNSIGNED NOT NULL,
    kategori_acara VARCHAR(100) NOT NULL,
    waktu_mulai TIME NOT NULL,
    waktu_selesai TIME NULL,
    kegiatan VARCHAR(255) NOT NULL,
    tingkat_akses ENUM('partial','full') NOT NULL DEFAULT 'full',
    created_at TIMESTAMP NULL,
    updated_at TIMESTAMP NULL,
    FOREIGN KEY (pesanan_id) REFERENCES pesanans(id) ON DELETE CASCADE
);

CREATE TABLE IF NOT EXISTS vendor_meetings (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    booking_id BIGINT UNSIGNED NOT NULL,
    vendor_id BIGINT UNSIGNED NULL,
    title VARCHAR(255) NOT NULL,
    meeting_date DATE NOT NULL,
    meeting_time TIME NULL,
    agenda_type VARCHAR(50) NOT NULL DEFAULT 'technical_meeting',
    status VARCHAR(50) NOT NULL DEFAULT 'scheduled',
    tingkat_akses ENUM('partial','full') NOT NULL DEFAULT 'partial',
    created_at TIMESTAMP NULL,
    updated_at TIMESTAMP NULL,
    FOREIGN KEY (booking_id) REFERENCES pesanans(id) ON DELETE CASCADE
);

-- ---------------------------------------------------------------------------
-- 6. OPERASIONAL LAPANGAN (alokasi dana admin → korlap)
-- ---------------------------------------------------------------------------
CREATE TABLE IF NOT EXISTS operasional_lapangan (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    pesanan_id BIGINT UNSIGNED NOT NULL,
    korlap_id BIGINT UNSIGNED NULL,
    allocated_by BIGINT UNSIGNED NULL,
    pembayaran_konfirmasi_id BIGINT UNSIGNED NULL,
    jumlah_dialokasikan DECIMAL(15,2) NOT NULL,
    jumlah_terpakai DECIMAL(15,2) NOT NULL DEFAULT 0,
    sumber ENUM('dp','pelunasan','manual') NOT NULL DEFAULT 'manual',
    status ENUM('draft','disalurkan','selesai') NOT NULL DEFAULT 'draft',
    catatan TEXT NULL,
    created_at TIMESTAMP NULL,
    updated_at TIMESTAMP NULL,
    FOREIGN KEY (pesanan_id) REFERENCES pesanans(id) ON DELETE CASCADE,
    FOREIGN KEY (korlap_id) REFERENCES users(id) ON DELETE SET NULL,
    FOREIGN KEY (allocated_by) REFERENCES users(id) ON DELETE SET NULL,
    FOREIGN KEY (pembayaran_konfirmasi_id) REFERENCES pembayaran_konfirmasis(id) ON DELETE SET NULL
);

-- ---------------------------------------------------------------------------
-- 7. REALISASI PENGUNAAN DANA (laporan korlap + bukti nota)
-- ---------------------------------------------------------------------------
CREATE TABLE IF NOT EXISTS realisasi_operasional (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    operasional_lapangan_id BIGINT UNSIGNED NOT NULL,
    pesanan_id BIGINT UNSIGNED NOT NULL,
    korlap_id BIGINT UNSIGNED NOT NULL,
    judul VARCHAR(255) NOT NULL,
    jumlah DECIMAL(15,2) NOT NULL,
    tanggal_pengeluaran DATE NOT NULL,
    keterangan TEXT NULL,
    bukti_nota VARCHAR(500) NULL,
    status ENUM('Menunggu Review','Disetujui','Ditolak') NOT NULL DEFAULT 'Menunggu Review',
    catatan_admin TEXT NULL,
    created_at TIMESTAMP NULL,
    updated_at TIMESTAMP NULL,
    FOREIGN KEY (operasional_lapangan_id) REFERENCES operasional_lapangan(id) ON DELETE CASCADE,
    FOREIGN KEY (pesanan_id) REFERENCES pesanans(id) ON DELETE CASCADE,
    FOREIGN KEY (korlap_id) REFERENCES users(id) ON DELETE CASCADE
);

-- ---------------------------------------------------------------------------
-- INDEKS & VIEW BANTU
-- ---------------------------------------------------------------------------
CREATE INDEX idx_pesanans_payment ON pesanans(status_pembayaran, akses_jadwal);
CREATE INDEX idx_pembayaran_status ON pembayaran_konfirmasis(status);
CREATE INDEX idx_operasional_pesanan ON operasional_lapangan(pesanan_id, status);

-- Contoh query: pesanan siap eksekusi lapangan penuh
-- SELECT * FROM pesanans WHERE status_pembayaran = 'fully_paid' AND akses_jadwal = 'full';
