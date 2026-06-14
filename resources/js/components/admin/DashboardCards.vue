<template>
    <loading-spinner
        v-if="loading"
        size="lg"
        color="primary"
        text="Загрузка статистики..."
        wrapper-class="py-5"
    />

    <div v-else class="row g-4">
        <div
            v-for="card in cards"
            :key="card.key"
            class="col-12 col-md-6 col-lg-4"
        >
            <a :href="card.url" class="text-decoration-none">
                <div
                    class="card dash-card"
                    :style="{ borderLeftColor: card.borderColor }"
                >
                    <div class="card-body p-4 d-flex flex-column">
                        <div class="d-flex gap-3 flex-grow-1">
                            <div
                                class="icon-wrap"
                                :style="card.iconStyle"
                            >
                                <i :class="'fa ' + card.icon + ' fa-xl'"></i>
                            </div>
                            <div class="flex-grow-1 min-w-0 d-flex flex-column">
                                <div class="d-flex align-items-center justify-content-between gap-2 mb-2">
                                    <div class="title text-body">{{ card.title }}</div>
                                    <div
                                        class="count"
                                        :style="{ color: card.borderColor }"
                                    >
                                        {{ card.count }}
                                    </div>
                                </div>
                                <div class="desc">{{ card.desc }}</div>
                                <span
                                    class="badge-dash mt-auto pt-2 d-inline-block text-white"
                                    :class="{ invisible: !card.badge }"
                                    :style="{ background: card.badgeBg }"
                                >
                                    {{ card.badge || '—' }}
                                </span>
                            </div>
                        </div>
                    </div>
                </div>
            </a>
        </div>
    </div>
</template>

<script setup>
import { ref, onMounted } from 'vue';
import LoadingSpinner from '@common/LoadingSpinner.vue';
import { ApiAdminDashboardCards } from '@api';
import { useResponseError } from '@composables/useResponseError';

const { parseResponseErrors } = useResponseError();

const loading = ref(true);
const cards   = ref([]);

const customColors = {
    teal   : { bg: '#20c997', css: 'color:#20c997;background:rgba(32,201,151,.1)' },
    purple : { bg: '#6f42c1', css: 'color:#6f42c1;background:rgba(111,66,193,.1)' },
    orange : { bg: '#fd7e14', css: 'color:#fd7e14;background:rgba(253,126,20,.1)' },
};

const cardConfig = [
    {
        key      : 'users',
        url      : '/admin/users',
        icon     : 'fa-user',
        color    : 'primary',
        title    : 'Пользователи',
        desc     : 'Учётные записи членов СНТ и администраторов. Создание, редактирование, привязка к участкам и ролям.',
        countKey : 'userCount',
        badgeKey : null,
    },
    {
        key      : 'accounts',
        url      : '/admin/accounts',
        icon     : 'fa-th-large',
        color    : 'success',
        title    : 'Участки',
        desc     : 'Дачные участки — основная учётная единица системы. Участок №1 — техническая запись СНТ. Ведение номера, площади, кадастрового номера.',
        countKey : 'accountCount',
        badgeKey : 'accounts',
        badgeFn  : (d) => d.withoutSnt + ' без СНТ',
    },
    {
        key      : 'roles',
        url      : '/admin/roles',
        icon     : 'fa-users',
        color    : 'teal',
        title    : 'Роли',
        desc     : 'Роли и права доступа. Настройка разрешений для каждого раздела системы (просмотр, редактирование, удаление).',
        countKey : 'roleCount',
        badgeKey : null,
    },
    {
        key      : 'invoices',
        url      : '/admin/invoices',
        icon     : 'fa-file-text',
        color    : 'warning',
        title    : 'Счета',
        desc     : (d) => d.activePeriod
            ? 'Счета участков в периоде «' + d.activePeriod.name + '». Содержат транзакции по услугам. Платежи распределяются автоматически сверху вниз.'
            : 'Счета участков с транзакциями по услугам. Автоматическое распределение платежей.',
        countKey : 'invoiceCount',
        badgeKey : 'invoices',
        badgeFn  : (d) => 'Регулярных ' + d.regular + ' · Доходов ' + d.income + ' · Расходов ' + d.outcome,
    },
    {
        key      : 'payments',
        url      : '/admin/invoices/payments',
        icon     : 'fa-credit-card',
        color    : 'info',
        title    : 'Платежи',
        desc     : 'Входящие платежи из публичной формы и личного кабинета. При привязке к счету автоматически распределяются по транзакциям.',
        countKey : 'paymentCount',
        badgeKey : 'payments',
        badgeFn  : (d, details) => {
            const parts = [];
            if (d.period) parts.push(d.period + ' в текущем периоде');
            if (d.unlinked) parts.push(d.unlinked + ' без счёта');
            return parts.join(' · ') || null;
        },
    },
    {
        key      : 'tickets',
        url      : '/admin/help-desk',
        icon     : 'fa-bullhorn',
        color    : 'danger',
        title    : 'Заявки',
        desc     : 'Обращения в службу поддержки от членов СНТ. Статусы: новая, в работе, ожидает ответа, закрыта, отклонена.',
        countKey : 'ticketCount',
        badgeKey : 'tickets',
        badgeFn  : (d) => d.new ? d.new + ' новых' : null,
    },
    {
        key      : 'periods',
        url      : '/admin/periods',
        icon     : 'fa-calendar',
        color    : 'secondary',
        title    : 'Периоды',
        desc     : 'Отчётные календарные циклы (сезонные годы СНТ). Внутри периода создаются регулярные счета и тарифы.',
        countKey : 'periodCount',
        badgeKey : 'activePeriod',
        badgeFn  : (d) => d ? 'Активен: ' + d.name : null,
    },
    {
        key      : 'services',
        url      : '/admin/services',
        icon     : 'fa-cogs',
        color    : 'purple',
        title    : 'Услуги',
        desc     : 'Тарифы и услуги СНТ: членские взносы, электроэнергия, целевые сборы, абонентская плата, перерасчёт, авансовые платежи.',
        countKey : 'serviceCount',
        badgeKey : null,
    },
    {
        key      : 'counters',
        url      : '/admin/counter-history',
        icon     : 'fa-bolt',
        color    : 'orange',
        title    : 'Счётчики',
        desc     : 'Приборы учёта электроэнергии участков. При подтверждении показаний автоматически создаётся транзакция в текущем периоде.',
        countKey : 'counterCount',
        badgeKey : null,
    },
];

function resolveColor(color, type) {
    const custom = customColors[color];
    if (type === 'border') return custom ? custom.bg : 'var(--bs-' + color + ')';
    if (type === 'icon') return custom ? custom.css : 'color:var(--bs-' + color + ');background:rgba(var(--bs-' + color + '-rgb),.1)';
    if (type === 'badge') return custom ? custom.bg : 'var(--bs-' + color + ')';
    return '';
}

onMounted(async () => {
    try {
        const response = await ApiAdminDashboardCards();
        const { stats, details } = response.data;

        cards.value = cardConfig.map((cfg) => {
            const count = stats[cfg.countKey];
            if (count === null) return null;

            const desc = typeof cfg.desc === 'function' ? cfg.desc(details) : cfg.desc;
            let badge = null;
            if (cfg.badgeKey) {
                const badgeData = details[cfg.badgeKey];
                if (badgeData !== undefined && badgeData !== null) {
                    badge = cfg.badgeFn ? cfg.badgeFn(badgeData, details) : String(badgeData);
                }
            }

            return {
                key         : cfg.key,
                url         : cfg.url,
                icon        : cfg.icon,
                title       : cfg.title,
                desc,
                count,
                badge,
                borderColor : resolveColor(cfg.color, 'border'),
                iconStyle   : resolveColor(cfg.color, 'icon'),
                badgeBg     : resolveColor(cfg.color, 'badge'),
            };
        }).filter(Boolean);
    }
    catch (error) {
        parseResponseErrors(error);
    }
    finally {
        loading.value = false;
    }
});
</script>
