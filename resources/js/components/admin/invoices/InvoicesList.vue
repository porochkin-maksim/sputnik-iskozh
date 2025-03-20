<template>
    <div>
        <table class="table table-sm table-bordered">
            <thead>
            <tr>
                <th class="text-center">№</th>
                <th class="text-center">Тип</th>
                <th class="text-center">Период</th>
                <th class="text-center">Участок</th>
                <th class="text-center">Стоимость</th>
                <th class="text-center">Оплачено</th>
                <th class="text-center">Создан</th>
                <th class="text-center"></th>
            </tr>
            </thead>
            <tbody>
            <template v-for="(invoice) in invoices">
                <tr class="align-middle" :class="[invoice.isPayed ? 'table-success' : '', invoice.cost === 0 ? 'table-warning' : '']">
                    <td class="text-end">
                        <a :href="invoice.viewUrl">
                            {{ invoice.id }}
                        </a>
                    </td>
                    <td class="text-center">{{ invoice.typeName }}</td>
                    <td class="text-center">{{ invoice.periodName }}</td>
                    <td class="text-end">
                        <template v-if="invoice.accountUrl">
                            <a :href="invoice.accountUrl">{{ invoice.accountNumber }}</a>
                        </template>
                        <template v-else>
                            {{ invoice.accountNumber }}
                        </template>
                    </td>
                    <td class="text-end">{{ $formatMoney(invoice.cost) }}</td>
                    <td class="text-end">{{ $formatMoney(invoice.payed) }}</td>
                    <td class="text-center">{{ invoice.created }}</td>
                    <td>
                        <div class="d-flex justify-content-center">
                            <history-btn
                                class="btn-link btn-sm underline-none"
                                :url="invoice.historyUrl" />
                        </div>
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
