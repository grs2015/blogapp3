import '../css/app.css';
import { createApp, h } from 'vue'
import { createInertiaApp } from '@inertiajs/inertia-vue3'
import { InertiaProgress } from '@inertiajs/progress'
import { i18nVue } from 'laravel-vue-i18n'
import Layout from '@/Shared/Layout.vue'
import { resolvePageComponent } from 'laravel-vite-plugin/inertia-helpers';
import { Quasar } from 'quasar'
import '@quasar/extras/material-icons/material-icons.css'
import 'quasar/src/css/index.sass'
import { ZiggyVue } from 'ziggy';
import { Ziggy } from './ziggy';

InertiaProgress.init()

createInertiaApp({
  resolve: async (name) => {
    let page = (await resolvePageComponent(`./Pages/${name}.vue`, import.meta.glob('./Pages/**/*.vue'))).default
    page.layout = page.layout || Layout
    return page
},
  setup({ el, App, props, plugin }) {
    const app = createApp({ render: () => h(App, props) })
    app.use(plugin)
    .use(i18nVue, {
        resolve: async lang => {
            const langs = import.meta.glob('../../lang/*.json');
            return await langs[`../../lang/${lang}.json`]();
        }
    })
    .use(Quasar, {
        plugins: {}, // import Quasar plugins and add here
    })
    .use(ZiggyVue, Ziggy)
    .mount(el)

  },
})
