document.addEventListener('DOMContentLoaded', () => {
    const reduceMotion = window.matchMedia('(prefers-reduced-motion: reduce)').matches;

    const header = document.querySelector('[data-site-header]');
    if (header) {
        let ticking = false;
        const transparentMode = header.classList.contains('is-transparent');

        const onScroll = () => {
            if (transparentMode) {
                header.classList.toggle('is-scrolled', window.scrollY > 24);
            } else {
                header.classList.add('is-scrolled');
            }
            ticking = false;
        };

        onScroll();
        window.addEventListener(
            'scroll',
            () => {
                if (!ticking) {
                    window.requestAnimationFrame(onScroll);
                    ticking = true;
                }
            },
            { passive: true }
        );
    }

    const mobileToggle = document.querySelector('[data-mobile-toggle]');
    const mobilePanel = document.querySelector('[data-mobile-panel]');

    const setMenuOpen = (open) => {
        if (!mobileToggle || !mobilePanel) {
            return;
        }

        mobilePanel.classList.toggle('is-open', open);
        mobileToggle.setAttribute('aria-expanded', open ? 'true' : 'false');
        document.body.classList.toggle('overflow-hidden', open);
        header?.classList.toggle('is-menu-open', open);
    };

    if (mobileToggle && mobilePanel) {
        mobileToggle.addEventListener('click', () => {
            const open = mobileToggle.getAttribute('aria-expanded') !== 'true';
            setMenuOpen(open);
        });

        mobilePanel.querySelectorAll('a').forEach((link) => {
            link.addEventListener('click', () => setMenuOpen(false));
        });
    }

    const lightbox = document.querySelector('[data-lightbox]');
    const lightboxImage = document.querySelector('[data-lightbox-image]');
    const lightboxClose = document.querySelector('[data-lightbox-close]');

    const openLightbox = (src, alt = '') => {
        if (!lightbox || !lightboxImage || !src) {
            return;
        }

        lightboxImage.src = src;
        lightboxImage.alt = alt;
        lightbox.classList.add('is-open');
        document.body.classList.add('overflow-hidden');
    };

    const closeLightbox = () => {
        if (!lightbox) {
            return;
        }

        lightbox.classList.remove('is-open');
        document.body.classList.remove('overflow-hidden');

        window.setTimeout(() => {
            if (lightboxImage && !lightbox.classList.contains('is-open')) {
                lightboxImage.src = '';
            }
        }, 350);
    };

    document.querySelectorAll('[data-lightbox-trigger]').forEach((trigger) => {
        trigger.addEventListener('click', (event) => {
            event.preventDefault();
            openLightbox(
                trigger.getAttribute('href') || trigger.dataset.src || '',
                trigger.dataset.alt || ''
            );
        });
    });

    lightboxClose?.addEventListener('click', closeLightbox);
    lightbox?.addEventListener('click', (event) => {
        if (event.target === lightbox) {
            closeLightbox();
        }
    });

    const successPopup = document.querySelector('[data-success-popup]');

    const setSuccessOpen = (open) => {
        if (!successPopup) {
            return;
        }

        successPopup.classList.toggle('is-open', open);
        successPopup.setAttribute('aria-hidden', open ? 'false' : 'true');

        if (open) {
            document.body.classList.add('overflow-hidden');
            window.setTimeout(() => {
                successPopup.querySelector('[data-success-close]')?.focus();
            }, 180);
            return;
        }

        if (!lightbox?.classList.contains('is-open') && mobileToggle?.getAttribute('aria-expanded') !== 'true') {
            document.body.classList.remove('overflow-hidden');
        }
    };

    successPopup?.querySelector('[data-success-close]')?.addEventListener('click', () => setSuccessOpen(false));
    successPopup?.addEventListener('click', (event) => {
        if (event.target === successPopup) {
            setSuccessOpen(false);
        }
    });

    if (successPopup?.classList.contains('is-open')) {
        document.body.classList.add('overflow-hidden');
    }

    document.addEventListener('keydown', (event) => {
        if (event.key === 'Escape') {
            closeLightbox();
            setSuccessOpen(false);
            setMenuOpen(false);
        }
    });

    const revealEls = document.querySelectorAll('.reveal');
    if (revealEls.length) {
        if (reduceMotion || !('IntersectionObserver' in window)) {
            revealEls.forEach((el) => el.classList.add('is-visible'));
        } else {
            const observer = new IntersectionObserver(
                (entries) => {
                    entries.forEach((entry) => {
                        if (entry.isIntersecting) {
                            entry.target.classList.add('is-visible');
                            observer.unobserve(entry.target);
                        }
                    });
                },
                {
                    threshold: 0.08,
                    rootMargin: '0px 0px -4% 0px',
                }
            );

            revealEls.forEach((el) => observer.observe(el));
        }
    }

    const heroImage = document.querySelector('[data-hero-image]');
    if (heroImage && !reduceMotion) {
        window.requestAnimationFrame(() => {
            heroImage.style.transform = 'scale(1)';
        });
    }
});
