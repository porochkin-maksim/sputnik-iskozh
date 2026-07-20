<template>
    <div class="profile-invoices-view">
        <div class="profile-filters">
            <div class="page-card profile-period-card">
                <div class="profile-period-card__label">Период</div>
                <div class="profile-period-card__form">
                    <select name="period"
                            class="form-select profile-period-select"
                            :value="selectedPeriodId"
                            @change="onPeriodChange">
                        <option v-for="p in periods"
                                :key="p.id"
                                :value="p.id">
                            {{ p.name }}
                        </option>
                    </select>
                </div>
            </div>
            <div class="page-card profile-period-card">
                <div class="profile-period-card__label">Участок</div>
                <account-search></account-search>
            </div>
        </div>

        <div v-if="isViewingOther && viewedAccount"
             class="alert alert-info d-flex justify-content-between align-items-center">
            <span>
                <i class="fa fa-eye" aria-hidden="true"></i>
                Просмотр участка <strong>{{ viewedAccount.number }}</strong>
            </span>
            <button class="btn btn-sm btn-outline-secondary" @click="resetView">
                <i class="fa fa-times" aria-hidden="true"></i>
                Вернуться к своим счетам
            </button>
        </div>

        <div class="page-hero mb-0">
            <h3 class="page-hero__title text-dark">
                Счета на участок "{{ viewedAccount?.number ?? '...' }}"
            </h3>
            <div class="page-hero__lead">
                Просмотр начислений, оплат и квитанций по выбранному периоду.
            </div>
        </div>

        <div v-if="totalDebt > 0 || accountBalance !== null"
             class="page-card profile-invoice-balance">
            <div class="profile-invoice-balance__col"
                 :class="totalDebt > 0 ? 'profile-invoice-balance__col--danger' : ''">
                <div class="profile-invoice-balance__label">
                    <i v-if="totalDebt > 0" class="fa fa-exclamation-triangle" aria-hidden="true"></i>
                    Общий долг по участку
                </div>
                <div class="profile-invoice-balance__value"
                     :class="totalDebt > 0 ? '' : 'profile-invoice-balance__value--neutral'">
                    {{ formatMoney(totalDebt) }}
                </div>
            </div>
            <div class="profile-invoice-balance__col"
                 :class="accountBalance > 0 ? 'profile-invoice-balance__col--success' : accountBalance < 0 ? 'profile-invoice-balance__col--danger' : ''">
                <div class="profile-invoice-balance__label"
                     :class="accountBalance > 0 ? '' : accountBalance < 0 ? '' : 'profile-invoice-balance__label--neutral'">
                    <i v-if="accountBalance > 0" class="fa fa-plus-circle" aria-hidden="true"></i>
                    <i v-else-if="accountBalance < 0" class="fa fa-exclamation-triangle" aria-hidden="true"></i>
                    Баланс
                </div>
                <div class="profile-invoice-balance__value"
                     :class="accountBalance > 0 ? 'profile-invoice-balance__value--success' : accountBalance < 0 ? '' : 'profile-invoice-balance__value--neutral'">
                    {{ formatMoney(accountBalance) }}
                </div>
            </div>
        </div>

        <div v-if="loading" class="text-center py-3">
            <i class="fa fa-spinner fa-spin fa-2x text-muted"></i>
        </div>

        <div v-else-if="invoices.length" class="profile-invoices-list">
            <details v-for="invoice in invoices"
                     :key="invoice.id"
                     class="page-card profile-invoice-card">
                <summary class="profile-invoice-card__summary">
                    <div class="profile-invoice-card__title">
                        <span class="profile-invoice-card__label">Счёт</span>
                        <span class="profile-invoice-card__value">{{ invoice.title }}</span>
                    </div>
                    <div v-if="invoice.periodName" class="profile-invoice-card__period">
                        <i class="fa fa-calendar" aria-hidden="true"></i>
                        {{ invoice.periodName }}
                    </div>
                    <div class="profile-invoice-card__meta">
                        <div class="profile-invoice-chip">
                            <span class="profile-invoice-chip__label">Тариф</span>
                            <span class="profile-invoice-chip__value">{{ invoice.cost }}</span>
                        </div>
                        <div class="profile-invoice-chip">
                            <span class="profile-invoice-chip__label">Оплачено</span>
                            <span class="profile-invoice-chip__value">{{ invoice.paid }}</span>
                        </div>
                        <div class="profile-invoice-chip">
                            <span class="profile-invoice-chip__label">Долг</span>
                            <span class="profile-invoice-chip__value"
                                  :class="invoice.deltaNumeric > 0 ? 'debt' : 'paid'">{{ invoice.delta }}</span>
                        </div>
                    </div>
                </summary>

                <div class="profile-invoice-card__body">
                    <div class="profile-invoice-claims">
                        <div class="profile-invoice-claims__list">
                            <div v-for="claim in invoice.claims"
                                 :key="claim.id"
                                 class="profile-invoice-claim-item">
                                <div class="profile-invoice-claim-item__name">{{ claim.name }}</div>
                                <div class="profile-invoice-claim-item__meta">
                                    <span class="profile-invoice-claim-item__chip">
                                        <span class="profile-invoice-claim-item__label">Тариф</span>
                                        <span class="profile-invoice-claim-item__value">{{ claim.tariff }}</span>
                                    </span>
                                    <span v-if="claim.quantity !== null" class="profile-invoice-claim-item__chip">
                                        <span class="profile-invoice-claim-item__label">Кол-во</span>
                                        <span class="profile-invoice-claim-item__value">{{ claim.quantity }}</span>
                                    </span>
                                    <span class="profile-invoice-claim-item__chip">
                                        <span class="profile-invoice-claim-item__label">Стоимость</span>
                                        <span class="profile-invoice-claim-item__value">{{ claim.cost }}</span>
                                    </span>
                                    <span class="profile-invoice-claim-item__chip">
                                        <span class="profile-invoice-claim-item__label">Оплачено</span>
                                        <span class="profile-invoice-claim-item__value">{{ claim.paid }}</span>
                                    </span>
                                    <span class="profile-invoice-claim-item__chip">
                                        <span class="profile-invoice-claim-item__label">Долг</span>
                                        <span class="profile-invoice-claim-item__value"
                                              :class="claim.deltaNumeric > 0 ? 'debt' : 'paid'">{{ claim.delta }}</span>
                                    </span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div v-if="invoice.payments.length" class="profile-invoice-payments">
                        <div class="profile-invoice-payments__title">Платежи</div>
                        <div class="profile-invoice-payments__list">
                            <div v-for="payment in invoice.payments"
                                 :key="payment.id"
                                 class="profile-invoice-payment-item">
                                <span class="profile-invoice-payment-item__date">{{ payment.date }}</span>
                                <span class="profile-invoice-payment-item__cost">{{ payment.cost }}</span>
                            </div>
                        </div>
                    </div>

                    <div class="profile-invoice-actions">
                        <template v-if="!isViewingOther && acquiringAvailable">
                            <form v-for="amount in invoice.acquiring"
                                  :key="`${invoice.id}-${amount.label}`"
                                  method="POST"
                                  :action="amount.url"
                                  target="_blank">
                                <input type="hidden"
                                       name="_token"
                                       :value="csrfToken">
                                <button class="btn btn-sm btn-success profile-invoice-action-btn">
                                    <i class="fa fa-credit-card"></i>
                                    Оплатить {{ amount.label }}
                                </button>
                            </form>
                        </template>
                        <a v-if="invoice.paymentUrl"
                           class="btn btn-sm btn-outline-primary profile-invoice-action-btn"
                           :href="invoice.paymentUrl">
                            <i class="fa fa-envelope"></i>
                            Сообщить о совершённом платеже
                        </a>
                        <a v-if="invoice.receiptUrl"
                           class="btn btn-sm btn-outline-danger profile-invoice-action-btn"
                           target="_blank"
                           :href="invoice.receiptUrl">
                            <i class="fa fa-file-pdf-o text-danger"></i>
                            Получить квитанцию
                        </a>
                    </div>
                </div>
            </details>
        </div>

        <div v-else-if="!loading" class="profile-empty-state">
            <hr>
            <h6 class="text-center text-secondary m-0"><i>счетов нет...</i></h6>
        </div>
    </div>
</template>

<script setup>
import {
    ref,
    onMounted,
    onUnmounted,
}                                 from 'vue';
import { useFormat }              from '@composables/useFormat';
import { ApiProfileInvoicesJson } from '@api';
import AccountSearch              from '@components/profile/account/AccountSearch.vue';

const props = defineProps({
    csrfToken: { type: String, default: '' },
});

const { formatMoney } = useFormat();

const periods            = ref([]);
const selectedPeriodId   = ref(null);
const invoices           = ref([]);
const totalDebt          = ref(0);
const acquiringAvailable = ref(false);
const isViewingOther     = ref(false);
const viewedAccount      = ref(null);
const accountBalance     = ref(null);
const loading            = ref(false);

const getUrlParams = () => {
    const params = new URLSearchParams(window.location.search);
    return {
        period: params.get('period'),
        v     : params.get('v'),
    };
};

const updateUrl = (params) => {
    const url = new URL(window.location.href);
    Object.entries(params).forEach(([key, value]) => {
        if (value) {
            url.searchParams.set(key, value);
        }
        else {
            url.searchParams.delete(key);
        }
    });
    window.history.replaceState({}, '', url.toString());
};

const loadData = async () => {
    const { period, v } = getUrlParams();
    loading.value       = true;

    try {
        const response = await ApiProfileInvoicesJson({
            period: period || '',
            v     : v || '',
        });

        const page               = response.data.page;
        periods.value            = page.periodOptions ?? [];
        selectedPeriodId.value   = page.selectedPeriodId;
        invoices.value           = page.invoiceItems ?? [];
        totalDebt.value          = page.totalDebt ?? 0;
        acquiringAvailable.value = page.acquiringAvailable ?? false;
        isViewingOther.value     = page.isViewingOther ?? false;
        viewedAccount.value      = page.viewedAccount ?? null;
        accountBalance.value     = page.accountBalance ?? null;

        if (viewedAccount.value) {
            updateUrl({ v: viewedAccount.value.number });
        }
    }
    catch {
        // keep current data on error
    }
    finally {
        loading.value = false;
    }
};

const resetView = () => {
    updateUrl({ period: '', v: '' });
    loadData();
};

const onPeriodChange = (event) => {
    const periodId = event.target.value;
    updateUrl({ period: periodId, v: getUrlParams().v });
    loadData();
};

const handleUrlChange = () => {
    loadData();
};

onMounted(() => {
    loadData();
    window.addEventListener('urlchange', handleUrlChange);
});

onUnmounted(() => {
    window.removeEventListener('urlchange', handleUrlChange);
});
</script>
