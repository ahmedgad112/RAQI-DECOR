import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';
import { bunny } from 'laravel-vite-plugin/fonts';
import tailwindcss from '@tailwindcss/vite';

export default defineConfig({
    plugins: [
        laravel({
            input: ['resources/css/app.css', 'resources/js/app.js', 'resources/js/admin.js'],
            refresh: true,
            fonts: [
                bunny('Alexandria', {
                    weights: [400, 500, 600],
                    subsets: ['latin', 'arabic'],
                    optimizedFallbacks: false,
                }),
                bunny('Cormorant Garamond', {
                    weights: [400, 500, 600],
                    styles: ['normal'],
                    optimizedFallbacks: false,
                }),
            ],
        }),
        tailwindcss(),
    ],
    server: {
        watch: {
            ignored: ['**/storage/framework/views/**'],
        },
    },
    build: {
        cssMinify: true,
        modulePreload: {
            polyfill: false,
        },
    },
});
