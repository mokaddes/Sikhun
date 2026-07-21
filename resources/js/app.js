import '../css/app.css';
import './bootstrap';

import { createApp, h } from 'vue';
import { createInertiaApp } from '@inertiajs/vue3';
import { resolvePageComponent } from 'laravel-vite-plugin/inertia-helpers';
import { createPinia } from 'pinia';
import VueApexCharts from 'vue3-apexcharts';
import { useThemeStore } from './Stores/theme';
import { useNotificationStore } from './Stores/notifications';

const appName = import.meta.env.VITE_APP_NAME || 'Sikhun.com';

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
            .use(pinia)
            .use(VueApexCharts);

        app.mount(el);

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
        color: '#6c63ff',
    },
});
