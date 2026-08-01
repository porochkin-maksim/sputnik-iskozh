import { createApp } from 'vue';
import UserFilter from './components/admin/history/UserFilter.vue';

const el = document.getElementById('user-filter-app');
if (el) {
    const app = createApp(UserFilter, {
        action: el.dataset.action,
        users: JSON.parse(el.dataset.users),
        userId: el.dataset.userId || '',
        exclude: el.dataset.exclude === '1',
        params: JSON.parse(el.dataset.params),
    });
    app.mount(el);
}
