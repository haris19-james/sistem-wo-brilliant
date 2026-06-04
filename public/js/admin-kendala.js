/**
 * Admin — Analisis Kendala: Tangani, Selesaikan (modal), update UI real-time.
 */
(function () {
    'use strict';

    const panel = document.getElementById('adminKendalaPanel');
    if (!panel) return;

    const csrf = document.querySelector('meta[name="csrf-token"]')?.content || '';
    const statusUrlBase = panel.dataset.statusUrl || '/admin/kendala';
    const aktifList = document.getElementById('adminKendalaAktifList');
    const selesaiList = document.getElementById('adminKendalaSelesaiList');
    const modal = document.getElementById('adminKendalaSelesaiModal');
    const modalForm = document.getElementById('adminKendalaSelesaiForm');
    const modalId = document.getElementById('adminKendalaModalId');
    const modalRingkasan = document.getElementById('adminKendalaModalRingkasan');
    const modalSolusi = document.getElementById('adminKendalaSolusi');
    const modalBatal = document.getElementById('adminKendalaModalBatal');

    const STATUS_BADGE = {
        'Menunggu Tindakan': 'bg-amber-50 text-amber-800 border-amber-200',
        'Dalam Penanganan': 'bg-blue-50 text-blue-700 border-blue-200',
        Selesai: 'bg-green-50 text-green-700 border-green-200',
    };

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

    async function patchStatus(kendalaId, payload) {
        const resp = await fetch(`${statusUrlBase}/${kendalaId}/status`, {
            method: 'PATCH',
            headers: {
                'Content-Type': 'application/json',
                Accept: 'application/json',
                'X-CSRF-TOKEN': csrf,
                'X-Requested-With': 'XMLHttpRequest',
            },
            body: JSON.stringify(payload),
        });
        const data = await parseJson(resp);
        if (data._invalid) {
            throw new Error('Respons server tidak valid (bukan JSON).');
        }
        if (!resp.ok || !data.success) {
            throw new Error(data.message || Object.values(data.errors || {}).flat().join(' ') || 'Gagal memperbarui status');
        }
        return data;
    }

    function updateStatusBadge(row, status, badgeClass) {
        const badge = row.querySelector('.kendala-status-badge');
        if (!badge) return;
        badge.textContent = status;
        badge.className = 'px-2 py-0.5 rounded text-xs font-bold border kendala-status-badge ' + (badgeClass || STATUS_BADGE[status] || STATUS_BADGE['Menunggu Tindakan']);
    }

    function buildSelesaiRowHtml(kendala, row) {
        const ringkasan = row.querySelector('.kendala-ringkasan')?.textContent?.trim() || kendala.ringkasan || '';
        const kondisiBadge = row.querySelector('.border')?.className || '';
        const kondisi = kendala.kondisi || row.querySelector('.border')?.textContent || '';
        const meta = row.querySelector('.text-xs.text-gray-500')?.outerHTML || '';
        const solusi = kendala.tindak_lanjut
            ? `<p class="text-xs text-green-800 mt-2 p-2 bg-green-50 rounded-lg border border-green-100 kendala-solusi"><span class="font-semibold">Solusi:</span> ${escapeHtml(kendala.tindak_lanjut)}</p>`
            : '';

        return `
<div class="px-6 py-4 flex flex-col sm:flex-row sm:items-start gap-4 bg-green-50/30" data-kendala-row="${kendala.id}" data-kendala-aktif="0">
  <div class="flex-1 min-w-0">
    <div class="flex flex-wrap items-center gap-2 mb-1">
      <span class="px-2 py-0.5 rounded text-xs font-bold border ${kendala.kondisi_badge_class || 'bg-gray-100'}">${escapeHtml(kondisi)}</span>
      <span class="px-2 py-0.5 rounded text-xs font-bold border kendala-status-badge ${kendala.status_tindak_badge_class || STATUS_BADGE.Selesai}">Selesai</span>
    </div>
    <p class="text-sm text-gray-900 kendala-ringkasan">${escapeHtml(ringkasan)}</p>
    ${meta}
    ${solusi}
  </div>
</div>`;
    }

    function escapeHtml(str) {
        const d = document.createElement('div');
        d.textContent = str;
        return d.innerHTML;
    }

    function moveToSelesai(row, kendala) {
        const html = buildSelesaiRowHtml(kendala, row);
        row.remove();
        document.getElementById('adminKendalaAktifEmpty')?.remove();
        if (selesaiList) {
            selesaiList.insertAdjacentHTML('afterbegin', html);
            document.getElementById('adminKendalaSelesaiEmpty')?.remove();
        }
        if (aktifList && !aktifList.querySelector('[data-kendala-row]')) {
            aktifList.innerHTML = '<p class="px-6 py-8 text-sm text-gray-500 text-center" id="adminKendalaAktifEmpty">Tidak ada kendala aktif saat ini.</p>';
        }
    }

    function openModal(kendalaId, ringkasan) {
        if (!modal) return;
        modalId.value = kendalaId;
        modalRingkasan.textContent = ringkasan ? 'Kendala: ' + ringkasan : '';
        modalSolusi.value = '';
        modal.classList.remove('hidden');
        modal.classList.add('flex');
        modalSolusi.focus();
    }

    function closeModal() {
        if (!modal) return;
        modal.classList.add('hidden');
        modal.classList.remove('flex');
        modalForm?.reset();
    }

    panel.addEventListener('click', async function (e) {
        const tangani = e.target.closest('.btn-admin-kendala-tangani');
        if (tangani && !tangani.disabled) {
            e.preventDefault();
            const id = tangani.dataset.kendalaId;
            tangani.disabled = true;
            const original = tangani.textContent;
            tangani.textContent = 'Memproses…';
            try {
                const data = await patchStatus(id, { status_tindak: 'Dalam Penanganan' });
                const row = document.querySelector(`[data-kendala-row="${id}"]`);
                if (row) {
                    updateStatusBadge(row, data.kendala.status_tindak, data.kendala.status_tindak_badge_class);
                    tangani.remove();
                }
                toast(data.message);
            } catch (err) {
                alert(err.message);
                tangani.disabled = false;
                tangani.textContent = original;
            }
            return;
        }

        const selesaikan = e.target.closest('.btn-admin-kendala-selesaikan');
        if (selesaikan) {
            e.preventDefault();
            openModal(selesaikan.dataset.kendalaId, selesaikan.dataset.ringkasan || '');
        }
    });

    modalBatal?.addEventListener('click', closeModal);
    modal?.addEventListener('click', function (e) {
        if (e.target === modal) closeModal();
    });

    modalForm?.addEventListener('submit', async function (e) {
        e.preventDefault();
        const id = modalId.value;
        const solusi = modalSolusi.value.trim();
        if (!solusi) {
            alert('Catatan solusi penyelesaian wajib diisi.');
            return;
        }
        const submitBtn = modalForm.querySelector('[type="submit"]');
        submitBtn.disabled = true;
        try {
            const data = await patchStatus(id, {
                status_tindak: 'Selesai',
                tindak_lanjut: solusi,
            });
            const row = document.querySelector(`[data-kendala-row="${id}"]`);
            if (row) {
                moveToSelesai(row, data.kendala);
            }
            closeModal();
            toast(data.message);
        } catch (err) {
            alert(err.message);
        } finally {
            submitBtn.disabled = false;
        }
    });

    function toast(message) {
        const el = document.createElement('div');
        el.className = 'fixed top-4 right-4 z-[300] bg-green-50 border border-green-200 text-green-800 px-4 py-3 rounded-lg text-sm shadow-lg max-w-sm';
        el.textContent = message;
        document.body.appendChild(el);
        setTimeout(() => el.remove(), 3500);
    }
})();
