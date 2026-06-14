<template>
    <div>
        <!-- Индикатор загрузки -->
        <loading-spinner
            v-if="loading && accounts.length === 0"
            size="lg"
            color="primary"
            text="Загрузка участков..."
            wrapper-class="py-5"
        />

        <template v-else>
            <accounts-toolbar
                :all-accounts="allAccounts"
                :can-create="canCreate"
                :current-page="currentPage"
                :history-url="historyUrl"
                :import-url="importUrl"
                :loading="loading"
                :per-page="perPage"
                :search="search"
                :total="total"
                @add-account="makeAction"
                @clear-search="clearSearch"
                @export="exportAction"
                @pagination-update="onPaginationUpdate"
                @per-page-change="loadAccounts"
                @search-change="searchAction"
                @update:search="search = $event"
                @update:per-page="perPage = $event"
            />

            <accounts-table
                :accounts="accounts"
                :can-user-view="canUserView"
                :sort-field="sortField"
                :sort-order="sortOrder"
                @sort="sort"
            />

            <div v-if="!loading && accounts.length === 0" class="text-center text-muted py-3">
                Нет участков для отображения
            </div>
        </template>
    </div>
    <account-item-add v-if="account"
                      :model-value="account"
                      @updated="onAccountUpdated" />
</template>

<script setup>
import {
    ref,
    computed,
    onMounted,
}                           from 'vue';
import { routeUri }         from '@utils/routeUri.js';
import { useResponseError } from '@composables/useResponseError';
import { usePermissions }   from '@composables/usePermissions.js';
import LoadingSpinner       from '@common/LoadingSpinner.vue';
import AccountItemAdd       from './AccountItemAdd.vue';
import AccountsToolbar      from './accounts-block/AccountsToolbar.vue';
import AccountsTable        from './accounts-block/AccountsTable.vue';
import {
    ApiAdminAccountCreate,
    ApiAdminAccountList,
}                           from '@api';

const { parseResponseErrors, clearResponseErrors } = useResponseError();

const { has } = usePermissions();

const canCreate   = computed(() => has('accounts', 'edit'));
const canUserView = computed(() => has('users', 'view'));

// Состояния
const loading     = ref(false);
const account     = ref(null);
const accounts    = ref([]);
const allAccounts = ref([]);
const historyUrl  = ref(null);
const total       = ref(null);
const perPage     = ref(25);
const skip        = ref(0);
const search      = ref('');
const sortField   = ref('sort_value');
const sortOrder   = ref('asc');
const routeState  = ref(0);
let searchTimeout = null;

// Вычисляемые свойства
const currentPage = computed(() => {
    return skip.value > 0 ? Math.floor(skip.value / perPage.value) + 1 : 1;
});

// Инициализация из URL
const initFromUrl = () => {
    const urlParams = new URLSearchParams(window.location.search);
    perPage.value   = parseInt(urlParams.get('limit')) || 25;
    skip.value      = parseInt(urlParams.get('skip')) || 0;
    search.value    = urlParams.get('search') || '';
    sortField.value = urlParams.get('sort_field') || 'sort_value';
    sortOrder.value = urlParams.get('sort_order') || 'asc';
};

// Загрузка списка участков
const loadAccounts = async () => {
    loading.value   = true;
    const getParams = {
        limit     : perPage.value,
        skip      : skip.value,
        search    : search.value,
        sort_field: sortField.value,
        sort_order: sortOrder.value,
    };

    const uri = new URL(window.location.href);
    Object.keys(getParams).forEach(key => {
        if (getParams[key]) {
            uri.searchParams.set(key, getParams[key]);
        }
        else {
            uri.searchParams.delete(key);
        }
    });
    window.history.pushState({ state: routeState.value++ }, '', uri.toString());

    try {
        const response    = await ApiAdminAccountList(getParams);
        accounts.value    = response.data.accounts || [];
        allAccounts.value = response.data.allAccounts;
        total.value       = response.data.total;
        historyUrl.value  = response.data.historyUrl;
    }
    catch (error) {
        parseResponseErrors(error);
    }
    finally {
        loading.value = false;
    }
};

// Добавление участка
const makeAction = async () => {
    try {
        clearResponseErrors();
        const response = await ApiAdminAccountCreate();
        account.value  = response.data;
    }
    catch (error) {
        parseResponseErrors(error);
    }
};

// Поиск
const searchAction = () => {
    if (searchTimeout) {
        clearTimeout(searchTimeout);
    }
    searchTimeout = setTimeout(() => {
        skip.value = 0;
        loadAccounts();
    }, 300);
};

// Очистка поиска
const clearSearch = () => {
    search.value = '';
    loadAccounts();
};

// Обновление пагинации
const onPaginationUpdate = (newSkip) => {
    skip.value = newSkip;
    loadAccounts();
};

// Сортировка
const sort = (field) => {
    if (sortField.value === field) {
        sortOrder.value = sortOrder.value === 'asc' ? 'desc' : 'asc';
    }
    else {
        sortOrder.value = 'asc';
    }
    sortField.value = field;
    loadAccounts();
};

// Экспорт
const importUrl = routeUri('adminAccountImportIndex');

const exportAction = () => {
    const params = {
        search    : search.value,
        sort_field: sortField.value,
        sort_order: sortOrder.value,
    };
    const url = routeUri('adminAccountExport', {}, params);
    window.open(url, '_blank');
};

// Обновление после добавления/редактирования
const onAccountUpdated = (accountData) => {
    if (accountData?.viewUrl) {
        window.location.href = accountData.viewUrl;
        return;
    }

    loadAccounts();
    account.value = null;
};

onMounted(() => {
    initFromUrl();
    loadAccounts();
});
</script>
