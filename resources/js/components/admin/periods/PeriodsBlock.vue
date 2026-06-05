<template>
    <div class="periods-block">
        <!-- Заголовок и кнопка добавления -->
        <div class="d-flex align-items-center justify-content-between mb-2">
            <div>
                <button
                    v-if="canCreate"
                    class="btn btn-success"
                    @click="showCreateDialog"
                >
                    <i class="fa fa-plus" aria-hidden="true"></i>&nbsp;
                    <span>Добавить период</span>
                </button>
            </div>
            <history-btn
                class="btn-link underline-none"
                :url="historyUrl"
            />
        </div>

        <!-- Индикатор загрузки -->
        <loading-spinner
            v-if="isLoading"
            size="lg"
            color="primary"
            text="Загрузка периодов..."
            wrapper-class="py-5"
        />

        <!-- Таблица с периодами -->
        <template v-else>
            <div v-if="periods.length">
                <table class="table table-sm table-hover align-middle admin-table-firm">
                    <caption class="visually-hidden">Список периодов</caption>
                    <thead>
                    <tr>
                        <th scope="col" class="table-thin-column">ID</th>
                        <th scope="col">Название</th>
                        <th scope="col">Начало</th>
                        <th scope="col">Окончание</th>
                        <th scope="col" class="text-center">Статус</th>
                        <th scope="col" class="text-center table-thin-column">Действия</th>
                    </tr>
                    </thead>
                    <tbody>
                    <tr v-for="period in periods" :key="period.id">
                        <td class="table-thin-column text-center">{{ period.id }}</td>
                        <td class="text-nowrap">{{ period.name }}</td>
                        <td>{{ formatDate(period.startAt) }}</td>
                        <td class="text-nowrap">{{ formatDate(period.endAt) }}</td>
                        <td class="text-center">
                            <span
                                v-if="period.isClosed"
                                class="badge bg-primary"
                            >
                                <i class="fa fa-check" aria-hidden="true"></i>
                                Закрыт
                            </span>
                            <span v-else class="badge bg-success">
                                <i class="fa fa-clock-o" aria-hidden="true"></i>
                                Активен
                            </span>
                        </td>
                        <td class="table-thin-column">
                            <div class="d-flex justify-content-end gap-1 flex-nowrap">
                                <button
                                    v-if="canEdit && !period.isClosed"
                                    class="btn btn-sm btn-outline-success admin-action-btn"
                                    type="button"
                                    @click="showEditDialog(period)"
                                >
                                    <i class="fa fa-edit" aria-hidden="true"></i>
                                </button>
                                <button
                                    v-if="canDrop && !period.isClosed"
                                    class="btn btn-sm btn-outline-danger admin-action-btn"
                                    type="button"
                                    @click="deleteAction(period)"
                                >
                                    <i class="fa fa-trash" aria-hidden="true"></i>
                                </button>
                                <a
                                    v-if="period.receiptUrl"
                                    :href="period.receiptUrl"
                                    target="_blank"
                                    class="btn btn-sm admin-action-btn"
                                >
                                    <i class="fa fa-file-pdf-o text-danger"></i>
                                </a>
                                <history-btn
                                    class="btn-link underline-none"
                                    :url="period.historyUrl"
                                />
                            </div>
                        </td>
                    </tr>
                    </tbody>
                </table>
            </div>

            <!-- Сообщение при пустом списке -->
            <div v-else class="alert alert-info text-center">
                Нет доступных периодов.
            </div>
        </template>

        <!-- Диалог редактирования -->
        <period-edit-dialog
            v-model:model-value="selectedPeriod"
            v-model:show="showDialog"
            @update:model-value="onPeriodUpdated"
        />
    </div>
</template>

<script setup>
import {
    ref,
    computed,
    onMounted,
}                           from 'vue';
import { useResponseError } from '@composables/useResponseError';
import { usePermissions }   from '@composables/usePermissions.js';
import HistoryBtn           from '@common/HistoryBtn.vue';
import LoadingSpinner       from '@common/LoadingSpinner.vue';
import {
    ApiAdminPeriodList,
    ApiAdminPeriodCreate,
    ApiAdminPeriodDelete,
}                           from '@api';
import { useFormat }        from '@composables/useFormat.js';
import PeriodEditDialog     from './PeriodEditDialog.vue';

const { parseResponseErrors, showInfo } = useResponseError();
const { formatDate }                    = useFormat();
const { has }                           = usePermissions();

const periods        = ref([]);
const historyUrl     = ref(null);
const selectedPeriod = ref(null);
const showDialog     = ref(false);
const isLoading      = ref(false);
const canCreate      = computed(() => has('periods', 'edit'));
const canEdit        = computed(() => has('periods', 'edit'));
const canDrop        = computed(() => has('periods', 'drop'));

const listAction = async () => {
    isLoading.value = true;
    try {
        const response   = await ApiAdminPeriodList();
        periods.value    = response.data.periods || [];
        historyUrl.value = response.data.historyUrl;
    }
    catch (error) {
        parseResponseErrors(error);
    }
    finally {
        isLoading.value = false;
    }
};

const showCreateDialog = async () => {
    try {
        const response       = await ApiAdminPeriodCreate();
        selectedPeriod.value = response.data;
        showDialog.value     = true;
    }
    catch (error) {
        parseResponseErrors(error);
    }
};

const showEditDialog = (period) => {
    if (!canEdit.value || period.isClosed) {
        return;
    }
    selectedPeriod.value = period;
    showDialog.value     = true;
};

const onPeriodUpdated = () => {
    listAction();
};

const deleteAction = async (period) => {
    if (!confirm('Удалить период?')) {
        return;
    }
    try {
        await ApiAdminPeriodDelete(period.id);
        periods.value = periods.value.filter(p => p.id !== period.id);
        showInfo('Период удален');
    }
    catch (error) {
        parseResponseErrors(error);
    }
};

onMounted(listAction);
</script>
