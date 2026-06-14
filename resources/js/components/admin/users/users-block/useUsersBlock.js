import {
    computed,
    onMounted,
    ref,
    watch,
} from 'vue';

import { useFormat }        from '@composables/useFormat';
import { usePermissions }   from '@composables/usePermissions';
import { useResponseError } from '@composables/useResponseError';
import { ApiAdminUserList } from '@api';
import { routeUri }         from '@utils/routeUri.js';

export function useUsersBlock () {
    const { parseResponseErrors, showInfo, showDanger } = useResponseError();
    const { formatDate }                                = useFormat();
    const { has }                                       = usePermissions();

    const users          = ref([]);
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

    const isMember        = ref(null);
    const isNotMember     = ref(null);
    const isDeleted       = ref(null);
    const hasVerifiedEmail = ref(null);

    const canView     = computed(() => has('users', 'view'));
    const canCreate   = computed(() => has('users', 'edit'));
    const currentPage = computed(() => Math.floor(skip.value / perPage.value) + 1);

    const buildParams = () => ({
        limit     : perPage.value,
        skip      : skip.value,
        sort_field: sortField.value,
        sort_order: sortOrder.value,
        search    : search.value || null,
        isMember  : isMember.value ? 'true' : isNotMember.value ? 'false' : null,
        isDeleted : isDeleted.value ? 'true' : null,
        hasVerifiedEmail : hasVerifiedEmail.value ? 'true' : null,
    });

    const loadUsers = async () => {
        isLoading.value = true;
        const params    = buildParams();
        const uri       = routeUri('adminUserIndex', {}, params);
        window.history.pushState({ state: routeState.value++ }, '', uri);

        try {
            const response   = await ApiAdminUserList(params);
            users.value      = response.data.users || [];
            total.value      = response.data.total;
            historyUrl.value = response.data.historyUrl;
        }
        catch (error) {
            parseResponseErrors(error);
        }
        finally {
            isLoading.value = false;
        }
    };

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
    };

    const exportAction = () => {
        const params = buildParams();
        const url    = routeUri('adminUserExport', {}, params);
        window.open(url, '_blank');
    };

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

    const onPaginationUpdate = (newSkip) => {
        skip.value = newSkip;
        loadUsers();
    };

    const onPerPageChange = () => {
        skip.value = 0;
        loadUsers();
    };

    const getViewLink  = (id) => routeUri('adminUserView', { id });
    const importUrl    = routeUri('adminUserImportIndex');

    const copyToClipboard = (text) => {
        navigator.clipboard?.writeText(text).then(() => {
            showInfo('Email скопирован');
        }).catch(() => {
            showDanger('Не удалось скопировать');
        });
    };

    const initFromUrl = () => {
        const urlParams   = new URLSearchParams(window.location.search);
        perPage.value     = parseInt(urlParams.get('limit') || '25');
        skip.value        = parseInt(urlParams.get('skip') || '0');
        sortField.value   = urlParams.get('sort_field') || 'id';
        sortOrder.value   = urlParams.get('sort_order') || 'asc';
        search.value      = urlParams.get('search') || '';
        isMember.value    = urlParams.get('isMember') === 'true' ? true : null;
        isNotMember.value = urlParams.get('isMember') === 'false' ? true : null;
        isDeleted.value        = urlParams.get('isDeleted') === 'true' ? true : null;
        hasVerifiedEmail.value = urlParams.get('hasVerifiedEmail') === 'true' ? true : null;
    };

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

    watch(hasVerifiedEmail, () => {
        skip.value = 0;
        loadUsers();
    });

    onMounted(() => {
        initFromUrl();
        loadUsers();
    });

    return {
        canView,
        canCreate,
        clearSearchAction,
        copyToClipboard,
        currentPage,
        exportAction,
        formatDate,
        getViewLink,
        historyUrl,
        importUrl,
        isDeleted,
        isLoading,
        isMember,
        isNotMember,
        hasVerifiedEmail,
        loadUsers,
        onPaginationUpdate,
        onPerPageChange,
        perPage,
        search,
        searchAction,
        sort,
        sortField,
        sortOrder,
        total,
        users,
        has,
    };
}
