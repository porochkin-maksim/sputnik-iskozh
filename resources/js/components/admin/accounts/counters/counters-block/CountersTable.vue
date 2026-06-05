<template>
    <table
        v-if="counters && counters.length"
        class="table align-middle m-0 text-center"
    >
        <thead>
        <tr>
            <th>Номер</th>
            <th>Показание</th>
            <th>Дата</th>
            <th>Счета</th>
            <th>Авто</th>
            <th>Поверка</th>
            <th></th>
        </tr>
        </thead>
        <tbody>
        <tr
            v-for="counter in counters"
            :key="counter.id"
        >
            <td>{{ counter.number }}</td>
            <td>{{ counter.value }}</td>
            <td>{{ counter.date }}</td>
            <td>{{ counter.isInvoicing ? 'да' : 'нет' }}</td>
            <td>{{ counter.increment ? '+' + counter.increment : '-' }}</td>
            <td>
                <div v-if="counter.expireAt">
                    {{ formatDate(counter.expireAt) }}
                </div>
                <file-item
                    v-if="counter.passport"
                    :file="counter.passport"
                    :show-download="false"
                    :name="'Паспорт'"
                />
            </td>
            <td>
                <div class="d-flex gap-1">
                    <a
                        v-if="counter.actions?.edit"
                        class="btn btn-sm btn-primary"
                        @click.prevent="$emit('edit-counter', counter)"
                    >
                        <i class="fa fa-edit"></i>
                    </a>
                    <a :href="counter.viewUrl"
                        class="btn btn-sm btn-outline-secondary"
                    >
                        <i class="fa fa-list"></i>
                    </a>
                    <a
                        v-if="counter.actions?.drop"
                        class="btn btn-sm btn-danger"
                        @click="$emit('drop-counter', counter)"
                    >
                        <i class="fa fa-trash"></i>
                    </a>
                </div>
            </td>
        </tr>
        </tbody>
    </table>
</template>

<script setup>
import {
    defineEmits,
    defineProps,
} from 'vue';

import FileItem   from '@common/files/FileItem.vue';
import HistoryBtn from '@common/HistoryBtn.vue';

defineProps({
    account   : {
        type    : Object,
        required: true,
    },
    counters  : {
        type    : Array,
        required: true,
    },
    formatDate: {
        type    : Function,
        required: true,
    },
    loading   : {
        type    : Boolean,
        required: true,
    },
    period    : {
        type   : Object,
        default: null,
    },
    vueId     : {
        type    : String,
        required: true,
    },
});

defineEmits(['edit-counter', 'drop-counter']);
</script>
