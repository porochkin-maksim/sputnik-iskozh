<template>
    <div v-if="summary">
        <table class="table table-sm table-bordered table-striped-columns text-center">
            <thead>
            <tr>
                <th></th>
                <th>План</th>
                <th>Оплачено</th>
                <th>Разница</th>
                <template v-if="showInvoice">
                    <th colspan="2">Всего счетов {{ summary.totalCount }}</th>
                </template>
            </tr>
            </thead>
            <tbody>
            <tr>
                <th>Доход</th>
                <td>{{ $formatMoney(summary.incomeCost) }}</td>
                <td>{{ $formatMoney(summary.incomePayed) }}</td>
                <td>{{ $formatMoney(summary.deltaIncome) }}</td>
                <template v-if="showInvoice">
                    <td>Регулярных</td>
                    <td>{{ summary.regularCount }}</td>
                </template>
            </tr>
            <tr>
                <th>Расход</th>
                <td>{{ $formatMoney(summary.outcomeCost) }}</td>
                <td>{{ $formatMoney(summary.outcomePayed) }}</td>
                <td>{{ $formatMoney(summary.deltaOutcome) }}</td>
                <template v-if="showInvoice">
                    <td>Доходных</td>
                    <td>{{ summary.incomeCount }}</td>
                </template>
            </tr>
            <tr>
                <th>Итого</th>
                <td>{{ $formatMoney(summary.deltaCost) }}</td>
                <td>{{ $formatMoney(summary.deltaPayed) }}</td>
                <td>{{ $formatMoney(summary.delta) }}</td>
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
        type       : {
            type   : Number,
            default: null,
        },
        periodId   : {
            type   : Number,
            default: null,
        },
        accountId  : {
            type   : Number,
            default: null,
        },
        showInvoice: {
            type   : Boolean,
            default: true,
        },
    },
    data () {
        return {
            summary: null,
        };
    },
    created () {
        this.summaryAction();
    },
    methods: {
        summaryAction () {
            let uri = Url.Generator.makeUri(Url.Routes.adminInvoiceSummary, {}, {
                type      : this.type,
                period_id : this.periodId,
                account_id: this.accountId,
            });

            window.axios[Url.Routes.adminInvoiceSummary.method](uri).then(response => {
                this.summary = response.data;
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
    },
};
</script>
