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
                    threshold: 0.14,
                    rootMargin: '0px 0px -8% 0px',
                }
            );

            revealEls.forEach((el) => observer.observe(el));
        }
    }

    const sortable = document.querySelector('[data-sortable-images]');
    if (sortable) {
        let dragEl = null;

        sortable.querySelectorAll('[data-image-id]').forEach((item) => {
            item.setAttribute('draggable', 'true');

            item.addEventListener('dragstart', () => {
                dragEl = item;
                item.classList.add('opacity-60');
            });

            item.addEventListener('dragend', async () => {
                item.classList.remove('opacity-60');
                dragEl = null;

                const order = [...sortable.querySelectorAll('[data-image-id]')].map((el) =>
                    Number(el.dataset.imageId)
                );

                const url = sortable.dataset.reorderUrl;
                const token = document.querySelector('meta[name="csrf-token"]')?.content;

                if (!url || !token) {
                    return;
                }

                await fetch(url, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': token,
                        Accept: 'application/json',
                    },
                    body: JSON.stringify({ order }),
                });
            });

            item.addEventListener('dragover', (event) => {
                event.preventDefault();
                if (!dragEl || dragEl === item) {
                    return;
                }
                const rect = item.getBoundingClientRect();
                const next = (event.clientY - rect.top) / rect.height > 0.5;
                sortable.insertBefore(dragEl, next ? item.nextSibling : item);
            });
        });
    }

    const heroImage = document.querySelector('[data-hero-image]');
    if (heroImage && !reduceMotion) {
        window.requestAnimationFrame(() => {
            heroImage.style.transform = 'scale(1)';
        });
    }

    document.querySelectorAll('[data-preview-input]').forEach((input) => {
        const target = document.querySelector(input.dataset.previewInput);
        if (!target) {
            return;
        }

        input.addEventListener('change', () => {
            const file = input.files?.[0];
            if (!file) {
                return;
            }
            const url = URL.createObjectURL(file);
            if (target.tagName === 'IMG') {
                target.src = url;
                target.classList.remove('hidden');
            } else {
                target.style.backgroundImage = `url(${url})`;
            }

            if (input.hasAttribute('data-hero-image-input')) {
                document.querySelector('[data-hero-placeholder]')?.classList.add('hidden');
                const removeToggle = document.querySelector('[data-hero-remove]');
                if (removeToggle) {
                    removeToggle.checked = false;
                }
            }
        });
    });

    document.querySelector('[data-hero-remove]')?.addEventListener('change', (event) => {
        const checked = event.target.checked;
        const preview = document.querySelector('[data-hero-preview]');
        const placeholder = document.querySelector('[data-hero-placeholder]');
        const fileInput = document.querySelector('[data-hero-image-input]');

        if (checked) {
            preview?.classList.add('hidden');
            placeholder?.classList.remove('hidden');
            if (fileInput) {
                fileInput.value = '';
            }
            return;
        }

        if (preview?.getAttribute('src')) {
            preview.classList.remove('hidden');
            placeholder?.classList.add('hidden');
        }
    });

    const socialLinks = document.querySelector('[data-social-links]');
    const socialTemplate = document.querySelector('[data-social-link-template]');
    const socialAdd = document.querySelector('[data-social-link-add]');

    const reindexSocialLinks = () => {
        if (!socialLinks) {
            return;
        }

        socialLinks.querySelectorAll('[data-social-link-row]').forEach((row, index) => {
            row.querySelectorAll('[data-social-label], [data-social-url]').forEach((input) => {
                const field = input.hasAttribute('data-social-label') ? 'label' : 'url';
                input.name = `social_links[${index}][${field}]`;
            });
        });
    };

    socialAdd?.addEventListener('click', () => {
        if (!socialLinks || !socialTemplate) {
            return;
        }

        const fragment = socialTemplate.content.cloneNode(true);
        socialLinks.appendChild(fragment);
        reindexSocialLinks();

        const rows = socialLinks.querySelectorAll('[data-social-link-row]');
        rows[rows.length - 1]?.querySelector('[data-social-url]')?.focus();
    });

    socialLinks?.addEventListener('click', (event) => {
        const removeButton = event.target.closest('[data-social-link-remove]');
        if (!removeButton) {
            return;
        }

        const rows = socialLinks.querySelectorAll('[data-social-link-row]');
        const row = removeButton.closest('[data-social-link-row]');

        if (!row) {
            return;
        }

        if (rows.length === 1) {
            const labelInput = row.querySelector('[data-social-label]');
            const urlInput = row.querySelector('[data-social-url]');

            if (labelInput) {
                labelInput.value = '';
            }

            if (urlInput) {
                urlInput.value = '';
            }

            return;
        }

        row.remove();
        reindexSocialLinks();
    });
});
