<template>
    <div class="d-flex align-items-center mb-2">
        <button class="btn btn-success"
                v-on:click="makeAction">Добавить период
        </button>
        <history-btn
            class="btn-link underline-none"
            :url="historyUrl" />
    </div>
    <div>
        <div v-for="(period, index) in periods">
            <period-item-edit :model-value="period"
                              :index="index"
                              class="mb-2" />
        </div>
    </div>
</template>

<script>
import ResponseError  from '../../../mixin/ResponseError.js';
import Url            from '../../../utils/Url.js';
import PeriodItemEdit from './PeriodItemEdit.vue';
import HistoryBtn     from '../../common/HistoryBtn.vue';

export default {
    name      : 'PeriodsBlock',
    components: { HistoryBtn, PeriodItemEdit },
    mixins    : [
        ResponseError,
    ],
    data () {
        return {
            periods   : [],
            historyUrl: null,
        };
    },
    created () {
        this.listAction();
    },
    methods: {
        makeAction () {
            window.axios[Url.Routes.adminPeriodCreate.method](Url.Routes.adminPeriodCreate.uri).then(response => {
                this.periods.push(response.data);
            }).catch(response => {
                this.parseResponseErrors(response);
            });
        },
        listAction () {
            window.axios[Url.Routes.adminPeriodList.method](Url.Routes.adminPeriodList.uri).then(response => {
                response.data.periods?.forEach(period => {
                    let exists = false;
                    this.periods.forEach(item => {
                        if (item.id === period.id) {
                            exists = true;
                        }
                    });
                    if (!exists) {
                        this.periods.push(period);
                    }
                });
                this.types      = response.data.types;
                this.historyUrl = response.data.historyUrl;
            }).catch(response => {
                this.parseResponseErrors(response);
            });
        },
    },
};
</script>