<template>
    <table v-if="localInvoice.detailCost" class="table table-borderless table-sm w-auto mb-0">
        <tbody>
        <tr>
            <th>Основа:</th>
            <td>{{ formatMoney(localInvoice.detailCost.main) }}</td>
            <th>Долг:</th>
            <td>{{ formatMoney(localInvoice.detailCost.debt) }}</td>
            <th v-if="localInvoice.detailCost.rounding">Округление:</th>
            <td v-if="localInvoice.detailCost.rounding" class="text-muted">
                {{ formatMoney(localInvoice.detailCost.rounding) }}
            </td>
            <th>Итого:</th>
            <td>{{ formatMoney(localInvoice.cost) }}</td>
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
                <span v-if="localInvoice.delta > 0" class="ms-2 text-danger fw-bold">
                    (Долг {{ formatMoney(localInvoice.delta) }})
                </span>
                <span v-else-if="localInvoice.delta < 0" class="ms-2 text-success">
                    (Переплата {{ formatMoney(Math.abs(localInvoice.delta)) }})
                </span>
            </div>
        </div>
    </div>

    <div
        v-if="localInvoice.detailCost?.rounding"
        class="mt-2 text-muted small"
    >
        <button v-if="canEdit" class="btn btn-sm btn-outline-secondary" @click="$emit('edit-rounding')">

            <i class="fa fa-calculator me-1"
               aria-hidden="true"></i>Округление: {{ formatMoney(localInvoice.detailCost.rounding) }}
        </button>
        <template v-else>
            <i class="fa fa-calculator me-1" aria-hidden="true"></i>
            <span>Округление: {{ formatMoney(localInvoice.detailCost.rounding) }}</span>
        </template>
    </div>

    <div
        v-if="balance !== null"
        class="my-3 p-3 bg-light rounded border"
    >
        <div class="d-flex align-items-center gap-2">
            <i class="fa fa-balance-scale text-info" aria-hidden="true"></i>
            <strong>Баланс участка:</strong>
            <span class="fw-bold" :class="balance >= 0 ? 'text-success' : 'text-danger'">
                {{ formatMoney(balance) }}
            </span>
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
    balance         : { type: Number, default: null },
    canEdit         : { type: Boolean, default: false },
    deltaClass      : { type: String, required: true },
    formatMoney     : { type: Function, required: true },
    localInvoice    : { type: Object, required: true },
    statusAlertClass: { type: String, required: true },
});

defineEmits(['edit-rounding']);
</script>
