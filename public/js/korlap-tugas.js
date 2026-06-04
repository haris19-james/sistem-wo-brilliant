/**
 * Korlap — Tugas Lapangan: kanban, drawer tambah tugas, verifikasi.
 */
/** Fallback jika Alpine belum siap (klik tombol header). */
window.openKorlapTugasDrawer = function openKorlapTugasDrawer() {
    const root = document.getElementById('tugasLapanganRoot');
    if (!root) return;
    const data = root._x_dataStack?.[0];
    if (data && typeof data.openDrawer === 'function') {
        data.openDrawer();
        return;
    }
    const filterVal = document.getElementById('filterAcara')?.value;
    if (!filterVal) {
        alert('Pilih acara di filter terlebih dahulu, atau pilih acara saat drawer terbuka.');
    }
};

/** Alias kompatibilitas — pemanggilan lama `addTask()` mengarah ke submit drawer. */
window.addTask = function addTask() {
    const root = document.getElementById('tugasLapanganRoot');
    const data = root?._x_dataStack?.[0];
    if (data?.handleSimpanTugas) {
        return data.handleSimpanTugas();
    }
    if (data?.submitDrawerForm) {
        return data.submitDrawerForm();
    }
    if (data?.openDrawer) {
        data.openDrawer();
    }
};

async function parseJsonResponse(resp) {
    const contentType = resp.headers.get('content-type') || '';
    if (!contentType.includes('application/json')) {
        return { _rawHtml: true };
    }
    try {
        return await resp.json();
    } catch {
        return { _parseError: true };
    }
}

/**
 * Fallback klik jika Alpine gagal bind — tetap log & panggil handler Alpine bila ada.
 */
function initTugasDrawerButtonFallback() {
    if (window.__tugasDrawerBtnBound) return;
    window.__tugasDrawerBtnBound = true;

    document.addEventListener('click', function (e) {
        const btn = e.target.closest('#btnSimpanTugas');
        if (!btn || btn.disabled) return;

        const root = document.getElementById('tugasLapanganRoot');
        const alpine = root?._x_dataStack?.[0];

        if (!alpine || typeof alpine.handleSimpanTugas !== 'function') {
            console.log('Button clicked! (Alpine tidak terikat — periksa error di konsol)');
            alert('Form tugas belum siap. Muat ulang halaman (Ctrl+F5).');
        }
    });
}

if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', initTugasDrawerButtonFallback);
} else {
    initTugasDrawerButtonFallback();
}

window.korlapTugasPage = function korlapTugasPage() {
    const root = document.getElementById('tugasLapanganRoot');
    const csrf = document.querySelector('meta[name="csrf-token"]')?.content || '';

    return {
        drawerOpen: false,
        toastShow: false,
        toastMessage: '',
        formErrors: [],
        isSubmitting: false,
        loadingVendors: false,
        vendors: [],
        checklists: [],
        selectedFilterPesanan: '',
        acaraMeta: {},
        storeUrl: '',
        vendorsUrlBase: '',
        verifyBase: '',

        form: {
            nama_tugas: '',
            pesanan_id: '',
            vendor_id: '',
            kategori: '',
            prioritas: 'medium',
            deadline_date: '',
            deadline_time: '',
            pic_id: '',
            catatan: '',
        },

        get hasSelectedAcara() {
            return !!this.selectedFilterPesanan;
        },

        initPage() {
            if (!root) return;

            this.verifyBase = root.dataset.verifyUrl || '/lapangan/tugas';
            this.storeUrl = root.dataset.storeUrl || '';
            this.vendorsUrlBase = root.dataset.vendorsUrlBase || '';
            this.form.pic_id = root.dataset.defaultPic || '';
            this.selectedFilterPesanan = root.dataset.selectedPesanan || '';

            try {
                this.acaraMeta = JSON.parse(root.dataset.acaraMeta || '{}');
            } catch {
                this.acaraMeta = {};
            }

            const filterAcara = document.getElementById('filterAcara');
            if (filterAcara) {
                this.selectedFilterPesanan = filterAcara.value || this.selectedFilterPesanan;
                filterAcara.addEventListener('change', () => {
                    this.selectedFilterPesanan = filterAcara.value;
                });
            }

            this.bindKanbanEvents();

            if (root.dataset.openDrawer === '1' && this.hasSelectedAcara) {
                this.$nextTick(() => this.openDrawer());
            }

            const flash = root.dataset.flashSuccess;
            if (flash) {
                this.showToast(flash);
            }

            initTugasDrawerButtonFallback();
        },

        openDrawer() {
            const filterVal = document.getElementById('filterAcara')?.value || this.selectedFilterPesanan;
            if (!filterVal) {
                alert('Pilih acara terlebih dahulu pada filter di atas.');
                return;
            }
            this.resetDrawerForm();
            this.form.pesanan_id = String(filterVal);
            this.formErrors = [];
            this.drawerOpen = true;
            this.onAcaraChange();
        },

        closeDrawer() {
            this.drawerOpen = false;
            this.isSubmitting = false;
            this.formErrors = [];
        },

        resetDrawerForm() {
            this.form = {
                nama_tugas: '',
                pesanan_id: '',
                vendor_id: '',
                kategori: '',
                prioritas: 'medium',
                deadline_date: '',
                deadline_time: '',
                pic_id: root?.dataset.defaultPic || '',
                catatan: '',
            };
            this.vendors = [];
            this.checklists = [];
        },

        showToast(message) {
            this.toastMessage = message;
            this.toastShow = true;
            setTimeout(() => {
                this.toastShow = false;
            }, 3500);
        },

        applyDeadlineFromAcara() {
            const meta = this.acaraMeta[this.form.pesanan_id];
            if (!meta?.tanggal) return;
            this.form.deadline_date = meta.tanggal;
            this.form.deadline_time = meta.jam || '12:00';
        },

        async onAcaraChange() {
            this.form.vendor_id = '';
            this.vendors = [];
            if (!this.form.pesanan_id) return;

            this.applyDeadlineFromAcara();
            this.loadingVendors = true;

            try {
                const res = await fetch(`${this.vendorsUrlBase}/${this.form.pesanan_id}/vendors`, {
                    headers: { Accept: 'application/json' },
                });
                const data = await parseJsonResponse(res);
                if (data._rawHtml || data._parseError) {
                    throw new Error('Respons server tidak valid saat memuat vendor.');
                }
                if (!res.ok) throw new Error(data.message || 'Gagal memuat vendor');
                this.vendors = data.vendors || [];
            } catch (e) {
                console.error(e);
                this.formErrors = ['Gagal memuat daftar vendor untuk acara ini.'];
            } finally {
                this.loadingVendors = false;
            }
        },

        onVendorChange() {
            const v = this.vendors.find((x) => String(x.id) === String(this.form.vendor_id));
            if (v?.kategori) {
                this.form.kategori = v.kategori;
            }
        },

        addChecklist() {
            this.checklists.push({ text: '', completed: false });
        },

        removeChecklist(index) {
            this.checklists.splice(index, 1);
        },

        /** Kumpulkan payload dari state Alpine (lebih andal daripada FormData DOM saja). */
        buildTaskPayload() {
            const payload = {
                nama_tugas: String(this.form.nama_tugas || '').trim(),
                pesanan_id: String(this.form.pesanan_id || '').trim(),
                vendor_id: String(this.form.vendor_id || '').trim(),
                kategori: String(this.form.kategori || '').trim(),
                prioritas: String(this.form.prioritas || 'medium').trim(),
                deadline_date: String(this.form.deadline_date || '').trim(),
                deadline_time: String(this.form.deadline_time || '').trim(),
                pic_id: String(this.form.pic_id || '').trim(),
                catatan: String(this.form.catatan || '').trim(),
                checklists: this.checklists
                    .map((c) => ({
                        text: String(c.text || '').trim(),
                        completed: !!c.completed,
                    }))
                    .filter((c) => c.text.length > 0),
            };

            const fd = new FormData();
            fd.append('_token', csrf);
            Object.entries(payload).forEach(([key, val]) => {
                if (key === 'checklists') return;
                fd.append(key, val);
            });
            payload.checklists.forEach((item, index) => {
                fd.append(`checklists_text[${index}]`, item.text);
                fd.append(`checklists_completed[${index}]`, item.completed ? '1' : '0');
            });

            return { payload, formData: fd };
        },

        validateTaskPayload(payload) {
            const errors = [];
            if (!payload.nama_tugas) errors.push('Nama tugas wajib diisi.');
            if (!payload.pesanan_id) errors.push('Pilih acara terlebih dahulu.');
            if (!payload.vendor_id) errors.push('Pilih vendor pada acara ini.');
            if (!payload.kategori) errors.push('Kategori wajib dipilih.');
            if (!payload.deadline_date) errors.push('Tanggal deadline wajib diisi.');
            if (!payload.deadline_time) errors.push('Waktu deadline wajib diisi.');
            if (!payload.pic_id) errors.push('PIC / penanggung jawab wajib dipilih.');
            if (this.vendors.length === 0 && payload.pesanan_id) {
                errors.push('Acara ini belum memiliki vendor — tidak bisa menambah tugas.');
            }
            const emptyChecklist = this.checklists.some((c) => !String(c.text || '').trim());
            if (this.checklists.length > 0 && emptyChecklist) {
                errors.push('Isi semua item checklist atau hapus baris kosong.');
            }
            return errors;
        },

        /**
         * Handler tombol "Simpan Tugas" — alias submitDrawerForm untuk debugging.
         */
        async handleSimpanTugas(event) {
            console.log('Button clicked!');

            if (this.isSubmitting) {
                console.warn('[Simpan Tugas] Tombol diklik saat masih menyimpan — diabaikan.');
                return;
            }

            if (event?.preventDefault) {
                event.preventDefault();
            }

            return this.submitDrawerForm(event);
        },

        refreshTaskBoard(pesananId) {
            const url = new URL(window.location.href);
            if (pesananId) url.searchParams.set('pesanan_id', pesananId);
            url.searchParams.delete('open_drawer');
            window.location.href = url.toString();
        },

        async submitDrawerForm(event) {
            if (event?.preventDefault) {
                event.preventDefault();
            }

            if (this.isSubmitting) {
                console.warn('[Simpan Tugas] Sudah memproses, abaikan klik ganda.');
                return;
            }

            this.formErrors = [];
            const { payload, formData } = this.buildTaskPayload();
            const validationErrors = this.validateTaskPayload(payload);

            console.log('[Simpan Tugas] Payload siap dikirim:', payload);

            if (validationErrors.length) {
                this.formErrors = validationErrors;
                console.warn('[Simpan Tugas] Validasi client gagal:', validationErrors);
                return;
            }

            if (!this.storeUrl) {
                this.formErrors = ['URL simpan tugas tidak dikonfigurasi.'];
                console.error('[Simpan Tugas] storeUrl kosong');
                return;
            }

            this.isSubmitting = true;

            try {
                console.log('[Simpan Tugas] POST', this.storeUrl);

                const resp = await fetch(this.storeUrl, {
                    method: 'POST',
                    headers: {
                        Accept: 'application/json',
                        'X-CSRF-TOKEN': csrf,
                        'X-Requested-With': 'XMLHttpRequest',
                    },
                    body: formData,
                });

                const data = await parseJsonResponse(resp);

                console.log('[Simpan Tugas] HTTP', resp.status, data);

                if (data._rawHtml && resp.ok) {
                    this.closeDrawer();
                    this.showToast('Tugas berhasil ditambahkan.');
                    this.refreshTaskBoard(payload.pesanan_id);
                    return;
                }

                if (data._rawHtml || data._parseError) {
                    const msg =
                        'Server mengembalikan respons bukan JSON (HTTP ' +
                        resp.status +
                        '). Periksa log [TugasController@store].';
                    this.formErrors = [msg];
                    console.error('[Simpan Tugas] JSON parse gagal', { status: resp.status, data });
                    return;
                }

                if (!resp.ok) {
                    const errPayload = {
                        status: resp.status,
                        statusText: resp.statusText,
                        body: data,
                    };
                    console.error('[Simpan Tugas] API error', errPayload);

                    if (resp.status === 422 && data.errors) {
                        this.formErrors = Object.values(data.errors).flat();
                    } else if (resp.status === 403) {
                        this.formErrors = [data.message || 'Akses ditolak (403).'];
                    } else if (resp.status === 404) {
                        this.formErrors = ['Endpoint tidak ditemukan (404).'];
                    } else if (resp.status >= 500) {
                        this.formErrors = [data.message || 'Kesalahan server (500). Cek log Laravel.'];
                    } else {
                        this.formErrors = [data.message || `Gagal menyimpan tugas (HTTP ${resp.status}).`];
                    }
                    return;
                }

                if (!data.success) {
                    this.formErrors = [data.message || 'Gagal menyimpan tugas.'];
                    console.error('[Simpan Tugas] success=false', data);
                    return;
                }

                console.log('[Simpan Tugas] Berhasil', data);
                this.closeDrawer();
                this.showToast(data.message || 'Tugas berhasil ditambahkan.');
                this.refreshTaskBoard(data.pesanan_id || payload.pesanan_id);
            } catch (err) {
                console.error('[Simpan Tugas] Network/exception', err);
                this.formErrors = [err.message || 'Terjadi kesalahan jaringan.'];
            } finally {
                this.isSubmitting = false;
            }
        },

        bindKanbanEvents() {
            const LOADING_MSG = 'Memuat data tugas...';

            const showLoader = () => {
                if (typeof window.showLoading === 'function') window.showLoading(LOADING_MSG);
            };
            const hideLoader = () => {
                if (typeof window.hideLoading === 'function') window.hideLoading();
            };

            const updateCounters = () => {
                const pending = document.querySelectorAll('.pending-column .task-card').length;
                const progress = document.querySelectorAll('.in-progress-column .task-card').length;
                const completed = document.querySelectorAll('.completed-column .task-card').length;
                const pc = document.querySelector('.pending-count');
                const ic = document.querySelector('.in-progress-count');
                const cc = document.querySelector('.completed-count');
                if (pc) pc.textContent = pending;
                if (ic) ic.textContent = progress;
                if (cc) cc.textContent = completed;
            };

            const columnForStatus = (status) => {
                if (status === 'pending') return '.pending-column';
                if (status === 'completed') return '.completed-column';
                return '.in-progress-column';
            };

            const applyBadge = (card, status, label) => {
                const badge = card?.querySelector('[data-status-badge]');
                if (!badge) return;
                badge.textContent = label || status;
                badge.className = 'inline-flex items-center px-2.5 py-1 rounded-lg text-xs font-semibold ';
                if (status === 'pending') badge.className += 'bg-gray-100 text-gray-700';
                else if (status === 'in_progress') badge.className += 'bg-green-50 text-green-700';
                else if (status === 'awaiting_verification') badge.className += 'bg-amber-50 text-amber-800 border border-amber-200';
                else if (status === 'completed') badge.className += 'bg-green-100 text-green-800';
            };

            document.getElementById('searchInput')?.addEventListener('input', function () {
                const term = this.value.toLowerCase();
                document.querySelectorAll('.task-card').forEach((card) => {
                    const name = (card.dataset.taskName || '').toLowerCase();
                    card.style.display = name.includes(term) ? '' : 'none';
                });
            });

            document.getElementById('filterPrioritas')?.addEventListener('change', function () {
                const pri = this.value;
                document.querySelectorAll('.task-card').forEach((card) => {
                    if (!pri) {
                        card.style.display = '';
                        return;
                    }
                    card.style.display = card.dataset.prioritas === pri ? '' : 'none';
                });
            });

            document.getElementById('filterAcara')?.addEventListener('change', function () {
                if (this.value) document.getElementById('tugasFilterForm')?.requestSubmit();
            });

            document.addEventListener('change', async function (e) {
                const el = e.target;
                if (!el.classList.contains('task-status-select')) return;

                const tugasId = el.dataset.taskId;
                const newStatus = el.value;
                const prevStatus = el.dataset.prevStatus;

                if (newStatus === 'completed') {
                    alert('Status Selesai hanya via tombol Verifikasi Selesai.');
                    el.value = prevStatus;
                    return;
                }

                showLoader();
                el.disabled = true;

                try {
                    const resp = await fetch(`/lapangan/tugas/${tugasId}/status`, {
                        method: 'PATCH',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': csrf,
                            Accept: 'application/json',
                        },
                        body: JSON.stringify({ status: newStatus }),
                    });

                    const data = await resp.json();
                    if (!resp.ok) throw new Error(data.message || 'Gagal memperbarui status');

                    const card = el.closest('.task-card');
                    const target = document.querySelector(columnForStatus(newStatus));
                    if (card && target) {
                        card.dataset.status = newStatus;
                        target.prepend(card);
                        applyBadge(card, newStatus, data.status_label);
                    }

                    el.dataset.prevStatus = newStatus;
                    if (newStatus === 'awaiting_verification') {
                        location.reload();
                    }
                    updateCounters();
                } catch (err) {
                    alert(err.message || 'Gagal menyimpan status');
                    el.value = prevStatus;
                } finally {
                    el.disabled = false;
                    hideLoader();
                }
            });

            document.addEventListener('click', async function (e) {
                const btn = e.target.closest('.btn-verify-task');
                if (!btn) return;

                const tugasId = btn.dataset.taskId;
                if (!confirm('Verifikasi tugas ini sebagai selesai di lapangan?')) return;

                showLoader();
                btn.disabled = true;

                try {
                    const resp = await fetch(`${root.dataset.verifyUrl}/${tugasId}/verify`, {
                        method: 'POST',
                        headers: {
                            'X-CSRF-TOKEN': csrf,
                            Accept: 'application/json',
                        },
                    });

                    const data = await resp.json();
                    if (!resp.ok) throw new Error(data.message || 'Verifikasi gagal');

                    const card = btn.closest('.task-card');
                    const target = document.querySelector('.completed-column');
                    if (card && target) {
                        card.dataset.status = 'completed';
                        target.prepend(card);
                        applyBadge(card, 'completed', data.status_label);
                        const actions = card.querySelector('.task-status-select')?.parentElement;
                        if (actions) {
                            actions.innerHTML =
                                '<p class="text-[10px] text-green-600 font-medium">✓ Diverifikasi Korlap</p>';
                        }
                    }
                    updateCounters();
                } catch (err) {
                    alert(err.message || 'Verifikasi gagal');
                } finally {
                    btn.disabled = false;
                    hideLoader();
                }
            });

            updateCounters();
        },
    };
};
