import './bootstrap';
import './theme-init.js'; // Inicialización temprana del tema
import 'bootstrap/dist/css/bootstrap.min.css';
import 'bootstrap/dist/js/bootstrap.bundle.min.js';
import '../css/app.css';
import '@vuepic/vue-datepicker/dist/main.css';
import '@fortawesome/fontawesome-free/css/all.min.css';
import 'primeicons/primeicons.css';
import 'primeflex/primeflex.css';

import { createApp, h } from 'vue';
import { createInertiaApp } from '@inertiajs/vue3';
import { resolvePageComponent } from 'laravel-vite-plugin/inertia-helpers';
import { ZiggyVue } from '../../vendor/tightenco/ziggy';
import VueKonva from 'vue-konva';
import PrimeVue from 'primevue/config';
import Lara from '@primevue/themes/lara';
import DataTable from 'primevue/datatable';
import Column from 'primevue/column';
import Button from 'primevue/button';
import Dialog from 'primevue/dialog';
import InputText from 'primevue/inputtext';
import Dropdown from 'primevue/dropdown';
import Password from 'primevue/password';
import Checkbox from 'primevue/checkbox';
import Message from 'primevue/message';
import FloatLabel from 'primevue/floatlabel';
import ToastService from 'primevue/toastservice';
import Toast from 'primevue/toast';
import i18n from './i18n/index.js';

const appName = import.meta.env.VITE_APP_NAME || 'Laravel';

createInertiaApp({
    title: (title) => `${title} - ${appName}`,
    resolve: (name) => resolvePageComponent(`./Pages/${name}.vue`, import.meta.glob('./Pages/**/*.vue')),
    setup({ el, App, props, plugin }) {
        const vueApp = createApp({ render: () => h(App, props) });
        vueApp.use(plugin);
        vueApp.use(PrimeVue, {
            theme: {
                preset: Lara,
                options: {
                    darkModeSelector: false,
                    cssLayer: false
                }
            }
        });
        vueApp.use(ToastService);
        vueApp.component('DataTable', DataTable);
        vueApp.component('Column', Column);
        vueApp.component('Button', Button);
        vueApp.component('Dialog', Dialog);
        vueApp.component('InputText', InputText);
        vueApp.component('Dropdown', Dropdown);
        vueApp.component('Password', Password);
        vueApp.component('Checkbox', Checkbox);
        vueApp.component('Message', Message);
        vueApp.component('FloatLabel', FloatLabel);
        vueApp.component('Toast', Toast);
        vueApp.use(ZiggyVue, Ziggy);
        vueApp.use(VueKonva);
        vueApp.use(i18n);
        vueApp.mount(el);
    },
    progress: {
        color: '#4B5563',
    },
});
