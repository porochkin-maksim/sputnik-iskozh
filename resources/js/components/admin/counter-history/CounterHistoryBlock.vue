<template>
    <div>
        <!-- Индикатор загрузки -->
        <loading-spinner
            v-if="loading && histories.length === 0"
            size="lg"
            color="primary"
            text="Загрузка показаний..."
            wrapper-class="py-5"
        />

        <template v-else>
            <div class="d-flex justify-content-between align-items-center mb-2">
                <div class="d-flex align-items-center">
                    <template v-if="computedStatuses && computedStatuses.length">
                        <simple-select v-model="verifiedStatus"
                                       :class="'d-inline-block form-select-sm w-auto'"
                                       :options="computedStatuses"
                                       @change="loadHistories"
                        />
                    </template>
                    <template v-if="!isVerifiedStatus">
                        <template v-if="histories.length">
                            <button class="btn btn-success ms-2"
                                    v-if="actions.edit"
                                    :disabled="!canSubmitAction"
                                    @click="confirmAction">
                                <i class="fa fa-check"></i> Подтвердить
                            </button>
                            <button class="btn btn-danger ms-2"
                                    v-if="actions.drop"
                                    :disabled="!canSubmitAction"
                                    @click="deleteAction">
                                <i class="fa fa-trash"></i> Удалить
                            </button>
                        </template>
                    </template>
                    <template v-else>
                        <div class="d-flex ms-2">
                            <div class="input-group input-group-sm">
                                <input class="form-control"
                                       v-model="searchAccount"
                                       name="users_search"
                                       placeholder="Участок..."
                                       @keyup="searchAction"
                                       ref="searchInput">
                                <button class="btn btn-light border"
                                        type="button"
                                        @click="clearSearch">
                                    <i class="fa fa-close"></i>
                                </button>
                            </div>
                        </div>
                    </template>
                </div>
                <div class="d-flex">
                    <template v-if="isVerifiedStatus">
                        <div>
                            <pagination :total="total"
                                        :per-page="perPage"
                                        :prop-classes="'pagination-sm mb-0'"
                                        @update="onPaginationUpdate" />
                        </div>
                        <div>
                            <simple-select v-model="perPage"
                                           :class="'d-inline-block form-select-sm w-auto ms-2'"
                                           :options="[15, 25, 50, 100, 500]"
                                           @change="loadHistories" />
                        </div>
                    </template>
                    <div class="d-flex align-items-center justify-content-center text-nowrap mx-2">
                        Всего: {{ total }}
                    </div>
                </div>
            </div>

            <div v-if="histories.length">
                <table class="table table-sm table-bordered align-middle">
                    <thead>
                    <tr class="text-center">
                        <th v-if="actions.edit && canCheckAction && !isVerifiedStatus">
                            <div>
                                <input @change="onAllCheck"
                                       v-model="allCheck"
                                       type="checkbox"
                                       class="form-check-input" />
                            </div>
                        </th>
                        <th>Участок</th>
                        <th>Счётчик</th>
                        <th>Дата</th>
                        <th>Показание</th>
                        <th v-if="canCheckAction">Предыдущее</th>
                        <th v-if="canCheckAction">Дельта</th>
                        <th>Выставлять счета</th>
                        <th>Файл</th>
                        <th></th>
                        <th v-if="actions.drop"></th>
                    </tr>
                    </thead>
                    <tbody>
                    <tr v-for="history in histories" :key="history.id">
                        <td v-if="actions.edit && canCheckAction && !isVerifiedStatus"
                            class="text-center">
                            <div>
                                <input @change="onChanged(history.id)"
                                       :checked="isChecked(history.id)"
                                       type="checkbox"
                                       class="form-check-input" />
                            </div>
                        </td>
                        <template v-if="history.accountId && history.counterId">
                            <td v-if="history.accountUrl"
                                class="text-end">
                                <a :href="history.accountUrl">{{ history.accountNumber }}</a>
                            </td>
                            <td v-else
                                class="text-end">{{ history.accountNumber }}
                            </td>
                            <td>{{ history.counterNumber }}</td>
                        </template>
                        <template v-else>
                            <td colspan="2"
                                class="text-center">
                                <button class="btn btn-sm border-0"
                                        v-if="actions.edit"
                                        @click="showLinkDialog(history.id)">
                                    <i class="fa fa-link"></i>&nbsp;привязать
                                </button>
                            </td>
                        </template>
                        <td class="text-center">{{ formatDate(history.date) }}</td>
                        <td class="text-end">{{ history.value }}</td>
                        <td class="text-end" v-if="canCheckAction">{{ history.before ? history.before : 'начальное' }}
                        </td>
                        <td class="text-end" v-if="canCheckAction">{{ history.delta ? history.delta : '' }}</td>
                        <td class="text-center">
                            <i v-if="history.isInvoicing" class="fa fa-check text-success"></i>
                            <i v-else class="fa fa-close text-danger"></i>
                        </td>
                        <td>
                            <div v-if="history.file">
                                <a :href="history.file.url"
                                   class="text-decoration-none"
                                   :data-lightbox="history.file.name"
                                   :data-title="history.file.name"
                                   target="_blank">{{ history.file.name }}</a>
                            </div>
                        </td>
                        <td>
                            <history-btn class="btn-link underline-none"
                                         :url="history.historyUrl" />
                        </td>
                        <td v-if="actions.drop">
                            <button class="btn btn-sm text-danger border-0"
                                    v-if="actions.drop"
                                    @click="dropAction(history.id)">
                                <i class="fa fa-trash"></i>
                            </button>
                        </td>
                    </tr>
                    </tbody>
                </table>
            </div>

            <div v-else-if="!loading && histories.length === 0" class="text-center text-muted py-3">
                Нет показаний для отображения
            </div>
        </template>
    </div>

    <view-dialog v-model:show="showDialog"
                 v-model:hide="hideDialog"
                 @hidden="closeLinkDialog">
        <template v-slot:title>Привязка показаний</template>
        <template v-slot:body>
            <div class="container-fluid">
                <label>Выберите участок</label>
                <search-select
                    v-model="accountId"
                    :prop-class="'form-control mb-2'"
                    :items="accounts"
                    :placeholder="'Участок...'"
                    @update:model-value="getCounters"
                />
                <template v-if="accountId && counters.length">
                    <label>Выберите счётчик</label>
                    <search-select
                        v-model="counterId"
                        :prop-class="'form-control mb-2'"
                        :items="counters"
                        :placeholder="'Счётчик...'"
                    />
                </template>
                <template v-else-if="accountId && loadedCounters">
                    <div class="alert alert-warning">
                        У участка нет ни одного счётчика
                    </div>
                </template>
            </div>
        </template>
        <template v-slot:footer>
            <button class="btn btn-success"
                    :disabled="!accountId || !counterId"
                    @click="linkAction">
                Привязать
            </button>
        </template>
    </view-dialog>
</template>

<script setup>
import {
    ref,
    computed,
    watch,
    onMounted,
    defineOptions,
}                           from 'vue';
import HistoryBtn           from '../../common/HistoryBtn.vue';
import ViewDialog           from '../../common/ViewDialog.vue';
import SearchSelect         from '../../common/form/SearchSelect.vue';
import Pagination           from '../../common/pagination/Pagination.vue';
import SimpleSelect         from '../../common/form/SimpleSelect.vue';
import LoadingSpinner       from '@common/LoadingSpinner.vue';
import { useResponseError } from '@composables/useResponseError';
import {
    ApiAdminRequestsCounterHistoryList,
    ApiAdminRequestsCounterHistoryDelete,
    ApiAdminRequestsCounterHistoryConfirm,
    ApiAdminRequestsCounterHistoryConfirmDelete,
    ApiAdminRequestsCounterHistoryLink,
    ApiAdminSelectsAccounts,
    ApiAdminSelectsCounters,
}                           from '@api';
import { useFormat }        from '@composables/useFormat.js';

defineOptions({
    name: 'CounterHistoryBlock',
});

const emit = defineEmits(['update:reload', 'update:selectedId', 'update:count']);

const { parseResponseErrors, showInfo, showDanger } = useResponseError();
const { formatDate }                                = useFormat();

// Состояния
const loading        = ref(false);
const total          = ref(null);
const perPage        = ref(25);
const skip           = ref(0);
const routeState     = ref(0);
const verifiedStatus = ref('false');
const histories      = ref([]);
const actions        = ref({});
const allCheck       = ref(false);
const checked        = ref([]);
const showDialog     = ref(false);
const hideDialog     = ref(false);
const historyId      = ref(null);
const accountId      = ref(null);
const accounts       = ref([]);
const counterId      = ref(null);
const counters       = ref([]);
const loadedCounters = ref(false);
const searchAccount  = ref(null);
let searchTimeout    = null;

// Вычисляемые свойства
const computedStatuses = computed(() => [
    { value: 'false', label: 'Непроверенные' },
    { value: 'true', label: 'Проверенные' },
]);

const isVerifiedStatus = computed(() => verifiedStatus.value === 'true');

const canCheckAction = computed(() => {
    return histories.value.every(history => history.counterId !== null);
});

const canSubmitAction = computed(() => {
    return checked.value.length && canCheckAction.value;
});

// Методы
const loadHistories = async () => {
    loading.value = true;

    const uri    = new URL(window.location.href);
    const params = {
        limit   : perPage.value,
        skip    : skip.value,
        verified: verifiedStatus.value,
        search  : searchAccount.value,
    };
    Object.keys(params).forEach(key => {
        if (params[key]) {
            uri.searchParams.set(key, params[key]);
        }
        else {
            uri.searchParams.delete(key);
        }
    });
    window.history.pushState({ state: routeState.value++ }, '', uri.toString());

    allCheck.value = false;
    checked.value  = [];

    try {
        const response  = await ApiAdminRequestsCounterHistoryList({
            limit   : perPage.value,
            skip    : skip.value,
            verified: verifiedStatus.value,
            search  : searchAccount.value,
        });
        actions.value   = response.data.histories.actions;
        histories.value = response.data.histories.histories || [];
        total.value     = response.data.total;
        emit('update:count', histories.value.length);
    }
    catch (error) {
        parseResponseErrors(error);
    }
    finally {
        loading.value = false;
    }
};

const getAccounts = async () => {
    accountId.value = null;
    accounts.value  = [];

    try {
        const response = await ApiAdminSelectsAccounts();
        accounts.value = response.data;
    }
    catch (error) {
        parseResponseErrors(error);
    }
};

const getCounters = async () => {
    counterId.value = null;
    counters.value  = [];

    if (!accountId.value) {
        return;
    }

    loadedCounters.value = false;
    try {
        const response       = await ApiAdminSelectsCounters(accountId.value);
        counters.value       = response.data;
        loadedCounters.value = true;
    }
    catch (error) {
        parseResponseErrors(error);
    }
};

const onAllCheck = () => {
    if (allCheck.value) {
        checked.value = histories.value.map(item => String(item.id));
    }
    else {
        checked.value = [];
    }
};

const isChecked = (id) => {
    return checked.value.includes(String(id));
};

const onChanged = (id) => {
    const index = checked.value.indexOf(String(id));
    if (index > -1) {
        checked.value.splice(index, 1);
    }
    else {
        checked.value.push(String(id));
    }
};

const showLinkDialog = (id) => {
    historyId.value  = id;
    showDialog.value = true;
};

const closeLinkDialog = () => {
    historyId.value  = null;
    showDialog.value = false;
    hideDialog.value = true;
};

const linkAction = async () => {
    const form = new FormData();
    form.append('id', historyId.value);
    form.append('account_id', accountId.value);
    form.append('counter_id', counterId.value);

    try {
        await ApiAdminRequestsCounterHistoryLink({}, form);
        showInfo('Показания привязаны');
        loadHistories();
        closeLinkDialog();
    }
    catch (error) {
        const text = error?.response?.data?.message || 'Не получилось привязать показания';
        showDanger(text);
        parseResponseErrors(error);
    }
};

const dropAction = async (id) => {
    if (!confirm(id ? 'Удалить показание?' : 'Удалить выделенные показания?')) {
        return;
    }

    try {
        const response = await ApiAdminRequestsCounterHistoryDelete(id);
        if (response.data) {
            loadHistories();
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

const confirmAction = async () => {
    if (!confirm('Подтвердить выделенные показания?')) {
        return;
    }

    const form = new FormData();
    checked.value.forEach(id => {
        form.append('ids[]', id);
    });

    try {
        await ApiAdminRequestsCounterHistoryConfirm({}, form);
        showInfo('Показания подтверждены');
        loadHistories();
    }
    catch (error) {
        const text = error?.response?.data?.message || 'Не получилось подтвердить показания';
        showDanger(text);
        parseResponseErrors(error);
    }
};

const deleteAction = async () => {
    if (!confirm('Удалить выделенные показания?')) {
        return;
    }

    const form = new FormData();
    checked.value.forEach(id => {
        form.append('ids[]', id);
    });

    try {
        await ApiAdminRequestsCounterHistoryConfirmDelete({}, form);
        showInfo('Показания удалены');
        await loadHistories();
    }
    catch (error) {
        const text = error?.response?.data?.message || 'Не получилось удалить показания';
        showDanger(text);
        parseResponseErrors(error);
    }
};

const onPaginationUpdate = (newSkip) => {
    skip.value = newSkip;
    loadHistories();
};

const searchAction = () => {
    if (searchTimeout) {
        clearTimeout(searchTimeout);
    }
    searchTimeout = setTimeout(() => {
        skip.value = 0;
        loadHistories();
    }, 300);
};

const clearSearch = () => {
    searchAccount.value = '';
    loadHistories();
};

// Следим за изменением reload (если нужно)
watch(() => emit('update:reload'), (value) => {
    if (value === false) {
        return;
    }
    loadHistories();
    emit('update:reload', false);
});

// Инициализация из URL
const initFromUrl = () => {
    const urlParams      = new URLSearchParams(window.location.search);
    perPage.value        = parseInt(urlParams.get('limit')) || 25;
    skip.value           = parseInt(urlParams.get('skip')) || 0;
    verifiedStatus.value = urlParams.get('verified') || 'false';
    searchAccount.value  = urlParams.get('search') || null;
};

onMounted(() => {
    initFromUrl();
    getAccounts();
    loadHistories();
});
</script>