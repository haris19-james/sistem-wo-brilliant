/**
 * Form booking customer — default dari paket + override custom (Alpine.js state).
 */
document.addEventListener('alpine:init', () => {
    Alpine.data('bookingFormState', (config) => ({
        apiDefaultsBase: config.apiDefaultsBase || '',
        lokasi: config.old?.lokasi || '',
        jumlahTamu: config.old?.jumlah_tamu ?? '',
        temaSelect: '',
        temaCustom: config.old?.tema || '',
        temaMode: 'custom',
        temas: [],
        paketMeta: null,
        capacityWarning: '',
        estimatedSurcharge: 0,
        estimatedTotal: null,
        loadingDefaults: false,
        defaultsApplied: false,

        init() {
            if (this.jumlahTamu === '' || this.jumlahTamu === null) {
                this.jumlahTamu = 200;
            }

            this.$watch('jumlahTamu', () => this.recalcSurcharge());

            const paketId = document.getElementById('paket_id')?.value;
            if (paketId) {
                this.onPaketSelected(paketId, false);
            } else if (config.old?.tema) {
                this.temaCustom = config.old.tema;
                this.temaMode = 'custom';
            }
        },

        get resolvedTema() {
            if (this.temaMode === 'custom') {
                return (this.temaCustom || '').trim();
            }

            return (this.temaSelect || '').trim();
        },

        formatRp(value) {
            return Number(value || 0).toLocaleString('id-ID');
        },

        async onPaketSelected(paketId, resetFields = true) {
            if (typeof window.updatePaketPreview === 'function') {
                window.updatePaketPreview();
            }
            if (typeof window.loadPaketVendors === 'function') {
                window.loadPaketVendors();
            }

            if (!paketId) {
                this.paketMeta = null;
                this.temas = [];
                this.capacityWarning = '';
                this.estimatedSurcharge = 0;
                this.estimatedTotal = null;
                this.defaultsApplied = false;
                return;
            }

            await this.fetchDefaults(paketId, resetFields);
        },

        async fetchDefaults(paketId, applyFields = true) {
            if (!this.apiDefaultsBase) {
                return;
            }

            this.loadingDefaults = true;
            const url = `${this.apiDefaultsBase}/${encodeURIComponent(paketId)}/defaults`;

            try {
                const response = await fetch(url, {
                    headers: {
                        Accept: 'application/json',
                        'X-Requested-With': 'XMLHttpRequest',
                    },
                    credentials: 'same-origin',
                });

                if (!response.ok) {
                    throw new Error('Gagal memuat default paket');
                }

                const json = await response.json();
                const data = json.data || json;
                this.paketMeta = data;

                if (applyFields) {
                    this.applyDefaults(data);
                } else {
                    this.temas = data.temas || [];
                    this.syncTemaFromOld();
                    this.recalcSurcharge();
                }

                this.defaultsApplied = true;
            } catch (e) {
                console.warn('[bookingFormState]', e);
            } finally {
                this.loadingDefaults = false;
            }
        },

        applyDefaults(data) {
            if (data.is_kustom) {
                this.temas = [];
                this.temaMode = 'custom';
                this.recalcSurcharge();
                return;
            }

            if (data.default_lokasi) {
                this.lokasi = data.default_lokasi;
            }

            if (data.kapasitas_tamu) {
                this.jumlahTamu = data.kapasitas_tamu;
            } else if (data.suggested_jumlah_tamu) {
                this.jumlahTamu = data.suggested_jumlah_tamu;
            }

            this.temas = data.temas || [];

            if (this.temas.length > 0) {
                this.temaMode = 'select';
                this.temaSelect = data.suggested_tema || this.temas[0].nama;
                this.temaCustom = '';
            } else {
                this.temaMode = 'custom';
                this.temaSelect = '';
                this.temaCustom = '';
            }

            this.recalcSurcharge();
        },

        syncTemaFromOld() {
            const oldTema = (config.old?.tema || '').trim();
            if (!oldTema) {
                return;
            }

            const match = this.temas.find((t) => t.nama === oldTema);
            if (match) {
                this.temaMode = 'select';
                this.temaSelect = match.nama;
                this.temaCustom = '';
            } else {
                this.temaMode = 'custom';
                this.temaCustom = oldTema;
            }
        },

        onTemaSelectChange() {
            if (this.temaSelect === '__custom__') {
                this.temaMode = 'custom';
                if (!this.temaCustom) {
                    this.temaCustom = '';
                }
            } else {
                this.temaMode = 'select';
            }
        },

        recalcSurcharge() {
            if (!this.paketMeta || this.paketMeta.is_kustom) {
                this.capacityWarning = '';
                this.estimatedSurcharge = 0;
                this.estimatedTotal = null;
                return;
            }

            const kapasitas = parseInt(this.paketMeta.kapasitas_tamu, 10) || null;
            const rate = parseInt(this.paketMeta.harga_tambahan_per_tamu, 10) || 0;
            const tamu = parseInt(this.jumlahTamu, 10) || 0;
            const base = parseInt(this.paketMeta.harga, 10) || 0;

            if (!kapasitas || tamu <= kapasitas) {
                this.capacityWarning = '';
                this.estimatedSurcharge = 0;
                this.estimatedTotal = base;
                return;
            }

            const extra = tamu - kapasitas;
            this.estimatedSurcharge = extra * rate;
            this.estimatedTotal = base + this.estimatedSurcharge;
            this.capacityWarning =
                `Tamu (${tamu}) melebihi kapasitas paket (${kapasitas} pax). ` +
                `Estimasi tambahan Rp ${this.formatRp(this.estimatedSurcharge)} akan ditambahkan ke total invoice.`;
        },
    }));
});
