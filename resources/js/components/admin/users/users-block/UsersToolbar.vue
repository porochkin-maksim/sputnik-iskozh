<template>
    <div class="d-flex justify-content-between align-items-center gap-2 mb-2 admin-toolbar">
        <div class="d-flex flex-wrap align-items-center gap-2">
            <a
                v-if="canCreate"
                class="btn btn-success me-2"
                :href="getViewLink(null)"
            >
                <i class="fa fa-plus" aria-hidden="true"></i>
                Добавить пользователя
            </a>

            <div>
                <div class="input-group input-group-sm">
                    <button
                        class="btn btn-light border"
                        type="button"
                        @click="onSearch"
                        :disabled="isLoading"
                    >
                        <i class="fa fa-search" aria-hidden="true"></i>
                    </button>
                    <input
                        ref="searchInput"
                        v-model="searchModel"
                        class="form-control"
                        placeholder="Поиск"
                        @keyup.enter="onSearch"
                        :disabled="isLoading"
                    />
                    <button
                        class="btn btn-light border"
                        type="button"
                        @click="onClearSearch"
                        :disabled="!searchModel || isLoading"
                    >
                        <i class="fa fa-close" aria-hidden="true"></i>
                    </button>
                </div>
            </div>

            <div class="btn-group" role="group">
                <a
                    class="btn btn-outline-success"
                    :href="importUrl"
                    title="Импор"
                >
                    <i class="fa fa-file-excel-o" aria-hidden="true"></i>
                    <span class="d-none d-sm-inline ms-1">Импорт</span>
                </a>
                <button
                    class="btn btn-success"
                    @click="$emit('export')"
                    :disabled="isLoading"
                    title="Экспорт"
                >
                    <i class="fa fa-file-excel-o" aria-hidden="true"></i>
                    <span class="d-none d-sm-inline ms-1">Экспорт</span>
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
                :disabled="isLoading"
            />
            <simple-select
                v-model="perPageModel"
                class="d-inline-block form-select-sm w-auto ms-2"
                :options="[15, 25, 50, 100]"
                @update:modelValue="onPerPageChange"
                :disabled="isLoading"
            />
            <div class="d-flex align-items-center justify-content-center mx-2">
                Всего: {{ total }}
            </div>
            <history-btn
                class="btn-link underline-none"
                :url="historyUrl"
                :disabled="isLoading"
            />
        </div>
    </div>
</template>

<script setup>
import {
    computed,
    ref,
} from 'vue';

import HistoryBtn   from '@common/HistoryBtn.vue';
import Pagination   from '@common/pagination/Pagination.vue';
import SimpleSelect from '@common/form/SimpleSelect.vue';

const props = defineProps({
    canCreate  : {
        type    : Boolean,
        required: true,
    },
    getViewLink: {
        type    : Function,
        required: true,
    },
    historyUrl : {
        type   : String,
        default: null,
    },
    importUrl  : {
        type   : String,
        default: null,
    },
    isLoading  : {
        type    : Boolean,
        required: true,
    },
    perPage    : {
        type    : Number,
        required: true,
    },
    currentPage: {
        type    : Number,
        required: true,
    },
    search     : {
        type    : String,
        required: true,
    },
    total      : {
        type    : Number,
        required: true,
    },
});

const emit = defineEmits(['search', 'clear-search', 'export', 'pagination-update', 'per-page-change', 'update:search', 'update:perPage']);

const searchInput = ref(null);

const searchModel = computed({
    get: () => props.search,
    set: (value) => emit('update:search', value),
});

const perPageModel = computed({
    get: () => props.perPage,
    set: (value) => emit('update:perPage', value),
});

const onSearch = () => {
    emit('search');
};

const onClearSearch = () => {
    emit('clear-search');
    searchInput.value?.focus();
};

const onPerPageChange = () => {
    emit('per-page-change');
};
</script>
