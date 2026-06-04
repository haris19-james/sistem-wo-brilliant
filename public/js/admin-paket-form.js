/**
 * Form Tambah/Edit Paket — input Rupiah (tampilan berformat, nilai kirim integer).
 */
function adminPaketForm(config) {
    const DP_MIN = config.dpMin ?? 1_000_000;

    return {
        isKustom: !!config.isKustom,
        hargaRaw: Number(config.harga) || 0,
        hargaDisplay: '',
        dpRaw: Number(config.dpMinimal) || DP_MIN,
        dpDisplay: '',
        dpError: '',

        init() {
            this.hargaDisplay = this.formatRupiah(this.hargaRaw);
            this.dpDisplay = this.formatRupiah(this.dpRaw);
            this.validateDp();
        },

        formatRupiah(value) {
            const n = this.parseDigits(value);
            if (n <= 0) {
                return '';
            }
            return n.toString().replace(/\B(?=(\d{3})+(?!\d))/g, '.');
        },

        parseDigits(value) {
            const digits = String(value ?? '').replace(/\D/g, '');
            if (digits === '') {
                return 0;
            }
            const parsed = parseInt(digits, 10);
            return Number.isFinite(parsed) ? parsed : 0;
        },

        onHargaInput(event) {
            const digits = event.target.value.replace(/\D/g, '');
            this.hargaRaw = digits === '' ? 0 : parseInt(digits, 10);
            this.hargaDisplay = this.formatRupiah(this.hargaRaw);
            event.target.value = this.hargaDisplay;
        },

        onDpInput(event) {
            const digits = event.target.value.replace(/\D/g, '');
            this.dpRaw = digits === '' ? 0 : parseInt(digits, 10);
            this.dpDisplay = this.formatRupiah(this.dpRaw);
            event.target.value = this.dpDisplay;
            this.validateDp();
        },

        validateDp() {
            if (this.dpRaw < DP_MIN) {
                this.dpError = 'DP minimal adalah Rp 1.000.000';
            } else {
                this.dpError = '';
            }
        },

        get canSubmit() {
            const hargaOk = this.isKustom ? this.hargaRaw >= 0 : this.hargaRaw > 0;
            return hargaOk && this.dpRaw >= DP_MIN;
        },

        prepareSubmit(event) {
            this.validateDp();
            if (!this.canSubmit) {
                event.preventDefault();
                return;
            }
        },
    };
}
