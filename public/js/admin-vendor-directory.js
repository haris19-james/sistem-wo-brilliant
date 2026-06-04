function adminVendorDirectory(config) {

    return {

        vendors: [],

        search: '',

        category: '',

        location: '',

        page: 1,

        lastPage: 1,

        total: config.total || 0,

        loading: false,

        loadingMore: false,

        cardsUrl: config.cardsUrl || '/admin/vendor/cards',

        modalOpen: false,

        modalLoading: false,

        detail: null,

        detailBase: config.detailBase || '/admin/vendor',

        placeholderVendor: config.placeholderVendor || '/images/placeholders/vendor.svg',

        _debounce: null,

        _observer: null,



        init() {

            this.fetchCards(true);

            this.$watch('search', () => this.debouncedFetch());

            this.$watch('category', () => this.fetchCards(true));

            this.$watch('location', () => this.fetchCards(true));

            this.$nextTick(() => this.setupInfiniteScroll());

        },



        setupInfiniteScroll() {

            const main = document.getElementById('app-main');

            const sentinel = this.$refs.loadMoreSentinel;

            if (!main || !sentinel) return;



            if (this._observer) {

                this._observer.disconnect();

            }



            this._observer = new IntersectionObserver(

                (entries) => {

                    if (entries[0]?.isIntersecting) {

                        this.fetchCards(false);

                    }

                },

                { root: main, rootMargin: '160px', threshold: 0 }

            );

            this._observer.observe(sentinel);

        },



        debouncedFetch() {

            clearTimeout(this._debounce);

            this._debounce = setTimeout(() => this.fetchCards(true), 300);

        },



        async fetchCards(reset = false) {

            if (reset) {

                this.page = 1;

                this.vendors = [];

            }

            if (this.loading || this.loadingMore) return;



            const fetchPage = reset ? 1 : this.page;

            if (!reset && fetchPage > this.lastPage) return;



            this.loading = reset;

            this.loadingMore = !reset;

            if (reset && window.brilliantNavLoading && typeof window.brilliantNavLoading.show === 'function') {
                window.brilliantNavLoading.show('Memuat daftar vendor...');
            } else if (reset && typeof window.showLoading === 'function') {
                window.showLoading('Memuat daftar vendor...');
            }

            const params = new URLSearchParams({ page: String(fetchPage) });

            if (this.search.trim()) params.set('q', this.search.trim());

            if (this.category) params.set('kategori', this.category);

            if (this.location) params.set('lokasi', this.location);



            try {

                const res = await fetch(`${this.cardsUrl}?${params}`, {

                    headers: { Accept: 'application/json', 'X-Requested-With': 'XMLHttpRequest' },

                });

                const json = await res.json();

                const rows = json.data || [];

                this.lastPage = json.meta?.last_page ?? 1;

                this.total = json.meta?.total ?? this.vendors.length;



                if (reset) {

                    this.vendors = rows;

                    this.page = rows.length ? 2 : 1;

                } else {

                    this.vendors = [...this.vendors, ...rows];

                    this.page = fetchPage + 1;

                }

            } catch (e) {

                console.error(e);

            } finally {

                this.loading = false;

                this.loadingMore = false;

                if (reset && window.brilliantNavLoading && typeof window.brilliantNavLoading.hide === 'function') {
                    window.brilliantNavLoading.hide();
                } else if (reset && typeof window.hideLoading === 'function') {
                    window.hideLoading();
                }

            }

        },



        resetFilters() {

            this.search = '';

            this.category = '';

            this.location = '';

            this.fetchCards(true);

        },



        async openDetail(id) {

            this.modalOpen = true;

            this.modalLoading = true;

            this.detail = null;

            try {

                const res = await fetch(`${this.detailBase}/${id}/detail`, {

                    headers: { Accept: 'application/json', 'X-Requested-With': 'XMLHttpRequest' },

                });

                if (!res.ok) {
                    throw new Error(`HTTP ${res.status}`);
                }

                const data = await res.json();

                if (data.success) this.detail = data.vendor;

            } catch (e) {

                console.error(e);
                this.detail = { nama_vendor: 'Gagal memuat detail vendor' };

            } finally {

                this.modalLoading = false;

            }

        },



        closeModal() {

            this.modalOpen = false;

            this.detail = null;

        },

    };

}

