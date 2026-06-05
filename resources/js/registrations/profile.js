import Alerts          from '@common/Alerts.vue';
import PasswordBlock   from '@components/profile/PasswordBlock.vue';
import AccountSwitcher from '@components/profile/account/AccountSwitcher.vue';
import AccountBlock    from '@components/profile/account/AccountBlock.vue';
import CounterItem     from '@components/profile/counters/CounterItem.vue';
import CountersBlock   from '@components/profile/counters/CountersBlock.vue';
import InvoicesBlock   from '@components/profile/invoices/InvoicesBlock.vue';
import SummaryBlock    from '@components/shared/summary/SummaryBlock.vue';

export function registerProfileComponents (app) {
    app.component('alerts-block', Alerts);
    app.component('password-block', PasswordBlock);
    app.component('account-switcher', AccountSwitcher);
    app.component('account-block', AccountBlock);
    app.component('counter-item', CounterItem);
    app.component('counters-block', CountersBlock);
    app.component('profile-invoices-block', InvoicesBlock);
    app.component('summary-block', SummaryBlock);
}
