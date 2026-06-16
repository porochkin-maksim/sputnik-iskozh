<template>
    <view-dialog
        v-model:show="showValue"
        @hidden="$emit('close')"
    >
        <template #title>Распределение транзакции</template>

        <template #body>
            <div class="alert alert-info py-2 mb-3">
                <strong>Сумма транзакции:</strong>
                {{ maxCost.toLocaleString('ru-RU', { minimumFractionDigits: 2 }) }} ₽
            </div>

            <div class="mb-3">
                <search-select
                    v-model="claimId"
                    :items="availableClaims"
                    label="Услуга"
                />
            </div>

            <div v-if="selectedClaim" class="small text-muted mb-2">
                Остаток услуги: {{ selectedClaim.delta.toLocaleString('ru-RU', { minimumFractionDigits: 2 }) }} ₽
            </div>

            <div class="mb-3">
                <label class="form-label">Сумма</label>
                <div class="input-group">
                    <input
                        v-model.number="cost"
                        type="number"
                        step="0.01"
                        :max="maxCost"
                        class="form-control"
                    />
                    <button
                        class="btn btn-outline-secondary"
                        type="button"
                        @click="setFullCost"
                    >
                        Вся сумма
                    </button>
                </div>
            </div>
        </template>

        <template #footer>
            <div class="d-flex justify-content-end w-100">
                <button
                    class="btn btn-secondary me-2"
                    @click="$emit('close')"
                    type="button"
                >
                    Отмена
                </button>
                <button
                    class="btn btn-success"
                    :disabled="!claimId || cost <= 0"
                    @click="confirm"
                >
                    <i class="fa fa-check"></i> Распределить
                </button>
            </div>
        </template>
    </view-dialog>
</template>

<script setup>
import { ref, computed, watch } from 'vue';
import ViewDialog               from '@common/ViewDialog.vue';
import SearchSelect             from '@common/form/SearchSelect.vue';

const props = defineProps({
    show           : { type: Boolean, default: false },
    availableClaims: { type: Array, default: () => [] },
    maxCost        : { type: Number, default: 0 },
});

const emit = defineEmits(['update:show', 'confirm', 'close']);

const showValue = computed({
    get: () => props.show,
    set: (val) => emit('update:show', val),
});

const claimId = ref(null);
const cost    = ref(0);

const selectedClaim = computed(() => {
    return props.availableClaims.find(c => c.value === claimId.value);
});

watch(() => props.show, (val) => {
    if (val) {
        claimId.value = null;
        cost.value    = 0;
    }
});

const setFullCost = () => {
    if (selectedClaim.value) {
        cost.value = Math.min(selectedClaim.value.delta, props.maxCost);
    }
};

const confirm = () => {
    if ( ! claimId.value || cost.value <= 0) return;
    emit('confirm', { claimId: claimId.value, cost: cost.value });
    claimId.value = null;
    cost.value    = 0;
};
</script>
