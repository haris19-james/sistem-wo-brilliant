/**
 * Client — laporkan kendala pada pesanan mereka.
 */
(function () {
    const panel = document.getElementById('clientKendalaPanel');
    if (!panel) return;

    const csrf = document.querySelector('meta[name="csrf-token"]')?.content || '';
    const storeUrl = panel.dataset.storeUrl;
    const listUrl = panel.dataset.listUrl;

    async function parseJson(resp) {
        const ct = resp.headers.get('content-type') || '';
        if (!ct.includes('application/json')) {
            return { _invalid: true };
        }
        try {
            return await resp.json();
        } catch {
            return { _invalid: true };
        }
    }

    function renderList(items) {
        const list = document.getElementById('clientKendalaList');
        if (!list) return;

        if (!items.length) {
            list.innerHTML = '<p class="text-sm text-gray-500 text-center py-4">Belum ada kendala dilaporkan.</p>';
            return;
        }

        list.innerHTML = items
            .map(
                (k) => `
            <div class="p-3 bg-gray-50 rounded-lg border border-gray-100 text-sm">
                <div class="flex flex-wrap gap-2 items-center mb-1">
                    <span class="text-xs font-bold text-gray-800">${escapeHtml(k.kondisi)}</span>
                    <span class="text-xs text-gray-500">${escapeHtml(k.tanggal || '')}</span>
                    <span class="text-xs px-2 py-0.5 rounded-full bg-amber-50 text-amber-800">${escapeHtml(k.status_tindak || 'Menunggu Tindakan')}</span>
                </div>
                <p class="text-gray-800">${escapeHtml(k.ringkasan)}</p>
            </div>`
            )
            .join('');
    }

    function escapeHtml(text) {
        const d = document.createElement('div');
        d.textContent = text ?? '';
        return d.innerHTML;
    }

    async function loadList() {
        if (!listUrl) return;
        try {
            const resp = await fetch(listUrl, {
                headers: { Accept: 'application/json', 'X-Requested-With': 'XMLHttpRequest' },
            });
            const data = await parseJson(resp);
            if (!resp.ok || data._invalid) return;
            renderList(data.data || []);
        } catch (e) {
            console.warn('Gagal memuat daftar kendala', e);
        }
    }

    const form = document.getElementById('clientKendalaForm');
    form?.addEventListener('submit', async function (e) {
        e.preventDefault();
        const btn = form.querySelector('button[type="submit"]');
        if (btn?.disabled) return;

        const fd = new FormData(form);
        btn.disabled = true;
        const original = btn.textContent;
        btn.textContent = 'Mengirim...';

        try {
            const resp = await fetch(storeUrl, {
                method: 'POST',
                headers: {
                    Accept: 'application/json',
                    'X-CSRF-TOKEN': csrf,
                    'X-Requested-With': 'XMLHttpRequest',
                },
                body: fd,
            });

            const data = await parseJson(resp);
            if (data._invalid) {
                throw new Error('Server mengembalikan respons tidak valid. Coba refresh halaman.');
            }
            if (!resp.ok || !data.success) {
                throw new Error(data.message || 'Gagal mengirim kendala');
            }

            form.reset();
            await loadList();
            alert(data.message || 'Kendala berhasil dilaporkan.');
        } catch (err) {
            alert(err.message || 'Gagal mengirim kendala');
        } finally {
            btn.disabled = false;
            btn.textContent = original;
        }
    });

    loadList();
})();
