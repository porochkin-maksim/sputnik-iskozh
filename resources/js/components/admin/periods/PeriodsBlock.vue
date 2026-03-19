<template>
    <div class="periods-block">
        <!-- Заголовок и кнопка добавления -->
        <div class="d-flex align-items-center justify-content-between mb-2">
            <div>
                <button
                    v-if="actions.create"
                    class="btn btn-success"
                    @click="showCreateDialog"
                >
                    <i class="fa fa-plus" aria-hidden="true"></i>
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
                <table class="table table-sm table-hover align-middle">
                    <caption class="visually-hidden">Список периодов</caption>
                    <thead>
                    <tr>
                        <th scope="col">ID</th>
                        <th scope="col">Название</th>
                        <th scope="col">Начало</th>
                        <th scope="col">Окончание</th>
                        <th scope="col">Статус</th>
                        <th scope="col" class="text-center">Действия</th>
                    </tr>
                    </thead>
                    <tbody>
                    <tr v-for="(period, index) in periods" :key="period.id">
                        <td>{{ period.id }}</td>
                        <td>{{ period.name }}</td>
                        <td class="text-nowrap">{{ $formatDate(period.startAt) }}</td>
                        <td class="text-nowrap">{{ $formatDate(period.endAt) }}</td>
                        <td>
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
                        <td>
                            <div class="d-flex justify-content-center gap-1">
                                <history-btn
                                    class="btn-link underline-none"
                                    :url="period.historyUrl"
                                />
                                <div>
                                    <a :href="period.receiptUrl" target="_blank" v-if="period.receiptUrl"
                                       class="btn ps-0 btn-link underline-none">
                                        <i class="fa fa-file-pdf-o text-danger"></i> Квитанция
                                    </a>
                                </div>
                                <div class="dropdown">
                                    <button
                                        class="btn btn-sm btn-light border"
                                        type="button"
                                        :id="'dropDown' + index + vueId"
                                        data-bs-toggle="dropdown"
                                        :disabled="!(actions.edit && actions.drop) || period.isClosed"
                                        :aria-disabled="!(actions.edit && actions.drop) || period.isClosed"
                                        aria-expanded="false"
                                    >
                                        <i class="fa fa-bars" aria-hidden="true"></i>
                                        <span class="visually-hidden">Действия</span>
                                    </button>
                                    <ul
                                        class="dropdown-menu"
                                        :aria-labelledby="'dropDown' + index + vueId"
                                    >
                                        <li v-if="actions.edit && !period.isClosed">
                                            <a
                                                class="dropdown-item cursor-pointer"
                                                @click="showEditDialog(period)"
                                                role="menuitem"
                                            >
                                                <i class="fa fa-edit" aria-hidden="true"></i>
                                                Редактировать
                                            </a>
                                        </li>
                                        <li v-if="actions.drop && !period.isClosed">
                                            <a
                                                class="dropdown-item cursor-pointer text-danger"
                                                @click="deleteAction(period)"
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
    onMounted,
    defineOptions,
}                           from 'vue';
import { useResponseError } from '@composables/useResponseError';
import HistoryBtn           from '../../common/HistoryBtn.vue';
import PeriodEditDialog     from './PeriodEditDialog.vue';
import LoadingSpinner       from '../../common/LoadingSpinner.vue';
import {
    ApiAdminPeriodList,
    ApiAdminPeriodCreate,
    ApiAdminPeriodDelete,
}                           from '@api';

defineOptions({
    name: 'PeriodsBlock',
});

const { parseResponseErrors, showInfo } = useResponseError();

const periods        = ref([]);
const historyUrl     = ref(null);
const actions        = ref({});
const selectedPeriod = ref(null);
const showDialog     = ref(false);
const vueId          = ref('uuid-' + Date.now() + '-' + Math.random().toString(36).substring(2, 9));
const isLoading      = ref(false);

const listAction = async () => {
    isLoading.value = true;
    try {
        const response   = await ApiAdminPeriodList();
        periods.value    = response.data.periods || [];
        actions.value    = response.data.actions;
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
    if (!actions.value.edit || period.isClosed) {
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
        await ApiAdminPeriodDelete({ id: period.id });
        periods.value = periods.value.filter(p => p.id !== period.id);
        showInfo('Период удален');
    }
    catch (error) {
        parseResponseErrors(error);
    }
};

onMounted(listAction);
</script>