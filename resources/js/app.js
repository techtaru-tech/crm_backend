import './bootstrap';

import Alpine from 'alpinejs';
import Sort from '@alpinejs/sort';
import Echo from 'laravel-echo';
import Pusher from 'pusher-js';

Alpine.plugin(Sort);
window.Alpine = Alpine;
Alpine.start();

window.Pusher = Pusher;
window.Echo = new Echo({
    broadcaster: import.meta.env.VITE_PUSHER_APP_KEY ? 'pusher' : 'reverb',
    key: import.meta.env.VITE_PUSHER_APP_KEY || import.meta.env.VITE_REVERB_APP_KEY,
    cluster: import.meta.env.VITE_PUSHER_APP_CLUSTER || 'mt1',
    wsHost: import.meta.env.VITE_PUSHER_HOST || import.meta.env.VITE_REVERB_HOST || window.location.hostname,
    wsPort: import.meta.env.VITE_PUSHER_PORT || import.meta.env.VITE_REVERB_PORT || 80,
    wssPort: import.meta.env.VITE_PUSHER_PORT || import.meta.env.VITE_REVERB_PORT || 443,
    forceTLS: (import.meta.env.VITE_PUSHER_SCHEME || import.meta.env.VITE_REVERB_SCHEME || 'https') === 'https',
    enabledTransports: ['ws', 'wss'],
});
