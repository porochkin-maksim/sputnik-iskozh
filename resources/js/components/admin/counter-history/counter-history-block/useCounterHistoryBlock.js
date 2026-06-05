import {
    computed,
    onMounted,
    ref,
} from 'vue';

import { usePermissions }   from '@composables/usePermissions.js';
import { useFormat }        from '@composables/useFormat';
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

export function useCounterHistoryBlock (props, emit) {
    const { parseResponseErrors, showInfo, showDanger } = useResponseError();
    const { formatDate }                                = useFormat();
    const { has }                                       = usePermissions();

    const loading        = ref(false);
    const total          = ref(0);
    const perPage        = ref(25);
    const skip           = ref(0);
    const routeState     = ref(0);
    const verifiedStatus = ref('false');
    const histories      = ref([]);
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
    const searchInput    = ref(null);
    let searchTimeout    = null;

    const computedStatuses = computed(() => [
        { value: 'false', label: 'Непроверенные' },
        { value: 'true', label: 'Проверенные' },
    ]);

    const isVerifiedStatus = computed(() => verifiedStatus.value === 'true');

    const canCheckAction = computed(() => histories.value.every(history => history.counterId !== null));
    const canEdit        = computed(() => has('counters', 'edit'));
    const canDrop        = computed(() => has('counters', 'drop'));

    const canSubmitAction = computed(() => checked.value.length && canCheckAction.value);

    const loadHistories = async () => {
        loading.value = true;

        const uri    = new URL(window.location.href);
        const params = {
            limit   : perPage.value,
            skip    : skip.value,
            verified: verifiedStatus.value,
            search  : searchAccount.value,
        };
        Object.keys(params).forEach((key) => {
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
            histories.value = response.data.histories || [];
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
        checked.value = allCheck.value ? histories.value.map(item => String(item.id)) : [];
    };

    const isChecked = (id) => checked.value.includes(String(id));

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
        hideDialog.value = false;
    };

    const closeLinkDialog = () => {
        historyId.value  = null;
        showDialog.value = false;
        hideDialog.value = false;
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
        checked.value.forEach((id) => {
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
        checked.value.forEach((id) => {
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
        searchInput.value?.focus();
    };

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

    return {
        accountId,
        accounts,
        allCheck,
        canCheckAction,
        canEdit,
        canDrop,
        canSubmitAction,
        checked,
        clearSearch,
        closeLinkDialog,
        computedStatuses,
        confirmAction,
        counterId,
        counters,
        deleteAction,
        dropAction,
        formatDate,
        getCounters,
        histories,
        hideDialog,
        historyId,
        isChecked,
        isVerifiedStatus,
        linkAction,
        loadHistories,
        loadedCounters,
        loading,
        onAllCheck,
        onChanged,
        onPaginationUpdate,
        perPage,
        searchAccount,
        searchAction,
        searchInput,
        showDialog,
        showLinkDialog,
        total,
        verifiedStatus,
    };
}
