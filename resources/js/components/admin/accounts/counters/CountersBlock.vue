<template>
    <div>
        <h5>Счётчики</h5>

        <loading-spinner
            v-if="loading && counters.length === 0"
            size="lg"
            color="primary"
            text="Загрузка счётчиков..."
            wrapper-class="py-5"
        />

        <template v-else>
            <table class="table align-middle m-0 text-center"
                   v-if="counters && counters.length">
                <thead>
                <tr>
                    <th>Номер</th>
                    <th>Показание</th>
                    <th>Дата</th>
                    <th>Счета</th>
                    <th>Авто</th>
                    <th>Поверка</th>
                    <th></th>
                </tr>
                </thead>
                <tbody>
                <tr v-for="counter in counters" :key="counter.id">
                    <td><a :href="counter.viewUrl">{{ counter.number }}</a></td>
                    <td>{{ counter.value }}</td>
                    <td>{{ counter.date }}</td>
                    <td>{{ counter.isInvoicing ? 'да' : 'нет' }}</td>
                    <td>{{ counter.increment ? '+' + counter.increment : '-' }}</td>
                    <td>
                        <div v-if="counter.expireAt">
                            {{ formatDate(counter.expireAt) }}
                        </div>
                        <file-item :file="counter.passport"
                                   v-if="counter.passport"
                                   :show-download="false"
                                   :name="'Паспорт'" />
                    </td>
                    <td>
                        <div class="dropdown">
                            <a class="btn btn-sm btn-light border"
                               href="#"
                               role="button"
                               :id="'dropDown' + counter.id + vueId"
                               data-bs-toggle="dropdown"
                               aria-expanded="false">
                                <i class="fa fa-bars"></i>
                            </a>
                            <ul class="dropdown-menu"
                                :aria-labelledby="'dropDown' + counter.id + vueId">
                                <li>
                                    <a class="dropdown-item cursor-pointer"
                                       v-if="counter.actions?.edit"
                                       @click.prevent="editCounterAction(counter)">
                                        <i class="fa fa-edit"></i> Редактировать
                                    </a>
                                </li>
                                <li v-if="counter.actions?.drop">
                                    <a class="dropdown-item cursor-pointer text-danger"
                                       @click="dropCounterAction(counter)">
                                        <i class="fa fa-trash"></i> Удалить
                                    </a>
                                </li>
                                <li>
                                    <history-btn class="dropdown-item btn btn-link text-decoration-none"
                                                 :url="counter.historyUrl" />
                                </li>
                            </ul>
                        </div>
                    </td>
                </tr>
                </tbody>
            </table>
            <div class="d-flex align-items-center justify-content-between mt-2">
                <div class="d-flex">
                    <button class="btn btn-success me-2"
                            v-if="account?.actions?.counters?.edit && !period?.isClosed"
                            @click="addCounterAction">
                        Добавить счётчик
                    </button>
                </div>
            </div>
        </template>
    </div>
    <counter-item
        :account="account"
        :counter="selectedCounter"
        :show-form="showCounterForm"
        @counter-updated="onCounterUpdated"
    />
</template>

<script setup>
import {
    ref,
    watch,
    onMounted,
}                           from 'vue';
import FileItem             from '../../../common/files/FileItem.vue';
import CounterItem          from './CounterItem.vue';
import HistoryBtn           from '../../../common/HistoryBtn.vue';
import LoadingSpinner       from '@common/LoadingSpinner.vue';
import { useResponseError } from '@composables/useResponseError';
import {
    ApiAdminCounterList,
    ApiAdminCounterDelete,
}                           from '@api';
import { useFormat }        from '@composables/useFormat.js';

const props = defineProps({
    account: {
        type    : Object,
        required: true,
    },
});

const { parseResponseErrors, showInfo, showDanger } = useResponseError();
const { formatDate }                                = useFormat();

// Состояния
const vueId           = ref('uuid_' + Date.now() + '_' + Math.random());
const loading         = ref(false);
const counters        = ref([]);
const period          = ref(null);
const selectedCounter = ref(null);
const showCounterForm = ref(false);

// Загрузка списка счётчиков
const loadCounters = async () => {
    loading.value = true;
    try {
        const response = await ApiAdminCounterList(props.account.id);
        counters.value = response.data.counters || [];
        period.value   = response.data.period;
    }
    catch (error) {

        console.log(props.account.id);
        parseResponseErrors(error);
    }
    finally {
        loading.value = false;
    }
};

// Добавление счётчика
const addCounterAction = () => {
    selectedCounter.value = null;
    showCounterForm.value = true;
};

// Редактирование счётчика
const editCounterAction = (counter) => {
    selectedCounter.value = counter;
    showCounterForm.value = true;
};

// Удаление счётчика
const dropCounterAction = async (counter) => {
    if (!confirm('Удалить счётчик?')) {
        return;
    }

    try {
        const response = await ApiAdminCounterDelete(props.account.id, counter.id);
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

// Обновление после добавления/редактирования счётчика
const onCounterUpdated = () => {
    loadCounters();
    showCounterForm.value = false;
    selectedCounter.value = null;
};

// Следим за изменением account.id
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
</script>