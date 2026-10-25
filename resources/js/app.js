const ready = (callback) => {
    if (document.readyState !== 'loading') {
        callback();
    } else {
        document.addEventListener('DOMContentLoaded', callback);
    }
};

ready(() => {
    const html = document.documentElement;
    const sidebar = document.querySelector('[data-sidebar]');
    const overlay = document.querySelector('[data-sidebar-overlay]');
    const toggles = document.querySelectorAll('[data-sidebar-toggle]');
    const closeButtons = document.querySelectorAll('[data-sidebar-close]');
    const collapseButton = document.querySelector('[data-sidebar-collapse]');
    const isDesktop = () => window.matchMedia('(min-width: 1024px)').matches;
    let lastFocused = null;

    const setExpanded = (expanded) => {
        toggles.forEach((button) => button.setAttribute('aria-expanded', String(expanded)));
    };

    const openSidebar = () => {
        if (!sidebar) return;
        lastFocused = document.activeElement;
        sidebar.classList.remove('-translate-x-full');
        overlay?.classList.remove('hidden');
        document.body.style.overflow = 'hidden';
        setExpanded(true);
        sidebar.querySelector('[data-sidebar-close]')?.focus();
    };

    const closeSidebar = () => {
        if (!sidebar) return;
        sidebar.classList.add('-translate-x-full');
        overlay?.classList.add('hidden');
        document.body.style.overflow = '';
        setExpanded(false);
        if (lastFocused instanceof HTMLElement) {
            lastFocused.focus();
            lastFocused = null;
        }
    };

    toggles.forEach((button) => {
        button.addEventListener('click', () => {
            if (sidebar?.classList.contains('-translate-x-full') || isDesktop()) {
                openSidebar();
            } else {
                closeSidebar();
            }
        });
    });

    closeButtons.forEach((button) => button.addEventListener('click', closeSidebar));
    overlay?.addEventListener('click', closeSidebar);

    document.addEventListener('keydown', (event) => {
        if (event.key === 'Escape') {
            if (sidebar && !sidebar.classList.contains('-translate-x-full') && !isDesktop()) {
                closeSidebar();
            }
            document.querySelectorAll('details[data-menu][open]').forEach((menu) => menu.removeAttribute('open'));
        }
    });

    window.addEventListener('resize', () => {
        if (isDesktop()) {
            overlay?.classList.add('hidden');
            document.body.style.overflow = '';
            setExpanded(false);
        }
    });

    if (collapseButton) {
        const collapsed = localStorage.getItem('glow-sidebar-collapsed') === '1';
        html.classList.toggle('sidebar-collapsed', collapsed);
        collapseButton.setAttribute('aria-pressed', String(collapsed));
        collapseButton.setAttribute('aria-label', collapsed ? 'Expand navigation' : 'Collapse navigation');

        collapseButton.addEventListener('click', () => {
            const next = !html.classList.contains('sidebar-collapsed');
            html.classList.toggle('sidebar-collapsed', next);
            localStorage.setItem('glow-sidebar-collapsed', next ? '1' : '0');
            collapseButton.setAttribute('aria-pressed', String(next));
            collapseButton.setAttribute('aria-label', next ? 'Expand navigation' : 'Collapse navigation');
        });
    }

    const siteNav = document.querySelector('[data-site-nav]');

    if (siteNav) {
        const updateNav = () => siteNav.classList.toggle('is-scrolled', window.scrollY > 8);
        updateNav();
        window.addEventListener('scroll', updateNav, { passive: true });
    }

    const revealTargets = [];
    const prefersReducedMotion = window.matchMedia('(prefers-reduced-motion: reduce)').matches;

    document.querySelectorAll('[data-reveal-group]').forEach((group) => {
        Array.from(group.children).forEach((child, index) => {
            child.style.setProperty('--reveal-delay', `${Math.min(index, 6) * 70}ms`);
            revealTargets.push(child);
        });
    });

    document.querySelectorAll('[data-reveal]').forEach((element) => revealTargets.push(element));

    if (revealTargets.length) {
        if (prefersReducedMotion || ! ('IntersectionObserver' in window)) {
            revealTargets.forEach((element) => element.classList.add('is-revealed'));
        } else {
            const revealObserver = new IntersectionObserver((entries, observer) => {
                entries.forEach((entry) => {
                    if (! entry.isIntersecting) return;
                    entry.target.classList.add('is-revealed');
                    observer.unobserve(entry.target);
                });
            }, { threshold: 0.12, rootMargin: '0px 0px -8% 0px' });

            revealTargets.forEach((element) => revealObserver.observe(element));
        }
    }

    document.addEventListener('click', (event) => {
        document.querySelectorAll('details[data-menu][open]').forEach((menu) => {
            if (!menu.contains(event.target)) {
                menu.removeAttribute('open');
            }
        });
    });

    document.querySelectorAll('[data-dismiss-alert]').forEach((button) => {
        button.addEventListener('click', () => {
            button.closest('[role="status"], [role="alert"]')?.remove();
        });
    });

    document.querySelectorAll('[data-password-toggle]').forEach((button) => {
        const input = document.getElementById(button.dataset.passwordToggle);

        if (!input) return;

        button.addEventListener('click', () => {
            const show = input.type === 'password';
            input.type = show ? 'text' : 'password';
            button.setAttribute('aria-pressed', String(show));
            button.setAttribute('aria-label', show ? 'Hide password' : 'Show password');
            button.querySelector('[data-eye-open]')?.classList.toggle('hidden', show);
            button.querySelector('[data-eye-off]')?.classList.toggle('hidden', ! show);
        });
    });

    const backToTop = document.querySelector('[data-back-to-top]');

    if (backToTop) {
        const toggleBackToTop = () => {
            backToTop.classList.toggle('hidden', window.scrollY < 400);
        };

        toggleBackToTop();
        window.addEventListener('scroll', toggleBackToTop, { passive: true });
        backToTop.addEventListener('click', () => {
            window.scrollTo({ top: 0, behavior: 'smooth' });
        });
    }

    document.querySelectorAll('[data-location-state]').forEach((stateSelect) => {
        const citySelect = document.getElementById(stateSelect.dataset.locationCity);

        if (!citySelect) return;

        const resetCity = (message = 'Select city / LGA') => {
            citySelect.replaceChildren(new Option(message, ''));
            citySelect.disabled = false;
        };

        stateSelect.addEventListener('change', async () => {
            const stateId = stateSelect.selectedOptions[0]?.dataset.stateId;

            if (!stateId) {
                resetCity();
                return;
            }

            resetCity('Loading cities...');
            citySelect.disabled = true;

            try {
                const url = new URL(stateSelect.dataset.lgaUrl, window.location.origin);
                url.searchParams.set('state', stateId);

                const response = await fetch(url, {
                    headers: { Accept: 'application/json' },
                });

                if (!response.ok) throw new Error('Unable to load cities.');

                const lgas = await response.json();
                citySelect.replaceChildren(new Option('Select city / LGA', ''));

                lgas.forEach((lga) => {
                    citySelect.add(new Option(lga.name, lga.name));
                });
            } catch (error) {
                resetCity('Unable to load cities');
                return;
            }

            citySelect.disabled = false;
        });
    });

    document.querySelectorAll('form[data-confirm]').forEach((form) => {
        form.addEventListener('submit', (event) => {
            if (!window.confirm(form.dataset.confirm)) {
                event.preventDefault();
            }
        });
    });

    document.querySelectorAll('button[data-confirm]').forEach((button) => {
        button.addEventListener('click', (event) => {
            if (!window.confirm(button.dataset.confirm)) {
                event.preventDefault();
            }
        });
    });

    document.querySelectorAll('form').forEach((form) => {
        if ((form.getAttribute('method') || 'get').toLowerCase() === 'get') return;
        if (form.hasAttribute('data-no-disable')) return;

        form.addEventListener('submit', (event) => {
            if (event.defaultPrevented) return;

            form.querySelectorAll('button[type="submit"]').forEach((button) => {
                button.disabled = true;
                button.setAttribute('aria-busy', 'true');
                button.classList.add('opacity-70', 'cursor-wait');
            });
        });
    });
});
