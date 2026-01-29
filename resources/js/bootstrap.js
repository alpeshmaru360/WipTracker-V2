import axios from 'axios';
window.axios = axios;

window.axios.defaults.headers.common['X-Requested-With'] = 'XMLHttpRequest';

import Echo from 'laravel-echo';
import Pusher from 'pusher-js';

console.log('Bootstrap.js is executing');

window.Pusher = Pusher;

window.Echo = new Echo({
    broadcaster: 'pusher',
    key: import.meta.env.VITE_PUSHER_APP_KEY,
    cluster: import.meta.env.VITE_PUSHER_APP_CLUSTER,
    forceTLS: true
});

console.log('Echo initialized in bootstrap.js:', window.Echo);
console.log('Pusher key:', import.meta.env.VITE_PUSHER_APP_KEY);
console.log('Pusher cluster:', import.meta.env.VITE_PUSHER_APP_CLUSTER);