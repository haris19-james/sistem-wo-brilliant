/**
 * Brilliant WO — loading untuk navigasi & form (admin & panel internal).
 * Menampilkan overlay premium + skeleton; pesan dari data-loading-message atau path URL.
 */
(function () {
    'use strict';

    const DEFAULT_MESSAGE = 'Memuat halaman Brilliant WO...';

    function minVisibleMs() {
        return document.body?.dataset?.brilliantPanel === 'client' ? 0 : 200;
    }

    let navStartedAt = 0;
    let initialLoadHandled = false;

    const ROUTE_MESSAGES = [
        { test: /\/admin\/dashboard/i, message: 'Memuat ringkasan dashboard...' },
        { test: /\/admin\/booking/i, message: 'Memuat data booking & pesanan...' },
        { test: /\/admin\/paket/i, message: 'Memuat daftar paket pernikahan...' },
        { test: /\/admin\/vendor/i, message: 'Memuat direktori vendor...' },
        { test: /\/admin\/jadwal-acara\/rundown/i, message: 'Menyiapkan rangkaian rundown acara...' },
        { test: /\/admin\/jadwal-acara\/meeting/i, message: 'Memuat jadwal meeting vendor...' },
        { test: /\/admin\/vendor-meetings/i, message: 'Memuat agenda meeting vendor...' },
        { test: /\/admin\/pembayaran/i, message: 'Memuat verifikasi pembayaran...' },
        { test: /\/admin\/chat/i, message: 'Membuka pusat chat...' },
        { test: /\/admin\/pengaturan/i, message: 'Memuat pengaturan sistem...' },
        { test: /\/admin\/meetings/i, message: 'Memuat jadwal meeting...' },
        { test: /\/admin\/notifications/i, message: 'Memuat notifikasi...' },
    ];

    function messageForHref(href) {
        if (!href) return DEFAULT_MESSAGE;
        const match = ROUTE_MESSAGES.find((row) => row.test.test(href));
        return match ? match.message : DEFAULT_MESSAGE;
    }

    function showSkeleton() {
        const el = document.getElementById('page-nav-skeleton');
        if (!el) return;
        el.classList.remove('hidden');
        el.setAttribute('aria-hidden', 'false');
    }

    function hideSkeleton() {
        const el = document.getElementById('page-nav-skeleton');
        if (!el) return;
        el.classList.add('hidden');
        el.setAttribute('aria-hidden', 'true');
    }

    function showLegacyOverlay(message) {
        if (window.loadingOverlay && typeof window.loadingOverlay.show === 'function') {
            window.loadingOverlay.show({ subtitle: message, autoHide: false });
        }
    }

    function hideLegacyOverlay() {
        if (window.loadingOverlay && typeof window.loadingOverlay.hide === 'function') {
            window.loadingOverlay.hide();
        }
    }

    function showNavLoading(message) {
        const text = message || DEFAULT_MESSAGE;
        navStartedAt = Date.now();

        showSkeleton();
        showLegacyOverlay(text);

        if (typeof window.showLoading === 'function') {
            window.showLoading(text);
        }
    }

    function hideNavLoading() {
        const elapsed = Date.now() - navStartedAt;
        const wait = Math.max(0, minVisibleMs() - elapsed);

        const finish = () => {
            hideSkeleton();
            hideLegacyOverlay();
            if (typeof window.hideLoading === 'function') {
                window.hideLoading();
            }
        };

        if (navStartedAt > 0 && wait > 0) {
            setTimeout(finish, wait);
            return;
        }

        finish();
        navStartedAt = 0;
    }

    function isSamePageNavigation(anchor) {
        const href = anchor?.getAttribute('href');
        if (!href || href.startsWith('javascript:')) return false;

        try {
            const url = new URL(anchor.href, window.location.origin);
            if (url.origin !== window.location.origin) return false;

            return url.pathname === window.location.pathname
                && (url.hash !== '' || url.search === window.location.search);
        } catch {
            return false;
        }
    }

    function shouldHandleLink(anchor) {
        if (!anchor || anchor.tagName !== 'A') return false;
        if (anchor.dataset.noLoading !== undefined || anchor.dataset.noSkeleton !== undefined) return false;

        const href = anchor.getAttribute('href');
        if (!href || href.startsWith('#') || href.startsWith('javascript:')) return false;
        if (anchor.target === '_blank' || anchor.hasAttribute('download')) return false;
        if (isSamePageNavigation(anchor)) return false;

        try {
            const url = new URL(anchor.href, window.location.origin);
            return url.origin === window.location.origin;
        } catch {
            return false;
        }
    }

    function shouldHandleForm(form) {
        if (!form || form.tagName !== 'FORM') return false;
        if (form.dataset.noLoading !== undefined || form.dataset.ajax !== undefined) return false;
        if (form.id === 'register-form' || (form.action || '').includes('/register')) return false;
        if (form.id === 'customerChatForm' || form.id === 'chatSendForm' || form.id === 'internalNoteForm') {
            return false;
        }
        return true;
    }

    function resolveMessage(el, fallbackHref) {
        return (
            el?.dataset?.loadingMessage ||
            el?.dataset?.loading ||
            messageForHref(fallbackHref || el?.action || window.location.href)
        );
    }

    function scrollToJadwalSection(hash) {
        const id = hash && hash.startsWith('#') ? hash.slice(1) : (hash || 'rundown-hari-h');
        const target = document.getElementById(id) || document.getElementById('rundown-hari-h');
        const scrollRoot = document.getElementById('app-main');

        if (!target) return;

        if (scrollRoot) {
            const rootRect = scrollRoot.getBoundingClientRect();
            const targetRect = target.getBoundingClientRect();
            scrollRoot.scrollTo({
                top: scrollRoot.scrollTop + (targetRect.top - rootRect.top) - 16,
                behavior: 'smooth',
            });
        } else {
            target.scrollIntoView({ behavior: 'smooth', block: 'start' });
        }
    }

    function handleSamePageNavigation(anchor, event) {
        if (!isSamePageNavigation(anchor)) return false;

        event.preventDefault();

        const url = new URL(anchor.href, window.location.origin);
        scrollToJadwalSection(url.hash || '#rundown-hari-h');
        history.replaceState(null, '', url.pathname + url.search + (url.hash || ''));

        return true;
    }

    function onLinkClick(event) {
        const anchor = event.target.closest('a');
        if (!anchor) return;
        if (event.metaKey || event.ctrlKey || event.shiftKey || event.altKey) return;

        if (handleSamePageNavigation(anchor, event)) return;
        if (!shouldHandleLink(anchor)) return;

        showNavLoading(resolveMessage(anchor, anchor.href));
    }

    function onFormSubmit(event) {
        const form = event.target;
        if (!shouldHandleForm(form)) return;

        const isLogout = (form.action || '').includes('logout');
        const msg = isLogout
            ? 'Keluar dari panel admin...'
            : resolveMessage(form, form.action);

        showNavLoading(msg);
    }

    function onInitialLoad() {
        if (initialLoadHandled) return;
        initialLoadHandled = true;

        const panel = document.body?.dataset?.brilliantPanel;
        if (!panel || panel === 'public' || panel === 'client') return;

        showNavLoading('Memuat panel admin Brilliant WO...');
    }

    document.addEventListener('click', onLinkClick, true);
    document.addEventListener('submit', onFormSubmit, true);

    document.addEventListener('DOMContentLoaded', () => {
        if (sessionStorage.getItem('brilliantFastEntry') === '1') {
            sessionStorage.removeItem('brilliantFastEntry');
            initialLoadHandled = true;
            hideNavLoading();
        } else {
            onInitialLoad();
            hideNavLoading();
        }

        if (window.location.hash === '#vendor-meetings') {
            requestAnimationFrame(() => scrollToJadwalSection('#vendor-meetings'));
        }
    });

    window.addEventListener('pageshow', hideNavLoading);
    window.addEventListener('load', hideNavLoading);

    window.brilliantNavLoading = {
        show: showNavLoading,
        hide: hideNavLoading,
        messageForHref,
    };
})();
