<template>
    <tr>
        <td class="text-end table-thin-column">
            <button
                type="button"
                class="link-firm"
                @click="$emit('edit', payment.id)"
            >
                {{ payment.id }}
            </button>
        </td>
        <td class="text-end text-nowrap table-thin-column">
            <a v-if="payment.accountId">
                {{ payment.accountNumber }}
            </a>
        </td>
        <td class="text-end">{{ formatMoney(payment.cost) }}</td>
        <td class="text-center text-nowrap">{{ payment.created }}</td>
        <td>
            <div
                v-if="payment.files?.length"
                class="d-flex flex-column gap-1"
            >
                <file-item
                    v-for="(file, index) in payment.files"
                    :key="file.id"
                    :file="file"
                    :edit="true"
                    :index="index"
                    :use-up-sort="index !== 0"
                    :use-down-sort="index !== payment.files.length - 1"
                    @updated="$emit('file-updated')"
                />
            </div>
            <span
                v-else
                class="text-muted small"
            >—</span>
        </td>
        <td>
            <div class="d-flex justify-content-center gap-2">
                <history-btn
                    v-if="payment.historyUrl"
                    class="btn-link underline-none p-0"
                    :url="payment.historyUrl"
                    aria-label="История изменений"
                />

                <button
                    v-if="canEdit"
                    class="btn btn-sm btn-outline-success admin-action-btn"
                    :disabled="actionLoading"
                    title="Привязать"
                    @click="$emit('edit', payment.id)"
                >
                    <i class="fa fa-link"></i>
                </button>

                <button
                    v-if="canDrop"
                    class="btn btn-sm btn-outline-danger admin-action-btn"
                    :disabled="dropLoading === payment.id"
                    title="Удалить"
                    @click="$emit('drop', payment.id)"
                >
                    <i
                        v-if="dropLoading === payment.id"
                        class="fa fa-spinner fa-spin"
                    ></i>
                    <i v-else class="fa fa-trash"></i>
                </button>
            </div>
        </td>
    </tr>
</template>

<script setup>
import FileItem   from '@common/files/FileItem.vue';
import HistoryBtn from '@common/HistoryBtn.vue';

defineProps({
    actionLoading: {
        type   : Boolean,
        default: false,
    },
    canDrop      : {
        type    : Boolean,
        required: true,
    },
    canEdit      : {
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
    payment      : {
        type    : Object,
        required: true,
    },
});

defineEmits(['edit', 'drop', 'file-updated']);
</script>
