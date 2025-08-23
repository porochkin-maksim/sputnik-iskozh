<template>
    <div>
        <h5>Счета</h5>
        <table class="table table-sm table-striped table-bordered mb-0"
               v-if="invoices && invoices.length">
            <thead>
            <tr class="text-center">
                <th>№</th>
                <th>Название/Тип</th>
                <th>Период</th>
                <th>Стоимость</th>
                <th>Оплачено</th>
                <th>Долг</th>
                <th>Обновлён</th>
            </tr>
            </thead>
            <tbody>
            <template v-for="(invoice) in invoices">
                <tr class="text-center align-middle"
                    :class="[invoice.isPayed ? 'table-success' : '', invoice.cost === 0 ? 'table-warning' : '', invoice.advance ? 'fw-bold' : '']">
                    <td class="text-end">
                        <a :href="invoice.viewUrl">
                            {{ invoice.id }}
                        </a>
                    </td>
                    <td>{{ invoice.displayName }}</td>
                    <td>{{ invoice.periodName }}</td>
                    <td class="text-end">{{ $formatMoney(invoice.advance ? invoice.cost - invoice.advance : invoice.cost) }}</td>
                    <td class="text-end">{{ $formatMoney(invoice.payed) }}</td>
                    <td class="text-end"
                        :class="[invoice.advance ? 'text-success' : '', invoice.delta ? 'text-danger' : '']">
                        {{ invoice.advance ? $formatMoney(-invoice.advance) : $formatMoney(invoice.delta) }}
                    </td>
                    <td>{{ invoice.updated }}</td>
                </tr>
            </template>
            </tbody>
        </table>
    </div>
</template>

<script>
import Url                         from '../../../../utils/Url.js';
import ResponseError               from '../../../../mixin/ResponseError.js';
import { adminAccountInvoiceList } from '../../../../routes-functions.js';

export default {
    components: {},
    props     : {
        account: Object,
    },
    mixins    : [
        ResponseError,
    ],
    data () {
        return {
            vueId: null,

            invoices: null,
        };
    },
    created () {
        this.vueId = 'uuid' + this.$_uid;
        this.listAction();
    },
    methods: {
        listAction () {
            Url.RouteFunctions.adminAccountInvoiceList(this.account.id).then(response => {
                this.invoices = response.data.invoices;
            }).catch(response => {
                this.parseResponseErrors(response);
            });
        },
    },
};
</script>