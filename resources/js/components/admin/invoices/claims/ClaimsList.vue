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
            <table class="table table-sm table-bordered">
                <thead>
                <tr>
                    <th class="text-center">№</th>
                    <th class="text-center">Услуга</th>
                    <th class="text-center">Тариф</th>
                    <th class="text-center">Стоимость</th>
                    <th class="text-center">Оплачено</th>
                    <th class="text-center">Долг</th>
                    <th class="text-center">Создана</th>
                    <th class="text-center">Действия</th>
                </tr>
                </thead>
                <tbody>
                <tr v-for="(claim, index) in claims"
                    :key="claim.id"
                    :class="parseFloat(claim.delta) !== 0 ? 'table-warning' : ''"
                >
                    <td class="text-end">{{ claim.id }}</td>
                    <td>{{ claim.service }}</td>
                    <td class="text-end">{{ formatMoney(claim.tariff) }}</td>
                    <td class="text-end">{{ formatMoney(claim.cost) }}</td>
                    <td class="text-end">{{ formatMoney(claim.paid) }}</td>
                    <td class="text-end">{{ formatMoney(claim.delta) }}</td>
                    <td class="text-center">{{ claim.created }}</td>
                    <td class="text-center">
                        <div class="d-flex justify-content-center gap-1">
                            <!-- Кнопка истории -->
                            <history-btn v-if="claim.historyUrl"
                                         class="btn-link underline-none p-0"
                                         :url="claim.historyUrl"
                                         aria-label="История изменений" />

                            <!-- Дропдаун с действиями -->
                            <div v-if="hasActions(claim)" class="dropdown">
                                <button class="btn btn-sm btn-light border"
                                        type="button"
                                        :id="'dropDown' + index + vueId"
                                        data-bs-toggle="dropdown"
                                        aria-expanded="false"
                                        :disabled="dropLoading === claim.id"
                                        :aria-label="'Действия для услуги ' + claim.id">
                                    <i v-if="dropLoading === claim.id"
                                       class="fa fa-spinner fa-spin"
                                       aria-hidden="true"></i>
                                    <i v-else class="fa fa-bars" aria-hidden="true"></i>
                                </button>
                                <ul class="dropdown-menu"
                                    :aria-labelledby="'dropDown' + index + vueId">
                                    <li v-if="claim.actions.edit">
                                        <a class="dropdown-item cursor-pointer"
                                           @click="editAction(claim.id)">
                                            <i class="fa fa-edit" aria-hidden="true"></i>
                                            Редактировать
                                        </a>
                                    </li>
                                    <li v-else-if="claim.actions.view">
                                        <a class="dropdown-item cursor-pointer"
                                           @click="editAction(claim.id)">
                                            <i class="fa fa-eye" aria-hidden="true"></i>
                                            Просмотр
                                        </a>
                                    </li>
                                    <li v-if="claim.actions.drop">
                                        <a class="dropdown-item cursor-pointer text-danger"
                                           @click="dropAction(claim.id)">
                                            <i class="fa fa-trash" aria-hidden="true"></i>
                                            Удалить
                                        </a>
                                    </li>
                                </ul>
                            </div>
                        </div>
                    </td>
                </tr>
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
    defineOptions,
}                           from 'vue';
import { useResponseError } from '@composables/useResponseError';
import { useFormat }        from '@composables/useFormat';
import HistoryBtn           from '../../../common/HistoryBtn.vue';
import LoadingSpinner       from '../../../common/LoadingSpinner.vue';
import {
    ApiAdminClaimList,
    ApiAdminClaimDelete,
}                           from '@api';

defineOptions({
    name: 'ClaimsList',
});

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

const claims      = ref([]);
const actions     = ref({});
const vueId       = ref('list-' + Date.now() + '-' + Math.random().toString(36).substring(2, 9));
const isLoading   = ref(false);
const dropLoading = ref(null); // ID удаляемой услуги

// Проверка наличия действий у claim
const hasActions = (claim) => {
    return claim.actions?.edit || claim.actions?.view || claim.actions?.drop;
};

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