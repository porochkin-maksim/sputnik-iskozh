<template>
    <div>
        <div class="d-flex mb-2 justify-content-between">
            <div>
                <button class="btn btn-sm btn-success ms-2"
                        v-if="actions.edit"
                        @click="addHistoryAction"
                        :disabled="loading">
                    Добавить показания
                </button>
            </div>
            <div>
                <button class="btn btn-outline-primary btn-sm"
                        @click="refreshData"
                        :disabled="loading">
                    <i class="fa fa-refresh" :class="loading ? 'fa-spin' : ''"></i>
                    {{ loading ? 'Обновление...' : 'Обновить' }}
                </button>

                <history-btn
                    class="btn-link underline-none"
                    :url="counter?.historyUrl" />
            </div>
        </div>

        <!-- Индикатор загрузки -->
        <loading-spinner
            v-if="loading && histories.length === 0"
            size="lg"
            color="primary"
            text="Загрузка показаний..."
            wrapper-class="py-5"
        />

        <template v-else>
            <!-- График -->
            <counter-item-chart-block v-if="histories.length > 0"
                                      :histories="histories"
                                      class="mb-3" />

            <table class="table table-sm text-center align-middle">
                <thead>
                <tr>
                    <th>#</th>
                    <th>Дата</th>
                    <th>Показания</th>
                    <th>Дней</th>
                    <th>Дельта</th>
                    <th>Статус</th>
                    <th>Файл</th>
                    <th>Оплачено</th>
                    <th>Стоимость</th>
                    <th>Тариф</th>
                    <th></th>
                </tr>
                </thead>
                <tbody>
                <tr v-for="history in histories" :key="history.id">
                    <td class="text-center">{{ history.id }}</td>
                    <td class="text-center">{{ $formatDate(history.date) }}</td>
                    <td class="text-center">{{ history.value }}</td>
                    <td class="text-center">{{ history.days === null ? '' : history.days }}</td>
                    <td class="text-center">{{ history.delta === null ? '' : history.delta }}</td>
                    <td class="text-center">
                        <b :class="history.isVerified ? 'text-success' : 'text-secondary'">
                            {{ history.isVerified ? 'Подтверждено' : 'Не подтверждено' }}
                        </b>
                    </td>
                    <td class="text-center">
                        <template v-if="history.file">
                            <file-item :file="history.file"
                                       :edit="false" />
                        </template>
                    </td>
                    <template v-if="history.claim">
                        <template v-if="history.invoiceUrl">
                            <td class="text-center">
                                <a :href="history.invoiceUrl"
                                   class="text-decoration-none">{{ $formatMoney(history.claim.tariff) }}</a>
                            </td>
                            <td class="text-center">
                                <a :href="history.invoiceUrl"
                                   class="text-decoration-none">{{ $formatMoney(history.claim.cost) }}</a>
                            </td>
                            <td class="text-center">
                                <a :href="history.invoiceUrl"
                                   class="text-decoration-none">{{ $formatMoney(history.claim.paid) }}</a>
                            </td>
                        </template>
                        <template v-else>
                            <td class="text-center">{{ $formatMoney(history.claim.tariff) }}</td>
                            <td class="text-center">{{ $formatMoney(history.claim.cost) }}</td>
                            <td class="text-center">{{ $formatMoney(history.claim.paid) }}</td>
                        </template>
                    </template>
                    <template v-else-if="history.delta && actions.edit && counter.isInvoicing">
                        <td colspan="3">
                            <button class="btn btn-sm btn-success"
                                    @click="addClaimForHistory(history)">
                                Добавить услугу
                            </button>
                        </td>
                    </template>
                    <template v-else>
                        <td colspan="3"></td>
                    </template>
                    <td class="text-center">
                        <div class="dropdown">
                            <a class="btn btn-sm btn-light border"
                               href="#"
                               role="button"
                               :id="'dropDownHistory' + history.id + vueId"
                               data-bs-toggle="dropdown"
                               :disabled="loading"
                               aria-expanded="false">
                                <i class="fa fa-bars"></i>
                            </a>
                            <ul class="dropdown-menu"
                                :aria-labelledby="'dropDownHistory' + history.id + vueId">
                                <li v-if="account.actions.counters.edit">
                                    <a class="dropdown-item cursor-pointer"
                                       @click="editHistoryAction(history)">
                                        <i class="fa fa-edit"></i> Редактировать
                                    </a>
                                </li>
                                <li v-if="account.actions.counters.drop">
                                    <a class="dropdown-item cursor-pointer text-danger"
                                       @click="dropHistoryAction(history)">
                                        <i class="fa fa-trash"></i> Удалить
                                    </a>
                                </li>
                                <li>
                                    <history-btn class="dropdown-item btn btn-link text-decoration-none"
                                                 :url="history.historyUrl" />
                                </li>
                            </ul>
                        </div>
                    </td>
                </tr>
                <tr v-if="histories?.length !== 0 && histories?.length < total">
                    <td colspan="11">
                        <div class="d-flex justify-content-center">
                            <button class="btn btn-link"
                                    v-if="!loading"
                                    @click="loadMore">
                                Показать ещё
                            </button>
                            <button class="btn border-0"
                                    disabled
                                    v-else>
                                <i class="fa fa-spinner fa-spin"></i> Подгрузка
                            </button>
                        </div>
                    </td>
                </tr>
                </tbody>
            </table>
        </template>
    </div>
    <counter-history-item
        v-if="selectedHistory"
        :counter="counter"
        :history="selectedHistory"
        @history-updated="onHistoryUpdated"
    />
</template>

<script setup>
import {
    ref,
    onMounted,
    defineOptions,
}                            from 'vue';
import FileItem              from '../../../common/files/FileItem.vue';
import CounterHistoryItem    from './CounterHistoryItem.vue';
import HistoryBtn            from '../../../common/HistoryBtn.vue';
import CounterItemChartBlock from '@common/blocks/CounterItemChartBlock.vue';
import LoadingSpinner        from '../../../common/LoadingSpinner.vue';
import { useResponseError }  from '@composables/useResponseError';
import {
    ApiAdminCounterHistoryList,
    ApiAdminRequestsCounterHistoryDelete,
    ApiAdminRequestsCounterHistoryCreateClaim,
}                            from '@api';

defineOptions({
    name: 'CounterItemView',
});

const props = defineProps({
    modelValue: {
        type    : Object,
        required: true,
    },
});

const { parseResponseErrors, showInfo, showDanger } = useResponseError();

// Состояния
const vueId           = ref('uuid_' + Date.now() + '_' + Math.random());
const limit           = ref(10);
const total           = ref(0);
const loading         = ref(false);
const counter         = ref(null);
const account         = ref(null);
const actions         = ref({});
const histories       = ref([]);
const selectedHistory = ref(null);

// Инициализация
const init = () => {
    counter.value = props.modelValue;
    account.value = counter.value.account;
    actions.value = counter.value.actions;
    refreshData();
};

// Полное обновление данных
const refreshData = async () => {
    if (loading.value) {
        return;
    }

    histories.value = [];
    total.value     = 0;
    await loadHistory(true);
};

// Загрузка истории
const loadHistory = async (isRefresh = false) => {
    loading.value = true;

    try {
        const response = await ApiAdminCounterHistoryList(counter.value.id, {
            limit: limit.value,
            skip : isRefresh ? 0 : histories.value.length,
        });

        if (isRefresh) {
            histories.value = response.data.histories.histories || [];
        }
        else {
            const newHistories = response.data.histories.histories || [];
            newHistories.forEach(history => {
                const exists = histories.value.some(item => item.id === history.id);
                if (!exists) {
                    histories.value.push(history);
                }
            });
        }

        total.value = response.data.total || total.value;
    }
    catch (error) {
        parseResponseErrors(error);
    }
    finally {
        loading.value = false;
    }
};

// Подгрузка ещё
const loadMore = () => {
    loadHistory(false);
};

// Добавление показаний
const addHistoryAction = () => {
    selectedHistory.value = {};
};

// Редактирование показаний
const editHistoryAction = (history) => {
    selectedHistory.value = history;
};

// Удаление показаний
const dropHistoryAction = async (history) => {
    if (!confirm('Удалить показания?')) {
        return;
    }

    try {
        const response = await ApiAdminRequestsCounterHistoryDelete(history.id);
        if (response.data) {
            await refreshData();
            showInfo('Показания удалены');
        }
        else {
            showDanger('Показания не удалены');
        }
    }
    catch (error) {
        parseResponseErrors(error);
    }
};

// Создание услуги по показаниям
const addClaimForHistory = async (history) => {
    try {
        const response = await ApiAdminRequestsCounterHistoryCreateClaim(history.id);
        if (response.data) {
            await refreshData();
            showInfo('Услуга создана');
        }
        else {
            showDanger('Услуга не создана');
        }
    }
    catch (error) {
        parseResponseErrors(error);
    }
};

// Обновление после добавления/редактирования истории
const onHistoryUpdated = () => {
    refreshData();
    selectedHistory.value = null;
};

onMounted(() => {
    init();
});
</script>