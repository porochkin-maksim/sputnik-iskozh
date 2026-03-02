import Vuex from 'vuex';

import alerts from './modules/alerts';
import auth   from './modules/auth.js';

export default new Vuex.Store({
    modules: {
        alerts,
        auth,
    },
});