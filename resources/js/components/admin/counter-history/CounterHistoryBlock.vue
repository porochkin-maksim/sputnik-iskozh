<template>
    <loading-spinner
        v-if="loading && histories.length === 0"
        size="lg"
        color="primary"
        text="Загрузка показаний..."
        wrapper-class="py-5"
    />

    <template v-else>
        <counter-history-toolbar
            :can-edit="canEdit"
            :can-drop="canDrop"
            :can-submit-action="canSubmitAction"
            :computed-statuses="computedStatuses"
            :histories="histories"
            :is-verified-status="isVerifiedStatus"
            :per-page="perPage"
            :search-account="searchAccount"
            :total="total"
            :verified-status="verifiedStatus"
            @change-status="loadHistories"
            @change-per-page="loadHistories"
            @clear-search="clearSearch"
            @confirm="confirmAction"
            @delete="deleteAction"
            @pagination-update="onPaginationUpdate"
            @search="searchAction"
            @update:perPage="perPage = $event"
            @update:searchAccount="searchAccount = $event"
            @update:verifiedStatus="verifiedStatus = $event"
        />

        <counter-history-table
            :can-edit="canEdit"
            :can-drop="canDrop"
            :all-check="allCheck"
            :can-check-action="canCheckAction"
            :format-date="formatDate"
            :histories="histories"
            :is-checked="isChecked"
            :is-verified-status="isVerifiedStatus"
            @check-all="onAllCheck"
            @drop="dropAction"
            @link="showLinkDialog"
            @toggle-item="onChanged"
        />
    </template>

    <counter-history-link-dialog
        :account-id="accountId"
        :accounts="accounts"
        :counter-id="counterId"
        :counters="counters"
        :hide-dialog="hideDialog"
        :loaded-counters="loadedCounters"
        :show-dialog="showDialog"
        @account-changed="getCounters"
        @close="closeLinkDialog"
        @link="linkAction"
        @update:accountId="accountId = $event"
        @update:counterId="counterId = $event"
    />
</template>

<script setup>
import {
    defineEmits,
} from 'vue';

import { useCounterHistoryBlock } from './counter-history-block/useCounterHistoryBlock';
import LoadingSpinner             from '@common/LoadingSpinner.vue';
import CounterHistoryLinkDialog   from './CounterHistoryLinkDialog.vue';
import CounterHistoryTable        from './CounterHistoryTable.vue';
import CounterHistoryToolbar      from './counter-history-block/CounterHistoryToolbar.vue';

const emit = defineEmits(['update:reload', 'update:selectedId', 'update:count']);

const {
          accountId,
          accounts,
          allCheck,
          canCheckAction,
          canEdit,
          canDrop,
          canSubmitAction,
          checked,
          clearSearch,
          closeLinkDialog,
          computedStatuses,
          confirmAction,
          counterId,
          counters,
          deleteAction,
          dropAction,
          formatDate,
          getCounters,
          histories,
          hideDialog,
          isChecked,
          isVerifiedStatus,
          linkAction,
          loading,
          loadedCounters,
          onAllCheck,
          onChanged,
          onPaginationUpdate,
          perPage,
          searchAccount,
          searchAction,
          showDialog,
          showLinkDialog,
          total,
          verifiedStatus,
      } = useCounterHistoryBlock({ reload: false }, emit);
</script>
