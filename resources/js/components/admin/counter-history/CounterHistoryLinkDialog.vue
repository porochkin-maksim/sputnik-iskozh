<template>
    <view-dialog
        :show="showDialog"
        :hide="hideDialog"
        @hidden="$emit('close')"
    >
        <template #title>Привязка показаний</template>
        <template #body>
            <div class="container-fluid">
                <label>Выберите участок</label>
                <search-select
                    v-model="accountIdModel"
                    :prop-class="'form-control mb-2'"
                    :items="accounts"
                    :placeholder="'Участок...'"
                    @update:model-value="$emit('account-changed')"
                />
                <template v-if="accountId && counters.length">
                    <label>Выберите счётчик</label>
                    <search-select
                        v-model="counterIdModel"
                        :prop-class="'form-control mb-2'"
                        :items="counters"
                        :placeholder="'Счётчик...'"
                    />
                </template>
                <template v-else-if="accountId && loadedCounters">
                    <div class="alert alert-warning">
                        У участка нет ни одного счётчика
                    </div>
                </template>
            </div>
        </template>
        <template #footer>
            <button
                class="btn btn-success"
                :disabled="!accountId || !counterId"
                @click="$emit('link')"
            >
                Привязать
            </button>
        </template>
    </view-dialog>
</template>

<script setup>
import { computed } from 'vue';

import SearchSelect from '@common/form/SearchSelect.vue';
import ViewDialog   from '@common/ViewDialog.vue';

const props = defineProps({
    accountId     : { type: [Number, String, null], default: null },
    accounts      : { type: Array, required: true },
    counterId     : { type: [Number, String, null], default: null },
    counters      : { type: Array, required: true },
    hideDialog    : { type: Boolean, required: true },
    loadedCounters: { type: Boolean, required: true },
    showDialog    : { type: Boolean, required: true },
});

const emit = defineEmits(['account-changed', 'close', 'link', 'update:accountId', 'update:counterId']);

const accountIdModel = computed({
    get: () => props.accountId,
    set: (value) => emit('update:accountId', value),
});

const counterIdModel = computed({
    get: () => props.counterId,
    set: (value) => emit('update:counterId', value),
});
</script>
