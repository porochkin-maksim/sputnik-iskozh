<template>
    <div class="card">
        <div class="card-header bg-white d-flex justify-content-between align-items-center">
            <h5 class="m-0">Счётчики</h5>

            <div>
                <button
                    v-if="canCreate"
                    class="btn btn-success"
                    @click="addCounterAction"
                >
                    <i class="fa fa-plus"></i>&nbsp;Добавить счётчик
                </button>
            </div>
        </div>

        <div class="card-body">
            <loading-spinner
                v-if="loading && counters.length === 0"
                size="lg"
                color="primary"
                text="Загрузка счётчиков..."
                wrapper-class="py-5"
            />

            <template v-else>
                <counters-table
                    v-if="counters && counters.length"
                    :account="account"
                    :counters="counters"
                    :format-date="formatDate"
                    :loading="loading"
                    :period="period"
                    :vue-id="vueId"
                    @edit-counter="editCounterAction"
                    @drop-counter="dropCounterAction"
                />

                <div v-else class="text-center text-muted py-3">
                    Нет счётчиков для отображения
                </div>
            </template>
        </div>

        <counter-item
            :account="account"
            :counter="selectedCounter"
            :show-form="showCounterForm"
            @counter-updated="onCounterUpdated"
        />
    </div>
</template>

<script setup>
import {
    defineProps,
} from 'vue';

import LoadingSpinner       from '@common/LoadingSpinner.vue';
import { useFormat }        from '@composables/useFormat.js';
import { useCountersBlock } from './counters-block/useCountersBlock';
import CounterItem          from './CounterItem.vue';
import CountersTable        from './counters-block/CountersTable.vue';

const props = defineProps({
    account: {
        type    : Object,
        required: true,
    },
});

const { formatDate } = useFormat();
const {
          canCreate,
          addCounterAction,
          counters,
          dropCounterAction,
          editCounterAction,
          loading,
          onCounterUpdated,
          period,
          selectedCounter,
          showCounterForm,
          vueId,
      }              = useCountersBlock(props);
</script>
