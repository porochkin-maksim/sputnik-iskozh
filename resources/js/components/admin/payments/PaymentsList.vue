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
                <table class="table table-bordered align-middle admin-table-firm">
                    <thead>
                    <tr class="text-center">
                    <th class="table-thin-column">№</th>
                        <th>Участок</th>
                        <th>Сумма</th>
                    <th class="table-thin-column">Создан</th>
                    <th class="table-thin-column">Файл</th>
                    <th class="table-thin-column">Действия</th>
                    </tr>
                    </thead>
                    <tbody>
                    <payments-row
                        v-for="payment in payments"
                        :key="payment.id"
                        :action-loading="actionLoading"
                        :can-drop="canDrop"
                        :can-edit="canEdit"
                        :drop-loading="dropLoading"
                        :format-money="formatMoney"
                        :payment="payment"
                        @drop="dropAction"
                        @edit="editAction"
                        @file-updated="onFileUpdated"
                    />
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
    computed,
    watch,
    onMounted,
    defineProps,
    defineEmits,
}                           from 'vue';
import { useResponseError } from '@composables/useResponseError';
import { useFormat }        from '@composables/useFormat';
import { usePermissions }   from '@composables/usePermissions.js';
import LoadingSpinner       from '@common/LoadingSpinner.vue';
import {
    ApiAdminNewPaymentList,
    ApiAdminNewPaymentDelete,
}                           from '@api';
import PaymentsRow          from './payments-block/PaymentsRow.vue';

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
const { formatMoney }                               = useFormat();
const { has }                                       = usePermissions();

const payments      = ref([]);
const isLoading     = ref(false);
const actionLoading = ref(false);
const dropLoading   = ref(null);
const canEdit       = computed(() => has('payments', 'edit'));
const canDrop       = computed(() => has('payments', 'drop'));

// Загрузка списка
const loadList = async () => {
    isLoading.value = true;
    try {
        const response = await ApiAdminNewPaymentList();
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
