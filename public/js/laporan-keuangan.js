/**
 * Admin Laporan Keuangan — export PDF/Excel & modal detail
 */
function laporanKeuanganAdmin(config) {
    return {
        exportUrl: config.exportUrl,
        detailUrlTemplate: config.detailUrlTemplate,
        verifyUrlTemplate: config.verifyUrlTemplate,
        filters: config.filters || {},
        exporting: false,
        buktiOpen: false,
        buktiUrl: '',
        buktiTrxId: '',
        rejectOpen: false,
        rejectTrxId: '',
        rejectAction: '',
        detailOpen: false,
        detailLoading: false,
        detailData: null,

        buildQueryParams() {
            const p = new URLSearchParams();
            const f = this.filters;
            if (f.status && f.status !== 'semua') p.set('status', f.status);
            if (f.date_from) p.set('date_from', f.date_from);
            if (f.date_to) p.set('date_to', f.date_to);
            if (f.q) p.set('q', f.q);
            if (f.booking_status && f.booking_status !== 'semua') p.set('booking_status', f.booking_status);
            return p.toString();
        },

        async fetchExportData() {
            const qs = this.buildQueryParams();
            const url = qs ? `${this.exportUrl}?${qs}` : this.exportUrl;
            const res = await fetch(url, {
                headers: { Accept: 'application/json', 'X-Requested-With': 'XMLHttpRequest' },
                credentials: 'same-origin',
            });
            if (!res.ok) throw new Error('Gagal memuat data export');
            const json = await res.json();
            if (!json.success) throw new Error('Data export tidak valid');
            return json;
        },

        formatRp(n) {
            const num = Number(n) || 0;
            return 'Rp ' + num.toLocaleString('id-ID');
        },

        async exportPdf() {
            if (this.exporting || typeof window.jspdf === 'undefined') return;
            this.exporting = true;
            try {
                const payload = await this.fetchExportData();
                const { jsPDF } = window.jspdf;
                const doc = new jsPDF({ orientation: 'landscape', unit: 'mm', format: 'a4' });
                const a = payload.analytics || {};

                doc.setFontSize(14);
                doc.text('Laporan Keuangan — Brilliant WO', 14, 14);
                doc.setFontSize(9);
                doc.text(`Dicetak: ${payload.generated_at}`, 14, 20);
                doc.text(`Pendapatan kotor: ${this.formatRp(a.pendapatan_kotor)}`, 14, 26);
                doc.text(`Booking — Pending: ${a.booking_pending || 0} | DP: ${a.booking_dp || 0} | Lunas: ${a.booking_lunas || 0}`, 14, 32);

                const head = [['ID', 'Klien', 'Pasangan', 'Paket', 'Nominal', 'Tipe', 'Tgl', 'Status', 'Bayar Klien']];
                const body = (payload.rows || []).map((r) => [
                    r.nomor_transaksi,
                    r.client,
                    r.nama_pasangan,
                    r.paket,
                    r.jumlah_fmt,
                    r.jenis_pembayaran,
                    r.tanggal_transfer || '',
                    r.status_label,
                    r.status_pembayaran_klien,
                ]);

                doc.autoTable({
                    head,
                    body,
                    startY: 38,
                    styles: { fontSize: 7, cellPadding: 1.5 },
                    headStyles: { fillColor: [45, 90, 61] },
                });

                doc.save(`laporan-keuangan-${Date.now()}.pdf`);
            } catch (e) {
                alert(e.message || 'Export PDF gagal');
            } finally {
                this.exporting = false;
            }
        },

        async exportExcel() {
            if (this.exporting || typeof window.XLSX === 'undefined') return;
            this.exporting = true;
            try {
                const payload = await this.fetchExportData();
                const a = payload.analytics || {};
                const sheetData = [
                    ['Laporan Keuangan — Brilliant WO'],
                    ['Dicetak', payload.generated_at],
                    ['Pendapatan Kotor', a.pendapatan_kotor],
                    ['Booking Pending', a.booking_pending],
                    ['Booking DP', a.booking_dp],
                    ['Booking Lunas', a.booking_lunas],
                    [],
                    ['ID Transaksi', 'Klien', 'Pasangan', 'No. Pesanan', 'Paket', 'Nominal', 'Jenis', 'Tgl Transfer', 'Status Verifikasi', 'Status Pembayaran Klien'],
                ];

                (payload.rows || []).forEach((r) => {
                    sheetData.push([
                        r.nomor_transaksi,
                        r.client,
                        r.nama_pasangan,
                        r.nomor_pesanan,
                        r.paket,
                        r.jumlah,
                        r.jenis_pembayaran,
                        r.tanggal_transfer,
                        r.status_label,
                        r.status_pembayaran_klien,
                    ]);
                });

                const wb = XLSX.utils.book_new();
                const ws = XLSX.utils.aoa_to_sheet(sheetData);
                XLSX.utils.book_append_sheet(wb, ws, 'Transaksi');
                XLSX.writeFile(wb, `laporan-keuangan-${Date.now()}.xlsx`);
            } catch (e) {
                alert(e.message || 'Export Excel gagal');
            } finally {
                this.exporting = false;
            }
        },

        async openDetail(id) {
            this.detailOpen = true;
            this.detailLoading = true;
            this.detailData = null;
            const url = this.detailUrlTemplate.replace('__ID__', String(id));
            try {
                const res = await fetch(url, {
                    headers: { Accept: 'application/json', 'X-Requested-With': 'XMLHttpRequest' },
                    credentials: 'same-origin',
                });
                const json = await res.json();
                if (json.success) this.detailData = json.data;
            } catch {
                alert('Gagal memuat detail transaksi');
                this.detailOpen = false;
            } finally {
                this.detailLoading = false;
            }
        },

        openBukti(url, trxId) {
            this.buktiUrl = url;
            this.buktiTrxId = trxId;
            this.buktiOpen = true;
        },

        openReject(id, trxId) {
            this.rejectTrxId = trxId;
            this.rejectAction = this.verifyUrlTemplate.replace('__ID__', String(id));
            this.rejectOpen = true;
        },
    };
}
