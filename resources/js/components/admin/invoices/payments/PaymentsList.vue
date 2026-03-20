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
            <table class="table table-sm table-bordered">
                <thead>
                <tr>
                    <th class="text-center">№</th>
                    <th class="text-center">Название</th>
                    <th class="text-center">Сумма</th>
                    <th class="text-center">Файлы</th>
                    <th class="text-center">Оплачен</th>
                    <th class="text-center">Создан</th>
                    <th class="text-center">Действия</th>
                </tr>
                </thead>
                <tbody>
                <tr v-for="(payment, index) in payments" :key="payment.id">
                    <td class="text-end">{{ payment.id }}</td>
                    <td>{{ payment.name }}</td>
                    <td class="text-end">{{ formatMoney(payment.cost) }}</td>
                    <td>
                        <div v-if="payment.files?.length" class="d-flex flex-column gap-1">
                            <file-item
                                v-for="(file, fileIndex) in payment.files"
                                :key="file.id"
                                :file="file"
                                :edit="true"
                                :index="fileIndex"
                                :use-up-sort="fileIndex !== 0"
                                :use-down-sort="fileIndex !== payment.files.length - 1"
                                @updated="onFileUpdated"
                            />
                        </div>
                        <span v-else class="text-muted small">—</span>
                    </td>
                    <td class="text-center">{{ payment.payed }}</td>
                    <td class="text-center">{{ payment.created }}</td>
                    <td class="text-center">
                        <div class="d-flex justify-content-center gap-1">
                            <!-- Кнопка истории -->
                            <history-btn
                                v-if="payment.historyUrl"
                                class="btn-link underline-none p-0"
                                :url="payment.historyUrl"
                                aria-label="История изменений"
                            />

                            <!-- Дропдаун с действиями -->
                            <div v-if="hasActions(payment)" class="dropdown">
                                <button
                                    class="btn btn-sm btn-light border"
                                    type="button"
                                    :id="'dropDown' + index + vueId"
                                    data-bs-toggle="dropdown"
                                    aria-expanded="false"
                                    :disabled="dropLoading === payment.id"
                                    :aria-label="'Действия для платежа ' + payment.id"
                                >
                                    <i v-if="dropLoading === payment.id"
                                       class="fa fa-spinner fa-spin"
                                       aria-hidden="true"></i>
                                    <i v-else class="fa fa-bars" aria-hidden="true"></i>
                                </button>
                                <ul
                                    class="dropdown-menu"
                                    :aria-labelledby="'dropDown' + index + vueId"
                                >
                                    <li v-if="payment.actions.edit">
                                        <a
                                            class="dropdown-item cursor-pointer"
                                            @click="editAction(payment.id)"
                                            role="menuitem"
                                        >
                                            <i class="fa fa-edit" aria-hidden="true"></i>
                                            Редактировать
                                        </a>
                                    </li>
                                    <li v-else-if="payment.actions.view">
                                        <a
                                            class="dropdown-item cursor-pointer"
                                            @click="editAction(payment.id)"
                                            role="menuitem"
                                        >
                                            <i class="fa fa-eye" aria-hidden="true"></i>
                                            Просмотр
                                        </a>
                                    </li>
                                    <li v-if="payment.actions.drop">
                                        <a
                                            class="dropdown-item cursor-pointer text-danger"
                                            @click="dropAction(payment.id)"
                                            role="menuitem"
                                        >
                                            <i class="fa fa-trash" aria-hidden="true"></i>
                                            Удалить
                                        </a>
                                    </li>
                                </ul>
                            </div>
                        </div>
                    </td>
                </tr>
                <tr v-if="!payments.length">
                    <td colspan="7" class="text-center py-3 text-muted">
                        <i class="fa fa-info-circle me-2" aria-hidden="true"></i>
                        Платежи не найдены
                    </td>
                </tr>
                </tbody>
            </table>
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
import HistoryBtn           from '../../../common/HistoryBtn.vue';
import FileItem             from '../../../common/files/FileItem.vue';
import LoadingSpinner       from '../../../common/LoadingSpinner.vue';
import {
    ApiAdminPaymentList,
    ApiAdminPaymentDelete,
}                           from '@api';

const props = defineProps({
    invoiceId : {
        type    : Number,
        required: true,
    },
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
const { formatMoney }                               = useFormat();

const payments    = ref([]);
const actions     = ref({});
const vueId       = ref('list-' + Date.now() + '-' + Math.random().toString(36).substring(2, 9));
const isLoading   = ref(false);
const dropLoading = ref(null);

// Проверка наличия действий у платежа
const hasActions = (payment) => {
    return payment.actions?.edit || payment.actions?.view || payment.actions?.drop;
};

// Загрузка списка
const loadList = async () => {
    isLoading.value = true;
    try {
        const response = await ApiAdminPaymentList(props.invoiceId);
        payments.value = response.data.payments || [];
        actions.value  = response.data.actions || {};

        emit('update:count', payments.value.length);
    }
    catch (error) {
        parseResponseErrors(error);
    }
    finally {
        isLoading.value = false;
    }
};

// Редактирование/просмотр
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
        const response = await ApiAdminPaymentDelete(props.invoiceId, id);

        if (response.data) {
            await loadList();
            showInfo('Платёж удалён');
        }
        else {
            showDanger('Платеж не удалён');
        }
    }
    catch (error) {
        parseResponseErrors(error);
    }
    finally {
        dropLoading.value = null;
    }
};

// Обновление файла
const onFileUpdated = () => {
    loadList();
};

// Следим за изменением флага перезагрузки
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