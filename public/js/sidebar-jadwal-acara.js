(function () {
    function scrollToJadwalTarget(hash) {
        const id = hash && hash.startsWith('#') ? hash.slice(1) : (hash || 'rundown-hari-h');
        const target = document.getElementById(id) || document.getElementById('rundown-hari-h');
        const scrollRoot = document.getElementById('app-main') || window;

        if (target) {
            if (scrollRoot === window) {
                target.scrollIntoView({ behavior: 'smooth', block: 'start' });
            } else {
                const rootRect = scrollRoot.getBoundingClientRect();
                const targetRect = target.getBoundingClientRect();
                scrollRoot.scrollTo({
                    top: scrollRoot.scrollTop + (targetRect.top - rootRect.top) - 16,
                    behavior: 'smooth',
                });
            }
        }

        if (hash) {
            history.replaceState(null, '', hash.startsWith('#') ? hash : `#${hash}`);
        }
    }

    function initJadwalNav(root) {
        const toggle = root.querySelector('.jadwal-acara-nav__toggle');
        const submenu = root.querySelector('.jadwal-acara-nav__submenu');
        const chevron = root.querySelector('.jadwal-acara-nav__chevron');

        if (!toggle || !submenu) {
            return;
        }

        const setOpen = (open) => {
            toggle.setAttribute('aria-expanded', open ? 'true' : 'false');
            submenu.classList.toggle('is-open', open);
            submenu.style.maxHeight = open ? submenu.scrollHeight + 'px' : '0';
            if (chevron) {
                chevron.classList.toggle('rotate-180', open);
            }
        };

        if (root.dataset.initialOpen === '1') {
            requestAnimationFrame(() => setOpen(true));
        }

        toggle.addEventListener('click', () => {
            const isOpen = toggle.getAttribute('aria-expanded') === 'true';
            setOpen(!isOpen);
        });

        root.querySelectorAll('.jadwal-acara-nav__link').forEach((link) => {
            link.addEventListener('click', (event) => {
                let url;

                try {
                    url = new URL(link.href, window.location.origin);
                } catch {
                    return;
                }

                if (url.pathname !== window.location.pathname) {
                    return;
                }

                event.preventDefault();
                event.stopPropagation();

                if (window.brilliantNavLoading && typeof window.brilliantNavLoading.hide === 'function') {
                    window.brilliantNavLoading.hide();
                }
                if (typeof window.hideLoading === 'function') {
                    window.hideLoading();
                }

                scrollToJadwalTarget(url.hash || '#rundown-hari-h');

                if (window.innerWidth < 1024) {
                    document.body.dispatchEvent(new CustomEvent('close-sidebar'));
                }
            }, true);
        });
    }

    document.addEventListener('DOMContentLoaded', () => {
        document.querySelectorAll('[data-jadwal-nav]').forEach(initJadwalNav);

        if (window.location.hash === '#vendor-meetings') {
            requestAnimationFrame(() => scrollToJadwalTarget('#vendor-meetings'));
        }
    });
})();
