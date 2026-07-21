<template>
    <div class="counter-history-list">
        <div v-for="(history, index) in histories" :key="history.id" class="counter-history-list__item p-3">
            <div class="counter-history-list__header d-flex justify-content-between align-items-center gap-3 flex-wrap">
                <div class="d-flex align-items-center gap-2 flex-wrap">
                    <span class="counter-history-list__badge">#{{ index + 1 }}</span>
                    <span class="fw-semibold">
                        Показания {{ history.value.toLocaleString('ru-RU') }}{{ history.delta === null ? '' : ' (' + history.delta.toLocaleString('ru-RU') + ' кВт)' }}
                    </span>
                </div>
                <div class="text-secondary small text-nowrap">
                    {{ formatDate(history.date) }}{{ history.days === null ? '' : ' · +' + history.days + ' дней' }}
                </div>
            </div>
            <div class="counter-history-list__meta mt-2 d-flex flex-wrap align-items-center gap-2">
                <span class="badge rounded-pill" :class="history.isVerified ? 'text-bg-success' : 'text-bg-secondary'">
                    {{ history.isVerified ? 'Проверено' : 'Не проверено' }}
                </span>
                <span v-if="history.claim" class="text-secondary small">
                    Оплачено: {{ formatMoney(history.claim.paid) }}/{{ formatMoney(history.claim.cost) }} по тарифу {{ formatMoney(history.claim.tariff) }}
                </span>
            </div>
            <div class="mt-3">
                <file-item
                    v-if="history.file"
                    :file="history.file"
                    :name="'Показания'"
                    :edit="false"
                />
            </div>
        </div>

        <div v-if="canLoadMore" class="counter-history-list__footer p-3 border-top text-center">
            <button v-if="!pending" class="btn btn-sm btn-outline-success" @click="$emit('load-more')">
                Показать ещё
            </button>
            <button v-else class="btn btn-sm btn-outline-success disabled" disabled>
                <i class="fa fa-spinner fa-spin"></i> Подгрузка
            </button>
        </div>
    </div>
</template>

<script setup>
import FileItem from '@common/files/FileItem.vue';

defineProps({
    canLoadMore: { type: Boolean, required: true },
    formatDate : { type: Function, required: true },
    formatMoney: { type: Function, required: true },
    histories  : { type: Array, required: true },
    pending    : { type: Boolean, required: true },
});

defineEmits(['load-more']);
</script>
