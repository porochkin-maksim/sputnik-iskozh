<template>
    <div class="table-responsive">
        <table class="table table-sm table-striped table-bordered" v-if="invoices && invoices.length">
            <thead>
                <tr>
                    <th class="text-center cursor-pointer" @click="sort('id')">
                        №
                        <i v-if="sortField === 'id'" :class="sortOrder === 'asc' ? 'fa fa-sort-asc' : 'fa fa-sort-desc'"></i>
                        <i v-else class="fa fa-sort"></i>
                    </th>
                    <th class="text-center">Тип</th>
                    <th class="text-center">Период</th>
                    <th class="text-center">Участок</th>
                    <th class="text-center cursor-pointer" @click="sort('cost')">
                        Стоимость
                        <i v-if="sortField === 'cost'" :class="sortOrder === 'asc' ? 'fa fa-sort-asc' : 'fa fa-sort-desc'"></i>
                        <i v-else class="fa fa-sort"></i>
                    </th>
                    <th class="text-center cursor-pointer" @click="sort('payed')">
                        Оплачено
                        <i v-if="sortField === 'payed'" :class="sortOrder === 'asc' ? 'fa fa-sort-asc' : 'fa fa-sort-desc'"></i>
                        <i v-else class="fa fa-sort"></i>
                    </th>
                    <th class="text-center">Долг</th>
                    <th class="text-center cursor-pointer" @click="sort('updated_at')">
                        Обновлён
                        <i v-if="sortField === 'updated_at'" :class="sortOrder === 'asc' ? 'fa fa-sort-asc' : 'fa fa-sort-desc'"></i>
                        <i v-else class="fa fa-sort"></i>
                    </th>
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
                    <td class="text-end">{{ $formatMoney(invoice.cost - invoice.payed) }}</td>
                    <td class="text-center">{{ invoice.updated }}</td>
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
    emits     : ['sort'],
    components: { HistoryBtn },
    mixins    : [
        ResponseError,
    ],
    props     : [
        'invoices',
        'sortField',
        'sortOrder'
    ],
    methods: {
        sort(field) {
            if (this.sortField === field) {
                this.$emit('sort', { 
                    field: field, 
                    order: this.sortOrder === 'asc' ? 'desc' : 'asc' 
                });
            } else {
                this.$emit('sort', { 
                    field: field, 
                    order: 'asc' 
                });
            }
        },
    }
};
</script>
