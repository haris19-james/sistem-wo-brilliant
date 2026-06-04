/**
 * @deprecated Logika navigasi dipusatkan di brilliant-nav-loading.js (tetap di-load untuk kompatibilitas).
 */
(function () {
    if (window.brilliantNavLoading) return;
    const overlay = () => document.getElementById('page-nav-skeleton');
    function showSkeleton() {
        const el = overlay();
        if (el) {
            el.classList.remove('hidden');
            el.setAttribute('aria-hidden', 'false');
        }
        if (typeof window.showLoading === 'function') {
            window.showLoading('Memuat halaman...');
        }
    }
    function hideSkeleton() {
        const el = overlay();
        if (el) {
            el.classList.add('hidden');
            el.setAttribute('aria-hidden', 'true');
        }
        if (typeof window.hideLoading === 'function') {
            window.hideLoading();
        }
    }
    function shouldHandleLink(anchor) {
        if (!anchor || anchor.tagName !== 'A') return false;
        const href = anchor.getAttribute('href');
        if (!href || href.startsWith('#') || href.startsWith('javascript:')) return false;
        if (anchor.target === '_blank' || anchor.hasAttribute('download')) return false;
        if (anchor.dataset.noSkeleton !== undefined || anchor.dataset.noLoading !== undefined) return false;
        try {
            return new URL(anchor.href, window.location.origin).origin === window.location.origin;
        } catch {
            return false;
        }
    }
    document.addEventListener('click', (event) => {
        const anchor = event.target.closest('a');
        if (!shouldHandleLink(anchor)) return;
        if (event.metaKey || event.ctrlKey || event.shiftKey || event.altKey) return;
        showSkeleton();
    }, true);
    window.addEventListener('pageshow', hideSkeleton);
    document.addEventListener('DOMContentLoaded', hideSkeleton);
})();