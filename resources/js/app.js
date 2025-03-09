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

app.config.globalProperties.$formatMoney = formatMoney;
app.config.devtools = import.meta.env.DEV;

app.use(PrimeVue);
app.use(Vuex);
app.use(store);
app.use(VueUidPlugin);

app.mount('#app');
