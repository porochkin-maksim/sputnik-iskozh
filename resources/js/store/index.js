import { createStore } from 'vuex';

import alerts      from './alerts';
import auth        from './auth.js';
import permissions from './permissions';

export default createStore({
    modules: {
        alerts,
        auth,
        permissions,
    },
});