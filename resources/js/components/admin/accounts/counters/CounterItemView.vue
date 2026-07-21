<template>
    <div class="d-flex mb-2 justify-content-between">
        <div>
            <button
                v-if="canEdit"
                class="btn btn-sm btn-outline-success admin-action-btn"
                :disabled="loading"
                @click="editCounterAction"
            >
                <i class="fa fa-edit"></i>&nbsp;Редактировать
            </button>
            <button
                v-if="canEdit"
                class="btn btn-sm btn-outline-success admin-action-btn ms-2"
                :disabled="loading"
                @click="addHistoryAction"
            >
                <i class="fa fa-plus"></i>&nbsp;Добавить показание
            </button>
        </div>
        <div>
            <button
                class="btn btn-outline-success btn-sm admin-action-btn"
                :disabled="loading"
                @click="refreshData"
            >
                <i class="fa fa-refresh" :class="loading ? 'fa-spin' : ''"></i>
                {{ loading ? 'Обновление...' : 'Обновить' }}
            </button>
            <button
                v-if="canEdit"
                class="btn btn-sm btn-outline-warning admin-action-btn ms-2"
                :disabled="loading"
                @click="rewatchAction"
            >
                <i class="fa fa-repeat"></i>&nbsp;Пересчитать
            </button>
            <history-btn
                class="btn-link underline-none ms-2"
                :url="counter?.historyUrl"
            />
        </div>
    </div>

    <counter-item
        :counter="counter"
        :show-form="showCounterForm"
        @counter-updated="onCounterUpdated"
    />

    <loading-spinner
        v-if="loading && histories.length === 0"
        size="lg"
        color="primary"
        text="Загрузка показаний..."
        wrapper-class="py-5"
    />

    <counter-history-table-panel
        v-else-if="counter && counter.id"
        :counter="counter"
        :can-edit="canEdit"
        :can-drop="canDrop"
        :format-date="formatDate"
        :format-money="formatMoney"
        :histories="histories"
        :loading="loading"
        :selected-history="selectedHistory"
        :total="total"
        :vue-id="vueId"
        @confirm-action="confirmAction"
        @add-claim="addClaimForHistory"
        @drop-history="dropHistoryAction"
        @edit-history="editHistoryAction"
        @history-updated="onHistoryUpdated"
        @load-more="loadMore"
    />
</template>

<script setup>
import {
    defineEmits,
    defineProps,
} from 'vue';

import LoadingSpinner           from '@common/LoadingSpinner.vue';
import HistoryBtn               from '@common/HistoryBtn.vue';
import { useCounterItemView }   from './counter-item-view/useCounterItemView';
import CounterHistoryTablePanel from './CounterHistoryTablePanel.vue';
import CounterItem              from '@components/admin/accounts/counters/CounterItem.vue';

const props = defineProps({
    modelValue: {
        type    : Object,
        required: true,
    },
});

const emit = defineEmits(['counterUpdated']);

const {
          canEdit,
          canDrop,
          addClaimForHistory,
          addHistoryAction,
          counter,
          dropHistoryAction,
          editHistoryAction,
          formatDate,
          formatMoney,
          histories,
          loading,
          loadMore,
          onHistoryUpdated,
          refreshData,
          selectedHistory,
          total,
          vueId,
          showCounterForm,
          onCounterUpdated,
          editCounterAction,
          confirmAction,
          rewatchAction,
      } = useCounterItemView(props, emit);
</script>
