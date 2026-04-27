<template>
    <div class="invoice-item-view">
        <!-- Заголовок -->
        <h4 class="mb-3" v-if="localInvoice.id">
            Детали счёта №{{ localInvoice.id }}
            для «{{ localInvoice.account?.number || '—' }}»
            <span class="text-muted fw-normal">
                 | {{ localInvoice.periodName }} | {{ localInvoice.displayName || '—' }}
            </span>
        </h4>

        <!-- Верхняя панель с действиями -->
        <div class="d-flex flex-wrap align-items-center justify-content-between gap-2 mb-3">
            <div class="d-flex flex-wrap align-items-center gap-2">
                <!-- Ссылка на участок -->
                <template>
                    <a v-if="localInvoice.account?.viewUrl && canAccountView"
                       class="btn btn-sm btn-outline-primary"
                       :href="localInvoice.account.viewUrl">
                        <i class="fa fa-home me-1" aria-hidden="true"></i>
                        Участок {{ localInvoice.account?.number }} ({{ localInvoice.account?.size }}м²)
                    </a>
                </template>

                <!-- Квитанция -->
                <a v-if="localInvoice.receiptUrl"
                   :href="localInvoice.receiptUrl"
                   target="_blank"
                   class="btn btn-sm btn-outline-danger">
                    <i class="fa fa-file-pdf-o me-1" aria-hidden="true"></i>
                    Квитанция
                </a>

                <button class="btn btn-outline-primary btn-sm" @click="recalcAction"
                        v-if="canEdit"
                        :class="isRecalculating ? 'disabled' : ''">
                    <i class="fa fa-refresh" :class="isRecalculating ? 'fa-spin' : ''"></i> Пересчитать
                </button>

                <!-- История -->
                <history-btn v-if="localInvoice.historyUrl"
                             class="btn-link underline-none p-0"
                             :url="localInvoice.historyUrl"
                             aria-label="История изменений" />
            </div>

            <!-- Кнопка удаления -->
            <button v-if="canDelete"
                    class="btn btn-sm btn-outline-danger"
                    @click="dropAction">
                <i class="fa fa-trash me-1" aria-hidden="true"></i>
                Удалить счёт
            </button>
        </div>

        <table class="table table-borderless table-sm w-auto mb-0" v-if="localInvoice.detailCost">
            <tbody>
            <tr>
                <th>Основа:</th>
                <td>{{ formatMoney(localInvoice.detailCost.main) }}</td>
                <th>Долг:</th>
                <td>{{ formatMoney(localInvoice.detailCost.debt) }}</td>
                <th>Аванс:</th>
                <td>{{ formatMoney(localInvoice.detailCost.advance) }}</td>
            </tr>
            </tbody>
        </table>

        <!-- Статус оплаты -->
        <div class="alert p-3 mb-3"
             v-if="actions.view && localInvoice.cost !== undefined"
             :class="statusAlertClass">
            <div class="d-flex align-items-center gap-2">
                <i class="fa"
                   :class="localInvoice.isPaid ? 'fa-check-circle text-success' : 'fa-times-circle text-secondary'"
                   aria-hidden="true"></i>
                <div>
                    <strong>Оплачено:</strong>
                    {{ formatMoney(localInvoice.paid || 0) }} / {{ formatMoney(localInvoice.cost || 0) }}
                    <span v-if="localInvoice.delta !== 0" class="ms-2" :class="deltaClass">
                        (Долг {{ formatMoney(Math.abs(localInvoice.delta)) }})
                    </span>
                </div>
            </div>
        </div>

        <!-- Блоки услуг и платежей -->
        <claim-block v-if="actions.claims?.view"
                     :invoice="invoice"
                     v-model:count="claimsCount"
                     v-model:reload="reload" />

        <div v-if="actions.claims?.view && actions.payments?.view" class="border-top my-3"></div>

        <payments-block v-if="actions.payments?.view"
                        :invoice="invoice"
                        v-model:count="paymentsCount"
                        v-model:reload="reload" />
    </div>
</template>

<script setup>
import {
    ref,
    computed,
    onMounted,
    watch,
    defineOptions,
}                           from 'vue';
import { useResponseError } from '@composables/useResponseError';
import { useFormat }        from '@composables/useFormat';
import { usePermissions }   from '@composables/usePermissions.js';
import HistoryBtn           from '@common/HistoryBtn.vue';
import ClaimBlock           from './claims/ClaimBlock.vue';
import PaymentsBlock        from './payments/PaymentsBlock.vue';
import {
    ApiAdminInvoiceGet,
    ApiAdminInvoiceDelete,
    ApiAdminInvoiceRecalc,
}                           from '@api';
import Url                  from '@utils/Url.js';

defineOptions({
    name: 'InvoiceItemView',
});

const props = defineProps({
    invoice: {
        type    : Object,
        required: true,
    },
});

const { parseResponseErrors, showInfo, showDanger } = useResponseError();
const { formatMoney }                               = useFormat();
const { has }                                       = usePermissions();

const canView        = computed(() => has('invoices', 'view'));
const canEdit        = computed(() => has('invoices', 'edit'));
const canDrop        = computed(() => has('invoices', 'drop'));
const canAccountView = computed(() => has('accounts', 'view'));

const localInvoice    = ref({});
const actions         = ref({});
const reload          = ref(false);
const claimsCount     = ref(0);
const paymentsCount   = ref(0);
const isLoading       = ref(true);
const isRecalculating = ref(false);

// Computed свойства для стилей
const statusAlertClass = computed(() => {
    if (!localInvoice.value.isPaid && localInvoice.value.cost === 0) {
        return 'alert-secondary';
    }
    return localInvoice.value.isPaid ? 'alert-success' : 'alert-warning';
});

const deltaClass = computed(() => {
    return localInvoice.value.delta > 0 ? 'text-danger fw-bold' : 'text-success';
});

// Загрузка данных
const loadInvoice = async () => {
    isLoading.value = true;
    try {
        const response     = await ApiAdminInvoiceGet(props.invoice.id);
        localInvoice.value = response.data;
        actions.value      = response.data.actions || {};
    }
    catch (error) {
        parseResponseErrors(error);
    }
    finally {
        isLoading.value = false;
    }
};

// Удаление счёта
const dropAction = async () => {
    if (!confirm('Удалить счёт?')) {
        return;
    }

    try {
        const response = await ApiAdminInvoiceDelete(props.invoice.id);
        if (response.data) {
            showInfo('Счёт удалён');
            setTimeout(() => {
                window.location.href = Url.Routes.adminInvoiceIndex.uri;
            }, 1000);
        }
        else {
            showDanger('Счёт не удалён');
        }
    }
    catch (error) {
        parseResponseErrors(error);
    }
};

// Пересчёт счёта
const recalcAction = async () => {
    isRecalculating.value = true;
    try {
        const response = await ApiAdminInvoiceRecalc(props.invoice.id);
        if (response.data) {
            await loadInvoice();
            showInfo('Счёт пересчитан');
        }
        else {
            showDanger('Не удалось пересчитать счёт');
        }
    }
    catch (error) {
        parseResponseErrors(error);
    }
    finally {
        isRecalculating.value = false;
    }
};

// Можно ли удалить
const canDelete = computed(() => {
    return canDrop && actions.value.drop && claimsCount.value === 0 && paymentsCount.value === 0;
});

// Следим за перезагрузкой
watch(reload, (value) => {
    if (value) {
        loadInvoice();
        setTimeout(() => {
            reload.value = false;
        }, 100);
    }
});

onMounted(() => {
    // Сразу устанавливаем данные из пропсов для первоначального рендера
    localInvoice.value = props.invoice;
    actions.value      = props.invoice.actions || {};
    // Затем загружаем свежие данные
    loadInvoice();
});
</script>