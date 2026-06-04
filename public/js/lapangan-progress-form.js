/**
 * Auto-hitung Total Progress (%) dari dropdown aspek persiapan.
 * Setara React useEffect: $watch pada setiap status → hitungProgress().
 */
(function () {
    'use strict';

    const BOBOT = { Menunggu: 0, Proses: 50, Selesai: 100 };
    const ASPEK_KEYS = ['venue', 'makeup', 'catering', 'dekorasi', 'dokumentasi'];

    window.hitungProgress = function hitungProgress(statuses) {
        const values = ASPEK_KEYS.map((key) => BOBOT[statuses[key]] ?? 0);
        if (values.length === 0) {
            return 0;
        }
        return Math.round(values.reduce((sum, n) => sum + n, 0) / values.length);
    };

    window.progressPersiapanForm = function progressPersiapanForm(initialStatuses, savedPersentase) {
        return {
            statuses: { ...initialStatuses },
            persentase: 0,
            manualOverride: false,

            init() {
                const auto = window.hitungProgress(this.statuses);
                const saved = Number(savedPersentase);
                if (!Number.isNaN(saved) && saved !== auto) {
                    this.manualOverride = true;
                    this.persentase = saved;
                } else {
                    this.manualOverride = false;
                    this.persentase = auto;
                }

                ASPEK_KEYS.forEach((key) => {
                    this.$watch(`statuses.${key}`, () => this.syncAutoProgress());
                });
            },

            syncAutoProgress() {
                if (!this.manualOverride) {
                    this.persentase = window.hitungProgress(this.statuses);
                }
            },

            onPersentaseInput() {
                if (this.persentase === '' || this.persentase === null || Number.isNaN(Number(this.persentase))) {
                    this.manualOverride = false;
                    this.persentase = window.hitungProgress(this.statuses);
                    return;
                }
                this.manualOverride = true;
                this.persentase = Math.min(100, Math.max(0, Number(this.persentase)));
            },

            onPersentaseBlur() {
                if (this.persentase === '' || this.persentase === null || Number.isNaN(Number(this.persentase))) {
                    this.manualOverride = false;
                    this.persentase = window.hitungProgress(this.statuses);
                }
            },

            beforeSubmit() {
                if (!this.manualOverride && this.$refs.persentaseInput) {
                    this.$refs.persentaseInput.removeAttribute('name');
                }
            },
        };
    };
})();
