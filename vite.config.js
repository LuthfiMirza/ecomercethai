import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';
import react from '@vitejs/plugin-react';

const devHost = process.env.VITE_DEV_SERVER_HOST ?? '127.0.0.1';
const devPort = Number(process.env.VITE_DEV_SERVER_PORT ?? 5173);

export default defineConfig({
    server: {
        host: devHost,
        port: devPort,
        hmr: {
            host: devHost,
            port: devPort,
        },
    },
    plugins: [
        laravel({
            input: ['resources/css/app.css', 'resources/js/app.js'],
            refresh: true,
        }),
        react(),
    ],
});
