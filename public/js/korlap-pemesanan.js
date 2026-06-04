/**
 * Korlap — Pemesanan Acara: list filter + detail panel dinamis.
 */
(function () {
    const root = document.getElementById('korlapPemesananRoot');
    if (!root) return;

    const listEl = document.getElementById('pesananList');
    const detailEl = document.getElementById('detailContent');
    const filterForm = document.getElementById('korlapFilterForm');
    const apiBookingsUrl = root.dataset.apiBookings;
    const apiDetailBase = root.dataset.apiDetail;
    const csrf = document.querySelector('meta[name="csrf-token"]')?.content || '';
    const LOADING_MSG = 'Memuat data operasional lapangan...';

    let selectedBookingId = root.dataset.initialBookingId
        ? parseInt(root.dataset.initialBookingId, 10)
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

    function statusBadgeClass(status) {
        if (status === 'Persiapan' || status === 'Menunggu') {
            return 'bg-blue-100 text-blue-800';
        }
        if (status === 'Sedang Berlangsung') {
            return 'bg-yellow-100 text-yellow-800';
        }
        return 'bg-green-100 text-green-800';
    }

    function vendorStatusIconClass(status) {
        const s = (status || '').toLowerCase();
        if (s === 'selesai') {
            return { wrap: 'bg-green-100', icon: 'text-green-600' };
        }
        if (s === 'proses' || s.includes('berjalan')) {
            return { wrap: 'bg-yellow-100', icon: 'text-yellow-600' };
        }
        return { wrap: 'bg-gray-100', icon: 'text-gray-500' };
    }

    function vendorPivotBadgeClass(status) {
        const s = (status || '').toLowerCase();
        if (s === 'hadir' || s === 'selesai') {
            return 'bg-green-100 text-green-800';
        }
        if (s.includes('perjalanan') || s === 'proses') {
            return 'bg-yellow-100 text-yellow-800';
        }
        return 'bg-gray-100 text-gray-700';
    }

    function setActiveCard(bookingId) {
        listEl.querySelectorAll('[data-booking-id]').forEach((card) => {
            const active = parseInt(card.dataset.bookingId, 10) === bookingId;
            card.classList.toggle('border-green-500', active);
            card.classList.toggle('bg-green-50', active);
            card.classList.toggle('border-gray-100', !active);
        });
    }

    function renderList(bookings) {
        if (!bookings.length) {
            listEl.innerHTML =
                '<div class="text-center py-12 text-gray-500 text-sm">Tidak ada pemesanan ditemukan</div>';
            return;
        }

        listEl.innerHTML = bookings
            .map((b) => {
                const active =
                    selectedBookingId && b.id === selectedBookingId
                        ? 'border-green-500 bg-green-50'
                        : 'border-gray-100';
                const badge = statusBadgeClass(b.status);
                const tanggal = b.tanggal_formatted || b.tanggal_acara || '—';
                const jam =
                    b.jam_mulai && b.jam_selesai
                        ? `${esc(b.jam_mulai)} - ${esc(b.jam_selesai)} WIB`
                        : '—';

                return `
                <button type="button" data-booking-id="${b.id}"
                    class="pesanan-card w-full text-left group p-4 rounded-lg border-2 transition-all hover:border-green-500/40 hover:bg-green-50/50 ${active}">
                    <div class="flex gap-3">
                        <div class="w-20 h-20 flex-shrink-0 rounded-lg overflow-hidden bg-gray-100">
                            <img src="${esc(b.foto_url)}" alt="Venue" class="w-full h-full object-cover">
                        </div>
                        <div class="flex-1 min-w-0">
                            <div class="flex items-start justify-between gap-2 mb-1">
                                <h3 class="font-bold text-gray-900 truncate">${esc(b.nama_pasangan)}</h3>
                                <span class="px-2 py-0.5 rounded-full text-xs font-semibold whitespace-nowrap ${badge}">${esc(b.status)}</span>
                            </div>
                            <p class="text-xs text-gray-600 mb-1">${esc(b.lokasi || 'Lokasi belum ditentukan')}</p>
                            <div class="flex items-center gap-4 text-xs text-gray-500 mb-2">
                                <span>📅 ${esc(tanggal)}</span>
                                <span>🕐 ${jam}</span>
                            </div>
                            <span class="inline-flex items-center text-xs font-semibold text-green-600 group-hover:text-green-700 transition">
                                Lihat Detail
                                <svg class="w-3 h-3 ml-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                            </span>
                        </div>
                    </div>
                </button>`;
            })
            .join('');

        listEl.querySelectorAll('[data-booking-id]').forEach((card) => {
            card.addEventListener('click', () => {
                const id = parseInt(card.dataset.bookingId, 10);
                selectBooking(id);
            });
        });
    }

    function renderDetail(data) {
        const b = data.booking;
        const pct = data.progress_percent ?? 0;
        const vendorGrid = (data.vendor_status || [])
            .map((v) => {
                const ic = vendorStatusIconClass(v.status);
                return `
                <div class="flex items-center gap-2 p-2 bg-gray-50 rounded-lg">
                    <div class="w-8 h-8 rounded-full ${ic.wrap} flex items-center justify-center flex-shrink-0">
                        <svg class="w-4 h-4 ${ic.icon}" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/></path></svg>
                    </div>
                    <div class="text-xs">
                        <p class="font-semibold text-gray-900">${esc(v.label)}</p>
                        <p class="text-gray-500 text-[10px]">${esc(v.status)}</p>
                    </div>
                </div>`;
            })
            .join('');

        const timeline = (data.rundowns || []).length
            ? data.rundowns
                  .map((r, i) => {
                      const line =
                          i < data.rundowns.length - 1
                              ? '<div class="w-0.5 h-6 bg-gray-200"></div>'
                              : '';
                      return `
                    <div class="flex gap-2">
                        <div class="text-right w-10 font-bold text-gray-900">${esc(r.waktu_mulai)}</div>
                        <div class="flex flex-col items-center">
                            <div class="w-2.5 h-2.5 rounded-full bg-green-600"></div>
                            ${line}
                        </div>
                        <p class="text-gray-600 mt-0.5">${esc(r.kegiatan)}</p>
                    </div>`;
                  })
                  .join('')
            : '<p class="text-xs text-gray-500">Belum ada rundown.</p>';

        const vendors = (data.vendors || []).length
            ? data.vendors
                  .map((v) => {
                      const badge = vendorPivotBadgeClass(v.status);
                      return `
                    <div class="flex items-center justify-between">
                        <div class="flex items-center gap-2">
                            <div class="w-6 h-6 rounded-full bg-green-100 flex items-center justify-center text-[10px]">${esc(v.icon)}</div>
                            <p class="font-medium text-gray-900">${esc(v.kategori || 'Vendor')}<br><span class="text-gray-500 font-normal">${esc(v.nama_vendor)}</span></p>
                        </div>
                        <span class="px-2 py-0.5 ${badge} rounded text-[10px] font-semibold">${esc(v.status)}</span>
                    </div>`;
                  })
                  .join('')
            : '<p class="text-xs text-gray-500">Belum ada vendor ditugaskan.</p>';

        const jam =
            b.jam_mulai && b.jam_selesai
                ? `${esc(b.jam_mulai)} - ${esc(b.jam_selesai)} WIB`
                : '—';

        detailEl.innerHTML = `
            <div class="p-4 border-b border-gray-200">
                <div class="flex gap-3 mb-4">
                    <img src="${esc(b.foto_url)}" alt="Venue" class="w-24 h-24 rounded-lg object-cover">
                    <div class="flex-1">
                        <h3 class="font-bold text-gray-900">${esc(b.nama_pasangan)}</h3>
                        <p class="text-xs text-gray-600 mt-1"><strong>Paket:</strong><br>${esc(b.paket_nama)}</p>
                        <p class="text-xs text-gray-600 mt-2"><strong>Tanggal:</strong> ${esc(b.tanggal_formatted)}</p>
                        <p class="text-xs text-gray-600 mt-1"><strong>Venue:</strong> ${esc(b.lokasi)}</p>
                        <p class="text-xs text-gray-600 mt-1"><strong>Waktu:</strong> ${jam}</p>
                    </div>
                </div>
                <div class="space-y-2">
                    <div class="flex items-center justify-between">
                        <span class="text-xs font-semibold text-gray-700">Progress Persiapan</span>
                        <span class="text-xs font-bold text-green-600">${pct}%</span>
                    </div>
                    <div class="w-full bg-gray-200 rounded-full h-3 overflow-hidden">
                        <div class="bg-green-600 h-full rounded-full transition-all" style="width:${pct}%"></div>
                    </div>
                </div>
            </div>
            <div class="p-4 border-b border-gray-200">
                <p class="text-xs font-semibold text-gray-700 mb-3">Status Vendor</p>
                <div class="grid grid-cols-2 gap-2">${vendorGrid || '<p class="text-xs text-gray-500 col-span-2">—</p>'}</div>
            </div>
            <div class="p-4 border-b border-gray-200 grid grid-cols-2 gap-4">
                <div>
                    <p class="text-xs font-semibold text-gray-700 mb-3">Timeline Acara</p>
                    <div class="space-y-3 text-xs">${timeline}</div>
                </div>
                <div>
                    <p class="text-xs font-semibold text-gray-700 mb-3">Vendor Assigned</p>
                    <div class="space-y-2 text-xs">${vendors}</div>
                </div>
            </div>
            <div class="p-4">
                <div class="bg-gray-50 rounded-lg p-3">
                    <p class="text-xs font-semibold text-gray-700 mb-2">Catatan</p>
                    <p class="text-xs text-gray-600">${esc(b.catatan_khusus || 'Tidak ada catatan.')}</p>
                </div>
                <a href="${esc(b.detail_url)}" class="mt-4 inline-flex w-full items-center justify-center px-4 py-2 border border-green-500 text-green-600 hover:bg-green-50 rounded-lg font-semibold text-sm transition">
                    Buka halaman lengkap
                </a>
            </div>`;
    }

    function renderDetailEmpty() {
        detailEl.innerHTML =
            '<div class="p-8 text-center text-sm text-gray-500">Pilih acara di daftar kiri untuk melihat detail.</div>';
    }

    async function fetchBookings(params = {}) {
        showLoader();
        try {
            const url = new URL(apiBookingsUrl, window.location.origin);
            if (params.search) url.searchParams.set('search', params.search);
            if (params.status) url.searchParams.set('status', params.status);
            if (params.date) url.searchParams.set('date', params.date);

            const res = await fetch(url.toString(), {
                headers: {
                    Accept: 'application/json',
                    'X-Requested-With': 'XMLHttpRequest',
                    'X-CSRF-TOKEN': csrf,
                },
                credentials: 'same-origin',
            });

            if (!res.ok) throw new Error('Gagal memuat daftar');

            const data = await res.json();
            renderList(data.bookings || []);

            if ((data.bookings || []).length) {
                const pick =
                    selectedBookingId &&
                    data.bookings.some((b) => b.id === selectedBookingId)
                        ? selectedBookingId
                        : data.bookings[0].id;
                await fetchDetailAcara(pick);
            } else {
                selectedBookingId = null;
                renderDetailEmpty();
            }
        } catch (e) {
            console.error(e);
        } finally {
            hideLoader();
        }
    }

    async function fetchDetailAcara(bookingId) {
        selectedBookingId = bookingId;
        setActiveCard(bookingId);
        showLoader();

        const detailPanel = document.getElementById('detailPanel');
        if (detailPanel && window.innerWidth < 1024) {
            detailPanel.classList.remove('hidden');
        }

        try {
            const res = await fetch(`${apiDetailBase}/${bookingId}`, {
                headers: {
                    Accept: 'application/json',
                    'X-Requested-With': 'XMLHttpRequest',
                    'X-CSRF-TOKEN': csrf,
                },
                credentials: 'same-origin',
            });

            if (!res.ok) throw new Error('Gagal memuat detail');

            const data = await res.json();
            renderDetail(data);
        } catch (e) {
            console.error(e);
            detailEl.innerHTML =
                '<div class="p-8 text-center text-sm text-red-600">Gagal memuat detail acara.</div>';
        } finally {
            hideLoader();
        }
    }

    function selectBooking(bookingId) {
        fetchDetailAcara(bookingId);
    }

    filterForm?.addEventListener('submit', (e) => {
        e.preventDefault();
        const fd = new FormData(filterForm);
        selectedBookingId = null;
        fetchBookings({
            search: fd.get('q') || '',
            status: fd.get('status') || '',
            date: fd.get('tanggal') || '',
        });
    });

    listEl?.addEventListener('click', (e) => {
        const card = e.target.closest('[data-booking-id]');
        if (!card) return;
        e.preventDefault();
    });

    document.getElementById('closeDetail')?.addEventListener('click', () => {
        document.getElementById('detailPanel')?.classList.add('hidden');
    });

    // Bind kartu SSR awal
    listEl.querySelectorAll('[data-booking-id]').forEach((card) => {
        card.addEventListener('click', () => {
            selectBooking(parseInt(card.dataset.bookingId, 10));
        });
    });

    if (selectedBookingId) {
        fetchDetailAcara(selectedBookingId);
    } else if (listEl.querySelector('[data-booking-id]')) {
        const first = listEl.querySelector('[data-booking-id]');
        fetchDetailAcara(parseInt(first.dataset.bookingId, 10));
    } else {
        renderDetailEmpty();
    }
})();
