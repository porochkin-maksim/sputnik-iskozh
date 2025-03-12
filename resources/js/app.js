import './bootstrap';
import './utils/common.js';
import './utils/menus/vertical-menu.js';

import 'primevue/resources/themes/aura-light-noir/theme.css';

import { createApp } from 'vue';

import PrimeVue     from 'primevue/config';
import VueUidPlugin from 'vue-uid';
import Vuex         from 'vuex';
import store        from './store/index.js';

const app = createApp({
    mounted () {
        // Включить Popover Bootstrap
        setTimeout(() => {
            const popoverTriggerList = document.querySelectorAll('[data-bs-toggle="popover"]');
            const popoverList        = [...popoverTriggerList].map(popoverTriggerEl => new bootstrap.Popover(popoverTriggerEl));
        }, 1);
        //<button type="button" class="btn btn-lg btn-danger" data-bs-toggle="popover" title="Popover title"
        // data-bs-content="And here's some amazing content. It's very engaging. Right?">Click to toggle
        // popover</button>
    },
});


Object.entries(import.meta.glob('./**/*.vue', { eager: true })).forEach(([path, definition]) => {
    if (definition.default.name) {
        // console.log(definition.default.name);
        app.component(definition.default.name, definition.default);
    }
    // app.component(path.split('/').pop().replace(/\.\w+$/, ''), definition.default);
});

function formatMoney (amount, currency = '₽') {
    const formattedAmount = amount.toLocaleString('ru-RU', {
        minimumFractionDigits: 2,
        maximumFractionDigits: 2,
    });

    return `${formattedAmount} ${currency}`;
}

function formatDateTime (isoString) {
    const date    = new Date(isoString);
    const day     = String(date.getDate()).padStart(2, '0');
    const month   = String(date.getMonth() + 1).padStart(2, '0'); // месяцы индексируются с 0
    const year    = date.getFullYear();
    const hours   = String(date.getHours()).padStart(2, '0');
    const minutes = String(date.getMinutes()).padStart(2, '0');

    return `${day}.${month}.${year} ${hours}:${minutes}`;
}

app.config.globalProperties.$formatMoney    = formatMoney;
app.config.globalProperties.$formatDateTime = formatDateTime;
app.config.devtools                         = import.meta.env.DEV;

app.use(PrimeVue);
app.use(Vuex);
app.use(store);
app.use(VueUidPlugin);

app.mount('#app');
