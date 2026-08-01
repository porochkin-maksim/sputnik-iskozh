<template>
    <div class="row">
        <div class="col-12 col-md-8 col-lg-6">
            <div class="counter-item-hero page-card p-3">
                <div class="d-flex justify-content-between align-items-start gap-3 flex-wrap">
                    <div class="flex-grow-1">
                        <div class="counter-item-hero__title">
                            Счётчик {{ counter.number }}
                        </div>
                        <div v-if="counter.expireAt" class="counter-item-hero__meta">
                            Поверен до {{ formatDate(counter.expireAt) }}
                        </div>
                        <counter-passport-uploader
                            class="mt-2"
                            :counter="counter"
                        />
                    </div>
                    <div class="d-flex flex-md-row flex-column gap-2 counter-item-hero__actions">
                        <button v-if="canAddNewHistory"
                                class="btn btn-sm btn-success"
                                @click="addHistoryValue"
                        >Добавить показания
                        </button>
                        <button v-else
                                class="btn btn-sm btn-success disabled"
                                data-bs-toggle="popover"
                                :data-bs-content="'Добавить показания можно будет в следующем месяце'"
                                data-bs-placement="bottom"
                        >Добавить показания
                        </button>
                        <button class="btn btn-sm btn-outline-success"
                                @click="editIncrement"
                        >Автопоказания
                        </button>
                    </div>
                </div>
            </div>
            <div class="page-section page-card p-0 mt-3 overflow-hidden">
                <counter-history-list
                    :can-load-more="canLoadMore"
                    :format-date="formatDate"
                    :format-money="formatMoney"
                    :histories="histories"
                    :pending="pending"
                    @load-more="loadMore"
                />
            </div>
        </div>
    </div>
    <div class="row mt-2" v-if="histories && histories.length > 1">
        <div class="col-12">
            <h5 class="text-center">График потребления</h5>
            <counter-item-chart-block :histories="histories"></counter-item-chart-block>
        </div>
    </div>
    <counter-add-history-dialog
        v-model:show-dialog="showDialog"
        v-model:hide-dialog="hideDialog"
        v-model:value="value"
        :can-submit-action="canSubmitAction"
        :errors="errors"
        :file="file"
        :pending="pending"
        @append-file="appendFile"
        @hidden="closeAction"
        @remove-file="removeFile"
        @submit="submitAction"
    />

    <counter-increment-dialog
        v-model:show-dialog="showIncrementDialog"
        v-model:hide-dialog="hideIncrementDialog"
        v-model:increment="increment"
        :can-submit-increment-action="canSubmitIncrementAction"
        :errors="errors"
        :pending="pending"
        @hidden="closeIncrementAction"
        @normalize-increment="calculateIncrement"
        @submit="saveIncrementAction"
    />
</template>

<script setup>
import {
    ref,
    computed,
    onMounted,
    defineProps,
}                              from 'vue';
import { useResponseError }    from '@composables/useResponseError';
import { useFormat }           from '@composables/useFormat';
import { usePermissions }      from '@composables/usePermissions.js';
import CounterItemChartBlock   from '@components/shared/counters/CounterItemChartBlock.vue';
import CounterAddHistoryDialog from './counter-item/CounterAddHistoryDialog.vue';
import CounterHistoryList      from './counter-item/CounterHistoryList.vue';
import CounterIncrementDialog  from './counter-item/CounterIncrementDialog.vue';
import CounterPassportUploader from './counter-item/CounterPassportUploader.vue';
import {
    ApiProfileCounterHistoryList,
    ApiProfileCounterAddValue,
    ApiProfileCountersIncrementSave,
}                              from '@api';

const props = defineProps({
    counter: {
        type    : Object,
        required: true,
    },
});

const { errors, parseResponseErrors, showSuccess } = useResponseError();
const { formatMoney, formatDate }                  = useFormat();
const { has }                                      = usePermissions();

const loaded              = ref(false);
const pending             = ref(false);
const showDialog          = ref(false);
const hideDialog          = ref(false);
const showIncrementDialog = ref(false);
const hideIncrementDialog = ref(false);
const value               = ref(null);
const file                = ref(null);
const histories           = ref([]);
const skip                = ref(0);
const total               = ref(null);
const limit               = ref(0);
const increment           = ref(props.counter.increment || 0);

onMounted(() => {
    listAction();
});

const loadMore = () => {
    listAction();
};

const listAction = () => {
    pending.value = true;
    skip.value += limit.value;

    ApiProfileCounterHistoryList({
        counter_id: props.counter.id,
        skip      : skip.value,
    })
        .then(response => {
            response.data.histories?.forEach(history => {
                const exists = histories.value.some(item => item.id === history.id);
                if (!exists) {
                    histories.value.push(history);
                }
            });

            total.value = response.data.total;
            limit.value = response.data.limit;
        })
        .catch(response => {
            parseResponseErrors(response);
        })
        .finally(() => {
            loaded.value  = true;
            pending.value = false;
        });
};

const submitAction = () => {
    if (pending.value) {
        return;
    }
    addHistoryValueAction();
};

const addHistoryValueAction = () => {
    pending.value = true;
    const form    = new FormData();
    form.append('counter_id', props.counter.id);
    form.append('value', value.value);
    form.append('file', file.value);

    ApiProfileCounterAddValue({}, form)
        .then(() => {
            onSuccessSubmit();
        })
        .catch(response => {
            parseResponseErrors(response);
        })
        .finally(() => {
            pending.value = false;
        });
};

const onSuccessSubmit = () => {
    showDialog.value = false;
    hideDialog.value = true;
    file.value       = null;
    showSuccess('Показания добавлены');
    skip.value      = 0;
    limit.value     = 0;
    histories.value = [];
    listAction();
};

const addHistoryValue = () => {
    if (!canAddNewHistory.value) {
        return;
    }

    const lastHistory = histories.value[0];
    if (lastHistory) {
        value.value = lastHistory.value + lastHistory.delta;
    }
    else {
        value.value = props.counter.value;
    }
    showDialog.value = true;
};

const closeAction = () => {
    showDialog.value = false;
};

const appendFile = (event) => {
    file.value = event.target.files[0];
};

const removeFile = () => {
    file.value = null;
};

const editIncrement = () => {
    showIncrementDialog.value = true;
};

const closeIncrementAction = () => {
    showIncrementDialog.value = false;
};

const calculateIncrement = () => {
    increment.value = increment.value < 0 ? increment.value * -1 : increment.value;
};

const saveIncrementAction = () => {
    if (pending.value) {
        return;
    }

    pending.value = true;
    const form    = new FormData();
    form.append('id', props.counter.id);
    form.append('increment', increment.value);

    ApiProfileCountersIncrementSave({}, {
        id       : props.counter.id,
        increment: increment.value,
    })
        .then(() => {
            onIncrementSuccessSubmit();
        })
        .catch(response => {
            parseResponseErrors(response);
        })
        .finally(() => {
            pending.value = false;
        });
};

const onIncrementSuccessSubmit = () => {
    showSuccess('Данные сохранены');
    showIncrementDialog.value = false;
    hideIncrementDialog.value = true;
};

const canSubmitAction          = computed(() => value.value && file.value);
const canAddNewHistory         = computed(() => {
    if (!loaded.value || !histories.value) {
        return false;
    }
    const lastHistory = histories.value[0];
    return lastHistory ? lastHistory.actions.create : true;
});
const canLoadMore              = computed(() => total.value && histories.value.length < total.value);
const canSubmitIncrementAction = computed(() => increment.value !== null);
</script>
