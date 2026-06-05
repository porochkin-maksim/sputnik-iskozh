<template>
    <tr
        :class="[item.accountId ? '' : 'table-danger']"
    >
        <td class="text-end">
            <a
                v-if="item.accountUrl"
                :href="item.accountUrl"
                target="_blank"
            >
                {{ item.accountNumber }}
            </a>
            <span v-else-if="item.accountNumber">{{ item.accountNumber }}</span>
        </td>
        <td class="text-center">
            <a
                v-if="item.invoiceId"
                :href="item.invoiceUrl"
                target="_blank"
            >
                №{{ item.invoiceId }}
            </a>
        </td>
        <td class="text-end" :class="item.invoiceAdvance ? 'fw-bold' : 'text-secondary'">
            {{ formatMoney(item.invoiceAdvance) }}
        </td>
        <td class="text-end" :class="item.invoiceDebt ? 'fw-bold' : 'text-secondary'">
            {{ formatMoney(item.invoiceDebt) }}
        </td>
        <td
            class="text-end text-secondary"
            :class="[item.changeInCost && item.invoiceMain !== item.invoiceCost ? 'text-danger fw-bold' : '']"
        >
            {{ formatMoney(item.invoiceMain) }}
        </td>
        <td
            class="text-end text-secondary"
            :class="[item.changeInCost ? 'table-danger text-danger fw-bold' : '']"
        >
            {{ formatMoney(item.invoiceCost) }}
        </td>
        <td
            class="text-end text-secondary"
            :class="[item.changeInCost ? 'table-danger text-danger fw-bold' : '']"
        >
            {{ formatMoney(item.cost) }}
        </td>
        <td
            class="text-end"
            :class="[item.invoicePaid ? 'text-success fw-bold' : item.changeInPaid ? 'text-danger fw-bold' : 'text-secondary']"
        >
            {{ formatMoney(item.invoicePaid) }}
        </td>
        <td
            class="text-end"
            :class="[item.paid ? 'text-success fw-bold' : item.changeInPaid ? 'text-danger fw-bold' : 'text-secondary']"
        >
            {{ formatMoney(item.paid) }}
        </td>
        <td
            class="text-end"
            :class="[item.invoiceDebt && item.changeInDelta ? 'fw-bold' : 'text-secondary']"
        >
            {{ formatMoney(item.invoiceDebt) }}
        </td>
        <td
            class="text-end"
            :class="[item.debt && item.changeInDelta ? 'fw-bold' : 'text-secondary', item.debt < 0 ? 'text-danger' : '']"
        >
            {{ formatMoney(item.debt) }}
        </td>
        <td>
            <div
                v-if="item.accountId"
                class="w-100"
            >
                <custom-input
                    :model-value="editedAmount"
                    type="number"
                    step="0.01"
                    :min="0"
                    :max="Math.max(item.invoiceDebt, item.debt)"
                    :disabled="submitting"
                    required
                    @update:modelValue="$emit('update:editedAmount', $event)"
                />
            </div>
        </td>
    </tr>
</template>

<script setup>
import CustomInput   from '@common/form/CustomInput.vue';
import { useFormat } from '@composables/useFormat';

const { formatMoney } = useFormat();

defineProps({
    editedAmount: {
        type   : [Number, String, null],
        default: null,
    },
    item        : {
        type    : Object,
        required: true,
    },
    submitting  : {
        type    : Boolean,
        required: true,
    },
});

defineEmits(['update:editedAmount']);
</script>
