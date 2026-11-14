import '../css/app.css';
import './bootstrap';

import { createApp, h } from 'vue';
import { createInertiaApp, router } from '@inertiajs/vue3';
import { resolvePageComponent } from 'laravel-vite-plugin/inertia-helpers';
import { createPinia } from 'pinia';
import { useThemeStore } from './Stores/theme';
import { useNotificationStore } from './Stores/notifications';

const appName = import.meta.env.VITE_APP_NAME || 'Sikhun.com';

function installScrollReveal() {
    const appRoot = document.getElementById('app');
    const reducedMotion = window.matchMedia('(prefers-reduced-motion: reduce)').matches;
    if (!appRoot || reducedMotion || !('IntersectionObserver' in window)) return;

    const selector = '.public-shell main section, .public-shell main article, .public-shell main .card, .public-shell main [data-scroll-reveal]';
    const observer = new IntersectionObserver((entries) => {
        entries.forEach((entry) => {
            if (!entry.isIntersecting) return;
            entry.target.classList.add('scroll-reveal-visible');
            observer.unobserve(entry.target);
        });
    }, { threshold: 0.08, rootMargin: '0px 0px -24px 0px' });

    const observeContent = (node) => {
        if (!(node instanceof Element)) return;
        if (node.matches(selector) && !node.classList.contains('scroll-reveal')) {
            node.classList.add('scroll-reveal');
            observer.observe(node);
        }
        node.querySelectorAll(selector).forEach((element) => {
            if (element.classList.contains('scroll-reveal')) return;
            element.classList.add('scroll-reveal');
            observer.observe(element);
        });
    };

    observeContent(appRoot);
    const mutationObserver = new MutationObserver((records) => {
        records.forEach((record) => {
            record.addedNodes.forEach(observeContent);
            record.removedNodes.forEach((node) => {
                if (!(node instanceof Element)) return;
                if (node.matches('.scroll-reveal')) observer.unobserve(node);
                node.querySelectorAll('.scroll-reveal').forEach((element) => observer.unobserve(element));
            });
        });
    });
    mutationObserver.observe(appRoot, { childList: true, subtree: true });
}

// SPA page-view tracking — Inertia navigations don't reload the page, so
// report each visit to Google Analytics manually and keep document lang synchronized.
router.on('navigate', (event) => {
    const loc = event.detail.page.props?.locale || 'bn';
    document.documentElement.lang = loc;
    if (typeof window.gtag === 'function') {
        window.gtag('config', 'G-102CKE347K', {
            page_path: event.detail.page.url,
        });
    }
});

createInertiaApp({
    title: (title) => (title ? `${title} | ${appName}` : appName),
    resolve: (name) =>
        resolvePageComponent(
            `./Pages/${name}.vue`,
            import.meta.glob('./Pages/**/*.vue'),
        ),
    setup({ el, App, props, plugin }) {
        const pinia = createPinia();
        const app = createApp({ render: () => h(App, props) })
            .use(plugin)
            .use(pinia);

        app.mount(el);

        installScrollReveal();

        // Sync document language attribute on mount
        const currentLocale = props.initialPage?.props?.locale || 'bn';
        document.documentElement.lang = currentLocale;

        // Apply saved/system theme immediately after mount to avoid flash.
        useThemeStore().init();

        // Wire up live notifications for a logged-in student only.
        const studentId = props.initialPage?.props?.auth?.student?.id;
        if (studentId) {
            const notifications = useNotificationStore();
            notifications.fetchInitial();
            notifications.listen(studentId);
        }

        return app;
    },
    progress: {
        color: '#e0781f',
    },
});
