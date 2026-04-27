<template>
    <div class="tickets-block">
        <loading-spinner
            v-if="!loaded"
            size="lg"
            color="primary"
            text="Загрузка заявок..."
            wrapper-class="my-5"
        />

        <template v-else>
            <div class="d-flex flex-wrap justify-content-between align-items-center gap-2 mb-3">
                <div class="d-flex flex-wrap align-items-center gap-2">
                    <a class="btn btn-success me-2" :href="Url.Routes.adminHelpDeskSettings.uri">
                        <i class="fa fa-gears"></i> Настройки
                    </a>
                    <button class="btn btn-success" @click="createTicket" v-if="actions.create">
                        <i class="fa fa-plus"></i> Создать заявку
                    </button>
                </div>
                <div class="d-flex flex-wrap align-items-center gap-2">
                    <pagination
                        :total="total"
                        :per-page="perPage"
                        :page="Math.ceil(skip / perPage) + 1"
                        :prop-classes="'pagination-sm mb-0'"
                        @update="onPaginationUpdate"
                    />
                    <simple-select
                        v-model="perPage"
                        class="d-inline-block form-select-sm w-auto"
                        :options="[15, 25, 50, 100]"
                        @change="onPerPageChange"
                    />
                    <span class="badge bg-secondary">Всего: {{ total }}</span>
                </div>
            </div>

            <div class="d-flex flex-wrap align-items-center gap-2 mb-3">
                <simple-select
                    v-model="filters.categoryId"
                    :options="categoryOptions"
                    @change="onCategoryChange"
                    class="form-control-sm w-auto"
                    label="Категория"
                />
                <simple-select
                    v-model="filters.serviceId"
                    :options="serviceOptions"
                    class="form-control-sm w-auto"
                    label="Услуга"
                    :disabled="!filters.categoryId && serviceOptions.length === 0"
                />
                <simple-select
                    v-model="filters.status"
                    :options="statusOptions"
                    class="form-control-sm w-auto"
                    label="Статус"
                />
                <simple-select
                    v-model="filters.priority"
                    :options="priorityOptions"
                    class="form-control-sm w-auto"
                    label="Приоритет"
                />
            </div>

            <tickets-list
                :tickets="tickets"
                :sort-field="sortField"
                :sort-order="sortOrder"
                @sort="onSort"
                @delete="deleteTicket"
            />
        </template>
    </div>
</template>

<script setup>
import {
    ref,
    computed,
    onMounted,
    watch,
}                           from 'vue';
import { useResponseError } from '@composables/useResponseError';
import LoadingSpinner       from '@common/LoadingSpinner.vue';
import Pagination           from '@common/pagination/Pagination.vue';
import SimpleSelect         from '@form/SimpleSelect.vue';
import TicketsList          from './TicketsList.vue';
import {
    ApiAdminHelpDeskTicketsList,
    ApiAdminHelpDeskTicketsDelete,
}                           from '@api';
import Url                  from '@utils/Url.js';

const props = defineProps({
    categories: { type: Array, default: () => [] },
    services  : { type: Array, default: () => [] },
    priorities: { type: Array, default: () => [] },
    statuses  : { type: Array, default: () => [] },
});

const { parseResponseErrors, showSuccess } = useResponseError();

const loaded           = ref(false);
const tickets          = ref([]);
const total            = ref(0);
const perPage          = ref(25);
const skip             = ref(0);
const sortField        = ref('id');
const sortOrder        = ref('desc');
const actions          = ref({});
const selectedTicketId = ref(null);
const routeState       = ref(0);

const filters = ref({
    categoryId: '',
    serviceId : '',
    status    : '',
    priority  : '',
});

// Инициализация из URL параметров
const initFromUrl = () => {
    const urlParams          = new URLSearchParams(window.location.search);
    perPage.value            = parseInt(urlParams.get('limit') || '25');
    skip.value               = parseInt(urlParams.get('skip') || '0');
    sortField.value          = urlParams.get('sort_field') || 'id';
    sortOrder.value          = urlParams.get('sort_order') || 'desc';
    filters.value.categoryId = urlParams.get('category') || '';
    filters.value.serviceId  = urlParams.get('service') || '';
    filters.value.status     = urlParams.get('status') || props.statuses[0]?.value;
    filters.value.priority   = urlParams.get('priority') || '';
};

// Обновление URL
const updateUrl = () => {
    const uri = Url.Generator.makeUri(Url.Routes.adminHelpDeskIndex, {}, {
        limit     : perPage.value,
        skip      : skip.value,
        sort_field: sortField.value,
        sort_order: sortOrder.value,
        category  : filters.value.categoryId || undefined,
        service   : filters.value.serviceId || undefined,
        status    : filters.value.status || undefined,
        priority  : filters.value.priority || undefined,
    });
    window.history.pushState({ state: routeState.value++ }, '', uri);
};

const categoryOptions = computed(() => [
    { value: '', label: 'Все категории' },
    ...props.categories.map(c => ({ value: c.id, label: c.name })),
]);

const serviceOptions = computed(() => {
    let list = props.services;
    if (filters.value.categoryId) {
        list = list.filter(s => s.category_id === filters.value.categoryId);
    }
    return [
        { value: '', label: 'Все услуги' },
        ...list.map(s => ({ value: s.id, label: s.name })),
    ];
});

const statusOptions = computed(() => [
    ...props.statuses.map(s => ({ value: s.value, label: s.label })),
]);

const priorityOptions = computed(() => [
    { value: '', label: 'Все приоритеты' },
    ...props.priorities.map(p => ({ value: p.value, label: p.label })),
]);

const loadTickets = async () => {
    try {
        const params = {
            limit     : perPage.value,
            skip      : skip.value,
            sort_field: sortField.value,
            sort_order: sortOrder.value,
        };
        if (filters.value.categoryId) {
            params.category = filters.value.categoryId;
        }
        if (filters.value.serviceId) {
            params.service = filters.value.serviceId;
        }
        if (filters.value.status) {
            params.status = filters.value.status;
        }
        if (filters.value.priority) {
            params.priority = filters.value.priority;
        }

        const response = await ApiAdminHelpDeskTicketsList(params);
        const data     = response.data;
        tickets.value  = data.tickets;
        total.value    = data.total || 0;
        actions.value  = data.actions || {};
        loaded.value   = true;
        updateUrl(); // после загрузки обновляем URL
    }
    catch (error) {
        parseResponseErrors(error);
    }
};

const applyFilters = () => {
    skip.value = 0;
    loadTickets();
};

const onSort = ({ field, order }) => {
    sortField.value = field;
    sortOrder.value = order;
    loadTickets();
};

const onPaginationUpdate = (newSkip) => {
    skip.value = newSkip;
    loadTickets();
};

const onPerPageChange = () => {
    skip.value = 0;
    loadTickets();
};

const onCategoryChange = () => {
    filters.value.serviceId = '';
    applyFilters();
};

const createTicket = () => {
    selectedTicketId.value = null;
    const url = Url.Generator.makeUri(Url.Routes.adminHelpDeskTicketsView.uri, {}, { id: null});

    window.open(url, '_blank');
};

const deleteTicket = async (id) => {
    if (!confirm('Удалить заявку?')) {
        return;
    }
    try {
        await ApiAdminHelpDeskTicketsDelete(id);
        showSuccess('Заявка удалена');
        await loadTickets();
    }
    catch (error) {
        parseResponseErrors(error);
    }
};

watch(() => filters.value.categoryId, () => {
    filters.value.serviceId = '';
    applyFilters();
});
watch([() => filters.value.serviceId, () => filters.value.status, () => filters.value.priority], () => {
    applyFilters();
});

onMounted(() => {
    initFromUrl();
    loadTickets();
});
</script>