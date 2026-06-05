<template>
    <view-dialog
        v-model:show="showProxy"
        v-model:hide="hideProxy"
        @hidden="$emit('hidden')"
    >
        <template #title>Внесение показаний счётчика</template>
        <template #body>
            <div class="container-fluid profile-counter-dialog">
                <div class="mt-2">
                    <custom-input
                        v-model="valueProxy"
                        :errors="errors.value"
                        type="number"
                        label="Текущие показания на счётчике"
                        :required="true"
                    />
                </div>
                <div class="mt-2">
                    <div v-if="file">
                        <button class="btn btn-sm btn-danger" @click="$emit('remove-file')">
                            <i class="fa fa-trash"></i>
                        </button>
                        &nbsp;
                        {{ file.name }}
                    </div>
                    <template v-else>
                        <button class="btn btn-outline-secondary w-100" @click="onChooseFile">
                            <i class="fa fa-paperclip"></i>&nbsp;Фото счётчика
                        </button>
                        <input
                            ref="fileElem"
                            class="d-none"
                            type="file"
                            accept="image/*"
                            @change="$emit('append-file', $event)"
                        />
                    </template>
                </div>
            </div>
        </template>
        <template #footer>
            <button
                v-if="!pending"
                class="btn btn-success"
                :disabled="!canSubmitAction"
                @click="$emit('submit')"
            >
                Добавить
            </button>
            <button v-else class="btn border-0" disabled>
                <i class="fa fa-spinner fa-spin"></i> Добавление
            </button>
        </template>
    </view-dialog>
</template>

<script setup>
import {
    computed,
    ref,
}                  from 'vue';
import CustomInput from '@common/form/CustomInput.vue';
import ViewDialog  from '@common/ViewDialog.vue';

const props = defineProps({
    canSubmitAction: { type: Boolean, required: true },
    errors         : { type: Object, required: true },
    file           : { type: [Object, null], default: null },
    hideDialog     : { type: Boolean, required: true },
    pending        : { type: Boolean, required: true },
    showDialog     : { type: Boolean, required: true },
    value          : { type: [Number, String, null], default: null },
});

const emit = defineEmits([
    'append-file',
    'hidden',
    'remove-file',
    'submit',
    'update:hideDialog',
    'update:showDialog',
    'update:value',
]);

const fileElem     = ref(null);
const onChooseFile = () => fileElem.value?.click();

const showProxy = computed({
    get: () => props.showDialog,
    set: (value) => emit('update:showDialog', value),
});

const hideProxy = computed({
    get: () => props.hideDialog,
    set: (value) => emit('update:hideDialog', value),
});

const valueProxy = computed({
    get: () => props.value,
    set: (value) => emit('update:value', value),
});
</script>
