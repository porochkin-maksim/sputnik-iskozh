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
            <div v-for="period in periods">
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
                    <tr v-for="(service, index) in services.filter(s => s.periodId && period.key && parseInt(s.periodId) === parseInt(period.key))"
                        :key="service.id">
                        <td>{{ service.id }}</td>
                        <td>{{ service.name }}</td>
                        <td>{{ $formatMoney(service.cost) }}</td>
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

<script>
import ResponseError     from '../../../mixin/ResponseError.js';
import Url               from '../../../utils/Url.js';
import HistoryBtn        from '../../common/HistoryBtn.vue';
import ServiceEditDialog from './ServiceEditDialog.vue';

export default {
    name      : 'ServicesBlock',
    components: {
        HistoryBtn,
        ServiceEditDialog,
    },
    mixins    : [ResponseError],
    data () {
        return {
            services       : [],
            periods        : [],
            types          : [],
            historyUrl     : null,
            Url            : Url,
            loaded         : false,
            actions        : {},
            selectedService: null,
            showDialog     : false,
            vueId          : null,
        };
    },
    created () {
        this.vueId = 'uuid' + this.$_uid;
        this.listAction();
    },
    methods: {
        showCreateDialog () {
            window.axios[Url.Routes.adminServiceCreate.method](Url.Routes.adminServiceCreate.uri)
                .then(response => {
                    this.selectedService = response.data.service;
                    this.showDialog      = true;
                })
                .catch(response => {
                    this.parseResponseErrors(response);
                });
        },
        showEditDialog (service) {
            if (!this.actions.edit) {
                return;
            }
            this.selectedService = service;
            this.showDialog      = true;
        },
        onServiceUpdated () {
            this.listAction();
        },
        deleteAction (service) {
            if (!confirm('Удалить услугу?')) {
                return;
            }

            let uri = Url.Generator.makeUri(Url.Routes.adminServiceDelete, {
                id: service.id,
            });
            window.axios[Url.Routes.adminServiceDelete.method](uri)
                .then(() => {
                    this.services = this.services.filter(s => s.id !== service.id);
                    this.showInfo('Услуга удалена');
                })
                .catch(response => {
                    this.parseResponseErrors(response);
                });
        },
        listAction () {
            window.axios[Url.Routes.adminServiceList.method](Url.Routes.adminServiceList.uri)
                .then(response => {
                    this.services   = response.data.services || [];
                    this.actions    = response.data.actions;
                    this.types      = response.data.types;
                    this.periods    = response.data.periods;
                    this.historyUrl = response.data.historyUrl;
                })
                .catch(response => {
                    this.parseResponseErrors(response);
                })
                .then(() => {
                    this.loaded = true;
                });
        },
    },
};
</script>

<style scoped>
.cursor-pointer {
    cursor : pointer;
}

.dropdown-item {
    cursor : pointer;
}

.disabled {
    pointer-events : none;
}
</style>
