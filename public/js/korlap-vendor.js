/**
 * Korlap — halaman Vendor: filter + panel detail monitoring.
 */
(function () {
    const root = document.getElementById('korlapVendorRoot');
    if (!root) return;

    const listEl = document.getElementById('vendorList');
    const detailEl = document.getElementById('vendorDetailContent');
    const filterForm = document.getElementById('korlapVendorFilterForm');
    const apiVendorsUrl = root.dataset.apiVendors;
    const apiDetailBase = root.dataset.apiDetail;
    const LOADING_MSG = 'Memuat data vendor...';

    let selectedVendorId = root.dataset.initialVendorId
        ? parseInt(root.dataset.initialVendorId, 10)
        : null;

    function esc(text) {
        const el = document.createElement('span');
        el.textContent = text ?? '';
        return el.innerHTML;
    }

    function showLoader() {
        if (typeof window.showLoading === 'function') {
            window.showLoading(LOADING_MSG);
        }
    }

    function hideLoader() {
        if (typeof window.hideLoading === 'function') {
            window.hideLoading();
        }
    }

    function monitoringBadge(v) {
        if (v.monitoring_status === 'aktif_di_acara') {
            const booking = v.nomor_pesanan
                ? `<span class="block text-[10px] font-medium text-green-700 mt-0.5">${esc(v.nomor_pesanan)}</span>`
                : '';
            return `<span class="inline-flex flex-col px-2.5 py-1 rounded-lg text-xs font-semibold text-green-600 bg-green-50 border border-green-100">
                Aktif di Acara${booking}
            </span>`;
        }
        return '<span class="px-2.5 py-1 rounded-lg text-xs font-semibold text-gray-600 bg-gray-100">Tersedia</span>';
    }

    function setActiveRow(vendorId) {
        listEl.querySelectorAll('[data-vendor-id]').forEach((row) => {
            const active = parseInt(row.dataset.vendorId, 10) === vendorId;
            row.classList.toggle('bg-green-50', active);
            row.classList.toggle('border-green-500', active);
            row.classList.toggle('border-transparent', !active);
        });
    }

    function renderList(vendors) {
        if (!vendors.length) {
            listEl.innerHTML =
                '<tr><td colspan="5" class="px-4 py-10 text-center text-sm text-gray-500">Tidak ada vendor ditemukan</td></tr>';
            return;
        }

        listEl.innerHTML = vendors
            .map((v) => {
                const active =
                    selectedVendorId && v.id === selectedVendorId
                        ? 'bg-green-50 border-green-500'
                        : 'border-transparent';
                const rating =
                    v.rating > 0
                        ? `${v.rating}<span class="text-gray-400 font-normal text-xs"> (${v.rating_count})</span>`
                        : '—';

                return `
                <tr data-vendor-id="${v.id}" class="vendor-row cursor-pointer border-l-4 hover:bg-green-50/40 transition ${active}">
                    <td class="px-4 py-3">
                        <div class="flex items-center gap-3">
                            <div class="w-10 h-10 flex-shrink-0 rounded-full overflow-hidden bg-gray-100">
                                <img src="${esc(v.image_url)}" alt="" class="w-full h-full object-cover">
                            </div>
                            <div class="min-w-0">
                                <p class="font-bold text-gray-900 truncate">${esc(v.nama_vendor)}</p>
                                <p class="text-xs text-gray-500 truncate">${esc(v.lokasi || '—')}</p>
                            </div>
                        </div>
                    </td>
                    <td class="px-4 py-3 text-gray-700">${esc(v.kategori)}</td>
                    <td class="px-4 py-3 text-xs text-gray-600">
                        <div>${esc(v.telepon)}</div>
                        <div class="truncate max-w-[140px]">${esc(v.email)}</div>
                    </td>
                    <td class="px-4 py-3">${monitoringBadge(v)}</td>
                    <td class="px-4 py-3 text-center">
                        <span class="text-yellow-500">★</span>
                        <span class="font-semibold text-gray-900 text-sm">${rating}</span>
                    </td>
                </tr>`;
            })
            .join('');

        listEl.querySelectorAll('[data-vendor-id]').forEach((row) => {
            row.addEventListener('click', () => {
                selectVendor(parseInt(row.dataset.vendorId, 10));
            });
        });
    }

    function starsHtml(rating, sizeClass = 'w-3.5 h-3.5') {
        let html = '';
        for (let i = 1; i <= 5; i++) {
            const filled = i <= rating;
            html += `<svg class="${sizeClass} ${filled ? 'text-green-500' : 'text-gray-200'} fill-current" viewBox="0 0 20 20"><path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/></svg>`;
        }
        return html;
    }

    function renderDetail(data) {
        const v = data.vendor;
        const isActive = v.monitoring_status === 'aktif_di_acara';
        const reviews = data.ulasan_klien || [];

        let jadwalBlock = '';
        if (isActive && (data.jadwal_aktif || []).length) {
            jadwalBlock = `
            <div class="space-y-3">
                ${data.jadwal_aktif
                    .map((j) => {
                        const checklist = (j.checklist || [])
                            .map(
                                (c) => `
                        <li class="flex items-start gap-2 text-xs ${c.done ? 'text-green-700' : 'text-gray-500'}">
                            <span class="mt-0.5 w-4 h-4 rounded-full flex items-center justify-center flex-shrink-0 ${c.done ? 'bg-green-600 text-white' : 'border border-gray-300'}">${c.done ? '✓' : ''}</span>
                            ${esc(c.label)}
                        </li>`
                            )
                            .join('');
                        return `
                    <div class="rounded-lg border border-green-100 bg-green-50/40 p-3">
                        <div class="flex items-start justify-between gap-2 mb-2">
                            <div>
                                <p class="font-semibold text-gray-900 text-sm">${esc(j.nama_pasangan)}</p>
                                <p class="text-xs text-gray-600">${esc(j.tanggal_formatted)} · ${esc(j.lokasi || '—')}</p>
                            </div>
                            <span class="text-[10px] font-bold text-green-600 bg-white px-2 py-0.5 rounded border border-green-200">${esc(j.nomor_pesanan)}</span>
                        </div>
                        <p class="text-xs font-medium text-gray-700 mb-2">Checklist kehadiran Hari-H: <span class="text-green-600">${esc(j.kehadiran.status)}</span></p>
                        <ul class="space-y-1">${checklist}</ul>
                    </div>`;
                    })
                    .join('')}
            </div>`;
        } else if (!isActive) {
            jadwalBlock =
                '<p class="text-xs text-gray-500 text-center py-2">Tidak ada penugasan aktif saat ini.</p>';
        }

        const reviewsBlock =
            reviews.length > 0
                ? reviews
                      .map(
                          (r) => `
                <div class="rounded-lg border border-gray-100 bg-white p-3">
                    <div class="flex items-center justify-between gap-2 mb-1">
                        <span class="text-xs font-semibold text-gray-800">${esc(r.customer_label)}</span>
                        <span class="text-[10px] text-gray-400">${esc(r.created_at)}</span>
                    </div>
                    <div class="flex items-center gap-0.5 mb-2">${starsHtml(r.rating)}</div>
                    ${r.ulasan ? `<p class="text-xs text-gray-700 leading-relaxed">${esc(r.ulasan)}</p>` : '<p class="text-xs text-gray-400 italic">Tanpa teks ulasan</p>'}
                    <p class="text-[10px] text-gray-500 mt-2">${esc(r.nomor_pesanan || '')} · ${esc(r.nama_pasangan || '')}</p>
                </div>`
                      )
                      .join('')
                : '<p class="text-xs text-gray-500 text-center py-4">Belum ada ulasan klien untuk vendor ini.</p>';

        detailEl.innerHTML = `
            <div class="p-4 border-b border-gray-100 text-center">
                <div class="w-24 h-24 mx-auto rounded-full overflow-hidden bg-gray-100 mb-3">
                    <img src="${esc(v.image_url)}" alt="" class="w-full h-full object-cover">
                </div>
                <h3 class="text-lg font-bold text-gray-900">${esc(v.nama_vendor)}</h3>
                <p class="text-xs text-green-600 font-semibold mt-1">${esc(v.kategori)}</p>
                <div class="flex items-center justify-center gap-1 mt-2">${starsHtml(Math.round(v.rating || 0), 'w-4 h-4')}</div>
                <p class="text-xs text-gray-500 mt-1">${v.rating_count ? `${v.rating_count} ulasan klien` : 'Belum ada ulasan'}</p>
                <div class="mt-2">${isActive ? monitoringBadge(v) : '<span class="px-2.5 py-1 rounded-lg text-xs font-semibold text-gray-600 bg-gray-100">Tersedia</span>'}</div>
            </div>
            <div class="flex border-b border-gray-100" id="vendorDetailTabs">
                <button type="button" data-tab="profil" class="vendor-tab flex-1 py-2.5 text-xs font-semibold text-green-600 border-b-2 border-green-600">Profil</button>
                <button type="button" data-tab="ulasan" class="vendor-tab flex-1 py-2.5 text-xs font-semibold text-gray-500 border-b-2 border-transparent">Ulasan Klien</button>
            </div>
            <div id="vendorTabProfil" class="vendor-tab-panel">
                <div class="p-4 border-b border-gray-100 space-y-3">
                    <h4 class="font-bold text-gray-900 text-sm">Informasi Profil</h4>
                    <div class="space-y-2 text-xs">
                        <div><p class="text-gray-500">Kontak / Telepon</p><p class="font-medium text-gray-900">${esc(v.telepon)}</p></div>
                        <div><p class="text-gray-500">Email</p><p class="font-medium text-gray-900">${esc(v.email)}</p></div>
                        <div><p class="text-gray-500">Kategori</p><p class="font-medium text-gray-900">${esc(v.kategori)}</p></div>
                        <div><p class="text-gray-500">Lokasi</p><p class="font-medium text-gray-900">${esc(v.lokasi)}</p></div>
                        ${v.harga_info ? `<div><p class="text-gray-500">Info Layanan</p><p class="font-medium text-gray-900">${esc(v.harga_info)}</p></div>` : ''}
                    </div>
                </div>
                ${isActive ? `<div class="p-4 border-b border-gray-100"><h4 class="font-bold text-gray-900 text-sm mb-3">Jadwal Kerja Sama Aktif</h4>${jadwalBlock}</div>` : `<div class="p-4 text-xs text-gray-500 text-center">${jadwalBlock}</div>`}
            </div>
            <div id="vendorTabUlasan" class="vendor-tab-panel hidden p-4 space-y-3">
                <h4 class="font-bold text-gray-900 text-sm">Ulasan Klien</h4>
                <p class="text-xs text-gray-500">Portofolio ulasan dari customer yang pernah menggunakan vendor ini setelah acara selesai.</p>
                ${reviewsBlock}
            </div>`;

        const tabs = detailEl.querySelectorAll('.vendor-tab');
        const panelProfil = detailEl.querySelector('#vendorTabProfil');
        const panelUlasan = detailEl.querySelector('#vendorTabUlasan');

        tabs.forEach((btn) => {
            btn.addEventListener('click', () => {
                const tab = btn.dataset.tab;
                tabs.forEach((b) => {
                    const active = b.dataset.tab === tab;
                    b.classList.toggle('text-green-600', active);
                    b.classList.toggle('border-green-600', active);
                    b.classList.toggle('text-gray-500', !active);
                    b.classList.toggle('border-transparent', !active);
                });
                panelProfil.classList.toggle('hidden', tab !== 'profil');
                panelUlasan.classList.toggle('hidden', tab !== 'ulasan');
            });
        });
    }

    function renderDetailEmpty() {
        detailEl.innerHTML =
            '<div class="p-8 text-center text-sm text-gray-500">Pilih vendor di tabel untuk melihat detail monitoring.</div>';
    }

    async function fetchVendors(params = {}) {
        showLoader();
        try {
            const url = new URL(apiVendorsUrl, window.location.origin);
            if (params.search) url.searchParams.set('search', params.search);
            if (params.kategori) url.searchParams.set('kategori', params.kategori);
            if (params.status) url.searchParams.set('status', params.status);

            const res = await fetch(url.toString(), {
                headers: { Accept: 'application/json', 'X-Requested-With': 'XMLHttpRequest' },
                credentials: 'same-origin',
            });
            if (!res.ok) throw new Error('Gagal memuat vendor');

            const data = await res.json();
            renderList(data.vendors || []);

            if ((data.vendors || []).length) {
                const pick =
                    selectedVendorId && data.vendors.some((v) => v.id === selectedVendorId)
                        ? selectedVendorId
                        : data.vendors[0].id;
                await fetchVendorDetail(pick);
            } else {
                selectedVendorId = null;
                renderDetailEmpty();
            }
        } catch (e) {
            console.error(e);
        } finally {
            hideLoader();
        }
    }

    async function fetchVendorDetail(vendorId) {
        selectedVendorId = vendorId;
        setActiveRow(vendorId);
        showLoader();

        const panel = document.getElementById('detailPanel');
        if (panel && window.innerWidth < 1024) {
            panel.classList.remove('hidden');
            panel.classList.add('flex');
        }

        try {
            const res = await fetch(`${apiDetailBase}/${vendorId}`, {
                headers: { Accept: 'application/json', 'X-Requested-With': 'XMLHttpRequest' },
                credentials: 'same-origin',
            });
            if (!res.ok) throw new Error('Gagal memuat detail');
            renderDetail(await res.json());
        } catch (e) {
            console.error(e);
            detailEl.innerHTML =
                '<div class="p-8 text-center text-sm text-red-600">Gagal memuat detail vendor.</div>';
        } finally {
            hideLoader();
        }
    }

    function selectVendor(vendorId) {
        fetchVendorDetail(vendorId);
    }

    filterForm?.addEventListener('submit', (e) => {
        e.preventDefault();
        const fd = new FormData(filterForm);
        selectedVendorId = null;
        fetchVendors({
            search: fd.get('search') || '',
            kategori: fd.get('kategori') || '',
            status: fd.get('monitoring_status') || '',
        });
    });

    let searchTimer;
    filterForm?.querySelector('[name="search"]')?.addEventListener('input', () => {
        clearTimeout(searchTimer);
        searchTimer = setTimeout(() => filterForm.requestSubmit(), 350);
    });

    filterForm?.querySelectorAll('select').forEach((sel) => {
        sel.addEventListener('change', () => filterForm.requestSubmit());
    });

    document.getElementById('closeDetail')?.addEventListener('click', () => {
        const panel = document.getElementById('detailPanel');
        panel?.classList.add('hidden');
        panel?.classList.remove('flex');
    });

    listEl.querySelectorAll('[data-vendor-id]').forEach((row) => {
        row.addEventListener('click', () => selectVendor(parseInt(row.dataset.vendorId, 10)));
    });

    if (selectedVendorId) {
        fetchVendorDetail(selectedVendorId);
    } else if (listEl.querySelector('[data-vendor-id]')) {
        const first = listEl.querySelector('[data-vendor-id]');
        fetchVendorDetail(parseInt(first.dataset.vendorId, 10));
    } else {
        renderDetailEmpty();
    }
})();
