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

        <!-- Режим загрузки -->
        <div class="mb-3">
            <div class="btn-group" role="group">
                <button
                    type="button"
                    class="btn btn-outline-primary"
                    :class="{ active: mode === 'single' }"
                    @click="mode = 'single'"
                >
                    Один файл
                </button>
                <button
                    type="button"
                    class="btn btn-outline-primary"
                    :class="{ active: mode === 'diff' }"
                    @click="mode = 'diff'"
                >
                    Сравнить два файла
                </button>
            </div>
        </div>

        <!-- Файлы для загрузки -->
        <div class="mb-3">
            <div class="row g-2">
                <div class="col-md-6">
                    <div class="border rounded p-2">
                        <label class="form-label">Основной файл (последний)</label>
                        <div class="input-group">
                            <button
                                class="btn btn-primary"
                                @click="triggerFileInput('main')"
                                :disabled="loading || !isColumnsValid"
                            >
                                <i class="fa fa-file-excel-o me-2"></i>
                                Выбрать файл
                            </button>
                            <span class="form-control bg-light" v-if="files.main">
                                {{ files.main.name }}
                            </span>
                        </div>
                        <input
                            ref="mainFileInput"
                            type="file"
                            class="d-none"
                            accept=".xlsx, .xls, .csv"
                            @change="onFileSelected('main', $event)"
                        />
                    </div>
                </div>
                <div class="col-md-6" v-if="mode === 'diff'">
                    <div class="border rounded p-2">
                        <label class="form-label">Предыдущий файл (для сравнения)</label>
                        <div class="input-group">
                            <button
                                class="btn btn-secondary"
                                @click="triggerFileInput('prev')"
                                :disabled="loading || !isColumnsValid"
                            >
                                <i class="fa fa-file-excel-o me-2"></i>
                                Выбрать файл
                            </button>
                            <span class="form-control bg-light" v-if="files.prev">
                                {{ files.prev.name }}
                            </span>
                        </div>
                        <input
                            ref="prevFileInput"
                            type="file"
                            class="d-none"
                            accept=".xlsx, .xls, .csv"
                            @change="onFileSelected('prev', $event)"
                        />
                    </div>
                </div>
            </div>
        </div>

        <!-- Кнопка загрузки -->
        <div class="mb-3">
            <div class="btn-group">
                <button
                    class="btn btn-success"
                    @click="uploadFiles"
                    :disabled="loading || !canUpload"
                >
                    <i class="fa fa-upload me-2"></i>
                    {{ loading ? 'Обработка...' : 'Загрузить и обработать' }}
                </button>
                <button
                    class="btn"
                    :class="submitting || !canSubmit ? 'btn-success' : 'btn-danger'"
                    @click="submitPayments"
                    :disabled="submitting || !canSubmit"
                >
                    <i class="fa" :class="submitting ? 'fa-spinner fa-spin' : 'fa-save'"></i>
                    {{ submitting ? loadingText : 'Сохранить платежи' }}
                </button>
            </div>
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
                <li v-for="(districtData, idx) in importData" :key="districtData.district" class="nav-item"
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
                        Участок {{ districtData.district }} ({{ districtData.items.length }})
                    </button>
                </li>
            </ul>

            <div class="tab-content">
                <div
                    v-for="(districtData, idx) in importData"
                    :key="districtData.district"
                    class="tab-pane fade"
                    :class="{ show: idx === activeTab, active: idx === activeTab }"
                    :id="`district-${districtData.district}`"
                    role="tabpanel"
                >
                    <!-- Панель автозаполнения для участка -->
                    <div class="d-flex justify-content-between align-items-center mb-2 mt-2">
                        <div class="w-50">
                            <custom-select
                                v-model="autoFillStrategy[districtData.district]"
                                :options="fillStrategies"
                                label="Автозаполнение"
                                :disabled="submitting"
                            />
                        </div>
                        <button
                            class="btn btn-sm btn-outline-primary"
                            @click="applyAutoFill(districtData.district)"
                            :disabled="submitting"
                        >
                            Применить к участку
                        </button>
                    </div>

                    <div class="table-responsive mt-3">
                        <table class="table table-sm table-bordered table-striped align-middle sticky-header">
                            <thead>
                            <tr class="text-center">
                                <th class="text-end">Участок</th>
                                <th>Счёт</th>
                                <th><i class="fa fa-database"></i> Аванс</th>
                                <th><i class="fa fa-database"></i> Долг</th>
                                <th><i class="fa fa-database"></i> Основа</th>
                                <th><i class="fa fa-database"></i> К оплате</th>
                                <th class="text-success"><i class="fa fa-file-excel-o"></i> К оплате</th>
                                <th><i class="fa fa-database"></i> Оплачено</th>
                                <th class="text-success"><i class="fa fa-file-excel-o"></i> Оплачено</th>
                                <th><i class="fa fa-database"></i> Осталось</th>
                                <th class="text-success"><i class="fa fa-file-excel-o"></i> Осталось</th>
                                <th>Сумма платежа</th>
                            </tr>
                            </thead>
                            <tbody>
                            <tr
                                v-for="item in districtData.items"
                                :key="item.invoiceId"
                                :class="[item.accountId ? '' : 'table-danger']"
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
                                <td class="text-end" :class="item.invoiceAdvance ? 'fw-bold' : 'text-secondary'"
                                >
                                    {{ formatMoney(item.invoiceAdvance) }}
                                </td>
                                <td class="text-end" :class="item.invoiceDebt ? 'fw-bold' : 'text-secondary'"
                                >
                                    {{ formatMoney(item.invoiceDebt) }}
                                </td>
                                <td class="text-end text-secondary"
                                    :class="[item.changeInCost && item.invoiceMain !== item.invoiceCost ? 'text-danger fw-bold' : '']"
                                >
                                    {{ formatMoney(item.invoiceMain) }}
                                </td>
                                <td class="text-end text-secondary"
                                    :class="[item.changeInCost ? 'table-danger text-danger fw-bold' : '']"
                                >
                                    {{ formatMoney(item.invoiceCost) }}
                                </td>
                                <td class="text-end text-secondary"
                                    :class="[item.changeInCost ? 'table-danger text-danger fw-bold' : '']"
                                >
                                    {{ formatMoney(item.cost) }}
                                </td>
                                <td class="text-end"
                                    :class="[item.invoicePaid ? 'text-success fw-bold' : item.changeInPaid ? 'text-danger fw-bold' : 'text-secondary']"
                                >
                                    {{ formatMoney(item.invoicePaid) }}
                                </td>
                                <td class="text-end"
                                    :class="[item.paid ? 'text-success fw-bold' : item.changeInPaid ? 'text-danger fw-bold' : 'text-secondary']"
                                >
                                    {{ formatMoney(item.paid) }}
                                </td>
                                <td class="text-end"
                                    :class="[item.invoiceDebt && item.changeInDelta ? 'fw-bold' : 'text-secondary']"
                                >
                                    {{ formatMoney(item.invoiceDebt) }}
                                </td>
                                <td class="text-end"
                                    :class="[
                                        item.debt && item.changeInDelta ? 'fw-bold' : 'text-secondary',
                                        item.debt < 0 ? 'text-danger' : ''
                                    ]"
                                >
                                    {{ formatMoney(item.debt) }}
                                </td>
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
import CustomSelect         from '@common/form/CustomSelect.vue';
import {
    ApiAdminInvoiceImportPaymentsParseFile,
    ApiAdminInvoiceImportPaymentsSave,
}                           from '@api';

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

const mode  = ref('single'); // 'single' | 'diff'
const files = ref({
    main: null,
    prev: null,
});

const mainFileInput = ref(null);
const prevFileInput = ref(null);

const loading          = ref(false);
const submitting       = ref(false);
const importData       = ref(null);
const editedAmounts    = ref({});
const error            = ref(null);
const activeTab        = ref(0);
const loadingStartTime = ref(null);
const loadingText      = ref('Обработка файла...');
const autoFillStrategy = ref({});

const fillStrategies = [
    { value: 'manual', label: 'Ручной ввод' },
    { value: 'difference', label: 'Разница "оплачено"' },
    { value: 'invoiceDelta', label: 'Остаток из базы' },
    { value: 'importDebt', label: 'Долг из импорта' },
    { value: 'maxDebt', label: 'Максимальный долг' },
    { value: 'minDebt', label: 'Минимальный долг' },
    { value: 'zero', label: 'Обнулить' },
];

const columns = ref({
    accrued: 'D',
    paid   : 'E',
    debt   : 'F',
});

const isColumnsValid = computed(() => {
    return columns.value.accrued.trim() !== '' &&
        columns.value.paid.trim() !== '' &&
        columns.value.debt.trim() !== '';
});

const canUpload = computed(() => {
    if (!isColumnsValid.value) {
        return false;
    }
    if (mode.value === 'single') {
        return !!files.value.main;
    }
    return !!files.value.main && !!files.value.prev;
});

const getKey = (district, item) => `${district}:${item.invoiceId}`;

let loadingInterval = null;

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
        const elapsed     = Math.floor((Date.now() - loadingStartTime.value) / 1000);
        const minutes     = Math.floor(elapsed / 60);
        const seconds     = elapsed % 60;
        const timeStr     = minutes > 0 ? `${minutes} мин ${seconds} сек` : `${seconds} сек`;
        loadingText.value = isSaving
            ? `Сохранение платежей... (${timeStr})`
            : `Обработка файлов... (${timeStr})`;
    }, 1000);
};

const stopTimer = () => {
    if (loadingInterval) {
        clearInterval(loadingInterval);
    }
    loadingStartTime.value = null;
    loadingText.value      = 'Обработка файлов...';
};

const triggerFileInput = (type) => {
    if (type === 'main') {
        mainFileInput.value?.click();
    }
    else {
        prevFileInput.value?.click();
    }
};

const onFileSelected = (type, event) => {
    const file = event.target.files[0];
    if (!file) {
        return;
    }
    files.value[type] = file;
};

const uploadFiles = async () => {
    if (!canUpload.value) {
        return;
    }

    loading.value = true;
    startTimer(false);
    error.value         = null;
    importData.value    = null;
    editedAmounts.value = {};

    const formData = new FormData();
    formData.append('col_accrued', columns.value.accrued);
    formData.append('col_paid', columns.value.paid);
    formData.append('col_debt', columns.value.debt);
    formData.append('mode', mode.value);
    formData.append('file_main', files.value.main);
    if (mode.value === 'diff') {
        formData.append('file_prev', files.value.prev);
    }

    try {
        const response   = await ApiAdminInvoiceImportPaymentsParseFile(props.periodId, {}, formData);
        importData.value = response.data;

        // Инициализация стратегий и значений
        for (const districtData of importData.value) {
            const district = districtData.district;
            if (!autoFillStrategy.value[district]) {
                autoFillStrategy.value[district] = 'difference';
            }
            applyAutoFill(district);
        }
    }
    catch (err) {
        error.value = err.response?.data?.message || 'Ошибка при загрузке файлов';
        parseResponseErrors(err);
    }
    finally {
        loading.value = false;
        stopTimer();
        // Очищаем файлы после загрузки (по желанию)
        files.value = { main: null, prev: null };
        if (mainFileInput.value) {
            mainFileInput.value.value = '';
        }
        if (prevFileInput.value) {
            prevFileInput.value.value = '';
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

    if (!confirm('Сохранить данные? Это действие необратимо')) {
        return;
    }

    submitting.value = true;
    startTimer(true);
    const payload = { payments: [] };

    for (const districtData of importData.value) {
        for (const item of districtData.items) {
            const key    = getKey(districtData.district, item);
            const amount = editedAmounts.value[key];
            if (amount > 0) {
                payload.payments.push({
                    invoice_id: item.invoiceId,
                    amount,
                });
            }
        }
    }

    if (payload.payments.length === 0) {
        showInfo('Нет платежей для сохранения');
        submitting.value = false;
        stopTimer();
        return;
    }

    try {
        await ApiAdminInvoiceImportPaymentsSave(props.periodId, {}, payload);
        showInfo('Платежи будут сохранены в фоне');
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

const applyAutoFill = (district) => {
    const districtData = importData.value?.find(d => d.district === district);
    if (!districtData) {
        return;
    }

    const strategy = autoFillStrategy.value[district] || 'manual';
    districtData.items.forEach(item => {
        const key     = getKey(district, item);
        let newAmount = 0;
        switch (strategy) {
            case 'difference':
                newAmount = Math.max(0, item.paid - item.invoicePaid);
                break;
            case 'importDebt':
                newAmount = item.debt;
                break;
            case 'invoiceDelta':
                newAmount = item.invoiceDelta;
                break;
            case 'maxDebt':
                newAmount = Math.max(item.invoiceDelta, item.debt);
                break;
            case 'minDebt':
                newAmount = Math.min(item.invoiceDelta, item.debt);
                break;
            case 'zero':
                newAmount = 0;
                break;
            default:
                return;
        }
        if (newAmount < 0) {
            newAmount = 0;
        }
        editedAmounts.value[key] = newAmount;
    });
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
    max-height : 800px;
    overflow-y : auto;
}

.period-payments-import-block thead th {
    position         : sticky;
    top              : 0;
    background-color : #f8f9fa;
    z-index          : 10;
}

.period-payments-import-block thead th::after {
    content       : '';
    position      : absolute;
    bottom        : 0;
    left          : 0;
    width         : 100%;
    border-bottom : 2px solid #dee2e6;
}
</style>