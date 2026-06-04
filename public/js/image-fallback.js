/**
 * Resolusi URL gambar & fallback placeholder (Paket / Vendor).
 */
(function () {
    const config = window.BrilliantImageConfig || {};

    function placeholder(type) {
        if (type === 'vendor') {
            return config.placeholderVendor || '/images/placeholders/vendor.svg';
        }

        return config.placeholderPackage || '/images/placeholders/package.svg';
    }

    function isAbsolute(url) {
        return /^https?:\/\//i.test(url || '');
    }

    function normalizeStoragePath(path) {
        if (!path) return '';
        let p = String(path).replace(/\\/g, '/').trim();
        if (isAbsolute(p)) return p;
        return p.replace(/^(?:public\/)?(?:storage\/)?/i, '').replace(/^\/+/, '');
    }

    function resolvePath(value, fallbackUrl, type) {
        if (value) {
            const normalized = normalizeStoragePath(value);
            if (isAbsolute(normalized)) {
                return normalized;
            }
            if (normalized) {
                return '/storage/' + normalized.split('/').map(encodeURIComponent).join('/');
            }
        }

        if (fallbackUrl) {
            const ext = String(fallbackUrl).trim();
            if (!ext) return placeholder(type);
            return isAbsolute(ext) ? ext : 'https://' + ext.replace(/^\/+/, '');
        }

        return placeholder(type);
    }

    function onError(img) {
        if (!img || img.dataset.fallbackApplied === '1') return;
        img.dataset.fallbackApplied = '1';
        img.onerror = null;
        img.src = img.dataset.placeholder || placeholder(img.dataset.placeholderType || 'package');
    }

    window.BrilliantImages = {
        resolvePath,
        onError,
        placeholder,
    };
})();
