import './bootstrap';
import './utils/common.js';
import './utils/menus/vertical-menu.js';

import { createApp } from 'vue';

import VueUidPlugin from 'vue-uid';
import Vuex         from 'vuex';
import store        from './store/index.js';

const app = createApp({
    mounted () {
        // Bootstrap теперь инициализируется глобально в bootstrap.js
    },
});

function formatMoney (amount, currency = '₽') {
    const formattedAmount = amount.toLocaleString('ru-RU', {
        minimumFractionDigits: 2,
        maximumFractionDigits: 2,
    });

    return `${formattedAmount} ${currency}`;
}

app.config.globalProperties.$formatMoney = formatMoney;

function formatDateTime (isoString) {
    if (!isoString) {
        return '';
    }

    const date    = new Date(isoString);
    const day     = String(date.getDate()).padStart(2, '0');
    const month   = String(date.getMonth() + 1).padStart(2, '0'); // месяцы индексируются с 0
    const year    = date.getFullYear();
    const hours   = String(date.getHours()).padStart(2, '0');
    const minutes = String(date.getMinutes()).padStart(2, '0');

    return `${day}.${month}.${year} ${hours}:${minutes}`;
}

app.config.globalProperties.$formatDateTime = formatDateTime;

function formatDate (isoString) {
    if (!isoString) {
        return '';
    }

    const date  = new Date(isoString);
    const day   = String(date.getDate()).padStart(2, '0');
    const month = String(date.getMonth() + 1).padStart(2, '0'); // месяцы индексируются с 0
    const year  = date.getFullYear();

    return `${day}.${month}.${year}`;
}

app.config.globalProperties.$formatDate = formatDate;

app.config.devtools = import.meta.env.DEV;

app.use(Vuex);
app.use(store);
app.use(VueUidPlugin);

if (window.userPermissions) {
    store.dispatch('permissions/setPermissions', window.userPermissions);
}

import AlertsBlock        from '@common/Alerts.vue';
import SummaryBlock       from '@common/blocks/SummaryBlock.vue';
import AuthBlock          from '@components/public/auth/AuthBlock.vue';
import FilesBlock         from '@components/public/files/FilesBlock.vue';
import FoldersBlock       from '@components/public/folders/FoldersBlock.vue';
import FileItem           from '@components/public/news/FileItem.vue';
import NewsBlock          from '@components/public/news/NewsBlock.vue';
import NewsShow           from '@components/public/news/NewsShow.vue';
import AnnouncementsBlock from '@components/public/news/announcements/AnnouncementsBlock.vue';
import IndexPage          from '@components/public/pages/IndexPage.vue';
import PageEditor         from '@components/public/pages/PageEditor.vue';
import CounterForm        from '@components/public/requests/CounterForm.vue';
import PaymentForm        from '@components/public/requests/PaymentForm.vue';
import ProposalForm       from '@components/public/requests/ProposalForm.vue';
import HelpDeskForm       from '@components/public/requests/HelpDeskForm.vue';
import SearchBlock        from '@components/public/search/SearchBlock.vue';

app.component('alerts-block', AlertsBlock);
app.component('summary-block', SummaryBlock);
app.component('auth-block', AuthBlock);
app.component('files-block', FilesBlock);
app.component('folders-block', FoldersBlock);
app.component('file-item', FileItem);
app.component('news-block', NewsBlock);
app.component('news-show', NewsShow);
app.component('announcements-block', AnnouncementsBlock);
app.component('index-page', IndexPage);
app.component('page-editor', PageEditor);
app.component('counter-form', CounterForm);
app.component('payment-form', PaymentForm);
app.component('proposal-form', ProposalForm);
app.component('help-desk-form', HelpDeskForm);
app.component('search-block', SearchBlock);

app.mount('#app');
