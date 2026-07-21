import Echo from 'laravel-echo';
import Pusher from 'pusher-js';

window.Pusher = Pusher;

/**
 * Reverb speaks the Pusher protocol, so Laravel Echo's 'reverb' broadcaster
 * is really just Pusher-compatible config under the hood. Only ever
 * initialized for a logged-in student (see app.js) — there is nothing to
 * subscribe to as a guest.
 */
export function createEcho() {
    const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');

    return new Echo({
        broadcaster: 'reverb',
        key: import.meta.env.VITE_REVERB_APP_KEY,
        wsHost: import.meta.env.VITE_REVERB_HOST,
        wsPort: import.meta.env.VITE_REVERB_PORT ?? 8080,
        wssPort: import.meta.env.VITE_REVERB_PORT ?? 8080,
        forceTLS: (import.meta.env.VITE_REVERB_SCHEME ?? 'http') === 'https',
        enabledTransports: ['ws', 'wss'],
        authEndpoint: '/broadcasting/auth',
        auth: {
            headers: { 'X-CSRF-TOKEN': csrfToken },
        },
    });
}
