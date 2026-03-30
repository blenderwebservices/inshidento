import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';
import react from '@vitejs/plugin-react';

export default defineConfig({
    plugins: [
        laravel({
            input: [
                'resources/css/app.css', 
                'resources/js/app.js',
                'resources/js/landing-page/main.jsx',
                'resources/js/landing-page/index.css'
            ],
            refresh: true,
        }),
        react(),
    ],
});
