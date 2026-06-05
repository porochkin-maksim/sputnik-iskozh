import './bootstrap';
import './utils/common.js';
import './utils/menus/vertical-menu.js';

import { createApp } from 'vue';

import { setupAppRuntime }           from './runtime/setup-app.js';
import { registerProfileComponents } from './registrations/profile.js';

const app = createApp({});
setupAppRuntime(app);

registerProfileComponents(app);

app.mount('#app');
