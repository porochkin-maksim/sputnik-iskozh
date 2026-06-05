<template>
    <view-dialog
        v-model:show="showProxy"
        v-model:hide="hideProxy"
        @hidden="$emit('hidden')"
    >
        <template #title>Выберите тип категории</template>
        <template #body>
            <simple-select
                v-model="typeProxy"
                :options="types"
                label="Тип заявки"
                :clearable="false"
            />
        </template>
        <template #footer>
            <button
                class="btn btn-primary"
                :disabled="creating"
                @click="$emit('create-category')"
            >
                <i
                    v-if="creating"
                    class="fa fa-spinner fa-spin"
                ></i>
                {{ creating ? 'Создание...' : 'Создать' }}
            </button>
        </template>
    </view-dialog>
</template>

<script setup>
import { computed } from 'vue';

import SimpleSelect from '@common/form/SimpleSelect.vue';
import ViewDialog   from '@common/ViewDialog.vue';

const props = defineProps({
    creating      : {
        type    : Boolean,
        required: true,
    },
    hideDialog    : {
        type    : Boolean,
        required: true,
    },
    selectedTypeId: {
        type   : [Number, String, null],
        default: null,
    },
    showDialog    : {
        type    : Boolean,
        required: true,
    },
    types         : {
        type    : Array,
        required: true,
    },
});

const emit = defineEmits([
    'create-category',
    'hidden',
    'update:hideDialog',
    'update:selectedTypeId',
    'update:showDialog',
]);

const showProxy = computed({
    get: () => props.showDialog,
    set: value => emit('update:showDialog', value),
});

const hideProxy = computed({
    get: () => props.hideDialog,
    set: value => emit('update:hideDialog', value),
});

const typeProxy = computed({
    get: () => props.selectedTypeId,
    set: value => emit('update:selectedTypeId', value),
});
</script>
