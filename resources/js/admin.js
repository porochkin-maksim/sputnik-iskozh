import './bootstrap';
import './utils/common.js';
import './utils/menus/vertical-menu.js';

import { createApp } from 'vue';
import VueUidPlugin  from 'vue-uid';
import store         from './store/index.js';

const app           = createApp({});
app.config.devtools = import.meta.env.DEV;

app.use(store);
app.use(VueUidPlugin);

if (window.userPermissions) {
    store.dispatch('permissions/setPermissions', window.userPermissions);
}

// ... импорты компонентов
import AlertsBlock               from '@common/Alerts.vue';
import TopPanelBlock             from '@components/admin/TopPanelBlock.vue';
import AccountsBlock             from '@components/admin/accounts/AccountsBlock.vue';
import AccountItemView           from '@components/admin/accounts/AccountItemView.vue';
import InvoiceItemView           from '@components/admin/invoices/InvoiceItemView.vue';
import InvoicesBlock             from '@components/admin/invoices/InvoicesBlock.vue';
import PeriodPaymentsImportBlock from '@components/admin/invoices/PeriodPaymentsImportBlock.vue';
import CounterItemView           from '@components/admin/accounts/counters/CounterItemView.vue';
import CounterHistoryBlock       from '@components/admin/counter-history/CounterHistoryBlock.vue';
import OptionsBlock              from '@components/admin/options/OptionsBlock.vue';
import PaymentsBlock             from '@components/admin/payments/PaymentsBlock.vue';
import PeriodsBlock              from '@components/admin/periods/PeriodsBlock.vue';
import RolesBlock                from '@components/admin/roles/RolesBlock.vue';
import ServicesBlock             from '@components/admin/services/ServicesBlock.vue';
import UserItemView              from '@components/admin/users/UserItemView.vue';
import UsersBlock                from '@components/admin/users/UsersBlock.vue';

app.component('alerts-block', AlertsBlock);
app.component('top-panel-block', TopPanelBlock);
app.component('accounts-block', AccountsBlock);
app.component('account-item-view', AccountItemView);
app.component('invoice-item-view', InvoiceItemView);
app.component('invoices-block', InvoicesBlock);
app.component('period-payments-import-block', PeriodPaymentsImportBlock);
app.component('counter-item-view', CounterItemView);
app.component('counter-history-block', CounterHistoryBlock);
app.component('options-block', OptionsBlock);
app.component('payments-block', PaymentsBlock);
app.component('periods-block', PeriodsBlock);
app.component('roles-block', RolesBlock);
app.component('services-block', ServicesBlock);
app.component('user-item-view', UserItemView);
app.component('users-block', UsersBlock);

app.mount('#app');