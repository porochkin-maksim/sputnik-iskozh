<template>
    <div class="d-flex justify-content-between align-items-center mb-2">
        <div class="d-flex align-items-center">
            <template v-if="computedStatuses?.length">
                <simple-select
                    v-model="verifiedStatusModel"
                    class="d-inline-block form-select-sm w-auto"
                    :options="computedStatuses"
                    @update:modelValue="$emit('change-status')"
                />
            </template>
            <template v-if="!isVerifiedStatus">
                <template v-if="histories.length">
                    <button
                        v-if="canEdit"
                        class="btn btn-success ms-2"
                        :disabled="!canSubmitAction"
                        @click="$emit('confirm')"
                    >
                        <i class="fa fa-check"></i> Подтвердить
                    </button>
                    <button
                        v-if="canDrop"
                        class="btn btn-danger ms-2"
                        :disabled="!canSubmitAction"
                        @click="$emit('delete')"
                    >
                        <i class="fa fa-trash"></i> Удалить
                    </button>
                </template>
            </template>
            <template v-else>
                <div class="d-flex ms-2">
                    <div class="input-group input-group-sm">
                        <inline-input
                            v-model="searchAccountModel"
                            name="users_search"
                            placeholder="Участок..."
                            :grouped="true"
                            :input-class="'flex-grow-1 rounded-0 border-0 shadow-none'"
                            @keyup="$emit('search')"
                        />
                        <button
                            class="btn btn-light border"
                            type="button"
                            @click="$emit('clear-search')"
                        >
                            <i class="fa fa-close"></i>
                        </button>
                    </div>
                </div>
            </template>
        </div>

        <div class="d-flex">
            <template v-if="isVerifiedStatus">
                <div>
                    <pagination
                        :total="total"
                        :per-page="perPage"
                        :prop-classes="'pagination-sm mb-0'"
                        @update="$emit('pagination-update', $event)"
                    />
                </div>
                <div>
                    <simple-select
                        v-model="perPageModel"
                        class="d-inline-block form-select-sm w-auto ms-2"
                        :options="[15, 25, 50, 100, 500]"
                        @update:modelValue="$emit('change-per-page')"
                    />
                </div>
            </template>
            <div class="d-flex align-items-center justify-content-center text-nowrap mx-2">
                Всего: {{ total }}
            </div>
        </div>
    </div>
</template>

<script setup>
import { computed } from 'vue';

import InlineInput        from '@common/form/InlineInput.vue';
import Pagination         from '@common/pagination/Pagination.vue';
import SimpleSelect       from '@common/form/SimpleSelect.vue';
import { usePermissions } from '@composables/usePermissions.js';

const props = defineProps({
    canSubmitAction : { type: Boolean, required: true },
    computedStatuses: { type: Array, required: true },
    histories       : { type: Array, required: true },
    isVerifiedStatus: { type: Boolean, required: true },
    perPage         : { type: Number, required: true },
    searchAccount   : { type: [String, null], default: null },
    total           : { type: [Number, null], default: null },
    verifiedStatus  : { type: String, required: true },
});

const emit = defineEmits(['change-status', 'change-per-page', 'clear-search', 'confirm', 'delete', 'pagination-update', 'update:perPage', 'update:searchAccount', 'update:verifiedStatus', 'search']);

const { has } = usePermissions();
const canEdit = computed(() => has('counters', 'edit'));
const canDrop = computed(() => has('counters', 'drop'));

const verifiedStatusModel = computed({
    get: () => props.verifiedStatus,
    set: (value) => emit('update:verifiedStatus', value),
});

const searchAccountModel = computed({
    get: () => props.searchAccount,
    set: (value) => emit('update:searchAccount', value),
});

const perPageModel = computed({
    get: () => props.perPage,
    set: (value) => emit('update:perPage', value),
});
</script>
