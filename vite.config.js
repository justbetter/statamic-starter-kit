import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';
import statamic from '@statamic/cms/vite-plugin';

export default defineConfig({
    plugins: [
        laravel({
            input: ['resources/js/justbetter-starter-kit.js', 'resources/css/justbetter-starter-kit.css'],
            publicDirectory: 'resources/dist',
        }),
        statamic(),
    ],
});
