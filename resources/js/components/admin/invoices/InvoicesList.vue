<template>
    <div class="table-responsive">
        <table class="table table-sm table-striped table-bordered" v-if="invoices && invoices.length">
            <thead>
                <tr class="text-center">
                    <th class="text-end cursor-pointer" @click="sort('id')">
                        №
                        <i v-if="sortField === 'id'" :class="sortOrder === 'asc' ? 'fa fa-sort-asc' : 'fa fa-sort-desc'"></i>
                        <i v-else class="fa fa-sort"></i>
                    </th>
                    <th>Название/Тип</th>
                    <th>Период</th>
                    <th class="cursor-pointer" @click="sort('account_sort')">
                        Участок
                        <i v-if="sortField === 'account_sort'" :class="sortOrder === 'asc' ? 'fa fa-sort-asc' : 'fa fa-sort-desc'"></i>
                        <i v-else class="fa fa-sort"></i>
                    </th>
                    <th class="cursor-pointer" @click="sort('cost')">
                        Стоимость
                        <i v-if="sortField === 'cost'" :class="sortOrder === 'asc' ? 'fa fa-sort-asc' : 'fa fa-sort-desc'"></i>
                        <i v-else class="fa fa-sort"></i>
                    </th>
                    <th class="cursor-pointer" @click="sort('payed')">
                        Оплачено
                        <i v-if="sortField === 'payed'" :class="sortOrder === 'asc' ? 'fa fa-sort-asc' : 'fa fa-sort-desc'"></i>
                        <i v-else class="fa fa-sort"></i>
                    </th>
                    <th>Долг</th>
                    <th class="cursor-pointer" @click="sort('updated_at')">
                        Обновлён
                        <i v-if="sortField === 'updated_at'" :class="sortOrder === 'asc' ? 'fa fa-sort-asc' : 'fa fa-sort-desc'"></i>
                        <i v-else class="fa fa-sort"></i>
                    </th>
                </tr>
            </thead>
            <tbody>
            <template v-for="(invoice) in invoices">
                <tr class="text-center align-middle" :class="[invoice.isPayed ? 'table-success' : '', invoice.cost === 0 ? 'table-warning' : '', invoice.advance ? 'fw-bold' : '']">
                    <td class="text-end">
                        <a :href="invoice.viewUrl">
                            {{ invoice.id }}
                        </a>
                    </td>
                    <td>{{ invoice.displayName }}</td>
                    <td>{{ invoice.periodName }}</td>
                    <td class="text-end">
                        <template v-if="invoice.accountUrl">
                            <a :href="invoice.accountUrl">{{ invoice.accountNumber }}</a>
                        </template>
                        <template v-else>
                            {{ invoice.accountNumber }}
                        </template>
                    </td>
                    <td class="text-end">{{ $formatMoney(invoice.advance ? invoice.cost - invoice.advance : invoice.cost) }}</td>
                    <td class="text-end">{{ $formatMoney(invoice.payed) }}</td>
                    <td class="text-end" :class="[invoice.advance ? 'text-success' : '', invoice.delta ? 'text-danger' : '']">
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
