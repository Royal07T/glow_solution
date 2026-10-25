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
