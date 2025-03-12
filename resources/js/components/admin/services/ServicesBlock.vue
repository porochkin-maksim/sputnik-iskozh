<template>
    <div v-if="loaded && (!periods || !periods.length)">
        <div class="alert alert-warning">
            <p><i class="fa fa-warning"></i> Не найдено ни одного периода</p>
            <a :href="Url.Routes.adminPeriodIndex.uri">
                Создайте период
            </a>
        </div>
    </div>
    <div v-if="periods && periods.length">
        <div class="d-flex align-items-center justify-content-between mb-2">
            <div class="d-flex">
                <button class="btn btn-success me-2"
                        v-if="actions.edit"
                        v-on:click="makeAction">Добавить услугу
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
                <template v-if="actions.edit">
                    <div v-for="service in services">
                        <service-item-edit :model-value="service"
                                           :types="types"
                                           :periods="periods"
                                           v-if="parseInt(service.periodId) === parseInt(period.key)" />
                    </div>
                </template>
                <template v-else>
                    <table class="table table-sm">
                        <thead>
                        <tr>
                            <th>ID</th>
                            <th>Активен</th>
                            <th>Название</th>
                            <th>Тариф</th>
                            <th>Период</th>
                            <th>Тип</th>
                            <th></th>
                        </tr>
                        </thead>
                        <tbody>
                        <tr v-for="service in services"
                            class="align-middle">
                            <service-item-edit :model-value="service"
                                               :types="types"
                                               :periods="periods"
                                               v-if="parseInt(service.periodId) === parseInt(period.key)" />
                        </tr>
                        </tbody>
                    </table>
                </template>
            </div>
        </div>
    </div>
</template>

<script>
import ResponseError   from '../../../mixin/ResponseError.js';
import Url             from '../../../utils/Url.js';
import ServiceItemEdit from './ServiceItemEdit.vue';
import HistoryBtn      from '../../common/HistoryBtn.vue';

export default {
    name      : 'ServicesBlock',
    components: { HistoryBtn, ServiceItemEdit },
    mixins    : [
        ResponseError,
    ],
    data () {
        return {
            services  : [],
            periods   : [],
            types     : [],
            historyUrl: null,
            Url       : Url,
            loaded    : false,
            actions   : {},
        };
    },
    created () {
        this.listAction();
    },
    methods: {
        makeAction () {
            window.axios[Url.Routes.adminServiceCreate.method](Url.Routes.adminServiceCreate.uri).then(response => {
                let service      = response.data.service;
                service.periodId = this.periods[0].key;
                this.services.push(service);
            }).catch(response => {
                this.parseResponseErrors(response);
            });
        },
        listAction () {
            window.axios[Url.Routes.adminServiceList.method](Url.Routes.adminServiceList.uri).then(response => {
                response.data.services?.forEach(service => {
                    let exists = false;
                    this.services.forEach(item => {
                        if (item.id === service.id) {
                            exists = true;
                        }
                    });
                    if (!exists) {
                        this.services.push(service);
                    }
                });
                this.actions    = response.data.actions;
                this.types      = response.data.types;
                this.periods    = response.data.periods;
                this.historyUrl = response.data.historyUrl;
            }).catch(response => {
                this.parseResponseErrors(response);
            }).then(() => {
                this.loaded = true;
            });
        },
    },
};
</script>
