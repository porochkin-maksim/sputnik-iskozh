<template>
    <div>
        <h5>Новые платежи</h5>

        <!-- Индикатор загрузки -->
        <loading-spinner
            v-if="isLoading"
            size="md"
            color="primary"
            text="Загрузка платежей..."
            wrapper-class="py-4"
        />

        <payments-list
            v-else
            v-model:selected-id="selectedId"
            v-model:reload="reloadList"
        />

        <view-dialog
            v-model:show="showDialog"
            v-model:hide="hideDialog"
            @hidden="closeAction"
            v-if="payment && (payment.actions.edit || payment.actions.view)"
        >
            <template #title>
                {{ payment.actions.edit ? 'Привязка платёжа' : 'Просмотр платёжа' }}
            </template>

            <template #body>
                <div class="container-fluid">
                    <!-- Режим редактирования -->
                    <template v-if="payment.actions.edit">
                        <!-- Участок -->
                        <div class="mb-2">
                            <search-select
                                v-model="payment.accountId"
                                :items="accounts"
                                label="Участок"
                                @update:modelValue="getInvoices"
                                :disabled="invoicesLoading"
                            />
                        </div>

                        <!-- Период -->
                        <div v-if="periods.length" class="mb-2">
                            <search-select
                                v-model="periodId"
                                :items="periods"
                                label="Период"
                                @update:modelValue="getInvoices"
                                :disabled="invoicesLoading"
                            />
                        </div>

                        <!-- Счёт -->
                        <div v-if="invoices.length" class="mb-2">
                            <search-select
                                v-model="payment.invoiceId"
                                :items="invoices"
                                label="Счёт"
                                :disabled="invoicesLoading"
                            />
                        </div>

                        <!-- Индикатор загрузки счетов -->
                        <loading-spinner
                            v-if="invoicesLoading"
                            size="sm"
                            color="primary"
                            :show-text="false"
                            wrapper-class="py-2"
                        />
                    </template>

                    <!-- Стоимость -->
                    <div class="mb-2">
                        <custom-input
                            v-model="payment.cost"
                            :errors="errors?.cost"
                            label="Стоимость"
                            type="number"
                            step="0.01"
                            :disabled="!payment.actions.edit"
                            @update:modelValue="clearError('cost')"
                        />
                    </div>

                    <!-- Название -->
                    <div class="mb-2">
                        <custom-input
                            v-model="payment.name"
                            :errors="errors?.name"
                            label="Название платежа"
                            type="text"
                            :disabled="!payment.actions.edit"
                            @update:modelValue="clearError('name')"
                        />
                    </div>

                    <!-- Комментарий -->
                    <div class="mb-2">
                        <custom-textarea
                            v-model="payment.comment"
                            :errors="errors?.comment"
                            label="Комментарий"
                            :rows="4"
                            :disabled="!payment.actions.edit"
                            @update:modelValue="clearError('comment')"
                        />
                    </div>

                    <!-- Существующие файлы -->
                    <template v-if="payment.files?.length">
                        <div class="mb-2">
                            <label class="form-label">Прикреплённые файлы</label>
                            <file-item
                                v-for="(file, index) in payment.files"
                                :key="file.id"
                                :file="file"
                                :edit="true"
                                :index="index"
                                :use-up-sort="index !== 0"
                                :use-down-sort="index !== payment.files.length - 1"
                                class="mb-2"
                                @updated="onFileUpdated"
                            />
                        </div>
                    </template>
                </div>
            </template>

            <template #footer>
                <button
                    v-if="payment.actions.edit"
                    class="btn btn-success"
                    :disabled="!canSave || saving"
                    @click="saveAction"
                >
                    <i class="fa" :class="saving ? 'fa-spinner fa-spin' : 'fa-save'"></i>
                    {{ payment.id ? 'Сохранить' : 'Создать' }} платёж
                </button>
            </template>
        </view-dialog>
    </div>
</template>

<script setup>
import {
    ref,
    computed,
    watch,
    defineProps,
    defineEmits,
    defineOptions,
    onMounted,
}                           from 'vue';
import { useResponseError } from '@composables/useResponseError';
import PaymentsList         from './PaymentsList.vue';
import ViewDialog           from '../../common/ViewDialog.vue';
import FileItem             from '../../common/files/FileItem.vue';
import SearchSelect         from '../../common/form/SearchSelect.vue';
import CustomInput          from '../../common/form/CustomInput.vue';
import CustomTextarea       from '@common/form/CustomTextarea.vue';
import LoadingSpinner       from '../../common/LoadingSpinner.vue';
import {
    ApiAdminNewPaymentView,
    ApiAdminNewPaymentSave,
    ApiAdminNewPaymentGetInvoices,
}                           from '@api';

defineOptions({
    name: 'PaymentsBlock',
});

const props = defineProps({
    reload: {
        type   : Boolean,
        default: false,
    },
});

const emit = defineEmits(['update:reload']);

const { errors, clearError, parseResponseErrors, showInfo, showDanger } = useResponseError();

const reloadList      = ref(false);
const payment         = ref(null);
const selectedId      = ref(null);
const accounts        = ref([]);
const invoices        = ref([]);
const periods         = ref([]);
const periodId        = ref(null);
const isLoading       = ref(false);
const invoicesLoading = ref(false);
const saving          = ref(false);
const showDialog      = ref(false);
const hideDialog      = ref(false);

// Получение платежа
const getAction = async () => {
    periodId.value = null;
    accounts.value = [];
    invoices.value = [];
    periods.value  = [];

    try {
        const response        = await ApiAdminNewPaymentView(selectedId.value);
        payment.value         = response.data.payment;
        periodId.value        = response.data.payment.invoice?.periodId;
        accounts.value        = response.data.accounts || [];
        periods.value         = response.data.periods || [];
        payment.value.cost    = parseFloat(payment.value.cost).toFixed(2);
        payment.value.comment = payment.value.comment ? String(payment.value.comment) : null;
        showDialog.value      = true;
    }
    catch (error) {
        parseResponseErrors(error);
    }
};

// Сохранение
const saveAction = async () => {
    saving.value = true;

    const data = {
        id        : payment.value.id,
        name      : payment.value.name || '',
        cost      : parseFloat(payment.value.cost),
        comment   : payment.value.comment || '',
        account_id: payment.value.accountId,
        invoice_id: payment.value.invoiceId,
    };

    try {
        await ApiAdminNewPaymentSave({}, data);
        showInfo('Платёж привязан');
        payment.value = null;
        onSaved();
    }
    catch (error) {
        const message = error?.response?.data?.message || 'Не получилось привязать платёж';
        showDanger(message);
        parseResponseErrors(error);
    }
    finally {
        saving.value     = false;
        selectedId.value = null;
    }
};

// Закрытие
const closeAction = () => {
    payment.value    = null;
    selectedId.value = null;
    periodId.value   = null;
    showDialog.value = false;
};

// После сохранения
const onSaved = () => {
    closeAction();
    reloadList.value = true;
    emit('update:reload', true);
};

// Получение счетов с автоматическим сбросом выбранного счёта
const getInvoices = async () => {
    // Если не хватает данных, сбрасываем счёт
    if (!periodId.value || !payment.value?.accountId) {
        if (payment.value) {
            payment.value.invoiceId = null;
        }
        invoices.value = [];
        return;
    }

    invoicesLoading.value = true;
    // Сбрасываем выбранный счёт перед загрузкой нового списка
    if (payment.value) {
        payment.value.invoiceId = null;
    }
    invoices.value = [];

    try {
        const response = await ApiAdminNewPaymentGetInvoices(
            payment.value.accountId,
            periodId.value,
        );
        invoices.value = response.data.invoices || [];
    }
    catch (error) {
        parseResponseErrors(error);
    }
    finally {
        invoicesLoading.value = false;
    }
};

// Обновление файла
const onFileUpdated = () => {
    getAction();
};

// Возможность сохранения
const canSave = computed(() => {
    return payment.value &&
        payment.value.cost > 0 &&
        payment.value.invoiceId; // обязателен выбранный счёт
});

// Следим за перезагрузкой
watch(() => props.reload, (value) => {
    if (value) {
        reloadList.value = true;
    }
});

// Следим за выбранным ID
watch(selectedId, (value) => {
    if (value) {
        getAction();
    }
    else {
        payment.value = null;
    }
});

// Следим за скрытием диалога
watch(hideDialog, () => {
    closeAction();
});

// Следим за перезагрузкой списка
watch(reloadList, (value) => {
    emit('update:reload', value);
});

onMounted(() => {
    // Инициализация при необходимости
});
</script>