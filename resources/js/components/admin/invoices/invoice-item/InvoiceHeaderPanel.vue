<template>
    <h4 class="mb-3" v-if="localInvoice.id">
        Детали счёта №{{ localInvoice.id }}
        для «{{ localInvoice.account?.number || '—' }}»
        <span class="text-muted fw-normal">
            | {{ localInvoice.periodName }} | {{ localInvoice.displayName || '—' }}
        </span>
    </h4>

    <div class="d-flex flex-wrap align-items-center justify-content-between gap-2 mb-3">
        <div class="d-flex flex-wrap align-items-center gap-2">
            <a
                v-if="localInvoice.account?.viewUrl && canAccountView"
                class="btn btn-sm btn-outline-primary"
                :href="localInvoice.account.viewUrl"
            >
                <i class="fa fa-home me-1" aria-hidden="true"></i>
                Участок {{ localInvoice.account?.number }} ({{ localInvoice.account?.size }}м²)
            </a>

            <a
                v-if="localInvoice.receiptUrl"
                :href="localInvoice.receiptUrl"
                target="_blank"
                class="btn btn-sm btn-outline-danger"
            >
                <i class="fa fa-file-pdf-o me-1" aria-hidden="true"></i>
                Квитанция
            </a>

            <button
                v-if="canEdit"
                class="btn btn-outline-primary btn-sm"
                :class="isRecalculating ? 'disabled' : ''"
                @click="$emit('recalc')"
            >
                <i class="fa" :class="isRecalculating ? 'fa-spin' : 'fa-refresh'"></i>
                Пересчитать
            </button>

            <history-btn
                v-if="localInvoice.historyUrl"
                class="btn-link underline-none p-0"
                :url="localInvoice.historyUrl"
                aria-label="История изменений"
            />
        </div>

        <button
            v-if="canDelete"
            class="btn btn-sm btn-outline-danger"
            @click="$emit('drop')"
        >
            <i class="fa fa-trash me-1" aria-hidden="true"></i>
            Удалить счёт
        </button>
    </div>
</template>

<script setup>
import HistoryBtn from '@common/HistoryBtn.vue';

defineProps({
    canAccountView : { type: Boolean, required: true },
    canDelete      : { type: Boolean, required: true },
    canEdit        : { type: Boolean, required: true },
    isRecalculating: { type: Boolean, required: true },
    localInvoice   : { type: Object, required: true },
});

defineEmits(['drop', 'recalc']);
</script>
