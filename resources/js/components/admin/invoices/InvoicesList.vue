<template>
    <div>
        <table class="table">
            <thead>
            <tr>
                <th>№</th>
                <th>Тип</th>
                <th>Период</th>
                <th>Участок</th>
                <th>Стоимость</th>
                <th>Оплачено</th>
                <th>Создан</th>
                <th></th>
            </tr>
            </thead>
            <tbody>
            <template v-for="(invoice) in invoices">
                <tr class="align-middle" :class="[invoice.isPayed ? 'table-success' : '', invoice.cost === 0 ? 'table-warning' : '']">
                    <td>
                        <a :href="invoice.viewUrl">
                            {{ invoice.id }}
                        </a>
                    </td>
                    <td>{{ invoice.typeName }}</td>
                    <td>{{ invoice.periodName }}</td>
                    <td>{{ invoice.accountNumber }}</td>
                    <td>{{ $formatMoney(invoice.cost) }}</td>
                    <td>{{ $formatMoney(invoice.payed) }}</td>
                    <td>{{ invoice.created }}</td>
                    <td>
                        <history-btn
                            class="btn-link btn-sm underline-none"
                            :url="invoice.historyUrl" />
                    </td>
                </tr>
            </template>
            </tbody>
        </table>
    </div>
</template>

<script>
import ResponseError from '../../../mixin/ResponseError.js';
import HistoryBtn    from '../../common/HistoryBtn.vue';

export default {
    components: { HistoryBtn },
    mixins    : [
        ResponseError,
    ],
    props     : [
        'invoices',
    ],
};
</script>
