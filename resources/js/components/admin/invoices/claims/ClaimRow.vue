<template>
    <tr :class="parseFloat(claim.delta) !== 0 ? 'table-warning' : ''">
        <td class="table-thin-column text-center">
            <span class="link-firm">
                {{ claim.id }}
            </span>
        </td>
        <td>{{ claim.service }}</td>
        <td class="text-end">{{ formatMoney(claim.tariff) }}</td>
        <td class="text-end">{{ formatMoney(claim.cost) }}</td>
        <td class="text-end">{{ formatMoney(claim.paid) }}</td>
        <td class="text-end">{{ formatMoney(claim.delta) }}</td>
        <td class="text-center">{{ claim.created }}</td>
        <td class="table-thin-column text-center">
            <div class="d-flex justify-content-center gap-1 flex-nowrap">
                <history-btn
                    v-if="claim.historyUrl"
                    class="btn-link underline-none p-0"
                    :url="claim.historyUrl"
                    aria-label="История изменений"
                />

                <button
                    v-if="canEdit"
                    class="btn btn-sm btn-outline-success admin-action-btn"
                    type="button"
                    :disabled="dropLoading === claim.id"
                    :aria-label="'Редактировать услугу ' + claim.id"
                    @click="$emit('edit', claim.id)"
                >
                    <i class="fa fa-edit" aria-hidden="true"></i>
                </button>

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
    claim      : {
        type    : Object,
        required: true,
    },
    canEdit    : {
        type    : Boolean,
        required: true,
    },
    canDrop    : {
        type    : Boolean,
        required: true,
    },
    dropLoading: {
        type   : [Number, null],
        default: null,
    },
    formatMoney: {
        type    : Function,
        required: true,
    },
});

defineEmits(['edit', 'drop']);
</script>
