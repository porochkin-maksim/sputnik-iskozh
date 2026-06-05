<template>
    <table v-if="localInvoice.detailCost" class="table table-borderless table-sm w-auto mb-0">
        <tbody>
        <tr>
            <th>Основа:</th>
            <td>{{ formatMoney(localInvoice.detailCost.main) }}</td>
            <th>Долг:</th>
            <td>{{ formatMoney(localInvoice.detailCost.debt) }}</td>
            <th>Аванс:</th>
            <td>{{ formatMoney(localInvoice.detailCost.advance) }}</td>
        </tr>
        </tbody>
    </table>

    <div
        v-if="canView && actions.view && localInvoice.cost !== undefined"
        class="alert p-3 mb-3"
        :class="statusAlertClass"
    >
        <div class="d-flex align-items-center gap-2">
            <i
                class="fa"
                :class="localInvoice.isPaid ? 'fa-check-circle text-success' : 'fa-times-circle text-secondary'"
                aria-hidden="true"
            ></i>
            <div>
                <strong>Оплачено:</strong>
                {{ formatMoney(localInvoice.paid || 0) }} / {{ formatMoney(localInvoice.cost || 0) }}
                <span v-if="localInvoice.delta !== 0" class="ms-2" :class="deltaClass">
                    (Долг {{ formatMoney(Math.abs(localInvoice.delta)) }})
                </span>
            </div>
        </div>
    </div>
</template>

<script setup>
import { computed }       from 'vue';
import { usePermissions } from '@composables/usePermissions.js';

const { has } = usePermissions();
const canView = computed(() => has('invoices', 'view'));

defineProps({
    actions         : { type: Object, required: true },
    deltaClass      : { type: String, required: true },
    formatMoney     : { type: Function, required: true },
    localInvoice    : { type: Object, required: true },
    statusAlertClass: { type: String, required: true },
});
</script>
