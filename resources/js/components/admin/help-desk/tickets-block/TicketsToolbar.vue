<template>
    <div class="d-flex flex-wrap justify-content-between align-items-center gap-2 mb-3 admin-toolbar">
        <div class="d-flex flex-wrap align-items-center gap-2">
            <a class="btn btn-success me-2" :href="settingsUrl">
                <i class="fa fa-gears"></i> Настройки
            </a>
            <button v-if="canCreate" class="btn btn-success" @click="$emit('create-ticket')">
                <i class="fa fa-plus"></i> Создать заявку
            </button>
        </div>
        <div class="d-flex flex-wrap align-items-center justify-content-end gap-2">
            <pagination
                :total="total"
                :per-page="perPage"
                :page="Math.ceil(skip / perPage) + 1"
                :prop-classes="'pagination-sm mb-0'"
                @update="$emit('pagination-update', $event)"
            />
            <simple-select
                :model-value="perPage"
                class="d-inline-block form-select-sm w-auto"
                :options="[15, 25, 50, 100]"
                @update:model-value="$emit('per-page-change', $event)"
            />
            <span class="badge bg-secondary">Всего: {{ total }}</span>
        </div>
    </div>
</template>

<script setup>
import Pagination   from '@common/pagination/Pagination.vue';
import SimpleSelect from '@common/form/SimpleSelect.vue';

const props = defineProps({
    canCreate  : { type: Boolean, default: false },
    perPage    : { type: Number, required: true },
    settingsUrl: { type: String, required: true },
    skip       : { type: Number, required: true },
    total      : { type: Number, required: true },
});

defineEmits(['create-ticket', 'pagination-update', 'per-page-change']);
</script>
