<template>
    <div class="d-flex flex-wrap justify-content-between align-items-start gap-2 mb-3 admin-toolbar">
        <div class="d-flex flex-wrap align-items-center gap-2">
            <simple-select
                v-if="computedPeriods.length"
                v-model="periodIdModel"
                class="form-select-sm w-auto"
                :options="computedPeriods"
                @update:modelValue="$emit('change')"
                aria-label="Фильтр по периоду"
            />

            <simple-select
                v-if="computedTypes.length"
                v-model="typeModel"
                class="form-select-sm w-auto"
                :options="computedTypes"
                @update:modelValue="$emit('change')"
                aria-label="Фильтр по типу"
            />

            <simple-select
                v-if="computedPaidStatus.length"
                v-model="paidStatusModel"
                class="form-select-sm w-auto"
                :options="computedPaidStatus"
                @update:modelValue="$emit('change')"
                aria-label="Фильтр по статусу оплаты"
            />

            <div
                v-if="computedAccounts.length"
                class="input-group input-group-sm"
                style="min-width: 80px; max-width: 150px;"
            >
                <button
                    class="btn btn-light border px-2"
                    @click="$emit('search')"
                    :disabled="!searchAccount && searchAccount !== ''"
                    type="button"
                    aria-label="Поиск"
                >
                    <i class="fa fa-search" aria-hidden="true"></i>
                </button>
                <inline-input
                    ref="searchInput"
                    v-model="searchAccountModel"
                    placeholder="Участок"
                    :grouped="true"
                    :input-class="'flex-grow-1 rounded-0 border-0 shadow-none'"
                    @submit="$emit('search')"
                    aria-label="Поиск по участку"
                />
                <button
                    v-if="searchAccount"
                    class="btn btn-light border px-2"
                    @click="$emit('clear-search')"
                    type="button"
                    aria-label="Очистить поиск"
                >
                    <i class="fa fa-close" aria-hidden="true"></i>
                </button>
            </div>
        </div>

        <div class="d-flex flex-wrap gap-2">
            <div class="btn-group" role="group">
                <button
                    v-if="canEdit && periodId"
                    class="btn btn-outline-success"
                    @click="$emit('recalc')"
                >
                    <i class="fa fa-calculator" aria-hidden="true"></i>
                    <span class="d-none d-sm-inline ms-1">Пересчёт</span>
                </button>
                <button
                    v-if="canEdit && periodId"
                    class="btn btn-outline-danger"
                    @click="$emit('reset-payments')"
                >
                    <i class="fa fa-undo" aria-hidden="true"></i>
                    <span class="d-none d-sm-inline ms-1">Сброс оплат</span>
                </button>
                <button
                    v-if="canEdit && periodId"
                    class="btn btn-outline-success"
                    @click="$emit('import')"
                >
                    <i class="fa fa-file-excel-o" aria-hidden="true"></i>
                    <span class="d-none d-sm-inline ms-1">Импорт</span>
                </button>
                <button
                    class="btn btn-success"
                    @click="$emit('export')"
                    aria-label="Экспорт в Excel"
                >
                    <i class="fa fa-file-excel-o" aria-hidden="true"></i>
                    <span class="d-none d-sm-inline ms-1">Экспорт</span>
                </button>
            </div>
        </div>
    </div>
</template>

<script setup>
import { computed } from 'vue';

import InlineInput  from '@common/form/InlineInput.vue';
import SimpleSelect from '@common/form/SimpleSelect.vue';

const props = defineProps({
    canEdit           : { type: Boolean, required: true },
    computedAccounts  : { type: Array, required: true },
    computedPaidStatus: { type: Array, required: true },
    computedPeriods   : { type: Array, required: true },
    computedTypes     : { type: Array, required: true },
    periodId          : { type: [Number, String, null], default: null },
    searchAccount     : { type: [String, null], default: null },
    type              : { type: [Number, String, null], default: null },
    paidStatus        : { type: [String, null], default: null },
});

const emit = defineEmits(['change', 'search', 'clear-search', 'export', 'import', 'recalc', 'reset-payments', 'update:searchAccount', 'update:periodId', 'update:type', 'update:paidStatus']);

const periodIdModel = computed({
    get: () => props.periodId,
    set: (value) => emit('update:periodId', value),
});

const typeModel = computed({
    get: () => props.type,
    set: (value) => emit('update:type', value),
});

const paidStatusModel = computed({
    get: () => props.paidStatus,
    set: (value) => emit('update:paidStatus', value),
});

const searchAccountModel = computed({
    get: () => props.searchAccount,
    set: (value) => emit('update:searchAccount', value),
});
</script>
