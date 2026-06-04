/**
 * Brilliant WO — Luxury Wedding Loading Overlay
 * Global: showLoading(message), hideLoading(), fetchWithLoading(url, options, message)
 */
(function () {
    'use strict';

    const overlay = document.getElementById('loading-overlay-premium');
    const messageEl = document.getElementById('loading-premium-message');
    const DEFAULT_MESSAGE = 'Memproses permintaan Anda...';

    let activeRequests = 0;

    function setMessage(message) {
        if (messageEl) {
            messageEl.textContent = message || DEFAULT_MESSAGE;
        }
    }

    function showLoading(message, options = {}) {
        if (!overlay) return;

        setMessage(message || options.subtitle || DEFAULT_MESSAGE);
        overlay.classList.remove('hidden', 'is-hiding');
        overlay.classList.add('is-visible');
        overlay.setAttribute('aria-hidden', 'false');
        activeRequests += 1;
    }

    function hideLoading() {
        if (!overlay) return;

        activeRequests = Math.max(0, activeRequests - 1);
        if (activeRequests > 0) return;

        overlay.classList.add('is-hiding');
        overlay.setAttribute('aria-hidden', 'true');

        requestAnimationFrame(() => {
            overlay.classList.add('hidden');
            overlay.classList.remove('is-visible', 'is-hiding');
        });
    }

    function hideLoadingImmediate() {
        activeRequests = 0;
        if (!overlay) return;
        overlay.classList.add('hidden');
        overlay.classList.remove('is-visible', 'is-hiding');
        overlay.setAttribute('aria-hidden', 'true');
    }

    async function fetchWithLoading(url, options = {}, message) {
        showLoading(message || 'Memeriksa ketersediaan tanggal acara...');
        try {
            return await fetch(url, options);
        } finally {
            hideLoadingImmediate();
        }
    }

    window.showLoading = showLoading;
    window.hideLoading = hideLoadingImmediate;
    window.fetchWithLoading = fetchWithLoading;
    window.loadingOverlayPremium = {
        show: showLoading,
        hide: hideLoadingImmediate,
        fetch: fetchWithLoading,
    };

    function releaseScrollLock() {
        document.documentElement.style.overflow = '';
        document.body.style.overflow = '';
    }

    function hideAllOverlays() {
        hideLoadingImmediate();
        releaseScrollLock();
        if (window.loadingOverlay && typeof window.loadingOverlay.hide === 'function') {
            window.loadingOverlay.hide();
        }
    }

    function scheduleHideAfterNavigation() {
        if (window.brilliantNavLoading && typeof window.brilliantNavLoading.hide === 'function') {
            window.brilliantNavLoading.hide();
            return;
        }
        hideAllOverlays();
    }

    document.addEventListener('DOMContentLoaded', () => {
        if (sessionStorage.getItem('brilliantFastEntry') === '1') {
            hideAllOverlays();
            return;
        }
        scheduleHideAfterNavigation();
    });
    window.addEventListener('pageshow', scheduleHideAfterNavigation);
    window.addEventListener('load', scheduleHideAfterNavigation);
})();
