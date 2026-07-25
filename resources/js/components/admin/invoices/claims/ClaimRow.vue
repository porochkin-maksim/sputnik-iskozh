<template>
    <tr :class="parseFloat(claim.delta) !== 0 ? 'table-warning' : ''">
        <td class="table-thin-column text-center">
            <span
                class="link-firm cursor-pointer"
                @click="$emit('edit', claim.id)"
            >
                {{ claim.id }}
            </span>
        </td>
        <td>{{ claim.service }}</td>
        <td class="text-end">{{ claim.quantity ?? '—' }}</td>
        <td class="text-end">{{ formatMoney(claim.tariff) }}</td>
        <td class="text-end">{{ formatMoney(claim.cost) }}</td>
        <td class="text-end">{{ formatMoney(claim.paid) }}</td>
        <td class="text-end">{{ formatMoney(claim.delta) }}</td>
        <td class="table-thin-column text-center" v-if="canEdit">
            <button
                v-if="parseFloat(claim.delta) > 0"
                class="btn btn-sm btn-success w-100"
                type="button"
                @click="$emit('pay', claim)"
            >
                <i class="fa fa-credit-card" aria-hidden="true"></i> Оплатить
            </button>
            <button
                v-if=" parseFloat(claim.paid) > 0"
                class="btn btn-sm btn-warning admin-action-btn w-100"
                type="button"
                :aria-label="'Снять оплату с услуги ' + claim.id"
                title="Снять оплату"
                @click="$emit('unpay', claim.id)"
            >
                <i class="fa fa-undo" aria-hidden="true"></i> Снять оплату
            </button>
        </td>
        <td class="text-center">{{ claim.created }}</td>
        <td class="table-thin-column text-center">
            <div class="d-flex justify-content-center gap-1 flex-nowrap">
                <history-btn
                    v-if="claim.historyUrl"
                    class="btn-link"
                    :url="claim.historyUrl"
                />

                <button
                    v-if="canDrop"
                    class="btn btn-sm btn-outline-danger admin-action-btn"
                    type="button"
                    :disabled="dropLoading === claim.id"
                    :aria-label="'Удалить услугу ' + claim.id"
                    @click="$emit('drop', claim.id)"
                >
                    <i
                        v-if="dropLoading === claim.id"
                        class="fa fa-spinner fa-spin"
                        aria-hidden="true"
                    ></i>
                    <i
                        v-else
                        class="fa fa-trash"
                        aria-hidden="true"
                    ></i>
                </button>
            </div>
        </td>
    </tr>
</template>

<script setup>
import HistoryBtn from '@common/HistoryBtn.vue';

defineProps({
    claim        : {
        type    : Object,
        required: true,
    },
    canEdit      : {
        type    : Boolean,
        required: true,
    },
    canDrop      : {
        type    : Boolean,
        required: true,
    },
    dropLoading  : {
        type   : [Number, null],
        default: null,
    },
    formatMoney  : {
        type    : Function,
        required: true,
    },
    showPayColumn: {
        type   : Boolean,
        default: true,
    },
});

defineEmits(['edit', 'drop', 'pay', 'unpay']);
</script>
