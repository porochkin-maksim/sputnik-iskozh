import {
    computed,
    onMounted,
    ref,
    watch,
} from 'vue';

import { useResponseError } from '@composables/useResponseError';
import {
    ApiAdminCounterDelete,
    ApiAdminCounterList,
}                           from '@api';
import { usePermissions }   from '@composables/usePermissions.js';

export function useCountersBlock (props) {
    const { parseResponseErrors, showInfo, showDanger } = useResponseError();
    const { has } = usePermissions();

    const canCreate   = computed(() => has('counters', 'edit'));

    const vueId           = ref('uuid_' + Date.now() + '_' + Math.random());
    const loading         = ref(false);
    const counters        = ref([]);
    const period          = ref(null);
    const selectedCounter = ref(null);
    const showCounterForm = ref(false);

    const loadCounters = async () => {
        loading.value = true;

        try {
            if (!props.account?.id) {
                return;
            }

            const response = await ApiAdminCounterList(props.account.id);
            counters.value = response.data.counters || [];
            period.value   = response.data.period;
        }
        catch (error) {
            parseResponseErrors(error);
        }
        finally {
            loading.value = false;
        }
    };

    const addCounterAction = () => {
        selectedCounter.value = null;
        showCounterForm.value = true;
    };

    const editCounterAction = (counter) => {
        selectedCounter.value = counter;
        showCounterForm.value = true;
    };

    const dropCounterAction = async (counter) => {
        if (!confirm('Удалить счётчик?')) {
            return;
        }

        try {
            if (!props.account?.id) {
                return;
            }

            const response = await ApiAdminCounterDelete(counter.id);
            if (response.data) {
                await loadCounters();
                showInfo('Счётчик удалён');
            }
            else {
                showDanger('Счётчик не удалён');
            }
        }
        catch (error) {
            parseResponseErrors(error);
        }
    };

    const onCounterUpdated = () => {
        loadCounters();
        showCounterForm.value = false;
        selectedCounter.value = null;
    };

    watch(() => props.account?.id, (newId, oldId) => {
        if (newId && newId !== oldId) {
            loadCounters();
        }
    }, { immediate: true });

    onMounted(() => {
        if (props.account?.id) {
            loadCounters();
        }
    });

    return {
        addCounterAction,
        counters,
        canCreate,
        dropCounterAction,
        editCounterAction,
        loading,
        loadCounters,
        onCounterUpdated,
        period,
        selectedCounter,
        showCounterForm,
        vueId,
    };
}
