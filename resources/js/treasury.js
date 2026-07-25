import './bootstrap';

import { createApp }               from 'vue';
import { setupAppRuntime }         from './runtime/setup-app.js';
import { registerTreasuryComponents } from './registrations/treasury.js';

const app = createApp({});
setupAppRuntime(app);

registerTreasuryComponents(app);

app.mount('#app');
