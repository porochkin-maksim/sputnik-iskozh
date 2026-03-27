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
            <div class="d-flex align-items-center justify-content-between mb-2">
                <div class="d-flex">
                    <button class="btn btn-success me-2"
                            v-if="actions.edit"
                            @click="makeAction">
                        Добавить участок
                    </button>
                    <template v-if="allAccounts && allAccounts.length">
                        <div class="d-flex">
                            <div class="input-group input-group-sm">
                                <button class="btn btn-light border"
                                        @click="searchAction">
                                    <i class="fa fa-search"></i>
                                </button>
                                <input class="form-control"
                                       v-model="search"
                                       name="users_search"
                                       placeholder="Поиск"
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
                    <div>
                        <pagination :total="total"
                                    :per-page="perPage"
                                    :page="currentPage"
                                    :prop-classes="'pagination-sm mb-0'"
                                    @update="onPaginationUpdate" />
                    </div>
                    <div>
                        <simple-select v-model="perPage"
                                       :class="'d-inline-block form-select-sm w-auto ms-2'"
                                       :options="[15, 25, 50, 100]"
                                       @change="loadAccounts" />
                    </div>
                    <div class="d-flex align-items-center justify-content-center mx-2">
                        Всего: {{ total }}
                    </div>
                    <history-btn class="btn-link underline-none"
                                 :url="historyUrl" />
                </div>
            </div>

            <div class="table-responsive">
                <table class="table table-sm table-striped table-bordered">
                    <thead>
                    <tr class="text-center">
                        <th class="cursor-pointer text-end" @click="sort('id')">
                            №
                            <i v-if="sortField === 'id'"
                               :class="sortOrder === 'asc' ? 'fa fa-sort-asc' : 'fa fa-sort-desc'"></i>
                            <i v-else class="fa fa-sort"></i>
                        </th>
                        <th class="cursor-pointer" @click="sort('sort_value')">
                            Номер
                            <i v-if="sortField === 'sort_value'"
                               :class="sortOrder === 'asc' ? 'fa fa-sort-asc' : 'fa fa-sort-desc'"></i>
                            <i v-else class="fa fa-sort"></i>
                        </th>
                        <th class="cursor-pointer" @click="sort('size')">
                            Площадь (м²)
                            <i v-if="sortField === 'size'"
                               :class="sortOrder === 'asc' ? 'fa fa-sort-asc' : 'fa fa-sort-desc'"></i>
                            <i v-else class="fa fa-sort"></i>
                        </th>
                        <th>Кадастр</th>
                        <th>Выставление счетов</th>
                        <th>Пользователи</th>
                        <th></th>
                    </tr>
                    </thead>
                    <tbody>
                    <tr v-for="account in accounts" :key="account.id" class="align-middle">
                        <td class="text-end">
                            <a :href="account.viewUrl">
                                {{ account.id }}
                            </a>
                        </td>
                        <td class="text-end">{{ account.number }}</td>
                        <td class="text-end">{{ account.size }}</td>
                        <td class="text-center">{{ account.cadastreNumber }}</td>
                        <td class="text-center">
                            <i :class="account.isInvoicing ? 'fa fa-check text-success' : ''"></i>
                        </td>
                        <td class="text-end">
                            <ol v-if="account.users && account.users.length" class="mb-0 ps-0">
                                <li v-for="user in account.users" :key="user.id"
                                    class="d-flex justify-content-between align-items-center">
                                        <span>
                                            <a v-if="user?.viewUrl" :href="user.viewUrl">{{ user.fullName }}</a>
                                            <span v-else>{{ user.fullName }}</span>
                                        </span>
                                    <span>
                                            <i class="fa fa-user"
                                               :class="[user.fractionPercent ? 'text-success' : 'text-light']"></i>
                                            &nbsp;{{ user.fractionPercent }}
                                        </span>
                                </li>
                            </ol>
                        </td>
                        <td>
                            <history-btn :disabled="!account.historyUrl"
                                         class="btn-link underline-none"
                                         :url="account.historyUrl ? account.historyUrl : ''" />
                        </td>
                    </tr>
                    </tbody>
                </table>
            </div>

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
    defineOptions,
}                           from 'vue';
import { useResponseError } from '@composables/useResponseError';
import HistoryBtn           from '../../common/HistoryBtn.vue';
import Pagination           from '../../common/pagination/Pagination.vue';
import AccountItemAdd       from './AccountItemAdd.vue';
import SimpleSelect         from '../../common/form/SimpleSelect.vue';
import LoadingSpinner       from '@common/LoadingSpinner.vue';
import {
    ApiAdminAccountCreate,
    ApiAdminAccountList,
}                           from '@api';

defineOptions({
    name: 'AccountsBlock',
});

const { parseResponseErrors } = useResponseError();

// Состояния
const loading     = ref(false);
const account     = ref(null);
const accounts    = ref([]);
const allAccounts = ref([]);
const historyUrl  = ref(null);
const actions     = ref({});
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
        actions.value     = response.data.actions;
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

// Обновление после добавления/редактирования
const onAccountUpdated = () => {
    loadAccounts();
    account.value = null;
};

onMounted(() => {
    initFromUrl();
    loadAccounts();
});
</script>