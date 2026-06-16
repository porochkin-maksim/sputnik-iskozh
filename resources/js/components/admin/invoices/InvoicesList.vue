<template>
    <div class="table-responsive">
        <table
            v-if="invoices && invoices.length"
            class="table table-sm table-striped table-bordered table-hover align-middle admin-table-firm"
        >
            <thead>
            <tr class="text-center">
                <th
                    class="table-thin-column text-end cursor-pointer"
                    @click="sort('id')"
                    scope="col"
                >
                    №
                    <i
                        v-if="sortField === 'id'"
                        :class="sortOrder === 'asc' ? 'fa fa-sort-asc' : 'fa fa-sort-desc'"
                        aria-hidden="true"
                    ></i>
                    <i v-else class="fa fa-sort" aria-hidden="true"></i>
                </th>

                <th scope="col">Название/Тип</th>
                <th scope="col">Период</th>

                <th
                    class="cursor-pointer"
                    @click="sort('account_sort')"
                    scope="col"
                >
                    Участок
                    <i
                        v-if="sortField === 'account_sort'"
                        :class="sortOrder === 'asc' ? 'fa fa-sort-asc' : 'fa fa-sort-desc'"
                        aria-hidden="true"
                    ></i>
                    <i v-else class="fa fa-sort" aria-hidden="true"></i>
                </th>

                <th
                    class="cursor-pointer text-end"
                    @click="sort('cost')"
                    scope="col"
                >
                    Стоимость
                    <i
                        v-if="sortField === 'cost'"
                        :class="sortOrder === 'asc' ? 'fa fa-sort-asc' : 'fa fa-sort-desc'"
                        aria-hidden="true"
                    ></i>
                    <i v-else class="fa fa-sort" aria-hidden="true"></i>
                </th>

                <th
                    class="cursor-pointer text-end"
                    @click="sort('paid')"
                    scope="col"
                >
                    Оплачено
                    <i
                        v-if="sortField === 'paid'"
                        :class="sortOrder === 'asc' ? 'fa fa-sort-asc' : 'fa fa-sort-desc'"
                        aria-hidden="true"
                    ></i>
                    <i v-else class="fa fa-sort" aria-hidden="true"></i>
                </th>

                <th class="text-end" scope="col">Долг</th>

                <th
                    class="cursor-pointer text-end"
                    @click="sort('updated_at')"
                    scope="col"
                >
                    Обновлён
                    <i
                        v-if="sortField === 'updated_at'"
                        :class="sortOrder === 'asc' ? 'fa fa-sort-asc' : 'fa fa-sort-desc'"
                        aria-hidden="true"
                    ></i>
                    <i v-else class="fa fa-sort" aria-hidden="true"></i>
                </th>

                <th class="table-thin-column text-center" scope="col"></th>
            </tr>
            </thead>

            <tbody>
            <tr
                v-for="invoice in invoices"
                :key="invoice.id"
                class="text-center align-middle"
                :class="[
                        invoice.isPaid ? 'table-success' : '',
                        invoice.cost === 0 ? 'table-warning' : '',
                    ]"
            >
                <td class="table-thin-column text-center">
                    <a :href="invoice.viewUrl" class="link-firm">
                        {{ invoice.id }}
                    </a>
                </td>

                <td>{{ invoice.displayName }}</td>
                <td>{{ invoice.periodName }}</td>

                <td class="text-end">
                    <a
                        v-if="invoice.accountUrl"
                        :href="invoice.accountUrl"
                        class="link-firm"
                    >
                        {{ invoice.accountNumber }}
                    </a>
                    <span v-else>{{ invoice.accountNumber }}</span>
                </td>

                <td class="text-end fw-medium">
                    {{ formatMoney(invoice.cost) }}
                </td>

                <td class="text-end fw-medium">
                    {{ formatMoney(invoice.paid) }}
                </td>

                <td
                    class="text-end fw-medium"
                    :class="[
                            invoice.delta ? 'text-danger' : ''
                        ]"
                >
                    {{ formatMoney(invoice.delta) }}
                </td>

                <td class="text-end text-muted small">
                    {{ invoice.updated }}
                </td>

                <td class="table-thin-column text-center">
                    <a
                        v-if="invoice.receiptUrl"
                        :href="invoice.receiptUrl"
                        target="_blank"
                        class="btn btn-sm admin-action-btn"
                        title="Скачать квитанцию"
                    >
                        <i class="fa fa-file-pdf-o text-danger fa-lg"></i>
                        <span class="visually-hidden">Квитанция</span>
                    </a>
                </td>
            </tr>
            </tbody>
        </table>

        <!-- Сообщение при пустом списке -->
        <div
            v-else-if="invoices && invoices.length === 0"
            class="alert alert-info text-center my-3"
        >
            <i class="fa fa-info-circle me-2" aria-hidden="true"></i>
            Счета не найдены
        </div>
    </div>
</template>

<script setup>
import {
    defineEmits,
    defineProps,
}                    from 'vue';
import { useFormat } from '@composables/useFormat';

const props = defineProps({
    invoices : {
        type   : Array,
        default: () => [],
    },
    sortField: {
        type   : String,
        default: null,
    },
    sortOrder: {
        type   : String,
        default: 'asc',
    },
});

const emit            = defineEmits(['sort']);
const { formatMoney } = useFormat();

const sort = (field) => {
    if (props.sortField === field) {
        emit('sort', {
            field: field,
            order: props.sortOrder === 'asc' ? 'desc' : 'asc',
        });
    }
    else {
        emit('sort', {
            field: field,
            order: 'asc',
        });
    }
};
</script>
