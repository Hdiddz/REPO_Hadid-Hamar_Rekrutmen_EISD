import Modal from 'bootstrap/js/dist/modal';

const setExpanded = (button, isExpanded) => {
    button.setAttribute('aria-expanded', String(isExpanded));
};

const closeMenus = (except = null) => {
    document.querySelectorAll('[data-menu]').forEach((menu) => {
        if (menu !== except) {
            menu.classList.add('hidden');
        }
    });

    document.querySelectorAll('[data-menu-toggle]').forEach((button) => {
        if (button.dataset.menuToggle !== except?.id) {
            setExpanded(button, false);
        }
    });
};

document.addEventListener('DOMContentLoaded', () => {
    document.querySelectorAll('[data-bs-toggle="modal"]').forEach((button) => {
        button.addEventListener('click', () => {
            const target = document.querySelector(button.dataset.bsTarget);
            if (target) {
                Modal.getOrCreateInstance(target).show();
            }
        });
    });

    document.querySelectorAll('[data-bs-dismiss="modal"]').forEach((button) => {
        button.addEventListener('click', () => {
            const modal = button.closest('.modal');
            if (modal) {
                Modal.getOrCreateInstance(modal).hide();
            }
        });
    });

    document.querySelectorAll('[data-menu-toggle]').forEach((button) => {
        button.addEventListener('click', (event) => {
            event.stopPropagation();
            const targetId = button.dataset.menuToggle;
            const menu = document.getElementById(targetId);

            if (!menu) {
                return;
            }

            // On mobile devices, hide floating user menu dropdown and trigger mobile nav drawer instead
            if (window.innerWidth < 1024 && targetId === 'portal-user-menu') {
                const mobileNavToggle = document.querySelector('[data-mobile-nav-toggle][aria-controls="portal-mobile-nav"]');
                if (mobileNavToggle) {
                    mobileNavToggle.click();
                }
                return;
            }
            if (window.innerWidth < 768 && targetId === 'main-user-menu') {
                const mobileNavToggle = document.querySelector('[data-mobile-nav-toggle][aria-controls="app-mobile-nav"]');
                if (mobileNavToggle) {
                    mobileNavToggle.click();
                }
                return;
            }

            const willOpen = menu.classList.contains('hidden');
            closeMenus(menu);
            menu.classList.toggle('hidden', !willOpen);
            setExpanded(button, willOpen);
        });
    });

    document.querySelectorAll('[data-mobile-nav-toggle]').forEach((button) => {
        const targetSelector = button.dataset.target || (button.getAttribute('aria-controls') ? `#${button.getAttribute('aria-controls')}` : '#portal-mobile-nav');
        const mobileNav = document.querySelector(targetSelector);
        const icon = button.querySelector('.material-symbols-outlined');

        button.addEventListener('click', (event) => {
            event.stopPropagation();
            if (!mobileNav) {
                return;
            }

            const willOpen = mobileNav.classList.contains('hidden');
            closeMenus();

            mobileNav.classList.toggle('hidden', !willOpen);
            setExpanded(button, willOpen);

            if (icon) {
                icon.textContent = willOpen ? 'close' : 'menu';
            }
        });

        mobileNav?.querySelectorAll('a:not([data-stay-open])').forEach((link) => {
            link.addEventListener('click', () => {
                mobileNav.classList.add('hidden');
                setExpanded(button, false);
                if (icon) {
                    icon.textContent = 'menu';
                }
            });
        });
    });

    document.querySelectorAll('[data-dialog-open]').forEach((button) => {
        button.addEventListener('click', () => {
            document.getElementById(button.dataset.dialogOpen)?.showModal();
        });
    });

    document.querySelectorAll('[data-dialog-close]').forEach((button) => {
        button.addEventListener('click', () => {
            button.closest('dialog')?.close();
        });
    });

    document.querySelectorAll('dialog').forEach((dialog) => {
        dialog.addEventListener('click', (event) => {
            if (event.target === dialog) {
                dialog.close();
            }
        });
    });

    const updateThemeElements = (isDark) => {
        document.querySelectorAll('[data-theme-toggle]').forEach((button) => {
            const icon = button.querySelector('.theme-toggle-icon') || button.querySelector('.material-symbols-outlined');
            const text = button.querySelector('.theme-toggle-text');
            if (icon) {
                icon.textContent = isDark ? 'light_mode' : 'dark_mode';
            }
            if (text && text.dataset.dynamicText !== 'false') {
                text.textContent = isDark ? 'Mode Terang' : 'Mode Gelap';
            }
            button.setAttribute('aria-pressed', String(isDark));
            button.setAttribute('title', isDark ? 'Beralih ke mode terang' : 'Beralih ke mode gelap');
        });

        document.querySelectorAll('[data-theme-value]').forEach((item) => {
            item.setAttribute('aria-pressed', String(item.dataset.themeValue === (isDark ? 'dark' : 'light')));
        });

        if (typeof window.updateThemeSelectionCards === 'function') {
            window.updateThemeSelectionCards();
        }
    };

    const applyTheme = (theme) => {
        const isDark = theme === 'dark';
        document.documentElement.classList.toggle('dark', isDark);
        localStorage.setItem('theme', theme);
        updateThemeElements(isDark);
    };

    const toggleTheme = () => {
        const isCurrentlyDark = document.documentElement.classList.contains('dark');
        applyTheme(isCurrentlyDark ? 'light' : 'dark');
    };

    window.applyTheme = applyTheme;
    window.toggleTheme = toggleTheme;

    document.querySelectorAll('[data-theme-toggle]').forEach((button) => {
        button.addEventListener('click', (event) => {
            event.stopPropagation();
            toggleTheme();
        });
    });

    document.querySelectorAll('[data-theme-value]').forEach((button) => {
        button.addEventListener('click', () => {
            applyTheme(button.dataset.themeValue);
        });
    });

    updateThemeElements(document.documentElement.classList.contains('dark'));


    document.querySelectorAll('[data-flash-alert]').forEach((alert) => {
        let isDismissed = false;
        const dismissAlert = () => {
            if (isDismissed) {
                return;
            }
            isDismissed = true;
            alert.style.transition = 'opacity 350ms ease, transform 350ms ease';
            alert.style.opacity = '0';
            alert.style.transform = 'translateY(-16px) scale(0.95)';
            setTimeout(() => {
                const container = alert.closest('[data-flash-container]') || alert;
                container.remove();
            }, 350);
        };

        alert.querySelectorAll('[data-alert-dismiss]').forEach((button) => {
            button.addEventListener('click', dismissAlert);
        });

        setTimeout(dismissAlert, 2000);
    });

    document.querySelectorAll('[data-password-toggle]').forEach((button) => {
        button.addEventListener('click', () => {
            const input = document.getElementById(button.dataset.passwordToggle);
            if (!input) {
                return;
            }
            const showing = input.type === 'text';
            input.type = showing ? 'password' : 'text';
            button.setAttribute('aria-label', showing ? 'Tampilkan kata sandi' : 'Sembunyikan kata sandi');
            const icon = button.querySelector('.material-symbols-outlined');
            if (icon) {
                icon.textContent = showing ? 'visibility' : 'visibility_off';
            }
        });
    });

    const roleInput = document.querySelector('[data-role-input]');
    const businessField = document.querySelector('[data-business-field]');
    const applyRole = (role) => {
        if (!roleInput) {
            return;
        }
        roleInput.value = role;
        document.querySelectorAll('[data-role-option]').forEach((button) => {
            const selected = button.dataset.roleOption === role;
            button.setAttribute('aria-pressed', String(selected));
            button.classList.toggle('is-selected', selected);
        });
        businessField?.classList.toggle('hidden', role !== 'employer');
        businessField?.querySelector('input')?.toggleAttribute('required', role === 'employer');
    };
    document.querySelectorAll('[data-role-option]').forEach((button) => {
        button.addEventListener('click', () => applyRole(button.dataset.roleOption));
    });
    if (roleInput) {
        applyRole(roleInput.value || 'jobseeker');
    }

    const revealElements = document.querySelectorAll('[data-reveal]');
    if (revealElements.length > 0) {
        const revealObserver = new IntersectionObserver((entries, observer) => {
            entries.forEach((entry) => {
                if (entry.isIntersecting) {
                    entry.target.classList.add('is-visible');
                    observer.unobserve(entry.target);
                }
            });
        }, { threshold: 0.12 });
        revealElements.forEach((element) => revealObserver.observe(element));
    }

    const closeMobileNavs = () => {
        document.querySelectorAll('[data-mobile-nav]').forEach((nav) => nav.classList.add('hidden'));
        const portalNav = document.getElementById('portal-mobile-nav');
        if (portalNav) portalNav.classList.add('hidden');
        document.querySelectorAll('[data-mobile-nav-toggle]').forEach((btn) => {
            setExpanded(btn, false);
            const icon = btn.querySelector('.material-symbols-outlined');
            if (icon) icon.textContent = 'menu';
        });
    };

    document.addEventListener('click', (event) => {
        closeMenus();
        if (!event.target.closest('[data-mobile-nav]') && !event.target.closest('#portal-mobile-nav') && !event.target.closest('[data-mobile-nav-toggle]')) {
            closeMobileNavs();
        }
    });

    document.addEventListener('keydown', (event) => {
        if (event.key === 'Escape') {
            closeMenus();
            closeMobileNavs();
        }
    });
});
