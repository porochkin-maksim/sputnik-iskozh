import {
    computed,
    onMounted,
    ref,
    watch,
}                           from 'vue';
import { usePermissions }   from '@composables/usePermissions.js';
import { useResponseError } from '@composables/useResponseError';
import {
    ApiAdminHelpDeskTicketsDelete,
    ApiAdminHelpDeskTicketsList,
}                           from '@api';
import { routeUri }         from '@utils/routeUri.js';

export function useTicketsBlock (props) {
    const { parseResponseErrors, showSuccess } = useResponseError();
    const { has }                              = usePermissions();

    const loaded     = ref(false);
    const tickets    = ref([]);
    const total      = ref(0);
    const perPage    = ref(25);
    const skip       = ref(0);
    const sortField  = ref('id');
    const sortOrder  = ref('desc');
    const routeState = ref(0);

    const filters = ref({
        categoryId: '',
        serviceId : '',
        status    : '',
        priority  : '',
    });

    const settingsUrl = routeUri('adminHelpDeskSettings');
    const canCreate   = computed(() => has('help_desk', 'edit'));
    const canDelete   = computed(() => has('help_desk', 'drop'));

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

    const updateUrl = () => {
        const uri = routeUri('adminHelpDeskIndex', {}, {
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
            loaded.value   = true;
            updateUrl();
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

    const onPerPageChange = (value) => {
        perPage.value = value;
        skip.value    = 0;
        loadTickets();
    };

    const onCategoryChange = () => {
        filters.value.serviceId = '';
        applyFilters();
    };

    const createTicket = () => {
        window.open(routeUri('adminHelpDeskTicketsView', { id: null }), '_blank');
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

    return {
        applyFilters,
        categoryOptions,
        createTicket,
        deleteTicket,
        canCreate,
        canDelete,
        filters,
        loaded,
        onCategoryChange,
        onPaginationUpdate,
        onPerPageChange,
        onSort,
        priorityOptions,
        perPage,
        props,
        serviceOptions,
        settingsUrl,
        skip,
        sortField,
        sortOrder,
        statusOptions,
        tickets,
        total,
    };
}
