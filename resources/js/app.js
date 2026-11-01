document.documentElement.classList.add('has-js');

const ready = (callback) => {
    if (document.readyState !== 'loading') {
        callback();
    } else {
        document.addEventListener('DOMContentLoaded', callback);
    }
};

ready(() => {
    const html = document.documentElement;
    const prefersReducedMotion = window.matchMedia('(prefers-reduced-motion: reduce)').matches;
    const preloader = document.querySelector('[data-site-preloader]');

    if (preloader) {
        let hasSeenPreloader = false;

        try {
            hasSeenPreloader = sessionStorage.getItem('glow-preloader-seen') === '1';
        } catch {}

        if (hasSeenPreloader || prefersReducedMotion) {
            preloader.remove();
        } else {
            try {
                sessionStorage.setItem('glow-preloader-seen', '1');
            } catch {}

            document.body.classList.add('preloader-active');

            const startedAt = performance.now();
            let hidden = false;

            const hidePreloader = () => {
                if (hidden) return;

                hidden = true;
                preloader.classList.add('is-hidden');
                document.body.classList.remove('preloader-active');
                window.setTimeout(() => preloader.remove(), 550);
            };

            const hideAfterMinimumDisplay = () => {
                const remaining = Math.max(0, 650 - (performance.now() - startedAt));
                window.setTimeout(hidePreloader, remaining);
            };

            if (document.readyState === 'complete') {
                hideAfterMinimumDisplay();
            } else {
                window.addEventListener('load', hideAfterMinimumDisplay, { once: true });
            }

            window.setTimeout(hidePreloader, 3800);
        }
    }

    const networkStatus = document.querySelector('[data-network-status]');

    if (networkStatus) {
        const networkMessage = networkStatus.querySelector('[data-network-message]');
        const networkRetry = networkStatus.querySelector('[data-network-retry]');
        let onlineTimer;

        const updateNetworkStatus = (online) => {
            window.clearTimeout(onlineTimer);
            networkStatus.dataset.state = online ? 'online' : 'offline';
            networkStatus.hidden = false;

            if (networkMessage) {
                networkMessage.textContent = online
                    ? 'Connection restored. You can continue.'
                    : 'You are offline. Some features may be unavailable.';
            }

            if (online) {
                onlineTimer = window.setTimeout(() => {
                    networkStatus.hidden = true;
                }, 4000);
            }
        };

        if (! navigator.onLine) {
            updateNetworkStatus(false);
        }

        window.addEventListener('offline', () => updateNetworkStatus(false));
        window.addEventListener('online', () => updateNetworkStatus(true));

        networkRetry?.addEventListener('click', () => {
            if (navigator.onLine) {
                window.location.reload();
            }
        });
    }

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

    const mobileMenuToggle = document.querySelector('[data-mobile-menu-toggle]');
    const mobileMenu = document.querySelector('[data-mobile-menu]');
    const menuOpenIcon = mobileMenuToggle?.querySelector('[data-menu-open]');
    const menuCloseIcon = mobileMenuToggle?.querySelector('[data-menu-close]');

    const setMobileMenu = (open) => {
        mobileMenu?.classList.toggle('hidden', !open);
        menuOpenIcon?.classList.toggle('hidden', open);
        menuCloseIcon?.classList.toggle('hidden', !open);
        mobileMenuToggle?.setAttribute('aria-expanded', String(open));
    };

    if (mobileMenuToggle && mobileMenu) {
        mobileMenuToggle.addEventListener('click', () => {
            setMobileMenu(mobileMenu.classList.contains('hidden'));
        });

        mobileMenu.querySelectorAll('a').forEach((link) => {
            link.addEventListener('click', () => setMobileMenu(false));
        });

        document.addEventListener('click', (event) => {
            if (!mobileMenu.classList.contains('hidden') && !siteNav?.contains(event.target)) {
                setMobileMenu(false);
            }
        });
    }

    const revealTargets = [];

    document.querySelectorAll('[data-reveal-group]').forEach((group) => {
        Array.from(group.children).forEach((child, index) => {
            child.style.setProperty('--reveal-delay', `${Math.min(index, 6) * 70}ms`);
            revealTargets.push(child);
        });
    });

    document.querySelectorAll('[data-reveal]').forEach((element) => revealTargets.push(element));

    document.querySelectorAll('[data-progress]').forEach((bar) => {
        const value = Math.max(0, Math.min(100, Number.parseFloat(bar.dataset.progress) || 0));
        bar.style.width = `${value}%`;
    });

    document.querySelectorAll('[data-overlay-opacity]').forEach((overlay) => {
        const value = Math.max(0, Math.min(100, Number.parseFloat(overlay.dataset.overlayOpacity) || 0));
        overlay.style.opacity = String(value / 100);
    });

    document.querySelectorAll('img[data-hide-on-error]').forEach((image) => {
        const hide = () => image.classList.add('hidden');

        if (image.complete && image.naturalWidth === 0) {
            hide();
        } else {
            image.addEventListener('error', hide, { once: true });
        }
    });

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

    document.querySelectorAll('[data-sensitive-toggle]').forEach((button) => {
        const container = button.closest('[data-sensitive]');
        const masked = container?.querySelector('[data-sensitive-masked]');
        const actual = container?.querySelector('[data-sensitive-actual]');

        if (!container || !masked || !actual) return;

        button.addEventListener('click', () => {
            const show = actual.classList.contains('hidden');
            const label = button.dataset.sensitiveLabel || 'information';
            const action = show ? 'Hide' : 'Show';

            masked.classList.toggle('hidden', show);
            actual.classList.toggle('hidden', ! show);
            button.setAttribute('aria-pressed', String(show));
            button.setAttribute('aria-label', `${action} ${label}`);
            button.setAttribute('title', `${action} ${label}`);
            button.querySelector('[data-sensitive-eye-open]')?.classList.toggle('hidden', show);
            button.querySelector('[data-sensitive-eye-off]')?.classList.toggle('hidden', ! show);
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

    const confirmDialog = document.querySelector('[data-confirm-dialog]');
    const confirmDialogTitle = confirmDialog?.querySelector('[data-confirm-dialog-title]');
    const confirmDialogMessage = confirmDialog?.querySelector('[data-confirm-dialog-message]');
    const confirmDialogConfirm = confirmDialog?.querySelector('[data-confirm-dialog-confirm]');
    const confirmDialogCancel = confirmDialog?.querySelector('[data-confirm-dialog-cancel]');
    const confirmDialogBackdrop = confirmDialog?.querySelector('[data-confirm-dialog-backdrop]');
    const confirmDialogIconWrap = confirmDialog?.querySelector('[data-confirm-dialog-icon-wrap]');
    const confirmDialogIcons = confirmDialog ? confirmDialog.querySelectorAll('[data-confirm-dialog-icon]') : [];
    const confirmDialogVariants = {
        primary: {
            iconWrap: ['bg-brand-50', 'text-brand-700'],
            confirm: ['bg-brand-700', 'hover:bg-brand-600', 'focus-visible:ring-brand-300'],
        },
        success: {
            iconWrap: ['bg-green-50', 'text-green-700'],
            confirm: ['bg-green-700', 'hover:bg-green-800', 'focus-visible:ring-green-300'],
        },
        danger: {
            iconWrap: ['bg-red-50', 'text-danger'],
            confirm: ['bg-danger', 'hover:bg-red-700', 'focus-visible:ring-red-300'],
        },
    };
    const confirmDialogVariantClasses = Object.values(confirmDialogVariants).flatMap((variant) => [
        ...variant.iconWrap,
        ...variant.confirm,
    ]);
    let activeConfirmCallback = null;
    let confirmDialogPreviousFocus = null;
    let confirmDialogPreviousOverflow = '';
    let confirmDialogIsOpen = false;

    const closeConfirmDialog = (restoreFocus = true) => {
        if (!confirmDialog || !confirmDialogIsOpen) return;

        confirmDialogIsOpen = false;
        confirmDialog.hidden = true;
        document.body.style.overflow = confirmDialogPreviousOverflow;

        if (restoreFocus && confirmDialogPreviousFocus instanceof HTMLElement) {
            confirmDialogPreviousFocus.focus();
        }

        confirmDialogPreviousFocus = null;
        activeConfirmCallback = null;
    };

    const openConfirmDialog = (options, callback) => {
        if (!confirmDialog) {
            callback();
            return;
        }

        if (confirmDialogIsOpen) {
            closeConfirmDialog(false);
        }

        const variant = confirmDialogVariants[options.variant] ? options.variant : 'primary';

        confirmDialogPreviousFocus = document.activeElement;
        confirmDialogPreviousOverflow = document.body.style.overflow;
        activeConfirmCallback = callback;
        confirmDialogIsOpen = true;

        confirmDialogTitle.textContent = options.title || 'Please confirm';
        confirmDialogMessage.textContent = options.message || 'Are you sure you want to continue?';
        confirmDialogConfirm.textContent = options.confirmLabel || 'Confirm';
        confirmDialogCancel.textContent = options.cancelLabel || 'Cancel';

        confirmDialogIconWrap.classList.remove(...confirmDialogVariantClasses);
        confirmDialogConfirm.classList.remove(...confirmDialogVariantClasses);
        confirmDialogIconWrap.classList.add(...confirmDialogVariants[variant].iconWrap);
        confirmDialogConfirm.classList.add(...confirmDialogVariants[variant].confirm);

        confirmDialogIcons.forEach((icon) => {
            icon.classList.toggle('hidden', icon.dataset.confirmDialogIcon !== variant);
        });

        confirmDialog.hidden = false;
        document.body.style.overflow = 'hidden';
        window.requestAnimationFrame(() => confirmDialogConfirm.focus());
    };

    const confirmOptionsFor = (source, submitter = null) => {
        const submitButton = submitter ?? (source.matches('button') ? source : source.querySelector('button[type="submit"]'));

        return {
            title: submitter?.dataset.confirmTitle || source.dataset.confirmTitle || 'Please confirm',
            message: source.dataset.confirm || submitter?.dataset.confirm || '',
            confirmLabel: submitter?.dataset.confirmLabel || source.dataset.confirmLabel || submitButton?.textContent.trim() || 'Confirm',
            cancelLabel: submitter?.dataset.confirmCancelLabel || source.dataset.confirmCancelLabel || 'Cancel',
            variant: submitter?.dataset.confirmVariant || source.dataset.confirmVariant || 'primary',
        };
    };

    document.querySelectorAll('form[data-confirm]').forEach((form) => {
        let approved = false;

        form.addEventListener('submit', (event) => {
            if (approved) {
                approved = false;
                return;
            }

            event.preventDefault();

            const submitter = event.submitter instanceof HTMLElement ? event.submitter : null;

            openConfirmDialog(confirmOptionsFor(form, submitter), () => {
                approved = true;

                if (typeof form.requestSubmit === 'function') {
                    form.requestSubmit(submitter ?? undefined);
                } else {
                    form.submit();
                }
            });
        });
    });

    const replayedConfirmClicks = new WeakSet();

    document.querySelectorAll('button[data-confirm]').forEach((button) => {
        if (button.closest('form[data-confirm]')) return;

        button.addEventListener('click', (event) => {
            if (replayedConfirmClicks.has(button)) {
                replayedConfirmClicks.delete(button);
                return;
            }

            if (button.type === 'submit' && button.form && ! button.form.checkValidity()) {
                return;
            }

            event.preventDefault();

            openConfirmDialog(confirmOptionsFor(button), () => {
                replayedConfirmClicks.add(button);
                button.click();
            });
        });
    });

    confirmDialogConfirm?.addEventListener('click', () => {
        const callback = activeConfirmCallback;
        closeConfirmDialog();
        callback?.();
    });

    confirmDialogCancel?.addEventListener('click', () => closeConfirmDialog());
    confirmDialogBackdrop?.addEventListener('click', () => closeConfirmDialog());

    confirmDialog?.addEventListener('keydown', (event) => {
        if (event.key === 'Escape') {
            event.preventDefault();
            closeConfirmDialog();
            return;
        }

        if (event.key !== 'Tab') return;

        const focusable = Array.from(confirmDialog.querySelectorAll('button:not([disabled]), [href], input:not([disabled]), select:not([disabled]), textarea:not([disabled]), [tabindex]:not([tabindex="-1"])'))
            .filter((element) => ! element.classList.contains('hidden'));

        if (! focusable.length) return;

        const first = focusable[0];
        const last = focusable[focusable.length - 1];

        if (event.shiftKey && document.activeElement === first) {
            event.preventDefault();
            last.focus();
        } else if (! event.shiftKey && document.activeElement === last) {
            event.preventDefault();
            first.focus();
        }
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

    const sessionTimeout = document.querySelector('[data-session-timeout]');
    const sessionTimeoutForm = document.querySelector('[data-session-timeout-form]');

    if (sessionTimeout && sessionTimeoutForm) {
        const remainingSeconds = Math.max(0, Number.parseInt(sessionTimeout.dataset.remaining, 10) || 0);
        const warningSeconds = Math.max(0, Number.parseInt(sessionTimeout.dataset.warning, 10) || 0);
        const countdown = sessionTimeout.querySelector('[data-session-timeout-countdown]');
        const signOutButton = sessionTimeout.querySelector('[data-session-timeout-signout]');
        const deadline = Date.now() + (remainingSeconds * 1000);
        let expired = false;

        const formatRemaining = (seconds) => `${Math.floor(seconds / 60)}:${String(seconds % 60).padStart(2, '0')}`;

        const expire = () => {
            if (expired) return;

            expired = true;
            sessionTimeoutForm.submit();
        };

        const updateCountdown = () => {
            const seconds = Math.max(0, Math.ceil((deadline - Date.now()) / 1000));

            if (countdown) {
                countdown.textContent = formatRemaining(seconds);
            }

            sessionTimeout.classList.toggle('hidden', seconds > warningSeconds);

            if (seconds === 0) {
                expire();
            }
        };

        signOutButton?.addEventListener('click', expire);
        document.addEventListener('visibilitychange', updateCountdown);
        window.addEventListener('focus', updateCountdown);

        updateCountdown();
        window.setInterval(updateCountdown, 1000);
    }
});

if (import.meta.env.PROD && 'serviceWorker' in navigator) {
    window.addEventListener('load', () => {
        navigator.serviceWorker.register('/sw.js').catch(() => {});
    });
}
