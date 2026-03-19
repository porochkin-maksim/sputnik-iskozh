<template>
    <div v-if="loaded && (!periods || !periods.length)">
        <div class="alert alert-warning">
            <p><i class="fa fa-warning"></i> Не найдено ни одного периода</p>
            <a :href="Url.Routes.adminPeriodIndex.uri">
                Создайте период
            </a>
        </div>
    </div>
    <div v-if="loaded && periods && periods.length && services">
        <div class="d-flex align-items-center justify-content-between mb-2">
            <div class="d-flex">
                <button class="btn btn-success me-2"
                        v-if="actions.edit"
                        @click="showCreateDialog">Добавить услугу
                </button>
            </div>
            <history-btn
                class="btn-link underline-none"
                :url="historyUrl" />
        </div>
        <div>
            <div v-for="period in periods" :key="period.value">
                <div class="fw-bold mb-2">
                    Период «{{ period.value }}»
                </div>
                <table class="table table-sm">
                    <thead>
                    <tr>
                        <th>ID</th>
                        <th>Название</th>
                        <th>Тариф</th>
                        <th>Тип</th>
                        <th class="text-center">Действия</th>
                    </tr>
                    </thead>
                    <tbody>
                    <tr v-for="(service, index) in services.filter(s => s.periodId && period.value && parseInt(s.periodId) === parseInt(period.value))"
                        :key="service.id">
                        <td>{{ service.id }}</td>
                        <td>{{ service.name }}</td>
                        <td>{{ formatMoney(service.cost) }}</td>
                        <td>{{ service.typeName }}</td>
                        <td>
                            <div class="d-flex justify-content-center">
                                <history-btn
                                    class="btn-link underline-none"
                                    :url="service.historyUrl" />
                                <div class="dropdown">
                                    <a class="btn btn-sm btn-light border"
                                       href="#"
                                       role="button"
                                       :id="'dropDown'+index+vueId"
                                       data-bs-toggle="dropdown"
                                       :class="{'disabled opacity-50': !(actions.edit || actions.drop)}"
                                       aria-expanded="false">
                                        <i class="fa fa-bars"></i>
                                    </a>
                                    <ul class="dropdown-menu"
                                        :aria-labelledby="'dropDown'+index+vueId">
                                        <li v-if="actions.edit">
                                            <a class="dropdown-item cursor-pointer"
                                               @click="showEditDialog(service)">
                                                <i class="fa fa-edit"></i> Редактировать
                                            </a>
                                        </li>
                                        <li v-if="actions.drop">
                                            <a class="dropdown-item cursor-pointer text-danger"
                                               @click="deleteAction(service)">
                                                <i class="fa fa-trash"></i> Удалить
                                            </a>
                                        </li>
                                    </ul>
                                </div>
                            </div>
                        </td>
                    </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <service-edit-dialog
        v-model:model-value="selectedService"
        v-model:show="showDialog"
        :types="types"
        :periods="periods"
        @update:model-value="onServiceUpdated" />
</template>

<script setup>
import {
    ref,
    onMounted,
    defineOptions,
}                           from 'vue';
import { useResponseError } from '@composables/useResponseError';
import { useFormat }        from '@composables/useFormat';
import HistoryBtn           from '../../common/HistoryBtn.vue';
import ServiceEditDialog    from './ServiceEditDialog.vue';
import {
    ApiAdminServiceList,
    ApiAdminServiceCreate,
    ApiAdminServiceDelete,
}                           from '@api';
import Url                  from '@utils/Url.js';

defineOptions({
    name: 'ServicesBlock',
});

const { parseResponseErrors, showInfo } = useResponseError();
const { formatMoney }                   = useFormat();

const services        = ref([]);
const periods         = ref([]);
const types           = ref([]);
const historyUrl      = ref(null);
const loaded          = ref(false);
const actions         = ref({});
const selectedService = ref(null);
const showDialog      = ref(false);
const vueId           = ref('uuid-' + Date.now() + '-' + Math.random().toString(36).substring(2, 9));

const listAction = async () => {
    try {
        const response   = await ApiAdminServiceList();
        services.value   = response.data.services || [];
        actions.value    = response.data.actions;
        types.value      = response.data.types;
        periods.value    = response.data.periods;
        historyUrl.value = response.data.historyUrl;
    }
    catch (error) {
        parseResponseErrors(error);
    }
    finally {
        loaded.value = true;
    }
};

const showCreateDialog = async () => {
    try {
        const response        = await ApiAdminServiceCreate();
        selectedService.value = response.data.service;
        showDialog.value      = true;
    }
    catch (error) {
        parseResponseErrors(error);
    }
};

const showEditDialog = (service) => {
    if (!actions.value.edit) {
        return;
    }
    selectedService.value = service;
    showDialog.value      = true;
};

const onServiceUpdated = () => {
    listAction();
};

const deleteAction = async (service) => {
    if (!confirm('Удалить услугу?')) {
        return;
    }

    try {
        await ApiAdminServiceDelete({ id: service.id });
        services.value = services.value.filter(s => s.id !== service.id);
        showInfo('Услуга удалена');
    }
    catch (error) {
        parseResponseErrors(error);
    }
};

onMounted(listAction);
</script>