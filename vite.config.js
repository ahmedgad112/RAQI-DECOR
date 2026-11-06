import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';
import { bunny } from 'laravel-vite-plugin/fonts';
import tailwindcss from '@tailwindcss/vite';

export default defineConfig({
    plugins: [
        laravel({
            input: ['resources/css/app.css', 'resources/js/app.js'],
            refresh: true,
            fonts: [
                bunny('Alexandria', {
                    weights: [300, 400, 500, 600, 700],
                    subsets: ['latin', 'arabic'],
                    optimizedFallbacks: false,
                }),
                bunny('Cormorant Garamond', {
                    weights: [400, 500, 600, 700],
                    styles: ['normal', 'italic'],
                    optimizedFallbacks: false,
                }),
                bunny('Inter', {
                    weights: [400, 500, 600],
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
});
