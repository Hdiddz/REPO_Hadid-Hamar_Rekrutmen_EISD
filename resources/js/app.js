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
            const menu = document.getElementById(button.dataset.menuToggle);

            if (!menu) {
                return;
            }

            const willOpen = menu.classList.contains('hidden');
            closeMenus(menu);
            menu.classList.toggle('hidden', !willOpen);
            setExpanded(button, willOpen);
        });
    });

    const mobileNavButton = document.querySelector('[data-mobile-nav-toggle]');
    const mobileNav = document.getElementById('portal-mobile-nav');

    mobileNavButton?.addEventListener('click', () => {
        const willOpen = mobileNav?.classList.contains('hidden') ?? false;
        mobileNav?.classList.toggle('hidden', !willOpen);
        setExpanded(mobileNavButton, willOpen);
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

    document.querySelectorAll('[data-theme-value]').forEach((button) => {
        button.addEventListener('click', () => {
            const theme = button.dataset.themeValue;
            localStorage.setItem('theme', theme);
            document.documentElement.classList.toggle('dark', theme === 'dark');
            document.querySelectorAll('[data-theme-value]').forEach((item) => {
                item.setAttribute('aria-pressed', String(item === button));
            });
        });
    });

    document.querySelectorAll('[data-alert-dismiss]').forEach((button) => {
        button.addEventListener('click', () => button.closest('[data-flash-alert]')?.remove());
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

    document.addEventListener('click', () => closeMenus());
    document.addEventListener('keydown', (event) => {
        if (event.key === 'Escape') {
            closeMenus();
            mobileNav?.classList.add('hidden');
            if (mobileNavButton) {
                setExpanded(mobileNavButton, false);
            }
        }
    });
});
