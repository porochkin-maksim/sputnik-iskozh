import './bootstrap';
import './utils/common.js';
import './utils/menus/vertical-menu.js';

import { createApp } from 'vue';

import { applyLegacyFormatters } from './runtime/app-formatters.js';
import { setupAppRuntime }       from './runtime/setup-app.js';
import { registerAppComponents } from './registrations/app.js';

const app = createApp({});
applyLegacyFormatters(app);
setupAppRuntime(app);

registerAppComponents(app);

app.mount('#app');
