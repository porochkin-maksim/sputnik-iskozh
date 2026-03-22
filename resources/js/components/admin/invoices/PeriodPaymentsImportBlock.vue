<template>
    <div class="period-payments-import-block">
        <!-- Выбор колонок -->
        <div class="mb-3 row g-2">
            <div class="col-md-4">
                <custom-input
                    v-model="columns.accrued"
                    label="Колонка Начислено"
                    placeholder="например: E или 5"
                    required
                />
            </div>
            <div class="col-md-4">
                <custom-input
                    v-model="columns.paid"
                    label="Колонка Оплачено"
                    placeholder="например: T или 20"
                    required
                />
            </div>
            <div class="col-md-4">
                <custom-input
                    v-model="columns.debt"
                    label="Колонка Долг"
                    placeholder="например: AI или 35"
                    required
                />
            </div>
        </div>

        <!-- Шаг 1: загрузка файла -->
        <div class="mb-3">
            <div class="btn-group">
                <button
                    class="btn btn-primary"
                    @click="triggerFileInput"
                    :disabled="loading || !isColumnsValid"
                >
                    <i class="fa fa-file-excel-o me-2"></i>
                    Загрузить файл Excel
                </button>
                <button
                    class="btn btn-success"
                    @click="submitPayments"
                    :disabled="submitting || !canSubmit"
                >
                    <i class="fa" :class="submitting ? 'fa-spinner fa-spin' : 'fa-save'"></i>
                    {{ submitting ? loadingText : 'Сохранить платежи' }}
                </button>
            </div>
            <input
                ref="fileInput"
                type="file"
                class="d-none"
                accept=".xlsx, .xls, .csv"
                @change="uploadFile"
            />
        </div>

        <!-- Индикатор загрузки -->
        <loading-spinner
            v-if="loading"
            size="md"
            color="primary"
            :text="loadingText"
            wrapper-class="py-4"
        />

        <!-- Шаг 2: предпросмотр и редактирование с вкладками -->
        <div v-if="importData && importData.length">
            <ul class="nav nav-tabs" role="tablist">
                <template v-for="(districtData, idx) in importData" :key="districtData.district">
                    <li v-if="districtData?.items?.length"
                        class="nav-item"
                        role="presentation">
                        <button
                            class="nav-link"
                            :class="{ active: idx === activeTab }"
                            :id="`tab-${districtData.district}`"
                            data-bs-toggle="tab"
                            :data-bs-target="`#district-${districtData.district}`"
                            type="button"
                            role="tab"
                            @click="activeTab = idx"
                            :disabled="submitting"
                        >
                            Район {{ districtData.district }} ({{ districtData.items.length }})
                        </button>
                    </li>
                </template>
            </ul>

            <div class="tab-content">
                <template
                    v-for="(districtData, idx) in importData"
                    :key="districtData.district">
                    <div
                        v-if="districtData?.items?.length"
                        class="tab-pane fade"
                        :class="{ show: idx === activeTab, active: idx === activeTab }"
                        :id="`district-${districtData.district}`"
                        role="tabpanel"
                    >
                        <div class="table-responsive mt-3">
                            <table class="table table-sm table-bordered table-striped align-middle sticky-header">
                                <thead>
                                <tr class="text-center">
                                    <th class="text-end">Участок</th>
                                    <th>Счёт</th>
                                    <th class="text-primary"><i class="fa fa-database"></i> Сумма</th>
                                    <th class="text-success"><i class="fa fa-file-excel-o"></i> Сумма</th>
                                    <th class="text-primary"><i class="fa fa-database"></i> Оплачено</th>
                                    <th class="text-success"><i class="fa fa-file-excel-o"></i> Оплачено</th>
                                    <th class="text-primary"><i class="fa fa-database"></i> Долг</th>
                                    <th class="text-success"><i class="fa fa-file-excel-o"></i> Долг</th>
                                    <th>Сумма платежа</th>
                                </tr>
                                </thead>
                                <tbody>
                                <tr
                                    v-for="item in districtData.items"
                                    :key="item.invoiceId"
                                    :class="[
                                        item.accountId || item.cost === 0 ? '' : 'table-danger',
                                        item.cost > 0 ? '' : 'table-warning',
                                        item.invoiceId ? '' : 'fw-bold text-danger',
                                    ]"
                                >
                                    <td class="text-end">
                                        <a :href="item.accountUrl" target="_blank" v-if="item.accountUrl">
                                            {{ item.accountNumber }}
                                        </a>
                                        <span v-else-if="item.accountNumber">
                                            {{ item.accountNumber }}
                                        </span>
                                    </td>
                                    <td class="text-center">
                                        <a :href="item.invoiceUrl" target="_blank" v-if="item.invoiceId">
                                            №{{ item.invoiceId }}
                                        </a>
                                    </td>
                                    <td class="text-end text-secondary">{{ formatMoney(item.invoiceCost) }}</td>
                                    <td class="text-end text-secondary">{{ formatMoney(item.cost) }}</td>
                                    <td class="text-end">{{ formatMoney(item.invoicePaid) }}</td>
                                    <td class="text-end fw-bold"
                                        :class="[
                                            item.paid === item.invoicePaid ? 'text-success' : '',
                                            item.paid && ! item.invoicePaid ? 'text-success' : '',
                                            ! item.paid && item.invoicePaid ? 'text-danger' : '',
                                        ]"
                                    >
                                        {{ formatMoney(item.paid) }}
                                    </td>
                                    <td class="text-end text-secondary">{{ formatMoney(item.invoiceDebt) }}</td>
                                    <td class="text-end text-secondary">{{ formatMoney(item.debt) }}</td>
                                    <td>
                                        <div class="w-100" v-if="item.accountId">
                                            <custom-input
                                                v-model="editedAmounts[getKey(districtData.district, item)]"
                                                type="number"
                                                step="0.01"
                                                :min="0"
                                                :max="Math.max(item.invoiceDebt, item.debt)"
                                                :disabled="submitting"
                                                required
                                                @update:modelValue="validateAmount(districtData.district, item)"
                                            />
                                        </div>
                                    </td>
                                </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </template>
            </div>
        </div>

        <!-- Сообщение об ошибке или пустом результате -->
        <div v-if="error" class="alert alert-danger mt-3">
            {{ error }}
        </div>
    </div>
</template>

<script setup>
import {
    ref,
    computed,
    defineProps,
    defineOptions,
    onMounted,
    onUnmounted,
}                           from 'vue';
import { useResponseError } from '@composables/useResponseError';
import { useFormat }        from '@composables/useFormat';
import LoadingSpinner       from '@common/LoadingSpinner.vue';
import CustomInput          from '@common/form/CustomInput.vue';
import {
    ApiAdminInvoiceImportPaymentsParseFile,
    ApiAdminInvoiceImportPaymentsSave,
}                           from '@api';
import { max }              from '@popperjs/core/lib/utils/math.js';

defineOptions({
    name: 'PeriodPaymentsImportBlock',
});

const props = defineProps({
    periodId: {
        type    : Number,
        required: true,
    },
});

const { parseResponseErrors, showInfo, showDanger } = useResponseError();
const { formatMoney }                               = useFormat();

const fileInput        = ref(null);
const loading          = ref(false);
const submitting       = ref(false);
const importData       = ref(null);
const editedAmounts    = ref({});
const error            = ref(null);
const activeTab        = ref(0);
const loadingStartTime = ref(null);
const loadingText      = ref('Обработка файла...');
let loadingInterval    = null;

const columns = ref({
    accrued: 'E',
    paid   : 'T',
    debt   : 'AI',
});

const isColumnsValid = computed(() => {
    return columns.value.accrued.trim() !== '' &&
        columns.value.paid.trim() !== '' &&
        columns.value.debt.trim() !== '';
});

const getKey = (district, item) => {
    return `${district}:${item.invoiceId}`;
};

const startTimer = (isSaving = false) => {
    loadingStartTime.value = Date.now();
    if (loadingInterval) {
        clearInterval(loadingInterval);
    }

    loadingInterval = setInterval(() => {
        if (!loading.value && !submitting.value) {
            clearInterval(loadingInterval);
            return;
        }
        const elapsed = Math.floor((Date.now() - loadingStartTime.value) / 1000);
        const minutes = Math.floor(elapsed / 60);
        const seconds = elapsed % 60;
        const timeStr = minutes > 0 ? `${minutes} мин ${seconds} сек` : `${seconds} сек`;

        if (isSaving) {
            loadingText.value = `Сохранение платежей... (${timeStr})`;
        }
        else {
            loadingText.value = `Обработка файла... (${timeStr})`;
        }
    }, 1000);
};

const stopTimer = () => {
    if (loadingInterval) {
        clearInterval(loadingInterval);
        loadingInterval = null;
    }
    loadingStartTime.value = null;
    loadingText.value      = 'Обработка файла...';
};

const triggerFileInput = () => {
    fileInput.value?.click();
};

const uploadFile = async (event) => {
    const file = event.target.files[0];
    if (!file) {
        return;
    }

    loading.value = true;
    startTimer(false);
    error.value         = null;
    importData.value    = null;
    editedAmounts.value = {};

    const formData = new FormData();
    formData.append('file', file);
    formData.append('col_accrued', columns.value.accrued);
    formData.append('col_paid', columns.value.paid);
    formData.append('col_debt', columns.value.debt);

    try {
        const response   = await ApiAdminInvoiceImportPaymentsParseFile(props.periodId, {}, formData);
        importData.value = response.data;

        importData.value.forEach(districtData => {
            districtData.items.forEach(item => {
                const key                = getKey(districtData.district, item);
                editedAmounts.value[key] = item.paid - item.invoicePaid > 0 ? item.paid - item.invoicePaid : 0;
            });
        });
    }
    catch (err) {
        error.value = err.response?.data?.message || 'Ошибка при загрузке файла';
        parseResponseErrors(err);
    }
    finally {
        loading.value = false;
        stopTimer();
        if (fileInput.value) {
            fileInput.value.value = '';
        }
    }
};

const validateAmount = (district, item) => {
    const key  = getKey(district, item);
    let amount = editedAmounts.value[key];
    if (amount === null || amount === undefined) {
        return;
    }

    const maxAmount = Math.max(item.invoiceDebt, item.debt);
    if (amount < 0) {
        editedAmounts.value[key] = 0;
    }
    if (amount > maxAmount) {
        editedAmounts.value[key] = maxAmount;
    }
};

const canSubmit = computed(() => {
    if (!importData.value) {
        return false;
    }

    for (const districtData of importData.value) {
        for (const item of districtData.items) {
            const key    = getKey(districtData.district, item);
            const amount = editedAmounts.value[key];
            if (amount < 0) {
                return false;
            }
        }
    }
    return true;
});

const submitPayments = async () => {
    if (!canSubmit.value) {
        return;
    }

    submitting.value = true;
    startTimer(true);
    const payload = { payments: [] };

    importData.value.forEach(districtData => {
        districtData.items.forEach(item => {
            const key    = getKey(districtData.district, item);
            const amount = editedAmounts.value[key];
            if (amount !== undefined && amount !== null && amount > 0) {
                payload.payments.push({
                    invoice_id: item.invoiceId,
                    amount    : amount,
                });
            }
        });
    });

    if (payload.payments.length === 0) {
        showInfo('Нет платежей для сохранения');
        submitting.value = false;
        stopTimer();
        return;
    }

    try {
        await ApiAdminInvoiceImportPaymentsSave(props.periodId, {}, payload);
        showInfo('Платежи успешно сохранены');
        importData.value    = null;
        editedAmounts.value = {};
    }
    catch (err) {
        const message = err.response?.data?.message || 'Ошибка при сохранении платежей';
        showDanger(message);
        parseResponseErrors(err);
    }
    finally {
        submitting.value = false;
        stopTimer();
    }
};

const handleBeforeUnload = (e) => {
    if (submitting.value) {
        e.preventDefault();
        e.returnValue = 'Идёт сохранение платежей. Вы уверены, что хотите покинуть страницу?';
        return e.returnValue;
    }
};

onMounted(() => {
    window.addEventListener('beforeunload', handleBeforeUnload);
});

onUnmounted(() => {
    window.removeEventListener('beforeunload', handleBeforeUnload);
    stopTimer();
});
</script>

<style scoped>
.period-payments-import-block {
    max-width : 100%;
}

.nav-tabs .nav-link {
    cursor : pointer;
}

.period-payments-import-block .table-responsive {
    max-height : 500px; /* ограничиваем высоту для появления скролла */
    overflow-y : auto;
}

.period-payments-import-block thead th {
    position         : sticky;
    top              : 0;
    background-color : #f8f9fa; /* цвет фона, чтобы не просвечивало содержимое */
    z-index          : 10;
}

/* Для Bootstrap 5, если есть тень/граница */
.period-payments-import-block thead th::after {
    content       : '';
    position      : absolute;
    bottom        : 0;
    left          : 0;
    width         : 100%;
    border-bottom : 2px solid #dee2e6;
}
</style>