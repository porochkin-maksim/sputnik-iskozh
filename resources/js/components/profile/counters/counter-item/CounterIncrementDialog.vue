<template>
    <view-dialog
        v-model:show="showProxy"
        v-model:hide="hideProxy"
        @hidden="$emit('hidden')"
    >
        <template #title>Изменение автопоказаний</template>
        <template #body>
            <div class="container-fluid profile-counter-dialog">
                <div class="mt-2">
                    <custom-input
                        v-model="incrementProxy"
                        :errors="errors.increment"
                        type="number"
                        :min="0"
                        :step="1"
                        label="Ежемесячное увеличение показаний на кВт"
                        :required="true"
                        @focusout="$emit('normalize-increment')"
                    />
                </div>
            </div>
        </template>
        <template #footer>
            <button
                v-if="!pending"
                class="btn btn-success"
                :disabled="!canSubmitIncrementAction"
                @click="$emit('submit')"
            >
                Сохранить
            </button>
            <button v-else class="btn border-0" disabled>
                <i class="fa fa-spinner fa-spin"></i> Сохранение
            </button>
        </template>
    </view-dialog>
</template>

<script setup>
import { computed } from 'vue';
import CustomInput from '@common/form/CustomInput.vue';
import ViewDialog from '@common/ViewDialog.vue';

const props = defineProps({
    canSubmitIncrementAction: { type: Boolean, required: true },
    errors                  : { type: Object, required: true },
    hideDialog              : { type: Boolean, required: true },
    increment               : { type: [Number, String, null], default: null },
    pending                 : { type: Boolean, required: true },
    showDialog              : { type: Boolean, required: true },
});

const emit = defineEmits([
    'hidden',
    'normalize-increment',
    'submit',
    'update:hideDialog',
    'update:increment',
    'update:showDialog',
]);

const showProxy = computed({
    get: () => props.showDialog,
    set: (value) => emit('update:showDialog', value),
});

const hideProxy = computed({
    get: () => props.hideDialog,
    set: (value) => emit('update:hideDialog', value),
});

const incrementProxy = computed({
    get: () => props.increment,
    set: (value) => emit('update:increment', value),
});
</script>
