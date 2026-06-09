<template>
    <view-dialog
        :show="showDialog"
        :hide="hideDialog"
        @hidden="$emit('hidden')"
        v-if="claim && (canEdit || canView) && (claim.actions.edit || claim.actions.view)"
    >
        <template #title>
            {{ claim.id ? (canEdit && claim.actions.edit ? 'Редактирование услуги' : 'Просмотр услуги') : 'Добавление услуги' }}
        </template>

        <template #body>
            <div class="mb-3">
                <simple-select
                    v-model="claim.serviceId"
                    :disabled="selectedId || loading"
                    :options="servicesSelect"
                    label="Услуга"
                    :required="true"
                    @update:modelValue="$emit('service-id-changed', $event)"
                />
            </div>

            <div class="mb-3">
                <custom-input
                    v-model="claim.name"
                    :errors="errors?.name"
                    label="Своё название услуги"
                    type="text"
                    :disabled="!canEdit || !claim.actions.edit || loading"
                    @update:modelValue="$emit('clear-error', 'name')"
                />
            </div>

            <div class="mb-3">
                <custom-input
                    v-model="claim.tariff"
                    :errors="errors?.tariff"
                    label="Тариф"
                    type="number"
                    step="0.01"
                    :disabled="!canEdit || !claim.actions.edit || loading"
                    @update:modelValue="onTariffChanged"
                />
            </div>

            <div class="mb-3">
                <custom-input
                    v-model="claim.cost"
                    :errors="errors?.cost"
                    label="Стоимость"
                    type="number"
                    step="0.01"
                    :disabled="!canEdit || !claim.actions.edit || loading"
                    @update:modelValue="$emit('cost-changed')"
                />
            </div>

            <div class="mb-3">
                <custom-input
                    v-model="claim.quantity"
                    :errors="errors?.quantity"
                    label="Количество"
                    type="number"
                    step="1"
                    min="1"
                    :disabled="!canEdit || !claim.actions.edit || loading"
                    @update:modelValue="onQuantityChanged"
                />
            </div>
        </template>

        <template #footer v-if="canEdit && claim.actions.edit">
            <div class="d-flex justify-content-end w-100">
                <button
                    class="btn btn-success"
                    :disabled="!canSave || loading"
                    @click="$emit('save')"
                >
                    <i class="fa" :class="loading ? 'fa-spinner fa-spin' : 'fa-save'"></i>
                    {{ claim.id ? 'Сохранить' : 'Создать' }}
                </button>
            </div>
        </template>
    </view-dialog>
</template>

<script setup>
import CustomInput        from '@common/form/CustomInput.vue';
import SimpleSelect       from '@common/form/SimpleSelect.vue';
import ViewDialog         from '@common/ViewDialog.vue';
import { usePermissions } from '@composables/usePermissions.js';
import { computed }       from 'vue';

const { has } = usePermissions();
const canEdit = computed(() => has('invoice_services', 'edit'));
const canView = computed(() => has('invoice_services', 'view'));

const props = defineProps({
    claim         : { type: Object, default: null },
    canSave       : { type: Boolean, required: true },
    errors        : { type: Object, default: () => ({}) },
    hideDialog    : { type: Boolean, required: true },
    loading       : { type: Boolean, required: true },
    selectedId    : { type: [Number, String, null], default: null },
    servicesSelect: { type: Array, required: true },
    showDialog    : { type: Boolean, required: true },
});

const emit = defineEmits(['clear-error', 'cost-changed', 'hidden', 'save', 'service-id-changed', 'tariff-changed']);

const recalcCost = () => {
    const tariff   = parseFloat(props.claim?.tariff);
    const quantity = parseInt(props.claim?.quantity);
    if ( ! isNaN(tariff) && ! isNaN(quantity) && quantity >= 1) {
        props.claim.cost = (tariff * quantity).toFixed(2);
    }
};

const onTariffChanged = () => {
    recalcCost();
    emit('tariff-changed');
};

const onQuantityChanged = () => {
    recalcCost();
};
</script>
