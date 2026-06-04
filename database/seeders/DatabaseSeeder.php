<?php

namespace Database\Seeders;

use App\Models\ChatMessage;
use App\Models\KuaChecklist;
use App\Models\ProgressPersiapan;
use App\Models\Rundown;
use App\Models\Invoice;
use App\Models\JadwalMeeting;
use App\Models\User;
use App\Models\Paket;
use App\Models\Pesanan;
use App\Models\Vendor;
use App\Models\VendorMeeting;
use App\Models\LaporanLapangan;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Schema;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $customer = User::updateOrCreate(
            ['email' => 'customer@brilliant.test'],
            [
                'name' => 'Customer Demo',
                'password' => 'password',
                'role' => 'client',
                'phone_number' => '081234567899',
                'address' => 'Garut, Jawa Barat',
            ]
        );

        User::updateOrCreate(
            ['email' => 'admin@brilliant.test'],
            ['name' => 'Admin Brilliant', 'password' => 'password', 'role' => 'admin']
        );

        User::updateOrCreate(
            ['email' => 'lapangan@brilliant.test'],
            [
                'name' => 'Tim Lapangan Garut',
                'password' => 'password',
                'role' => 'lapangan',
                'phone_number' => '081234567800',
            ]
        );

        $pakets = [
            ['nama_paket' => 'Silver Package', 'deskripsi' => 'Paket pernikahan hemat dengan layanan inti.', 'harga' => 15000000, 'layanan_termasuk' => ['Dekorasi Basic', 'Catering 200 Pax', 'Dokumentasi Foto'], 'gambar_url' => 'https://images.unsplash.com/photo-1519741497674-611481863552?auto=format&fit=crop&w=600&q=80'],
            ['nama_paket' => 'Gold Package', 'deskripsi' => 'Paket lengkap untuk acara menengah.', 'harga' => 30000000, 'layanan_termasuk' => ['Dekorasi Premium', 'Catering 400 Pax', 'MC', 'Dokumentasi Foto & Video'], 'gambar_url' => 'https://images.unsplash.com/photo-1464366400600-7168b8af9bc3?auto=format&fit=crop&w=600&q=80'],
            ['nama_paket' => 'Platinum Package', 'deskripsi' => 'Paket mewah dengan wedding organizer penuh.', 'harga' => 50000000, 'layanan_termasuk' => ['Dekorasi Exclusive', 'Catering 1000+ Pax', 'MC & Entertainment', 'WO Full Service'], 'gambar_url' => 'https://images.unsplash.com/photo-1544078751-58fee2d8a03b?auto=format&fit=crop&w=600&q=80'],
        ];

        foreach ($pakets as $paket) {
            Paket::updateOrCreate(['nama_paket' => $paket['nama_paket']], $paket);
        }

        Paket::updateOrCreate(
            ['nama_paket' => 'Paket Kustom'],
            [
                'deskripsi' => 'Tentukan budget Anda — sistem memperkirakan paket standar terdekat (Silver/Gold/Platinum) dan layanan yang bisa didapat. Tim WO menyesuaikan final.',
                'harga' => 0,
                'is_kustom' => true,
                'layanan_termasuk' => ['Fleksibel sesuai brief Anda', 'Dikoordinasikan terpusat oleh Brilliant WO'],
                'gambar_url' => 'https://images.unsplash.com/photo-1511795409834-ef04bbd61622?auto=format&fit=crop&w=600&q=80',
            ]
        );

        $vendors = [
            ['nama_vendor' => 'Aesthetic Decoration', 'kategori' => 'Dekorasi', 'lokasi' => 'Garut', 'harga_info' => 'Mulai Rp 8.000.000', 'rating_avg' => 4.9, 'rating_count' => 112, 'kontak' => '081234567890', 'status' => 'Aktif', 'gambar_url' => 'https://images.unsplash.com/photo-1519225421980-715cb0215aed?auto=format&fit=crop&w=600&q=80'],
            ['nama_vendor' => 'Dapoor Catering', 'kategori' => 'Catering', 'lokasi' => 'Garut', 'harga_info' => 'Mulai Rp 25.000/Pax', 'rating_avg' => 4.8, 'rating_count' => 86, 'kontak' => '081234567891', 'status' => 'Aktif', 'gambar_url' => 'https://images.unsplash.com/photo-1522673607200-164d1b6ce486?auto=format&fit=crop&w=600&q=80'],
            ['nama_vendor' => 'Glow Makeup Artist', 'kategori' => 'Makeup', 'lokasi' => 'Garut', 'harga_info' => 'Mulai Rp 3.500.000', 'rating_avg' => 4.9, 'rating_count' => 124, 'kontak' => '081234567892', 'status' => 'Aktif', 'gambar_url' => 'https://images.unsplash.com/photo-1487412720507-e7ab37603c6f?auto=format&fit=crop&w=600&q=80'],
            ['nama_vendor' => 'Moment Picture', 'kategori' => 'Dokumentasi', 'lokasi' => 'Garut', 'harga_info' => 'Mulai Rp 6.000.000', 'rating_avg' => 4.7, 'rating_count' => 73, 'kontak' => '081234567893', 'status' => 'Aktif', 'gambar_url' => 'https://images.unsplash.com/photo-1511895426328-dc8714191300?auto=format&fit=crop&w=600&q=80'],
            ['nama_vendor' => 'MC Bangkit Nusantara', 'kategori' => 'MC', 'lokasi' => 'Garut', 'harga_info' => 'Mulai Rp 2.500.000', 'rating_avg' => 4.8, 'rating_count' => 65, 'kontak' => '081234567894', 'status' => 'Aktif', 'gambar_url' => 'https://images.unsplash.com/photo-1478146896981-b80fe463b330?auto=format&fit=crop&w=600&q=80'],
            ['nama_vendor' => 'Lens & Frame Studio', 'kategori' => 'Foto & Video', 'lokasi' => 'Garut', 'harga_info' => 'Mulai Rp 7.500.000', 'rating_avg' => 4.8, 'rating_count' => 58, 'kontak' => '081234567895', 'status' => 'Aktif', 'gambar_url' => 'https://images.unsplash.com/photo-1516035069371-29a1b244cc32?auto=format&fit=crop&w=600&q=80'],
        ];

        foreach ($vendors as $vendor) {
            Vendor::updateOrCreate(['nama_vendor' => $vendor['nama_vendor']], $vendor);
        }

        $vendorByName = Vendor::pluck('id', 'nama_vendor');
        $silver = Paket::where('nama_paket', 'Silver Package')->first();
        $gold = Paket::where('nama_paket', 'Gold Package')->first();
        $platinum = Paket::where('nama_paket', 'Platinum Package')->first();

        if ($silver) {
            $silver->vendors()->sync(array_filter([
                $vendorByName['Aesthetic Decoration'] ?? null,
                $vendorByName['Dapoor Catering'] ?? null,
                $vendorByName['Moment Picture'] ?? null,
            ]));
        }

        if ($gold) {
            $gold->vendors()->sync(array_filter([
                $vendorByName['Aesthetic Decoration'] ?? null,
                $vendorByName['Dapoor Catering'] ?? null,
                $vendorByName['Glow Makeup Artist'] ?? null,
                $vendorByName['Moment Picture'] ?? null,
                $vendorByName['MC Bangkit Nusantara'] ?? null,
            ]));
        }

        if ($platinum) {
            $platinum->vendors()->sync($vendorByName->values()->all());
        }

        $this->seedPaketBookingDefaults($silver, $gold, $platinum);

        $gold = Paket::where('nama_paket', 'Gold Package')->first();
        $silver = Paket::where('nama_paket', 'Silver Package')->first();
        $timLapangan = User::where('email', 'lapangan@brilliant.test')->first();
        $admin = User::where('email', 'admin@brilliant.test')->first();

        if ($gold) {
            $pesanan = Pesanan::updateOrCreate(
                ['nomor_pesanan' => 'BR-WO-2026-001'],
                [
                    'user_id' => $customer->id,
                    'paket_id' => $gold->id,
                    'korlap_id' => $timLapangan?->id,
                    'nama_pasangan' => 'Dinda & Arya',
                    'tanggal_acara' => now()->addMonths(2)->toDateString(),
                    'jam_acara' => '08:00:00',
                    'lokasi' => 'Sabda Alam Hotel, Garut',
                    'tema' => 'Garden Elegant',
                    'jumlah_tamu' => 350,
                    'status' => 'Sedang Berlangsung',
                    'status_pembayaran' => 'dp_paid',
                    'status_pemesanan' => 'on_progress',
                    'catatan_khusus' => 'Preferensi warna putih-hijau.',
                ]
            );

            ProgressPersiapan::updateOrCreate(
                ['pesanan_id' => $pesanan->id],
                ['persentase' => 65, 'status_venue' => 'Selesai', 'status_makeup' => 'Proses', 'status_catering' => 'Proses', 'status_dekorasi' => 'Menunggu', 'status_dokumentasi' => 'Menunggu']
            );

            $invoice = Invoice::updateOrCreate(
                ['nomor_invoice' => 'INV-2026-001'],
                [
                    'pesanan_id' => $pesanan->id,
                    'total_biaya' => 30000000,
                    'dp_dibayar' => 9000000,
                    'sisa_pembayaran' => 21000000,
                    'status' => 'DP Lunas',
                    'tanggal_invoice' => now()->toDateString(),
                ]
            );
            $invoice->applyPaymentSchedule();
            $invoice->save();

            JadwalMeeting::updateOrCreate(
                [
                    'pesanan_id' => $pesanan->id,
                    'judul_meeting' => 'Meeting Finalisasi Vendor',
                ],
                [
                    'tanggal_meeting' => now()->addWeeks(2)->toDateString(),
                    'waktu_meeting' => '10:00:00',
                    'lokasi' => 'Kantor Brilliant WO',
                    'status' => 'Akan Datang',
                ]
            );

            JadwalMeeting::updateOrCreate(
                [
                    'pesanan_id' => $pesanan->id,
                    'judul_meeting' => 'Technical Meeting & Cek Venue',
                ],
                [
                    'tanggal_meeting' => now()->addMonths(2)->subDays(14)->toDateString(),
                    'waktu_meeting' => '14:00:00',
                    'lokasi' => 'Sabda Alam Hotel Garut',
                    'status' => 'Akan Datang',
                ]
            );

            if ($timLapangan) {
                $vendorMeetings = [
                    [
                        'title' => 'Meeting Finalisasi Vendor',
                        'meeting_date' => now()->addDays(10)->toDateString(),
                        'meeting_time' => '10:00:00',
                        'location' => 'Kantor Brilliant WO',
                        'agenda_type' => 'technical_meeting',
                        'status' => 'scheduled',
                    ],
                    [
                        'title' => 'Food Testing & Fitting',
                        'meeting_date' => now()->addDays(20)->toDateString(),
                        'meeting_time' => '10:00:00',
                        'location' => 'Lokasi Catering & Fitting',
                        'agenda_type' => 'self_preparation',
                        'status' => 'scheduled',
                    ],
                    [
                        'title' => 'Batas Kelengkapan Berkas KUA',
                        'meeting_date' => now()->addDays(15)->toDateString(),
                        'meeting_time' => '17:00:00',
                        'location' => 'KUA / Kantor Catatan Sipil',
                        'agenda_type' => 'self_preparation',
                        'status' => 'scheduled',
                        'is_auto_generated' => true,
                        'days_before_event' => 30,
                    ],
                    [
                        'title' => 'Technical Meeting & Cek Venue',
                        'meeting_date' => now()->addMonths(2)->subDays(14)->toDateString(),
                        'meeting_time' => '14:00:00',
                        'location' => 'Sabda Alam Hotel Garut',
                        'agenda_type' => 'technical_meeting',
                        'status' => 'scheduled',
                    ],
                ];

                foreach ($vendorMeetings as $meeting) {
                    VendorMeeting::updateOrCreate(
                        [
                            'booking_id' => $pesanan->id,
                            'title' => $meeting['title'],
                        ],
                        array_merge($meeting, [
                            'korlap_id' => $timLapangan->id,
                            'is_auto_generated' => false,
                        ])
                    );
                }

                KuaChecklist::updateOrCreate(
                    ['booking_id' => $pesanan->id],
                    [
                        'title' => 'Checklist Legalitas Administrasi',
                        'status' => 'in_progress',
                        'updated_by_user_id' => $admin?->id,
                        'notes' => 'Berkas KTP & KK sudah diunggah customer.',
                    ]
                );
            }

            $rundowns = [
                ['kategori_acara' => 'Akad Nikah', 'waktu_mulai' => '07:00:00', 'waktu_selesai' => '08:00:00', 'kegiatan' => 'Persiapan makeup & busana pengantin'],
                ['kategori_acara' => 'Akad Nikah', 'waktu_mulai' => '08:00:00', 'waktu_selesai' => '09:00:00', 'kegiatan' => 'Prosesi akad nikah'],
                ['kategori_acara' => 'Resepsi', 'waktu_mulai' => '10:30:00', 'waktu_selesai' => '11:00:00', 'kegiatan' => 'Persiapan resepsi & foto pre-event'],
                ['kategori_acara' => 'Resepsi', 'waktu_mulai' => '11:00:00', 'waktu_selesai' => '14:00:00', 'kegiatan' => 'Acara resepsi berlangsung'],
            ];

            foreach ($rundowns as $rundown) {
                Rundown::updateOrCreate(
                    [
                        'pesanan_id' => $pesanan->id,
                        'kategori_acara' => $rundown['kategori_acara'],
                        'waktu_mulai' => $rundown['waktu_mulai'],
                        'kegiatan' => $rundown['kegiatan'],
                    ],
                    [
                        'waktu_selesai' => $rundown['waktu_selesai'],
                    ]
                );
            }

            if ($timLapangan) {
                LaporanLapangan::updateOrCreate(
                    [
                        'pesanan_id' => $pesanan->id,
                        'tanggal' => now()->toDateString(),
                        'ringkasan' => 'Survey venue selesai. Layout akad dan resepsi sudah dikonfirmasi dengan pihak hotel.',
                    ],
                    [
                        'user_id' => $timLapangan->id,
                        'kondisi' => 'Baik',
                        'tindak_lanjut' => 'Lanjut koordinasi dekorasi minggu depan.',
                    ]
                );
            }

            if ($admin) {
                ChatMessage::updateOrCreate(
                    [
                        'pesanan_id' => $pesanan->id,
                        'pesan' => 'Halo! Booking Anda sudah kami terima. Ada pertanyaan seputar paket Gold?',
                    ],
                    [
                        'user_id' => $admin->id,
                        'dari_admin' => true,
                    ]
                );

                ChatMessage::updateOrCreate(
                    [
                        'pesanan_id' => $pesanan->id,
                        'pesan' => 'Terima kasih admin, kami ingin konfirmasi jadwal fitting baju.',
                    ],
                    [
                        'user_id' => $customer->id,
                        'dari_admin' => false,
                    ]
                );
            }
        }

        if ($silver && $timLapangan) {
            $pesananHariH = Pesanan::updateOrCreate(
                ['nomor_pesanan' => 'BR-WO-2026-002'],
                [
                    'user_id' => $customer->id,
                    'paket_id' => $silver->id,
                    'korlap_id' => $timLapangan->id,
                    'nama_pasangan' => 'Niki & Abdi',
                    'tanggal_acara' => now()->addDay()->toDateString(),
                    'jam_acara' => '08:00:00',
                    'lokasi' => 'Kp. Babakan Nanggerang dea sukjadi kec. trogong kaler garut',
                    'tema' => 'Rustic Green',
                    'jumlah_tamu' => 200,
                    'status' => 'Sedang Berlangsung',
                    'status_pembayaran' => 'fully_paid',
                    'status_pemesanan' => 'on_progress',
                ]
            );

            VendorMeeting::updateOrCreate(
                [
                    'booking_id' => $pesananHariH->id,
                    'title' => 'Meeting Finalisasi Vendor',
                ],
                [
                    'korlap_id' => $timLapangan->id,
                    'meeting_date' => now()->addDays(10)->toDateString(),
                    'meeting_time' => '10:00:00',
                    'location' => 'Kantor Brilliant WO',
                    'agenda_type' => 'technical_meeting',
                    'status' => 'scheduled',
                    'is_auto_generated' => false,
                ]
            );

            VendorMeeting::updateOrCreate(
                [
                    'booking_id' => $pesananHariH->id,
                    'title' => 'Food Testing & Fitting',
                ],
                [
                    'korlap_id' => $timLapangan->id,
                    'meeting_date' => now()->addDays(20)->toDateString(),
                    'meeting_time' => '10:00:00',
                    'location' => 'Lokasi Catering & Fitting',
                    'agenda_type' => 'self_preparation',
                    'status' => 'scheduled',
                    'is_auto_generated' => false,
                ]
            );

            VendorMeeting::updateOrCreate(
                [
                    'booking_id' => $pesananHariH->id,
                    'title' => 'Batas Kelengkapan Berkas KUA',
                ],
                [
                    'korlap_id' => $timLapangan->id,
                    'meeting_date' => now()->addDays(15)->toDateString(),
                    'meeting_time' => '17:00:00',
                    'location' => 'KUA / Kantor Catatan Sipil',
                    'agenda_type' => 'self_preparation',
                    'status' => 'scheduled',
                    'is_auto_generated' => true,
                    'days_before_event' => 30,
                ]
            );

            KuaChecklist::updateOrCreate(
                ['booking_id' => $pesananHariH->id],
                [
                    'title' => 'Checklist Legalitas Administrasi',
                    'status' => 'complete',
                    'customer_check_in_at' => now()->setTime(12, 0),
                    'updated_by_user_id' => $customer->id,
                    'notes' => 'Semua berkas KUA lengkap dan terverifikasi.',
                ]
            );
        }
    }

    private function seedPaketBookingDefaults(?Paket $silver, ?Paket $gold, ?Paket $platinum): void
    {
        if (! Schema::hasColumn('pakets', 'default_lokasi')) {
            return;
        }

        $configs = [
            'Silver Package' => $silver,
            'Gold Package' => $gold,
            'Platinum Package' => $platinum,
        ];

        $defaults = [
            'Silver Package' => [
                'default_lokasi' => 'Sabda Alam Resort, Garut',
                'kapasitas_tamu' => 200,
                'harga_tambahan_per_tamu' => 75_000,
                'temas' => ['Rustic Green', 'Minimalis Modern', 'Garden Simple'],
            ],
            'Gold Package' => [
                'default_lokasi' => 'Sabda Alam Hotel, Garut',
                'kapasitas_tamu' => 400,
                'harga_tambahan_per_tamu' => 50_000,
                'temas' => ['Garden Elegant', 'Classic White', 'Modern Luxury'],
            ],
            'Platinum Package' => [
                'default_lokasi' => 'Grand Ballroom Garut',
                'kapasitas_tamu' => 1000,
                'harga_tambahan_per_tamu' => 35_000,
                'temas' => ['Royal Gold', 'Exclusive Modern', 'Grand Classic'],
            ],
        ];

        foreach ($defaults as $name => $cfg) {
            $paket = $configs[$name] ?? Paket::where('nama_paket', $name)->first();
            if (! $paket) {
                continue;
            }

            $paket->update([
                'default_lokasi' => $cfg['default_lokasi'],
                'kapasitas_tamu' => $cfg['kapasitas_tamu'],
                'harga_tambahan_per_tamu' => $cfg['harga_tambahan_per_tamu'],
            ]);

            if (! Schema::hasTable('paket_temas')) {
                continue;
            }

            $paket->temas()->delete();
            foreach ($cfg['temas'] as $index => $tema) {
                $paket->temas()->create([
                    'nama_tema' => $tema,
                    'urutan' => $index,
                ]);
            }
        }
    }
}
