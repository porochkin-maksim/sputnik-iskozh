<template>
    <div class="row">
        <div class="col-12 col-md-8 col-lg-6">
            <div class="border bg-light d-flex justify-content-between align-items-start p-2">
                <div>
                    <div>
                        <b>Счётчик {{ counter.number }}</b>
                    </div>
                    <div v-if="counter.expireAt">
                        Поверен до {{ formatDate(counter.expireAt) }}
                    </div>
                    <file-item :file="counter.passport"
                               v-if="counter.passport"
                               :name="'Паспорт'" />
                </div>
                <div class="d-flex flex-md-row flex-column">
                    <button v-if="canAddNewHistory"
                            class="btn btn-sm btn-success mb-md-0 mb-2"
                            @click="addHistoryValue"
                    >Добавить показания
                    </button>
                    <button v-else
                            class="btn btn-sm btn-success mb-md-0 mb-2 disabled"
                            data-bs-toggle="popover"
                            :data-bs-content="'Добавить показания можно будет в следующем месяце'"
                            data-bs-placement="bottom"
                    >Добавить показания
                    </button>
                    <button class="btn btn-sm btn-outline-success ms-md-2 ms-0 "
                            @click="editIncrement"
                    >Автопоказания
                    </button>
                </div>
            </div>
            <template v-for="history in histories">
                <div class="border border-top-0 p-2">
                    <div>
                        <b>Показания:</b> {{ history.value.toLocaleString('ru-RU') }}{{ history.delta === null ? '' : ' (' + history.delta.toLocaleString('ru-RU') + 'кВт)' }}
                    </div>
                    <div class="mt-1">
                        <b>Дата:</b> {{ formatDate(history.date) }}{{ history.days === null ? '' : ' (+' + history.days + ' дней)' }}
                    </div>
                    <div class="mt-1">
                        <b>Статус:</b>
                        <b :class="history.isVerified ? 'text-success' : 'text-secondary'">&nbsp;{{ history.isVerified ? 'Проверено' : 'Не проверено' }}</b>
                    </div>
                    <div class="mt-1"
                         v-if="history.claim">
                        <b>Оплачено:</b> {{ formatMoney(history.claim.paid) }}/{{ formatMoney(history.claim.cost) }} по тарифу {{ formatMoney(history.claim.tariff) }}
                    </div>
                    <div class="mt-1">
                        <file-item :file="history.file"
                                   v-if="history.file"
                                   :edit="false"
                        />
                    </div>
                </div>
            </template>
            <template v-if="canLoadMore">
                <div class="d-flex justify-content-center border border-top-0 p-2">
                    <button class="btn btn-link"
                            v-if="!pending"
                            @click="loadMore">
                        Показать ещё
                    </button>
                    <button class="btn border-0"
                            disabled
                            v-else>
                        <i class="fa fa-spinner fa-spin"></i> Подгрузка
                    </button>
                </div>
            </template>
        </div>
    </div>
    <div class="row mt-2" v-if="histories && histories.length">
        <div class="col-12 col-md-8 col-lg-6">
            <h5 class="text-center">График показаний</h5>
            <counter-item-chart-block :histories="histories"></counter-item-chart-block>
        </div>
    </div>
    <view-dialog v-model:show="showDialog"
                 v-model:hide="hideDialog"
                 @hidden="closeAction"
    >
        <template v-slot:title>Внесение показаний счётчика</template>
        <template v-slot:body>
            <div class="container-fluid">
                <div class="mt-2">
                    <custom-input v-model="value"
                                  :errors="errors.value"
                                  type="number"
                                  label="Текущие показания на счётчике"
                                  :required="true"
                    />
                </div>
                <div class="mt-2">
                    <div v-if="file">
                        <button class="btn btn-sm btn-danger"
                                @click="removeFile">
                            <i class="fa fa-trash"></i>
                        </button>
                        &nbsp;
                        {{ file.name }}
                    </div>
                    <template v-else>
                        <button class="btn btn-outline-secondary"
                                @click="chooseFile"
                                v-if="!file">
                            <i class="fa fa-paperclip "></i>&nbsp;Фото счётчика
                        </button>
                        <input class="d-none"
                               type="file"
                               ref="fileElem"
                               accept="image/*"
                               @change="appendFile"
                        />
                    </template>
                </div>
            </div>
        </template>
        <template v-slot:footer>
            <button class="btn btn-success"
                    v-if="!pending"
                    @click="submitAction"
                    :disabled="!canSubmitAction">
                Добавить
            </button>
            <button class="btn border-0"
                    disabled
                    v-else>
                <i class="fa fa-spinner fa-spin"></i> Добавление
            </button>
        </template>
    </view-dialog>
    <view-dialog v-model:show="showIncrementDialog"
                 v-model:hide="hideIncrementDialog"
                 @hidden="closeIncrementAction"
    >
        <template v-slot:title>Изменение автопоказаний</template>
        <template v-slot:body>
            <div class="container-fluid">
                <div class="mt-2">
                    <custom-input v-model="increment"
                                  :errors="errors.increment"
                                  type="number"
                                  :min="0"
                                  :step="1"
                                  label="Ежемесячное увеличение показаний на кВт"
                                  :required="true"
                                  @focusout="calculateIncrement"
                    />
                </div>
            </div>
        </template>
        <template v-slot:footer>
            <button class="btn btn-success"
                    v-if="!pending"
                    @click="saveIncrementAction"
                    :disabled="!canSubmitIncrementAction">
                Сохранить
            </button>
            <button class="btn border-0"
                    disabled
                    v-else>
                <i class="fa fa-spinner fa-spin"></i> Сохранение
            </button>
        </template>
    </view-dialog>
</template>

<script setup>
import {
    ref,
    computed,
    onMounted,
    defineProps,
}                            from 'vue';
import { useResponseError }  from '@composables/useResponseError';
import { useFormat }         from '@composables/useFormat';
import CustomInput           from '@common/form/CustomInput.vue';
import ViewDialog            from '@common/ViewDialog.vue';
import FileItem              from '@common/files/FileItem.vue';
import CounterItemChartBlock from '@common/blocks/CounterItemChartBlock.vue';
import {
    ApiProfileCounterHistoryList,
    ApiProfileCounterAddValue,
    ApiProfileCountersIncrementSave,
}                            from '@api';

const props = defineProps({
    counter: {
        type    : Object,
        required: true,
    },
});

const { errors, parseResponseErrors, showSuccess } = useResponseError();
const { formatMoney, formatDate }                  = useFormat();

const loaded              = ref(false);
const pending             = ref(false);
const showDialog          = ref(false);
const hideDialog          = ref(false);
const showIncrementDialog = ref(false);
const hideIncrementDialog = ref(false);
const value               = ref(null);
const file                = ref(null);
const fileElem            = ref(null);
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
    location.reload();
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

const chooseFile = () => {
    fileElem.value?.click();
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