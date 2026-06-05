<template>
    <view-dialog
        :show="showDialog"
        :hide="hideDialog"
        @hidden="$emit('close')"
    >
        <template #title>Добавление участка</template>
        <template #body>
            <div class="container-fluid">
                <div>
                    <custom-input
                        v-model="numberModel"
                        name="number"
                        :required="true"
                        :label="'Номер участка'"
                    />
                </div>
                <div class="mt-2">
                    <custom-input
                        v-model="sizeModel"
                        name="size"
                        :label="'Площадь (м²)'"
                        :type="'number'"
                        :min="0"
                        :step="1"
                        :required="true"
                    />
                </div>
                <div class="mt-2">
                    <custom-checkbox
                        v-model="isInvoicingModel"
                        name="is_invoicing"
                        :label="'Выставлять счета'"
                        switch-style
                    />
                </div>
                <div>
                    <custom-input
                        v-model="cadastreNumberModel"
                        name="cadastreNumber"
                        :label="'Кадастровый номер'"
                    />
                </div>
            </div>
        </template>
        <template #footer>
            <button
                class="btn btn-success"
                :disabled="!canSave || loading"
                @click="$emit('save')"
            >
                <i class="fa" :class="loading ? 'fa-spinner fa-spin' : 'fa-save'"></i>
                {{ loading ? 'Создание...' : 'Создать' }}
            </button>
        </template>
    </view-dialog>
</template>

<script setup>
import { computed } from 'vue';

import CustomCheckbox from '@common/form/CustomCheckbox.vue';
import CustomInput    from '@common/form/CustomInput.vue';
import ViewDialog     from '@common/ViewDialog.vue';

const props = defineProps({
    canSave   : { type: Boolean, required: true },
    formData  : { type: Object, required: true },
    hideDialog: { type: Boolean, required: true },
    loading   : { type: Boolean, required: true },
    showDialog: { type: Boolean, required: true },
});

const emit = defineEmits(['close', 'save', 'update:cadastreNumber', 'update:isInvoicing', 'update:number', 'update:size']);

const numberModel = computed({
    get: () => props.formData.number,
    set: (value) => emit('update:number', value),
});

const sizeModel = computed({
    get: () => props.formData.size,
    set: (value) => emit('update:size', value),
});

const isInvoicingModel = computed({
    get: () => props.formData.isInvoicing,
    set: (value) => emit('update:isInvoicing', value),
});

const cadastreNumberModel = computed({
    get: () => props.formData.cadastreNumber,
    set: (value) => emit('update:cadastreNumber', value),
});
</script>
