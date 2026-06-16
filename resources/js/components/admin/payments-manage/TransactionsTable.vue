<template>
    <div v-if="transactions.length" class="mb-3">
        <label class="form-label">Транзакции</label>
        <table class="table table-sm table-bordered admin-table-firm mb-0">
            <thead>
            <tr>
                <th>Назначение</th>
                <th class="text-end">Сумма</th>
                <th>Дата</th>
                <th>Действия</th>
            </tr>
            </thead>
            <tbody>
            <tr v-for="t in transactions" :key="t.id">
                <td>{{ t.name }}</td>
                <td class="text-end">{{ formatMoney(t.cost) }}</td>
                <td>{{ t.createdAt }}</td>
                <td>
                    <button
                        v-if="!t.claimId"
                        class="btn btn-sm btn-primary"
                        @click="$emit('allocate', t)"
                        type="button"
                    >
                        <i class="fa fa-share"></i> Распределить
                    </button>
                </td>
            </tr>
            </tbody>
        </table>
    </div>
</template>

<script setup>
import { useFormat } from '@composables/useFormat';

const { formatMoney } = useFormat();

defineProps({
    transactions: { type: Array, default: () => [] },
});
defineEmits(['allocate']);
</script>
