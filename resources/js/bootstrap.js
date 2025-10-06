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
} else {
    console.warn('[Echo] Pusher key missing; realtime features disabled. Set VITE_PUSHER_APP_KEY in your .env.');
}
