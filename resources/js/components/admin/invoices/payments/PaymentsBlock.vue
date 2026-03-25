<template>
    <div>
        <h5>Платежи</h5>

        <div class="d-flex mb-2">
            <button
                class="btn btn-success"
                v-if="invoice.actions.payments.edit"
                :disabled="loading"
                @click="makeAction"
            >
                <i class="fa fa-plus" aria-hidden="true"></i>
                Добавить платёж
            </button>

            <button
                class="btn btn-outline-success ms-2"
                v-if="invoice.actions.payments.edit && !invoice.isPaid && !forcePaid"
                :disabled="loading"
                @click="makePaid"
            >
                <i class="fa fa-credit-card" aria-hidden="true"></i>
                Оплатить всё
            </button>
        </div>

        <payments-list
            :invoice-id="invoice.id"
            v-model:selected-id="selectedId"
            v-model:reload="reloadList"
            v-model:count="paymentsCount"
            @update:count="onUpdatedCount"
        />

        <view-dialog
            v-model:show="showDialog"
            v-model:hide="hideDialog"
            @hidden="closeAction"
            v-if="payment && (payment.actions.edit || payment.actions.view)"
        >
            <template #title>
                {{ payment.id ? (payment.actions.edit ? 'Редактирование платёжа' : 'Просмотр платёжа') : 'Добавление платежа' }}
            </template>

            <template #body>
                <!-- Название платежа -->
                <div class="mb-3">
                    <custom-input
                        v-model="payment.name"
                        :errors="errors?.name"
                        label="Название платежа"
                        type="text"
                        :disabled="!payment.actions.edit || loading"
                        @update:modelValue="clearError('name')"
                    />
                </div>

                <!-- Стоимость и дата -->
                <div class="row mb-3">
                    <div class="col-6">
                        <custom-input
                            v-model="payment.cost"
                            :errors="errors?.cost"
                            label="Стоимость"
                            type="number"
                            step="0.01"
                            :disabled="!payment.actions.edit || loading"
                            @update:modelValue="clearError('cost')"
                        />
                    </div>
                    <div class="col-6">
                        <custom-calendar
                            v-model="payment.paid"
                            :error="errors?.paid"
                            label="Дата платежа"
                            :disabled="!payment.actions.edit || loading"
                            @update:modelValue="clearError('paid')"
                        />
                    </div>
                </div>

                <!-- Комментарий -->
                <div class="mb-3">
                    <custom-textarea
                        v-model="payment.comment"
                        :errors="errors?.comment"
                        label="Комментарий"
                        :disabled="!payment.actions.edit || loading"
                        :rows="4"
                        @update:modelValue="clearError('comment')"
                    />
                </div>

                <!-- Существующие файлы -->
                <template v-if="payment.files?.length">
                    <div class="mb-3">
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

                <!-- Новые файлы -->
                <template v-if="files.length">
                    <div class="mb-3">
                        <label class="form-label">Новые файлы</label>
                        <ul class="list-unstyled">
                            <li v-for="(file, index) in files"
                                :key="index"
                                class="mb-2 d-flex justify-content-between align-items-center p-2 border rounded"
                            >
                                <div>
                                    <button
                                        class="btn btn-sm btn-danger me-2"
                                        @click="removeFile(index)"
                                        type="button"
                                    >
                                        <i class="fa fa-trash" aria-hidden="true"></i>
                                    </button>
                                    {{ index + 1 }}. {{ file.name }}
                                </div>
                                <span class="text-secondary small">
                                    {{ (file.size / (1024 * 1024)).toFixed(2) }} MB
                                </span>
                            </li>
                        </ul>
                        <div class="d-flex justify-content-end small">
                            <span :class="fileSizeExceed ? 'text-danger' : 'text-secondary'">
                                Общий размер: {{ filesSize }} MB
                            </span>
                        </div>
                    </div>
                </template>

                <!-- Кнопка добавления файлов -->
                <button
                    v-if="!fileCountExceed"
                    class="btn btn-outline-secondary w-100"
                    @click="chooseFiles"
                    :disabled="loading"
                    type="button"
                >
                    <i class="fa fa-paperclip me-2" aria-hidden="true"></i>
                    Добавить файлы
                </button>

                <input
                    ref="fileElem"
                    type="file"
                    class="d-none"
                    accept="image/*,application/pdf"
                    @change="appendFiles"
                    multiple
                />
            </template>

            <template #footer v-if="payment.actions.edit">
                <div class="d-flex justify-content-end w-100">
                    <button
                        class="btn btn-success"
                        :disabled="!canSave || loading"
                        @click="saveAction"
                    >
                        <i class="fa" :class="loading ? 'fa-spinner fa-spin' : 'fa-save'"></i>
                        {{ payment.id ? 'Сохранить' : 'Создать' }}
                    </button>
                </div>
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
    onMounted,
}                           from 'vue';
import { useResponseError } from '@composables/useResponseError';
import { useFormat }        from '@composables/useFormat';
import PaymentsList         from './PaymentsList.vue';
import ViewDialog           from '../../../common/ViewDialog.vue';
import FileItem             from '../../../common/files/FileItem.vue';
import CustomInput          from '../../../common/form/CustomInput.vue';
import CustomCalendar       from '../../../common/form/CustomCalendar.vue';
import {
    ApiAdminPaymentAutoCreate,
    ApiAdminPaymentCreate,
    ApiAdminPaymentView,
    ApiAdminPaymentSave,
}                           from '@api';
import CustomTextarea       from '@common/form/CustomTextarea.vue';

const props = defineProps({
    invoice: {
        type    : Object,
        required: true,
    },
    reload : {
        type   : Boolean,
        default: false,
    },
    count  : {
        type   : Number,
        default: 0,
    },
});

const emit = defineEmits(['update:reload', 'update:count']);

const { errors, clearError, parseResponseErrors, showInfo, showDanger } = useResponseError();
const { formatMoney }                                                   = useFormat();

const paymentsCount = ref(0);
const reloadList    = ref(false);
const payment       = ref(null);
const selectedId    = ref(null);
const files         = ref([]);
const loading       = ref(false);
const showDialog    = ref(false);
const hideDialog    = ref(false);
const forcePaid     = ref(false);
const fileElem      = ref(null);

// Вычисляемые свойства
const canSave = computed(() => {
    return payment.value && payment.value.cost >= 0;
});

const filesSize = computed(() => {
    const total = files.value.reduce((acc, file) => acc + file.size, 0);
    return (total / (1024 * 1024)).toFixed(2);
});

const fileSizeExceed = computed(() => {
    return parseFloat(filesSize.value) > 20;
});

const fileCountExceed = computed(() => {
    return files.value.length > 4;
});

// Инициализация
const init = () => {
    paymentsCount.value = props.count || 0;
};

// Оплатить всё
const makePaid = async () => {
    if (!confirm('Создать платежи для каждой неоплаченной услуги?')) {
        return;
    }

    loading.value = true;
    try {
        const response  = await ApiAdminPaymentAutoCreate(props.invoice.id);
        forcePaid.value = true;
        showInfo('Счёт оплачен');
        onSaved();
    }
    catch (error) {
        parseResponseErrors(error);
    }
    finally {
        loading.value = false;
    }
};

// Добавить платёж
const makeAction = async () => {
    try {
        const response   = await ApiAdminPaymentCreate(props.invoice.id);
        payment.value    = response.data.payment;
        showDialog.value = true;
    }
    catch (error) {
        parseResponseErrors(error);
    }
};

// Получить платёж
const getAction = async () => {
    try {
        const response        = await ApiAdminPaymentView(props.invoice.id, selectedId.value);
        payment.value         = response.data.payment;
        payment.value.cost    = parseFloat(payment.value.cost).toFixed(2);
        payment.value.comment = payment.value.comment ? String(payment.value.comment) : null;
        showDialog.value      = true;
    }
    catch (error) {
        parseResponseErrors(error);
    }
};

// Сохранить платёж
const saveAction = async () => {
    loading.value = true;

    const formData = new FormData();
    formData.append('id', payment.value.id);
    formData.append('cost', parseFloat(payment.value.cost));
    formData.append('name', payment.value.name || '');
    formData.append('comment', payment.value.comment ? String(payment.value.comment) : '');
    formData.append('paidAt', payment.value.paid);

    files.value.forEach((file, index) => {
        formData.append(`file${index}`, file);
    });

    try {
        const response = await ApiAdminPaymentSave(props.invoice.id, {}, formData);

        const message = payment.value.id ? 'Платёж обновлён' : `Платёж ${response.data.payment.id} создан`;
        showInfo(message);

        payment.value = null;
        onSaved();
        showDialog.value = false;
    }
    catch (error) {
        const message = error?.response?.data?.message ||
            `Не удалось ${payment.value.id ? 'сохранить' : 'создать'} платёж`;
        showDanger(message);
        parseResponseErrors(error);
    }
    finally {
        loading.value    = false;
        selectedId.value = null;
        files.value      = [];
    }
};

// Закрыть диалог
const closeAction = () => {
    payment.value    = null;
    selectedId.value = null;
    files.value      = [];
};

// После сохранения
const onSaved = () => {
    reloadList.value = true;
    emit('update:reload', true);
};

// Выбрать файлы
const chooseFiles = () => {
    fileElem.value?.click();
};

// Добавить файлы
const appendFiles = (event) => {
    const newFiles = Array.from(event.target.files);
    for (const file of newFiles) {
        if (!fileCountExceed.value) {
            files.value.push(file);
        }
    }
    // Очищаем input, чтобы можно было выбрать те же файлы снова
    event.target.value = '';
};

// Удалить файл
const removeFile = (index) => {
    files.value = files.value.filter((_, i) => i !== index);
};

// Обновление количества
const onUpdatedCount = (value) => {
    paymentsCount.value = value;
    emit('update:count', value);
};

// Обновление файла
const onFileUpdated = () => {
    // Можно перезагрузить список или обновить данные
    // Пока просто вызываем getAction для обновления
    if (selectedId.value) {
        getAction();
    }
};

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

// Следим за количеством
watch(paymentsCount, (value) => {
    emit('update:count', value);
});

onMounted(init);
</script>