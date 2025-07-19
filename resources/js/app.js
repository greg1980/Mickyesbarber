import '../css/app.css';
import './bootstrap';

import axios from 'axios';

axios.defaults.withCredentials = true;
axios.defaults.withXSRFToken = true;

// Set CSRF token for all requests
const token = document.head.querySelector('meta[name="csrf-token"]');
if (token) {
    axios.defaults.headers.common['X-CSRF-TOKEN'] = token.content;
} else {
    console.error('CSRF token not found: https://laravel.com/docs/csrf#csrf-x-csrf-token');
}

import { createInertiaApp } from '@inertiajs/vue3';
import { resolvePageComponent } from 'laravel-vite-plugin/inertia-helpers';
import { createApp, h } from 'vue';
import { ZiggyVue } from '../../vendor/tightenco/ziggy';

const appName = import.meta.env.VITE_APP_NAME || 'Laravel';

// Service worker registration removed. Offline feature disabled.

createInertiaApp({
    title: (title) => `${title} - ${appName}`,
    resolve: (name) =>
        resolvePageComponent(
            `./Pages/${name}.vue`,
            import.meta.glob('./Pages/**/*.vue'),
        ),
    setup({ el, App, props, plugin }) {
        const app = createApp({ render: () => h(App, props) })
            .use(plugin)
            .use(ZiggyVue);

        // Add performance monitoring in development
        if (import.meta.env.DEV) {
            import('./Components/PerformanceMonitor.vue').then(({ default: PerformanceMonitor }) => {
                app.component('PerformanceMonitor', PerformanceMonitor);
            });
        }

        return app.mount(el);
    },
    progress: {
        color: '#4B5563',
    },
});
