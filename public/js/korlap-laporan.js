/**
 * Korlap — Pusat Intelejen Lapangan: chart kendala & konfirmasi kehadiran.
 */
(function () {
    const root = document.getElementById('laporanIntelRoot');
    if (!root) return;

    const csrf = document.querySelector('meta[name="csrf-token"]')?.content || '';
    const confirmBase = root.dataset.confirmBase || '/lapangan/laporan/pesanan';

    let chartData = [];
    try {
        chartData = JSON.parse(root.dataset.chart || '[]');
    } catch {
        chartData = [];
    }

    const greenShades = [
        '#16a34a',
        '#22c55e',
        '#4ade80',
        '#86efac',
        '#15803d',
        '#bbf7d0',
    ];

    function initChart() {
        const canvas = document.getElementById('kendalaChartCanvas');
        if (!canvas || !chartData.length || typeof Chart === 'undefined') return;

        new Chart(canvas, {
            type: 'doughnut',
            data: {
                labels: chartData.map((d) => d.label),
                datasets: [
                    {
                        data: chartData.map((d) => d.count),
                        backgroundColor: chartData.map((_, i) => greenShades[i % greenShades.length]),
                        borderWidth: 2,
                        borderColor: '#fff',
                    },
                ],
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: { position: 'bottom', labels: { boxWidth: 12, font: { size: 11 } } },
                },
            },
        });
    }

    document.addEventListener('click', async function (e) {
        const btn = e.target.closest('.btn-confirm-attendance');
        if (!btn) return;

        const pesananId = btn.dataset.pesanan;
        const vendorId = btn.dataset.vendor;
        if (!pesananId || !vendorId) return;

        if (!confirm('Konfirmasi kehadiran vendor ini di lapangan?')) return;

        btn.disabled = true;
        btn.textContent = 'Memproses...';

        try {
            const resp = await fetch(`${confirmBase}/${pesananId}/vendor/${vendorId}/confirm-attendance`, {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': csrf,
                    Accept: 'application/json',
                    'Content-Type': 'application/json',
                },
            });

            const data = await resp.json();
            if (!resp.ok) throw new Error(data.message || 'Gagal mengonfirmasi');

            const row = document.querySelector(`[data-attendance-row="${pesananId}-${vendorId}"]`);
            if (row) {
                const statusEl = row.querySelector('.attendance-status');
                if (statusEl) {
                    const isLate = data.attendance?.is_late;
                    statusEl.textContent = data.attendance?.status || 'Hadir';
                    statusEl.className =
                        'attendance-status inline-flex px-2 py-0.5 rounded text-xs font-semibold ' +
                        (isLate ? 'bg-amber-100 text-amber-800' : 'bg-green-100 text-green-800');
                }
                const cells = row.querySelectorAll('td');
                if (cells[2]) cells[2].textContent = data.attendance?.arrived_at || '—';
                btn.outerHTML = '<span class="text-xs text-green-600 font-medium">✓ Divalidasi</span>';
            }

            setTimeout(() => window.location.reload(), 600);
        } catch (err) {
            alert(err.message || 'Konfirmasi gagal');
            btn.disabled = false;
            btn.textContent = 'Konfirmasi';
        }
    });

    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', initChart);
    } else {
        initChart();
    }
})();
