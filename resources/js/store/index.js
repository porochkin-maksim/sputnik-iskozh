import Vuex from 'vuex';

import alerts from './modules/alerts';

export default new Vuex.Store({
    modules: {
        alerts,
    },
});