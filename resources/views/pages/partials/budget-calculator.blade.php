@props([
    'paketStandarJson' => [],
    'minBudget' => 10_000_000,
    'variant' => 'light',
    'inputName' => null,
    'required' => false,
])

@php
    $maxHarga = collect($paketStandarJson)->max('harga') ?: 50_000_000;
    $sliderMax = (int) (ceil($maxHarga * 1.2 / 1_000_000) * 1_000_000);
@endphp

<div
    x-data="budgetCalculator({
        pakets: @js($paketStandarJson),
        minBudget: {{ (int) $minBudget }},
        initialBudget: {{ (int) old('estimasi_budget', $minBudget) }},
    })"
    class="{{ $variant === 'dark' ? 'text-white' : '' }}"
>
    <label class="block text-sm font-semibold mb-2 {{ $variant === 'dark' ? 'text-white' : 'text-gray-800' }}">
        Budget acara Anda
    </label>

    <div class="flex flex-col sm:flex-row gap-3 mb-3">
        <div class="relative flex-1">
            <span class="absolute left-3 top-1/2 -translate-y-1/2 text-sm {{ $variant === 'dark' ? 'text-white/70' : 'text-gray-500' }}">Rp</span>
            <input
                type="number"
                @if($inputName) name="{{ $inputName }}" @endif
                @if($required) required @endif
                x-model.number="budget"
                @input="onBudgetInput()"
                :min="minBudget"
                step="500000"
                class="w-full pl-10 pr-3 py-2.5 rounded-xl border {{ $variant === 'dark' ? 'border-white/30 bg-white/10 text-white placeholder-white/50' : 'border-gray-300 bg-white' }} focus:ring-2 focus:ring-bottle focus:border-bottle"
                placeholder="Contoh: 25000000"
            >
        </div>
    </div>

    <input
        type="range"
        x-model.number="budget"
        @input="onBudgetInput()"
        :min="minBudget"
        :max="sliderMax"
        step="500000"
        class="w-full h-2 rounded-lg appearance-none cursor-pointer {{ $variant === 'dark' ? 'accent-white' : 'accent-bottle' }} mb-4"
    >

    <div
        x-show="recommendation"
        x-cloak
        class="rounded-xl p-4 border {{ $variant === 'dark' ? 'bg-white/10 border-white/20' : 'bg-white border-green-100 shadow-sm' }}"
    >
        <p class="text-xs font-bold uppercase tracking-wide mb-2 {{ $variant === 'dark' ? 'text-white/80' : 'text-bottle' }}">
            Perkiraan paket untuk budget ini
        </p>
        <p class="font-bold text-lg mb-1" x-text="recommendation?.paketNama ? '≈ ' + recommendation.paketNama : ''"></p>
        <p class="text-sm mb-3 {{ $variant === 'dark' ? 'text-white/85' : 'text-gray-600' }}" x-text="recommendation?.ringkasan"></p>

        <ul class="space-y-1.5 text-sm mb-3" x-show="recommendation?.layanan?.length">
            <template x-for="(item, i) in recommendation.layanan" :key="i">
                <li class="flex items-start gap-2">
                    <span class="{{ $variant === 'dark' ? 'text-white' : 'text-bottle' }}">✓</span>
                    <span x-text="item" class="{{ $variant === 'dark' ? 'text-white/90' : 'text-gray-700' }}"></span>
                </li>
            </template>
        </ul>

        <p
            x-show="recommendation?.upgradeNama"
            class="text-xs rounded-lg px-3 py-2 {{ $variant === 'dark' ? 'bg-white/15 text-white/90' : 'bg-leafSoft text-gray-700' }}"
        >
            <span class="font-semibold">Ingin lebih lengkap?</span>
            Tambah <span x-text="formatRp(recommendation?.gapToNext)"></span> untuk mendekati
            <span class="font-semibold" x-text="recommendation?.upgradeNama"></span>.
        </p>
    </div>

    <p class="text-xs mt-2 {{ $variant === 'dark' ? 'text-white/70' : 'text-gray-500' }}">
        Simulasi berdasarkan paket standar Brilliant WO. Penawaran final tetap dikonfirmasi admin.
    </p>
</div>

@once
@push('scripts')
<script>
function budgetCalculator({ pakets, minBudget, initialBudget }) {
    const sorted = [...pakets].sort((a, b) => a.harga - b.harga);
    const sliderMax = sorted.length
        ? Math.ceil(sorted[sorted.length - 1].harga * 1.2 / 500000) * 500000
        : 50000000;

    return {
        pakets: sorted,
        minBudget,
        sliderMax,
        budget: initialBudget || minBudget,
        recommendation: null,

        init() {
            this.recalculate();
            this.$watch('budget', () => this.recalculate());
        },

        onBudgetInput() {
            if (this.budget < this.minBudget) {
                this.budget = this.minBudget;
            }
            this.recalculate();
        },

        recalculate() {
            const budget = Number(this.budget) || 0;
            if (!this.pakets.length) {
                this.recommendation = { ringkasan: 'Hubungi admin untuk simulasi paket.', layanan: [] };
                return;
            }

            const termurah = this.pakets[0];
            let cocok = termurah;
            let next = null;

            if (budget < termurah.harga) {
                next = termurah;
                this.recommendation = {
                    status: 'below_min',
                    paketNama: termurah.nama,
                    paketHarga: termurah.harga,
                    layanan: termurah.layanan || [],
                    upgradeNama: termurah.nama,
                    gapToNext: termurah.harga - budget,
                    ringkasan: `Budget Rp ${this.formatRp(budget)} mendekati ${termurah.nama} (Rp ${this.formatRp(termurah.harga)}). Kurang sekitar Rp ${this.formatRp(termurah.harga - budget)} — tim WO bisa sesuaikan.`,
                };
                return;
            }

            for (const p of this.pakets) {
                if (p.harga <= budget) {
                    cocok = p;
                }
            }
            next = this.pakets.find(p => p.harga > budget) || null;

            let ringkasan = `Dengan budget Rp ${this.formatRp(budget)}, kira-kira setara ${cocok.nama} (Rp ${this.formatRp(cocok.harga)}):`;
            this.recommendation = {
                status: next ? 'matched_with_upgrade' : 'matched_max',
                paketNama: cocok.nama,
                paketHarga: cocok.harga,
                layanan: cocok.layanan || [],
                upgradeNama: next ? next.nama : null,
                gapToNext: next ? next.harga - budget : null,
                ringkasan,
            };
        },

        formatRp(n) {
            return new Intl.NumberFormat('id-ID').format(Math.max(0, Math.round(n || 0)));
        },
    };
}
</script>
@endpush
@endonce
