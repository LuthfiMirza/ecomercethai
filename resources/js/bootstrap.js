import axios from 'axios';
window.axios = axios;

window.axios.defaults.headers.common['X-Requested-With'] = 'XMLHttpRequest';

const csrf = document.querySelector('meta[name="csrf-token"]');
if (csrf) {
    window.axios.defaults.headers.common['X-CSRF-TOKEN'] = csrf.getAttribute('content');
}

import Echo from 'laravel-echo';
import Pusher from 'pusher-js';

window.Pusher = Pusher;

const pusherKey = import.meta.env.VITE_PUSHER_APP_KEY;

if (typeof pusherKey === 'string' && pusherKey.trim() !== '') {
    window.Echo = new Echo({
        broadcaster: 'pusher',
        key: pusherKey,
        cluster: import.meta.env.VITE_PUSHER_APP_CLUSTER ?? 'mt1',
        wsHost: import.meta.env.VITE_PUSHER_HOST ?? window.location.hostname,
        wsPort: Number(import.meta.env.VITE_PUSHER_PORT ?? window.location.port ?? 80),
        wssPort: Number(import.meta.env.VITE_PUSHER_PORT ?? 443),
        forceTLS: (import.meta.env.VITE_PUSHER_SCHEME ?? 'https') === 'https',
        enabledTransports: ['ws', 'wss'],
    });

    const attachSocketHeader = () => {
        const socketId = window.Echo?.socketId();
        if (socketId) {
            window.axios.defaults.headers.common['X-Socket-Id'] = socketId;
        }
    };

    window.axios.interceptors.request.use((config) => {
        const socketId = window.Echo?.socketId();
        if (socketId) {
            config.headers = config.headers || {};
            config.headers['X-Socket-Id'] = socketId;
        }
        return config;
    });

    const connection = window.Echo.connector?.pusher?.connection;
    if (connection) {
        connection.bind('connected', attachSocketHeader);
    }

    attachSocketHeader();
}
