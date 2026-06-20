<template>
    <div class="d-flex flex-wrap justify-content-between align-items-center gap-2 mb-3 admin-toolbar">
        <div class="d-flex flex-wrap gap-2">
            <div class="btn-group" role="group">
                <button
                    v-if="canEdit && !periodClosed"
                    class="btn btn-success"
                    @click="$emit('add')"
                >
                    <i class="fa fa-plus" aria-hidden="true"></i>
                    Добавить счёт
                </button>
                <button
                    v-if="canEdit && periodId && !periodClosed"
                    class="btn btn-success"
                    @click="$emit('regular')"
                >
                    <i class="fa fa-calendar-plus-o" aria-hidden="true"></i>
                    Выставить регулярные
                </button>
            </div>
        </div>

        <div class="d-flex flex-wrap align-items-center justify-content-end gap-2">
            <pagination
                :total="total"
                :per-page="perPage"
                :page="currentPage"
                :prop-classes="'pagination-sm mb-0'"
                @update="$emit('pagination-update', $event)"
            />

            <simple-select
                v-model="perPageModel"
                class="d-inline-block form-select-sm w-auto"
                :options="[15, 25, 50, 100]"
                @update:modelValue="$emit('per-page-change')"
                aria-label="Элементов на странице"
            />

            <span class="badge bg-secondary">
                Всего: {{ total }}
            </span>

            <history-btn
                class="btn-link underline-none"
                :url="historyUrl"
                aria-label="История изменений"
            />
        </div>
    </div>
</template>

<script setup>
import { computed } from 'vue';

import HistoryBtn   from '@common/HistoryBtn.vue';
import Pagination   from '@common/pagination/Pagination.vue';
import SimpleSelect from '@common/form/SimpleSelect.vue';

const props = defineProps({
    canEdit      : { type: Boolean, required: true },
    currentPage  : { type: Number, required: true },
    historyUrl   : { type: [String, null], default: null },
    perPage      : { type: Number, required: true },
    periodId     : { type: [Number, String, null], default: null },
    periodClosed : { type: Boolean, default: false },
    total        : { type: Number, required: true },
});

const emit = defineEmits(['add', 'regular', 'pagination-update', 'per-page-change', 'update:perPage']);

const perPageModel = computed({
    get: () => props.perPage,
    set: (value) => emit('update:perPage', value),
});
</script>
