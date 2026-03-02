import './bootstrap';
import './utils/common.js';
import './utils/menus/vertical-menu.js';

import { createApp } from 'vue';

import VueUidPlugin from 'vue-uid';
import Vuex         from 'vuex';
import store        from './store/index.js';

const app           = createApp({});
app.config.devtools = import.meta.env.DEV;

app.use(Vuex);
app.use(store);
app.use(VueUidPlugin);

import Alerts          from '@common/Alerts.vue';
import PasswordBlock   from '@components/profile/PasswordBlock.vue';
import AccountSwitcher from '@components/profile/account/AccountSwitcher.vue';
import AccountBlock    from '@components/profile/account/AccountBlock.vue';
import CounterItem     from '@components/profile/counters/CounterItem.vue';
import CountersBlock   from '@components/profile/counters/CountersBlock.vue';
import SummaryBlock    from '@components/common/blocks/SummaryBlock.vue';

app.component('alerts-block', Alerts);
app.component('password-block', PasswordBlock);
app.component('account-switcher', AccountSwitcher);
app.component('account-block', AccountBlock);
app.component('counter-item', CounterItem);
app.component('counters-block', CountersBlock);
app.component('summary-block', SummaryBlock);

app.mount('#app');
