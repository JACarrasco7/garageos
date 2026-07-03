import '../css/app.css';
import './bootstrap';

import { createInertiaApp } from '@inertiajs/vue3';
import { resolvePageComponent } from 'laravel-vite-plugin/inertia-helpers';
import { createApp, DefineComponent, h } from 'vue';
import { ZiggyVue } from '../../vendor/tightenco/ziggy';
import Toaster from 'vue-sonner';

const appName = import.meta.env.VITE_APP_NAME || 'Laravel';

// Sentry error monitoring (opcional)
// if (import.meta.env.VITE_SENTRY_DSN && typeof import.meta.env.VITE_SENTRY_DSN === 'string') {
//     try {
//         import('@sentry/vue').then(({ init: initSentry }) => {
//             initSentry({
//                 appName,
//                 dsn: import.meta.env.VITE_SENTRY_DSN,
//                 tracesSampleRate: 1.0,
//                 replaysSessionSampleRate: 0.1,
//                 replaysErrorSampleRate: 1.0,
//             });
//         }).catch(error => {
//             console.error('Failed to initialize Sentry:', error);
//         });
//     } catch (error) {
//         console.error('Sentry module not available:', error);
//     }
// }

// Register Service Worker for PWA
if ('serviceWorker' in navigator) {
    window.addEventListener('load', () => {
        navigator.serviceWorker.register('/sw.js')
            .then(registration => console.log('SW registered:', registration.scope))
            .catch(error => console.log('SW registration failed:', error));
    });
}

createInertiaApp({
    title: (title) => `${title} - ${appName}`,
    resolve: (name) =>
        resolvePageComponent(
            `./Pages/${name}.vue`,
            import.meta.glob<DefineComponent>('./Pages/**/*.vue'),
        ),
    setup({ el, App, props, plugin }) {
        createApp({ render: () => h(App, props) })
            .use(plugin)
            .use(ZiggyVue)
            .component('Toaster', Toaster)
            .mount(el);
    },
    progress: {
        color: '#4B5563',
    },
});
