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
        // Bootstrap теперь инициализируется глобально в bootstrap.js
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

app.config.globalProperties.$formatMoney = formatMoney;

function formatDateTime (isoString) {
    if (!isoString) {
        return '';
    }

    const date    = new Date(isoString);
    const day     = String(date.getDate()).padStart(2, '0');
    const month   = String(date.getMonth() + 1).padStart(2, '0'); // месяцы индексируются с 0
    const year    = date.getFullYear();
    const hours   = String(date.getHours()).padStart(2, '0');
    const minutes = String(date.getMinutes()).padStart(2, '0');

    return `${day}.${month}.${year} ${hours}:${minutes}`;
}

app.config.globalProperties.$formatDateTime = formatDateTime;

function formatDate (isoString) {
    if (!isoString) {
        return '';
    }

    const date  = new Date(isoString);
    const day   = String(date.getDate()).padStart(2, '0');
    const month = String(date.getMonth() + 1).padStart(2, '0'); // месяцы индексируются с 0
    const year  = date.getFullYear();

    return `${day}.${month}.${year}`;
}

app.config.globalProperties.$formatDate = formatDate;

app.config.devtools = import.meta.env.DEV;

app.use(PrimeVue);
app.use(Vuex);
app.use(store);
app.use(VueUidPlugin);

// Компоненты админки
import QueueManager from './components/QueueManager.vue';
app.component('queue-manager', QueueManager);

app.mount('#app');
