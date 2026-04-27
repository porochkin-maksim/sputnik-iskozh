<template>
    <div>
        <!-- Верхняя панель -->
        <div class="d-flex justify-content-between align-items-center mb-2">
            <div class="d-flex">
                <a
                    v-if="actions.edit"
                    class="btn btn-success me-2"
                    :href="getViewLink(null)"
                >
                    <i class="fa fa-plus" aria-hidden="true"></i>
                    Добавить пользователя
                </a>

                <!-- Поиск -->
                <div>
                    <div class="input-group input-group-sm">
                        <button
                            class="btn btn-light border"
                            type="button"
                            @click="searchAction"
                            :disabled="isLoading"
                        >
                            <i class="fa fa-search" aria-hidden="true"></i>
                        </button>
                        <input
                            class="form-control"
                            v-model="search"
                            placeholder="Поиск"
                            @keyup.enter="searchAction"
                            ref="searchInput"
                            :disabled="isLoading"
                        />
                        <button
                            class="btn btn-light border"
                            type="button"
                            @click="clearSearchAction"
                            :disabled="!search || isLoading"
                        >
                            <i class="fa fa-close" aria-hidden="true"></i>
                        </button>
                    </div>
                </div>

                <!-- Кнопка экспорта -->
                <div class="ms-2">
                    <button
                        class="btn btn-success"
                        @click="exportAction"
                        :disabled="isLoading"
                        title="Экспорт в Excel"
                    >
                        <i class="fa fa-file-excel-o" aria-hidden="true"></i>
                    </button>
                </div>
            </div>

            <!-- Пагинация и общая информация -->
            <div class="d-flex">
                <pagination
                    :total="total"
                    :per-page="perPage"
                    :page="currentPage"
                    :prop-classes="'pagination-sm mb-0'"
                    @update="onPaginationUpdate"
                    :disabled="isLoading"
                />
                <simple-select
                    v-model="perPage"
                    class="d-inline-block form-select-sm w-auto ms-2"
                    :options="[15, 25, 50, 100]"
                    @update:modelValue="onPerPageChange"
                    :disabled="isLoading"
                />
                <div class="d-flex align-items-center justify-content-center mx-2">
                    Всего: {{ total }}
                </div>
                <history-btn
                    class="btn-link underline-none"
                    :url="historyUrl"
                    :disabled="isLoading"
                />
            </div>
        </div>

        <!-- Фильтры -->
        <div class="mt-2 mb-3">
            <div class="d-flex align-items-center">
                <custom-checkbox
                    v-model="isMember"
                    label="Члены СНТ"
                    switch-style
                    :disabled="isLoading"
                />
                <custom-checkbox
                    v-model="isNotMember"
                    label="Не члены СНТ"
                    switch-style
                    class="ms-2"
                    :disabled="isLoading"
                />
                <custom-checkbox
                    v-model="isDeleted"
                    label="Удалён"
                    switch-style
                    class="ms-2"
                    :disabled="isLoading"
                />
            </div>
        </div>

        <!-- Индикатор загрузки -->
        <loading-spinner
            v-if="isLoading && !users.length"
            size="lg"
            color="primary"
            text="Загрузка пользователей..."
            wrapper-class="py-5"
        />

        <!-- Таблица пользователей -->
        <div v-else class="table-responsive">
            <table class="table table-sm table-striped table-bordered">
                <thead>
                <tr class="text-start">
                    <th class="cursor-pointer text-end" @click="sort('id')">
                        №
                        <i
                            v-if="sortField === 'id'"
                            :class="sortOrder === 'asc' ? 'fa fa-sort-asc' : 'fa fa-sort-desc'"
                            aria-hidden="true"
                        ></i>
                        <i v-else class="fa fa-sort" aria-hidden="true"></i>
                    </th>
                    <th class="text-end">Участок</th>
                    <th class="text-center">Право</th>
                    <th class="cursor-pointer" @click="sort('last_name')">
                        Фамилия
                        <i
                            v-if="sortField === 'last_name'"
                            :class="sortOrder === 'asc' ? 'fa fa-sort-asc' : 'fa fa-sort-desc'"
                            aria-hidden="true"
                        ></i>
                        <i v-else class="fa fa-sort" aria-hidden="true"></i>
                    </th>
                    <th class="cursor-pointer" @click="sort('first_name')">
                        Имя
                        <i
                            v-if="sortField === 'first_name'"
                            :class="sortOrder === 'asc' ? 'fa fa-sort-asc' : 'fa fa-sort-desc'"
                            aria-hidden="true"
                        ></i>
                        <i v-else class="fa fa-sort" aria-hidden="true"></i>
                    </th>
                    <th class="cursor-pointer" @click="sort('middle_name')">
                        Отчество
                        <i
                            v-if="sortField === 'middle_name'"
                            :class="sortOrder === 'asc' ? 'fa fa-sort-asc' : 'fa fa-sort-desc'"
                            aria-hidden="true"
                        ></i>
                        <i v-else class="fa fa-sort" aria-hidden="true"></i>
                    </th>
                    <th class="cursor-pointer" @click="sort('email')">
                        Почта
                        <i
                            v-if="sortField === 'email'"
                            :class="sortOrder === 'asc' ? 'fa fa-sort-asc' : 'fa fa-sort-desc'"
                            aria-hidden="true"
                        ></i>
                        <i v-else class="fa fa-sort" aria-hidden="true"></i>
                    </th>
                    <th>Телефон</th>
                    <th>Членство</th>
                </tr>
                </thead>
                <tbody>
                <tr
                    v-for="user in users"
                    :key="user.id"
                    class="text-start"
                >
                    <td class="text-end">
                        <a :href="user.viewUrl">{{ user.id }}</a>
                    </td>
                    <td class="text-end">
                        <template v-for="account in user.accounts" :key="account.id">
                            <div>
                                <a v-if="account?.viewUrl" :href="account.viewUrl">
                                    {{ account.number }}
                                </a>
                                <span v-else>{{ account.number }}</span>
                            </div>
                        </template>
                    </td>
                    <td class="text-center">
                        <template v-for="account in user.accounts" :key="account.id">
                            <div class="d-flex justify-content-between align-items-center">
                                <span>{{ formatDate(account.ownerDate) }}</span>
                                <span>
                                        <i
                                            class="fa fa-user"
                                            :class="account.fractionPercent ? 'text-success' : 'text-light'"
                                            aria-hidden="true"
                                        ></i>
                                        {{ account.fractionPercent }}&nbsp;
                                    </span>
                            </div>
                        </template>
                    </td>
                    <td>{{ user.lastName }}</td>
                    <td>{{ user.firstName }}</td>
                    <td>{{ user.middleName }}</td>
                    <td>
                            <span
                                :data-copy="user.email"
                                class="text-primary cursor-pointer"
                                @click="copyToClipboard(user.email)"
                                title="Скопировать email"
                            >
                                {{ user.email }}
                            </span>
                    </td>
                    <td>{{ user.phone }}</td>
                    <td>{{ formatDate(user.membershipDate) }}</td>
                </tr>
                <tr v-if="users.length === 0">
                    <td colspan="9" class="text-center py-3 text-muted">
                        <i class="fa fa-info-circle me-2" aria-hidden="true"></i>
                        Пользователи не найдены
                    </td>
                </tr>
                </tbody>
            </table>
        </div>
    </div>
</template>

<script setup>
import {
    ref,
    computed,
    watch,
    onMounted,
    defineProps,
    defineOptions,
}                           from 'vue';
import { useResponseError } from '@composables/useResponseError';
import { useFormat }        from '@composables/useFormat';
import { usePermissions }   from '@composables/usePermissions';
import HistoryBtn           from '@common/HistoryBtn.vue';
import Pagination           from '@common/pagination/Pagination.vue';
import SimpleSelect         from '@common/form/SimpleSelect.vue';
import CustomCheckbox       from '@common/form/CustomCheckbox.vue';
import LoadingSpinner       from '@common/LoadingSpinner.vue';
import {
    ApiAdminUserList,
}                           from '@api';
import Url                  from '@utils/Url.js';

defineOptions({
    name: 'UsersBlock',
});

const props = defineProps({
    permissions: {
        type   : Object,
        default: () => ({}),
    },
});

const { parseResponseErrors, showInfo, showDanger } = useResponseError();
const { formatDate }                                = useFormat();
const { has }                                       = usePermissions();

const users          = ref([]);
const actions        = ref({});
const historyUrl     = ref(null);
const total          = ref(0);
const perPage        = ref(25);
const skip           = ref(0);
const routeState     = ref(0);
const search         = ref('');
const sortField      = ref('id');
const sortOrder      = ref('asc');
const isLoading      = ref(false);
const searchProgress = ref(null);
const searchInput    = ref(null);

// Фильтры
const isMember    = ref(null);
const isNotMember = ref(null);
const isDeleted   = ref(null);

const canView = computed(() => has('users', 'view'));

// Текущая страница для пагинации
const currentPage = computed(() => Math.floor(skip.value / perPage.value) + 1);

// Формирование параметров запроса
const buildParams = () => ({
    limit     : perPage.value,
    skip      : skip.value,
    sort_field: sortField.value,
    sort_order: sortOrder.value,
    search    : search.value || null,
    isMember  : isMember.value ? 'true' : isNotMember.value ? 'false' : null,
    isDeleted : isDeleted.value ? 'true' : null,
});

// Загрузка списка пользователей
const loadUsers = async () => {
    isLoading.value = true;
    const params    = buildParams();

    // Обновляем URL
    const uri = Url.Generator.makeUri(Url.Routes.adminUserIndex, {}, params);
    window.history.pushState({ state: routeState.value++ }, '', uri);

    try {
        const response   = await ApiAdminUserList(params);
        users.value      = response.data.users || [];
        total.value      = response.data.total;
        actions.value    = response.data.actions || {};
        historyUrl.value = response.data.historyUrl;
    }
    catch (error) {
        parseResponseErrors(error);
    }
    finally {
        isLoading.value = false;
    }
};

// Поиск с debounce
const searchAction = () => {
    clearTimeout(searchProgress.value);
    searchProgress.value = setTimeout(() => {
        skip.value = 0;
        loadUsers();
    }, 300);
};

const clearSearchAction = () => {
    search.value = '';
    searchAction();
    searchInput.value?.focus();
};

// Экспорт в Excel
const exportAction = () => {
    const params = buildParams();
    const url    = Url.Generator.makeUri(Url.Routes.adminUserExport, {}, params);
    window.open(url, '_blank');
};

// Сортировка
const sort = (field) => {
    if (sortField.value === field) {
        sortOrder.value = sortOrder.value === 'asc' ? 'desc' : 'asc';
    }
    else {
        sortOrder.value = 'asc';
        sortField.value = field;
    }
    skip.value = 0;
    loadUsers();
};

// Пагинация
const onPaginationUpdate = (newSkip) => {
    skip.value = newSkip;
    loadUsers();
};

const onPerPageChange = () => {
    skip.value = 0;
    loadUsers();
};

// Ссылка на просмотр/редактирование пользователя
const getViewLink = (id) => {
    return Url.Generator.makeUri(Url.Routes.adminUserView, { id });
};

// Копирование email в буфер обмена
const copyToClipboard = (text) => {
    navigator.clipboard?.writeText(text).then(() => {
        showInfo('Email скопирован');
    }).catch(() => {
        showDanger('Не удалось скопировать');
    });
};

// Инициализация из URL параметров
const initFromUrl = () => {
    const urlParams   = new URLSearchParams(window.location.search);
    perPage.value     = parseInt(urlParams.get('limit') || '25');
    skip.value        = parseInt(urlParams.get('skip') || '0');
    sortField.value   = urlParams.get('sort_field') || 'id';
    sortOrder.value   = urlParams.get('sort_order') || 'asc';
    search.value      = urlParams.get('search') || '';
    isMember.value    = urlParams.get('isMember') === 'true' ? true : null;
    isNotMember.value = urlParams.get('isMember') === 'false' ? true : null;
    isDeleted.value   = urlParams.get('isDeleted') === 'true' ? true : null;
};

// Следим за фильтрами с взаимным исключением
watch(isMember, (val) => {
    if (val) {
        isNotMember.value = null;
    }
    skip.value = 0;
    loadUsers();
});

watch(isNotMember, (val) => {
    if (val) {
        isMember.value = null;
    }
    skip.value = 0;
    loadUsers();
});

watch(isDeleted, () => {
    skip.value = 0;
    loadUsers();
});

onMounted(() => {
    initFromUrl();
    loadUsers();
});
</script>