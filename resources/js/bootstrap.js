import axios from 'axios';

window.axios = axios;
window.axios.defaults.headers.common['X-Requested-With'] = 'XMLHttpRequest';

// Ensures the session/XSRF cookie is always sent, including in setups where
// the app is accessed via a proxied domain or the admin panel is treated as
// a separate "app" by the browser — without this, some hosting/proxy setups
// silently drop the cookie on certain requests, which shows up as "click a
// link, nothing happens or it reloads; click again and it works" because
// the session had to be re-established on the retry.
window.axios.defaults.withCredentials = true;
window.axios.defaults.xsrfCookieName = 'XSRF-TOKEN';
window.axios.defaults.xsrfHeaderName = 'X-XSRF-TOKEN';

// Ensure `fetch` (used by Inertia) sends credentials by default for same-origin
// requests only. Never inject `credentials: 'include'` on cross-origin third-party
// APIs (such as Firebase / Google APIs), or browsers will reject them via CORS.
const originalFetch = window.fetch.bind(window);
window.fetch = async (input, init = {}) => {
    const rawUrl = typeof input === 'string' ? input : input?.url;
    let isSameOrigin = true;
    if (rawUrl) {
        try {
            const parsed = new URL(rawUrl, window.location.origin);
            isSameOrigin = parsed.origin === window.location.origin;
        } catch {
            isSameOrigin = true;
        }
    }

    const fetchInit = isSameOrigin ? { credentials: 'include', ...init } : init;
    return originalFetch(input, fetchInit);
};
