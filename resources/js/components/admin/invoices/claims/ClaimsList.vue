<template>
    <div>
        <!-- Индикатор загрузки -->
        <loading-spinner
            v-if="isLoading"
            size="lg"
            color="primary"
            text="Загрузка услуг..."
            wrapper-class="py-5"
        />

        <template v-else>
            <table class="table table-sm table-bordered admin-table-firm">
                <thead>
                <tr>
                    <th class="text-center table-thin-column">№</th>
                    <th class="text-center">Услуга</th>
                    <th class="text-center">Тариф</th>
                    <th class="text-center">Стоимость</th>
                    <th class="text-center">Оплачено</th>
                    <th class="text-center">Долг</th>
                    <th class="text-center">Создана</th>
                    <th class="text-center table-thin-column">Действия</th>
                </tr>
                </thead>
                <tbody>
                <claim-row
                    v-for="claim in claims"
                    :key="claim.id"
                    :can-edit="canEdit"
                    :can-drop="canDrop"
                    :claim="claim"
                    :drop-loading="dropLoading"
                    :format-money="formatMoney"
                    @drop="dropAction"
                    @edit="editAction"
                />
                <tr v-if="!claims.length">
                    <td colspan="8" class="text-center py-3 text-muted">
                        <i class="fa fa-info-circle me-2" aria-hidden="true"></i>
                        Услуги не найдены
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
import LoadingSpinner       from '@common/LoadingSpinner.vue';
import {
    ApiAdminClaimList,
    ApiAdminClaimDelete,
}                           from '@api';
import { usePermissions }   from '@composables/usePermissions.js';
import ClaimRow             from './ClaimRow.vue';

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

const canEdit = computed(() => has('invoice_services', 'edit'));
const canDrop = computed(() => has('invoice_services', 'drop'));

const claims      = ref([]);
const actions     = ref({});
const isLoading   = ref(false);
const dropLoading = ref(null); // ID удаляемой услуги

// Проверка наличия действий у claim
// Загрузка списка
const loadList = async () => {
    isLoading.value = true;
    try {
        const response = await ApiAdminClaimList(props.invoiceId);
        claims.value   = response.data.claims || [];
        actions.value  = response.data.actions || {};

        emit('update:count', claims.value.length);
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
    if (!confirm('Удалить услугу?')) {
        return;
    }

    dropLoading.value = id;
    try {
        const response = await ApiAdminClaimDelete(props.invoiceId, id);

        if (response.data) {
            await loadList();
            showInfo('Услуга удалена');
        }
        else {
            showDanger('Услуга не удалена');
        }
    }
    catch (error) {
        parseResponseErrors(error);
    }
    finally {
        dropLoading.value = null;
    }
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
