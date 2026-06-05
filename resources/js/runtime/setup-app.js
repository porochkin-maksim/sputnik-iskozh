import VueUidPlugin from 'vue-uid';
import store        from '../store/index.js';

export function setupAppRuntime (app) {
    app.config.devtools = import.meta.env.DEV;

    app.use(store);
    app.use(VueUidPlugin);

    const permissions = window?.userPermissions;
    if (permissions) {
        store.dispatch('permissions/setPermissions', permissions);
    }
}
