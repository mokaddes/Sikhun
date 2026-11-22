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
    const isHttps = typeof window !== 'undefined' && window.location.protocol === 'https:';
    const currentHost = typeof window !== 'undefined' ? window.location.hostname : 'localhost';
    const envHost = import.meta.env.VITE_REVERB_HOST;
    const isLocalEnvHost = !envHost || envHost === 'localhost' || envHost === '127.0.0.1';
    const wsHost = isLocalEnvHost && currentHost !== 'localhost' && currentHost !== '127.0.0.1'
        ? currentHost
        : (envHost || currentHost);
    const envPort = Number(import.meta.env.VITE_REVERB_PORT) || 8080;

    return new Echo({
        broadcaster: 'reverb',
        key: import.meta.env.VITE_REVERB_APP_KEY,
        wsHost,
        wsPort: isHttps ? 80 : envPort,
        wssPort: isHttps ? 443 : envPort,
        forceTLS: isHttps,
        enabledTransports: ['ws', 'wss'],
        authEndpoint: '/broadcasting/auth',
        auth: {
            headers: { 'X-CSRF-TOKEN': csrfToken },
        },
    });
}
