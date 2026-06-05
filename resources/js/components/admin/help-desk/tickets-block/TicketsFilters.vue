<template>
    <div class="d-flex flex-wrap align-items-center gap-2 mb-3">
        <simple-select
            :model-value="categoryId"
            :options="categoryOptions"
            class="form-control-sm w-auto"
            label="Категория"
            @update:model-value="$emit('update:categoryId', $event)"
            @change="$emit('category-change')"
        />
        <simple-select
            :model-value="serviceId"
            :options="serviceOptions"
            class="form-control-sm w-auto"
            label="Услуга"
            :disabled="!categoryId && serviceOptions.length === 0"
            @update:model-value="$emit('update:serviceId', $event)"
        />
        <simple-select
            :model-value="status"
            :options="statusOptions"
            class="form-control-sm w-auto"
            label="Статус"
            @update:model-value="$emit('update:status', $event)"
        />
        <simple-select
            :model-value="priority"
            :options="priorityOptions"
            class="form-control-sm w-auto"
            label="Приоритет"
            @update:model-value="$emit('update:priority', $event)"
        />
    </div>
</template>

<script setup>
import SimpleSelect from '@common/form/SimpleSelect.vue';

defineProps({
    categoryOptions: { type: Array, required: true },
    categoryId     : { type: [String, Number], default: '' },
    priorityOptions: { type: Array, required: true },
    priority       : { type: [String, Number], default: '' },
    serviceId      : { type: [String, Number], default: '' },
    serviceOptions : { type: Array, required: true },
    status         : { type: [String, Number], default: '' },
    statusOptions  : { type: Array, required: true },
});

defineEmits(['category-change', 'update:categoryId', 'update:priority', 'update:serviceId', 'update:status']);
</script>
