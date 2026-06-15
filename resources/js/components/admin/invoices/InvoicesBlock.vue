<template>
    <loading-spinner
        v-if="!loaded"
        size="lg"
        color="primary"
        text="Загрузка счетов..."
        wrapper-class="my-5"
    />

    <template v-else>
        <div v-if="!periods || !periods.length">
            <div class="alert alert-warning d-flex align-items-center gap-2">
                <i class="fa fa-warning fa-lg" aria-hidden="true"></i>
                <div>
                    <p class="mb-1">Не найдено ни одного периода</p>
                    <a :href="periodIndexUrl" class="alert-link">
                        Создайте период
                    </a>
                </div>
            </div>
        </div>

        <template v-else>
            <invoices-toolbar
                :can-edit="canEdit"
                :current-page="currentPage"
                :history-url="historyUrl"
                :per-page="perPage"
                :period-id="periodId"
                :total="total"
                @add="makeAction"
                @regular="makeRegularAction"
                @pagination-update="onPaginationUpdate"
                @per-page-change="listAction"
                @update:perPage="perPage = $event"
            />

            <invoices-filters
                :can-edit="canEdit"
                :computed-accounts="computedAccounts"
                :computed-paid-status="computedPaidStatus"
                :computed-periods="computedPeriods"
                :computed-types="computedTypes"
                :period-id="periodId"
                :search-account="searchAccount"
                :type="type"
                :paid-status="paidStatus"
                @change="listAction"
                @search="searchAction"
                @clear-search="clearSearchAction"
                @export="exportAction"
                @import="importAction"
                @recalc="recalcAction"
                @update:searchAccount="searchAccount = $event"
                @update:periodId="periodId = $event"
                @update:type="type = $event"
                @update:paidStatus="paidStatus = $event"
            />

            <summary-block
                :account-id="parseInt(accountId)"
                :account-search="searchAccount"
                :type="parseInt(type)"
                :period-id="parseInt(periodId)"
                class="mb-3"
            />

            <invoices-list
                :invoices="invoices"
                :sort-field="sortField"
                :sort-order="sortOrder"
                @sort="onSort"
            />

            <invoice-item-edit
                v-if="invoice && canEdit"
                :model-value="invoice"
                :accounts="accounts"
                :periods="activePeriods"
                :types="activeTypes"
                @updated="listAction"
            />
        </template>
    </template>
</template>

<script setup>
import { computed }         from 'vue';
import { useInvoicesBlock } from './invoices-block/useInvoicesBlock';
import { usePermissions }   from '@composables/usePermissions.js';
import LoadingSpinner       from '@common/LoadingSpinner.vue';
import SummaryBlock         from '@components/shared/summary/SummaryBlock.vue';
import InvoicesList         from './InvoicesList.vue';
import InvoiceItemEdit      from './InvoiceItemEdit.vue';
import InvoicesFilters      from './invoices-block/InvoicesFilters.vue';
import InvoicesToolbar      from './invoices-block/InvoicesToolbar.vue';

const {
          accountId,
          accounts,
          activePeriods,
          activeTypes,
          clearSearchAction,
          computedAccounts,
          computedPaidStatus,
          computedPeriods,
          computedTypes,
          currentPage,
          exportAction,
          historyUrl,
          importAction,
          recalcAction,
          invoice,
          invoices,
          listAction,
          loaded,
          makeAction,
          makeRegularAction,
          onPaginationUpdate,
          onSort,
          paidStatus,
          perPage,
          periodId,
          periodIndexUrl,
          periods,
          searchAccount,
          searchAction,
          sortField,
          sortOrder,
          total,
          type,
      } = useInvoicesBlock();

const { has } = usePermissions();
const canEdit = computed(() => has('invoices', 'edit'));
</script>
