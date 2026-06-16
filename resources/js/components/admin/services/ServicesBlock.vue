<template>
    <div v-if="loaded && (!periods || !periods.length)">
        <div class="alert alert-warning">
            <p><i class="fa fa-warning"></i> Не найдено ни одного периода</p>
            <a :href="periodIndexUrl">
                Создайте период
            </a>
        </div>
    </div>
    <div v-if="loaded && periods && periods.length && services">
        <div class="d-flex align-items-center justify-content-between mb-2">
            <div class="d-flex">
                <button class="btn btn-success me-2"
                        v-if="canEdit"
                        @click="showCreateDialog">Добавить услугу
                </button>
            </div>
            <history-btn
                class="btn-link underline-none"
                :url="historyUrl" />
        </div>
        <div>
            <table class="table table-sm admin-table-firm">
                <tbody>
                <template v-for="period in periodsInfo" :key="period.id">
                    <tr class="text-center text-success table-success">
                        <th colspan="5">
                            <b>Период «{{ period.name }}»</b> <span
                            class="text-muted small">{{ formatDate(period.startAt) }} - {{ formatDate(period.endAt) }}</span>
                        </th>
                    </tr>
                    <tr class="text-end">
                        <th class="table-thin-column">ID</th>
                        <th>Тип</th>
                        <th>Название</th>
                        <th class="pe-3">Тариф</th>
                        <th class="text-center table-thin-column">Действия</th>
                    </tr>
                    <tr v-for="service in services.filter(s => s.periodId && period.id && parseInt(s.periodId) === parseInt(period.id))"
                        :key="service.id">
                        <td class="table-thin-column text-end">{{ service.id }}</td>
                        <td class="text-end">{{ service.typeName }}</td>
                        <td class="text-end">{{ service.name }}</td>
                        <td class="text-end pe-3">{{ formatMoney(service.cost) }}</td>
                        <td class="table-thin-column">
                            <div class="d-flex justify-content-end gap-1 flex-nowrap">
                                <button
                                    v-if="canEdit && service.actions?.edit"
                                    class="btn btn-sm btn-outline-success admin-action-btn"
                                    @click="showEditDialog(service)"
                                >
                                    <i class="fa fa-edit"></i>
                                </button>
                                <button
                                    v-if="canDrop && service.actions?.drop"
                                    class="btn btn-sm btn-outline-danger admin-action-btn"
                                    @click="deleteAction(service)"
                                >
                                    <i class="fa fa-trash"></i>
                                </button>
                                <history-btn
                                    class="btn-link underline-none"
                                    :url="service.historyUrl" />
                            </div>
                        </td>
                    </tr>
                </template>
                </tbody>
            </table>
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
    computed,
    onMounted,
}                           from 'vue';
import { useResponseError } from '@composables/useResponseError';
import { usePermissions }   from '@composables/usePermissions.js';
import { useFormat }        from '@composables/useFormat';
import HistoryBtn           from '@common/HistoryBtn.vue';
import ServiceEditDialog    from './ServiceEditDialog.vue';
import {
    ApiAdminServiceList,
    ApiAdminServiceCreate,
    ApiAdminServiceDelete,
}                           from '@api';
import { routeUri }         from '@utils/routeUri.js';

const { parseResponseErrors, showInfo } = useResponseError();
const { formatMoney, formatDate }       = useFormat();
const { has }                           = usePermissions();

const services        = ref([]);
const periods         = ref([]);
const periodsInfo     = ref([]);
const types           = ref([]);
const historyUrl      = ref(null);
const loaded          = ref(false);
const selectedService = ref(null);
const showDialog      = ref(false);
const periodIndexUrl  = routeUri('adminPeriodIndex');
const canEdit         = computed(() => has('services', 'edit'));
const canDrop         = computed(() => has('services', 'drop'));

const listAction = async () => {
    try {
        const response    = await ApiAdminServiceList();
        services.value    = response.data.services || [];
        types.value       = response.data.types;
        periods.value     = response.data.periods;
        periodsInfo.value = response.data.periodsInfo;
        historyUrl.value  = response.data.historyUrl;
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
    if (!canEdit.value) {
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
        await ApiAdminServiceDelete(service.id);
        services.value = services.value.filter(s => s.id !== service.id);
        showInfo('Услуга удалена');
    }
    catch (error) {
        parseResponseErrors(error);
    }
};

onMounted(listAction);
</script>
