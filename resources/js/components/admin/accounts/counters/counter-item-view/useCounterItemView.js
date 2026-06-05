import {
    computed,
    onMounted,
    ref,
} from 'vue';

import { useFormat }        from '@composables/useFormat';
import { usePermissions }   from '@composables/usePermissions.js';
import { useResponseError } from '@composables/useResponseError';
import {
    ApiAdminCounterHistoryList,
    ApiAdminRequestsCounterHistoryDelete,
    ApiAdminRequestsCounterHistoryCreateClaim,
}                           from '@api';

export function useCounterItemView (props) {
    const { parseResponseErrors, showInfo, showDanger } = useResponseError();
    const { formatDate, formatMoney }                   = useFormat();
    const { has }                                       = usePermissions();

    const vueId           = ref('uuid_' + Date.now() + '_' + Math.random());
    const limit           = ref(10);
    const total           = ref(0);
    const loading         = ref(false);
    const counter         = ref(null);
    const histories       = ref([]);
    const selectedHistory = ref(null);
    const showCounterForm = ref(false);
    const canEdit         = computed(() => has('counters', 'edit'));
    const canDrop         = computed(() => has('counters', 'drop'));

    const init = () => {
        counter.value = props.modelValue;
        refreshData();
    };

    const loadHistory = async (isRefresh = false) => {
        loading.value = true;

        try {
            const response = await ApiAdminCounterHistoryList(counter.value.id, {
                limit: limit.value,
                skip : isRefresh ? 0 : histories.value.length,
            });

            if (isRefresh) {
                histories.value = response.data.histories || [];
            }
            else {
                const newHistories = response.data.histories || [];
                newHistories.forEach((history) => {
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

    const refreshData = async () => {
        if (loading.value) {
            return;
        }

        histories.value = [];
        total.value     = 0;
        await loadHistory(true);
    };

    const onCounterUpdated = () => {
        showCounterForm.value = false;
    };

    const loadMore = () => {
        loadHistory(false);
    };

    const addHistoryAction = () => {
        selectedHistory.value = {};
    };

    const editHistoryAction = (history) => {
        selectedHistory.value = history;
    };

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

    const onHistoryUpdated = () => {
        refreshData();
        selectedHistory.value = null;
    };

    const editCounterAction = (counter) => {
        showCounterForm.value = true;
    };

    onMounted(init);

    return {
        canEdit,
        canDrop,
        addClaimForHistory,
        addHistoryAction,
        counter,
        dropHistoryAction,
        editHistoryAction,
        formatDate,
        formatMoney,
        histories,
        loading,
        loadMore,
        onHistoryUpdated,
        selectedHistory,
        total,
        vueId,
        refreshData,
        showCounterForm,
        onCounterUpdated,
        editCounterAction,
    };
}
