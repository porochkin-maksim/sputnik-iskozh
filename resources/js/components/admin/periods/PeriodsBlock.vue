<template>
    <div class="d-flex align-items-center justify-content-between mb-2">
        <div>
            <button class="btn btn-success"
                    v-if="actions.edit"
                    v-on:click="makeAction">Добавить период
            </button>
        </div>
        <history-btn
            class="btn-link underline-none"
            :url="historyUrl" />
    </div>
    <div>
        <template v-if="actions.edit">
            <div v-for="period in periods">
                <period-item-edit :model-value="period" />
            </div>
        </template>
        <template v-else>
            <table class="table table-sm">
                <thead>
                <tr>
                    <th>ID</th>
                    <th>Название</th>
                    <th>Начало</th>
                    <th>Окончание</th>
                    <th></th>
                </tr>
                </thead>
                <tbody>
                <tr v-for="period in periods" class="align-middle">
                    <period-item-edit :model-value="period" />
                </tr>
                </tbody>
            </table>
        </template>
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
            actions   : {},
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
                this.actions    = response.data.actions;
                this.types      = response.data.types;
                this.historyUrl = response.data.historyUrl;
            }).catch(response => {
                this.parseResponseErrors(response);
            });
        },
    },
};
</script>