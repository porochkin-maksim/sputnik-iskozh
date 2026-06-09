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
            <table class="table table-sm table-bordered admin-table-firm">
                <thead>
                <tr>
                    <th class="text-center table-thin-column">№</th>
                    <th class="text-center">Название</th>
                    <th class="text-center">Сумма</th>
                    <th class="text-center table-thin-column">Файлы</th>
                    <th class="text-center">Оплачен</th>
                    <th class="text-center">Создан</th>
                    <th class="text-center table-thin-column">Действия</th>
                </tr>
                </thead>
                <tbody>
                <tr v-for="(payment, index) in payments" :key="payment.id">
                    <td class="table-thin-column text-center">
                        <span class="link-firm"
                              @click="editAction(payment.id)">
                            {{ payment.id }}
                        </span>
                    </td>
                    <td>{{ payment.name }}</td>
                    <td class="text-end">{{ formatMoney(payment.cost) }}</td>
                    <td class="table-thin-column">
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
                    <td class="table-thin-column text-center">{{ payment.paid }}</td>
                    <td class="table-thin-column text-center">{{ payment.created }}</td>
                    <td class="table-thin-column text-center">
                        <div class="d-flex justify-content-center gap-1 flex-nowrap">
                            <history-btn
                                v-if="payment.historyUrl"
                                class="btn-link underline-none p-0"
                                :url="payment.historyUrl"
                                aria-label="История изменений"
                            />

                            <button
                                v-if="canDrop"
                                class="btn btn-sm btn-outline-danger admin-action-btn"
                                type="button"
                                :disabled="dropLoading === payment.id"
                                :aria-label="'Удалить платёж ' + payment.id"
                                @click="dropAction(payment.id)"
                            >
                                <i
                                    v-if="dropLoading === payment.id"
                                    class="fa fa-spinner fa-spin"
                                    aria-hidden="true"
                                ></i>
                                <i v-else class="fa fa-trash" aria-hidden="true"></i>
                            </button>
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
    computed,
}                           from 'vue';
import { useResponseError } from '@composables/useResponseError';
import { useFormat }        from '@composables/useFormat';
import { usePermissions }   from '@composables/usePermissions.js';
import HistoryBtn           from '@common/HistoryBtn.vue';
import FileItem             from '@common/files/FileItem.vue';
import LoadingSpinner       from '@common/LoadingSpinner.vue';
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
const { has }                                       = usePermissions();

const canEdit = computed(() => has('payments', 'edit'));
const canDrop = computed(() => has('payments', 'drop'));

const payments    = ref([]);
const vueId       = ref('list-' + Date.now() + '-' + Math.random().toString(36).substring(2, 9));
const isLoading   = ref(false);
const dropLoading = ref(null);

// Загрузка списка
const loadList = async () => {
    isLoading.value = true;
    try {
        const response = await ApiAdminPaymentList(props.invoiceId);
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
