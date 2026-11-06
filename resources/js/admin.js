import './app.js';

document.addEventListener('DOMContentLoaded', () => {
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
