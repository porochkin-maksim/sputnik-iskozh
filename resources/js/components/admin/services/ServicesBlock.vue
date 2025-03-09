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
        <div class="d-flex align-items-center mb-2">
            <button class="btn btn-success"
                    v-on:click="makeAction">Добавить услугу
            </button>
            <history-btn
                class="btn-link underline-none"
                :url="historyUrl" />
        </div>
        <div v-for="period in periods">
            <div class="fw-bold mb-2">
                Период «{{ period.value }}»
            </div>
            <div v-for="(service, index) in services">
                <service-item-edit v-if="parseInt(service.periodId) === parseInt(period.key)"
                                   :model-value="service"
                                   :types="types"
                                   :periods="periods"
                                   :index="index"
                                   class="mb-2" />
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
            })
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
