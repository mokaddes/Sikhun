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

// Ensure `fetch` (used by Inertia) sends credentials by default. Some proxy
// or cross-origin setups drop the session cookie on the first XHR visit
// unless `credentials: 'include'` is set; that manifests as "first click
// reloads the current page, second click works". Wrapping `fetch` forces
// credentials to be included for Inertia's requests.
const originalFetch = window.fetch.bind(window);
window.fetch = async (input, init = {}) => {
    const url = typeof input === 'string' ? input : input?.url;
    try {
        console.debug('[fetch wrapper] request', url, init);
    } catch (e) { /* ignore */ }

    const res = await originalFetch(input, { credentials: 'include', ...init });

    try {
        console.debug('[fetch wrapper] response', url, res.status, 'x-inertia=', res.headers.get('x-inertia'), 'x-inertia-location=', res.headers.get('x-inertia-location'));
        console.debug('[fetch wrapper] document.cookie', document.cookie);
    } catch (e) { /* ignore */ }

    return res;
};
