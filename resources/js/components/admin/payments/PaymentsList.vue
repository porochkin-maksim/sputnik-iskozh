<template>
    <div>
        <!-- Индикатор загрузки -->
        <loading-spinner
            v-if="isLoading"
            size="lg"
            color="primary"
            text="Загрузка платежей..."
            wrapper-class="py-5"
        />

        <template v-else>
            <div v-if="payments.length">
                <table class="table table-bordered align-middle ">
                    <thead>
                    <tr class="text-center">
                        <th>№</th>
                        <th>Участок</th>
                        <th>Сумма</th>
                        <th>Создан</th>
                        <th>Файл</th>
                        <th>Действия</th>
                    </tr>
                    </thead>
                    <tbody>
                    <tr v-for="payment in payments" :key="payment.id">
                        <td class="text-center">{{ payment.id }}</td>
                        <td class="text-end">{{ payment.accountNumber }}</td>
                        <td class="text-end">{{ formatMoney(payment.cost) }}</td>
                        <td class="text-center">{{ payment.created }}</td>
                        <td>
                            <div v-if="payment.files?.length" class="d-flex flex-column gap-1">
                                <file-item
                                    v-for="(file, index) in payment.files"
                                    :key="file.id"
                                    :file="file"
                                    :edit="true"
                                    :index="index"
                                    :use-up-sort="index !== 0"
                                    :use-down-sort="index !== payment.files.length - 1"
                                    @updated="onFileUpdated"
                                />
                            </div>
                            <span v-else class="text-muted small">—</span>
                        </td>
                        <td>
                            <div class="d-flex justify-content-center gap-2">
                                <!-- Кнопка привязки -->
                                <button
                                    v-if="actions.edit"
                                    class="btn btn-sm btn-link p-0"
                                    @click="editAction(payment.id)"
                                    :disabled="actionLoading"
                                    title="Привязать"
                                >
                                    <i class="fa fa-link text-primary"></i>
                                </button>

                                <!-- Кнопка истории -->
                                <history-btn
                                    v-if="payment.historyUrl"
                                    class="btn-link underline-none p-0"
                                    :url="payment.historyUrl"
                                    aria-label="История изменений"
                                />

                                <!-- Кнопка удаления -->
                                <button
                                    v-if="actions.drop"
                                    class="btn btn-sm btn-link p-0 text-danger"
                                    @click="dropAction(payment.id)"
                                    :disabled="dropLoading === payment.id"
                                    title="Удалить"
                                >
                                    <i v-if="dropLoading === payment.id" class="fa fa-spinner fa-spin"></i>
                                    <i v-else class="fa fa-trash"></i>
                                </button>
                            </div>
                        </td>
                    </tr>
                    </tbody>
                </table>
            </div>

            <!-- Пустой список -->
            <div v-else class="alert alert-info text-center my-3">
                <i class="fa fa-info-circle me-2" aria-hidden="true"></i>
                Платежи не найдены
            </div>
        </template>
    </div>
</template>

<script setup>
import {
    ref,
    watch,
    onMounted,
    defineProps,
    defineEmits,
}                           from 'vue';
import { useResponseError } from '@composables/useResponseError';
import { useFormat }        from '@composables/useFormat';
import HistoryBtn           from '../../common/HistoryBtn.vue';
import FileItem             from '../../common/files/FileItem.vue';
import LoadingSpinner       from '../../common/LoadingSpinner.vue';
import {
    ApiAdminNewPaymentList,
    ApiAdminNewPaymentDelete,
}                           from '@api';

const props = defineProps({
    selectedId: {
        type   : Number,
        default: null,
    },
    reload    : {
        type   : Boolean,
        default: false,
    },
    count     : {
        type   : Number,
        default: 0,
    },
});

const emit = defineEmits(['update:reload', 'update:selectedId', 'update:count']);

const { parseResponseErrors, showInfo, showDanger } = useResponseError();
const { formatMoney, formatDate }                   = useFormat();

const payments      = ref([]);
const actions       = ref({});
const isLoading     = ref(false);
const actionLoading = ref(false);
const dropLoading   = ref(null);

// Загрузка списка
const loadList = async () => {
    isLoading.value = true;
    try {
        const response = await ApiAdminNewPaymentList();
        actions.value  = response.data.actions || {};
        payments.value = response.data.payments || [];
        emit('update:count', payments.value.length);
    }
    catch (error) {
        parseResponseErrors(error);
    }
    finally {
        isLoading.value = false;
    }
};

// Редактирование/привязка
const editAction = (id) => {
    emit('update:selectedId', id);
};

// Удаление
const dropAction = async (id) => {
    if (!confirm('Удалить платёж?')) {
        return;
    }

    dropLoading.value = id;
    try {
        const response = await ApiAdminNewPaymentDelete(id);
        if (response.data) {
            await loadList();
            showInfo('Платёж удалён');
        }
        else {
            showDanger('Платёж не удалён');
        }
    }
    catch (error) {
        parseResponseErrors(error);
    }
    finally {
        dropLoading.value = null;
    }
};

// Обновление после изменения файлов
const onFileUpdated = () => {
    loadList();
};

// Следим за флагом перезагрузки
watch(() => props.reload, (value) => {
    if (value) {
        loadList();
        emit('update:reload', false);
    }
});

onMounted(() => {
    loadList();
});
</script>