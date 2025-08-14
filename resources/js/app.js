import '../css/app.css';
import './bootstrap';

import { createInertiaApp, Link } from '@inertiajs/vue3';
import { resolvePageComponent } from 'laravel-vite-plugin/inertia-helpers';
import { createApp, h } from 'vue';
import { ZiggyVue } from '../../vendor/tightenco/ziggy';
import { Quasar } from 'quasar';
import 'quasar/src/css/index.sass'
import '@quasar/extras/material-icons/material-icons.css'

const appName = import.meta.env.VITE_APP_NAME || 'Laravel';

createInertiaApp({
    title: (title) => `${title} - ${appName}`,
    resolve: (name) =>
        resolvePageComponent(
            `./Pages/${name}.vue`,
            import.meta.glob('./Pages/**/*.vue'),
        ),
    setup({ el, App, props, plugin }) {
        return createApp({ render: () => h(App, props) })
            .component('Link', Link)
            .use(plugin)
            .use(ZiggyVue)
            .use(Quasar, {plugins: {},}
            )
            .mount(el);
    },
    progress: {
        color: '#4B5563',
    },
});
