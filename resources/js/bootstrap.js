import axios from 'axios';
import Echo from 'laravel-echo';
import Pusher from 'pusher-js';

window.axios = axios;
window.axios.defaults.headers.common['X-Requested-With'] = 'XMLHttpRequest';

console.log('Setting up Echo connection...');

window.Echo = new Echo({
    broadcaster: 'pusher',
    key: import.meta.env.VITE_REVERB_APP_KEY,
    wsHost: import.meta.env.VITE_REVERB_HOST || window.location.hostname,
    wsPort: import.meta.env.VITE_REVERB_PORT || 8070,
    forceTLS: false,
    encrypted: false,
    enabledTransports: ['ws', 'wss'],
    disableStats: true,
    cluster: 'mt1',
    authEndpoint: '/broadcasting/auth',
    namespace: '',
});

// Test WebSocket connection
window.Echo.connector.pusher.connection.bind('connected', () => {
    console.log('WebSocket Connected Successfully!');
});

window.Echo.connector.pusher.connection.bind('error', (error) => {
    console.error('WebSocket Connection Error:', error);
});

window.Echo.connector.pusher.connection.bind('disconnected', () => {
    console.log('WebSocket Disconnected');
});

// Subscribe to a test channel
window.Echo.channel('test-channel')
    .listen('test-event', (e) => {
        console.log('Received test event:', e);
    });

console.log('Echo setup complete. Attempting connection...');

/**
 * Echo exposes an expressive API for subscribing to channels and listening
 * for events that are broadcast by Laravel. Echo and event broadcasting
 * allow your team to quickly build robust real-time web applications.
 */
