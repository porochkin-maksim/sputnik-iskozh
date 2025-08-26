<template>
    <div v-if="summary" class="table-responsive">
        <table class="table table-sm table-bordered table-striped-columns text-center">
            <thead>
            <tr class="table-info">
                <th></th>
                <th class="text-center">План</th>
                <th class="text-center">Оплачено</th>
                <th class="text-center">Долг</th>
                <template v-if="showInvoice">
                    <th colspan="2">Всего счетов {{ summary.totalCount }}</th>
                </template>
            </tr>
            </thead>
            <tbody>
            <tr class="table-secondary">
                <th class="d-flex flex-column flex-sm-row flex-wrap justify-content-between">
                    <span>
                        Доход
                    </span>
                    <a href=""
                       v-if="!summaryIncome"
                       @click.prevent="showDetailsIncome">Подробнее</a>
                    <a href=""
                       v-else
                       @click.prevent="summaryIncome=null">Скрыть</a>
                </th>
                <td class="text-end">{{ $formatMoney(summary.incomeCost) }}</td>
                <td class="text-end">{{ $formatMoney(summary.incomePayed) }}</td>
                <td class="text-end">{{ $formatMoney(summary.deltaIncome) }}</td>
                <template v-if="showInvoice">
                    <td>Регулярных</td>
                    <td>{{ summary.regularCount }}</td>
                </template>
            </tr>
            <template v-if="summaryIncome">
                <tr v-for="item in summaryIncome"
                    class="">
                    <td class="text-start">{{ item.service }}</td>
                    <td class="text-end">{{ $formatMoney(parseFloat(item.cost)) }}</td>
                    <td class="text-end">{{ $formatMoney(parseFloat(item.payed)) }}</td>
                    <td class="text-end">{{ $formatMoney(parseFloat(item.delta)) }}</td>
                    <template v-if="showInvoice">
                        <td></td>
                        <td></td>
                    </template>
                </tr>
            </template>
            <tr class="table-secondary">
                <th class="d-flex flex-column flex-sm-row flex-wrap justify-content-between">
                    <span>
                        Расход
                    </span>
                    <a href=""
                       v-if="!summaryOutcome"
                       @click.prevent="showDetailsOutcome">Подробнее</a>
                    <a href=""
                       v-else
                       @click.prevent="summaryOutcome=null">Скрыть</a>
                </th>
                <td class="text-end">{{ $formatMoney(summary.outcomeCost) }}</td>
                <td class="text-end">{{ $formatMoney(summary.outcomePayed) }}</td>
                <td class="text-end">{{ $formatMoney(summary.deltaOutcome) }}</td>
                <template v-if="showInvoice">
                    <td>Доходных</td>
                    <td>{{ summary.incomeCount }}</td>
                </template>
            </tr>
            <template v-if="summaryOutcome">
                <tr v-for="item in summaryOutcome"
                    class="">
                    <td class="text-start">{{ item.service }}</td>
                    <td class="text-end">{{ $formatMoney(parseFloat(item.cost)) }}</td>
                    <td class="text-end">{{ $formatMoney(parseFloat(item.payed)) }}</td>
                    <td class="text-end">{{ $formatMoney(parseFloat(item.delta)) }}</td>
                    <template v-if="showInvoice">
                        <td></td>
                        <td></td>
                    </template>
                </tr>
            </template>
            <tr class="table-info">
                <th class="text-end">Итого:</th>
                <td class="text-end">{{ $formatMoney(summary.deltaCost) }}</td>
                <td class="text-end">{{ $formatMoney(summary.deltaPayed) }}</td>
                <td class="text-end">{{ $formatMoney(summary.delta) }}</td>
                <template v-if="showInvoice">
                    <td>Расходных</td>
                    <td>{{ summary.outcomeCount }}</td>
                </template>
            </tr>
            </tbody>
        </table>
    </div>
</template>

<script>
import ResponseError from '../../../mixin/ResponseError.js';
import Url           from '../../../utils/Url.js';

export default {
    name  : 'SummaryBlock',
    mixins: [
        ResponseError,
    ],
    props : {
        type         : {
            type   : Number,
            default: null,
        },
        periodId     : {
            type   : Number,
            default: null,
        },
        accountId    : {
            type   : Number,
            default: null,
        },
        accountSearch: {
            type   : String,
            default: null,
        },
        showInvoice  : {
            type   : Boolean,
            default: true,
        },
    },
    data () {
        return {
            summary       : null,
            summaryIncome : null,
            summaryOutcome: null,
        };
    },
    created () {
        this.summaryAction();
    },
    methods: {
        summaryAction () {
            this.summaryOutcome = null;
            this.summaryIncome  = null;
            let uri             = Url.Generator.makeUri(Url.Routes.commonSummary, {}, {
                type      : this.type,
                period_id : this.periodId,
                account_id: this.accountId,
                search    : this.accountSearch,
            });

            window.axios[Url.Routes.commonSummary.method](uri).then(response => {
                this.summary = response.data;
            }).catch(response => {
                this.parseResponseErrors(response);
            });
        },
        showDetailsIncome () {
            this.summaryIncome = [];
            let uri            = Url.Generator.makeUri(Url.Routes.commonSummaryDetailing, {
                type: 'income',
            }, {
                type      : this.type,
                period_id : this.periodId,
                account_id: this.accountId,
                search    : this.accountSearch,
            });

            window.axios[Url.Routes.commonSummaryDetailing.method](uri).then(response => {
                this.summaryIncome = response.data;
            }).catch(response => {
                this.parseResponseErrors(response);
            });
        },
        showDetailsOutcome () {
            this.summaryOutcome = [];
            let uri             = Url.Generator.makeUri(Url.Routes.commonSummaryDetailing, {
                type: 'outcome',
            }, {
                type      : this.type,
                period_id : this.periodId,
                account_id: this.accountId,
                search    : this.accountSearch,
            });

            window.axios[Url.Routes.commonSummaryDetailing.method](uri).then(response => {
                this.summaryOutcome = response.data;
            }).catch(response => {
                this.parseResponseErrors(response);
            });
        },
    },
    watch  : {
        type () {
            this.summaryAction();
        },
        periodId () {
            this.summaryAction();
        },
        accountId () {
            this.summaryAction();
        },
        accountSearch () {
            this.summaryAction();
        },
    },
};
</script>
