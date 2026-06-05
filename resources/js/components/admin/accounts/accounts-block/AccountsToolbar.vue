<template>
    <div class="d-flex align-items-center justify-content-between gap-2 mb-2 admin-toolbar">
        <div class="d-flex flex-wrap align-items-center gap-2">
            <button
                v-if="canCreate"
                class="btn btn-success me-2"
                @click="$emit('add-account')"
            >
                Добавить участок
            </button>
            <template v-if="allAccounts && allAccounts.length">
                <div class="d-flex">
                    <div class="input-group input-group-sm admin-search-group">
                        <button class="btn btn-light border" @click="$emit('search-change')">
                            <i class="fa fa-search"></i>
                        </button>
                        <inline-input
                            :model-value="search"
                            name="users_search"
                            placeholder="Поиск"
                            :grouped="true"
                            :input-class="'flex-grow-1 rounded-0 border-0 shadow-none'"
                            @keyup="$emit('search-change')"
                            @update:modelValue="$emit('update:search', $event)"
                        />
                        <button class="btn btn-light border" type="button" @click="$emit('clear-search')">
                            <i class="fa fa-close"></i>
                        </button>
                    </div>
                </div>
            </template>
        </div>
        <div class="d-flex flex-wrap align-items-center justify-content-end gap-2">
            <div>
                <pagination
                    :total="total"
                    :per-page="perPage"
                    :page="currentPage"
                    :prop-classes="'pagination-sm mb-0'"
                    @update="$emit('pagination-update', $event)"
                />
            </div>
            <div>
                <simple-select
                    :model-value="perPage"
                    :class="'d-inline-block form-select-sm w-auto ms-2'"
                    :options="[15, 25, 50, 100]"
                    @update:modelValue="$emit('update:per-page', $event)"
                    @change="$emit('per-page-change')"
                />
            </div>
            <div class="d-flex align-items-center justify-content-center mx-2">
                Всего: {{ total }}
            </div>
            <history-btn class="btn-link underline-none" :url="historyUrl" />
        </div>
    </div>
</template>

<script setup>
import HistoryBtn   from '@common/HistoryBtn.vue';
import Pagination   from '@common/pagination/Pagination.vue';
import SimpleSelect from '@common/form/SimpleSelect.vue';
import InlineInput  from '@common/form/InlineInput.vue';

defineProps({
    allAccounts: { type: [Array, null], default: null },
    canCreate  : { type: Boolean, required: true },
    currentPage: { type: Number, required: true },
    historyUrl : { type: [String, null], default: null },
    loading    : { type: Boolean, required: true },
    perPage    : { type: Number, required: true },
    search     : { type: String, default: '' },
    total      : { type: [Number, null], default: null },
});

defineEmits([
    'add-account',
    'clear-search',
    'pagination-update',
    'per-page-change',
    'search-change',
    'update:per-page',
    'update:search',
]);
</script>
