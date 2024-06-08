import './bootstrap';
import { createApp } from 'vue';

import PrimeVue from 'primevue/config';
import 'primevue/resources/themes/aura-light-noir/theme.css'

const app = createApp({

});

Object.entries(import.meta.glob('./**/*.vue', { eager: true })).forEach(([path, definition]) => {
    if (definition.default.name) {
        app.component(definition.default.name, definition.default);
    }
    // app.component(path.split('/').pop().replace(/\.\w+$/, ''), definition.default);
});

app.mount('#app');
app.use(PrimeVue);

app.config.devtools = import.meta.env.DEV
