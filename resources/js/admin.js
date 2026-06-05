import './bootstrap';
import './utils/common.js';
import './utils/menus/vertical-menu.js';

import { createApp }               from 'vue';
import { setupAppRuntime }         from './runtime/setup-app.js';
import { registerAdminComponents } from './registrations/admin.js';

const app = createApp({});
setupAppRuntime(app);

registerAdminComponents(app);

app.mount('#app');
