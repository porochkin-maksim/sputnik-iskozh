<template>
    <div class="tickets-block">
        <loading-spinner
            v-if="!loaded"
            size="lg"
            color="primary"
            text="Загрузка заявок..."
            wrapper-class="my-5"
        />

        <template v-else>
            <tickets-toolbar
                :can-create="canCreate"
                :per-page="perPage"
                :settings-url="settingsUrl"
                :skip="skip"
                :total="total"
                @create-ticket="createTicket"
                @pagination-update="onPaginationUpdate"
                @per-page-change="onPerPageChange"
            />

            <tickets-filters
                v-model:category-id="filters.categoryId"
                v-model:priority="filters.priority"
                v-model:service-id="filters.serviceId"
                v-model:status="filters.status"
                :category-options="categoryOptions"
                :priority-options="priorityOptions"
                :service-options="serviceOptions"
                :status-options="statusOptions"
                @category-change="onCategoryChange"
            />

            <tickets-list
                :can-delete="canDelete"
                :sort-field="sortField"
                :sort-order="sortOrder"
                :tickets="tickets"
                @delete="deleteTicket"
                @sort="onSort"
            />
        </template>
    </div>
</template>

<script setup>
import LoadingSpinner      from '@common/LoadingSpinner.vue';
import TicketsFilters      from './tickets-block/TicketsFilters.vue';
import TicketsList         from './TicketsList.vue';
import TicketsToolbar      from './tickets-block/TicketsToolbar.vue';
import { useTicketsBlock } from './tickets-block/useTicketsBlock';

const props = defineProps({
    categories: { type: Array, default: () => [] },
    services  : { type: Array, default: () => [] },
    priorities: { type: Array, default: () => [] },
    statuses  : { type: Array, default: () => [] },
});

const {
          canCreate,
          canDelete,
          categoryOptions,
          createTicket,
          deleteTicket,
          filters,
          loaded,
          onCategoryChange,
          onPaginationUpdate,
          onPerPageChange,
          onSort,
          perPage,
          serviceOptions,
          settingsUrl,
          skip,
          sortField,
          sortOrder,
          statusOptions,
          priorityOptions,
          tickets,
          total,
      } = useTicketsBlock(props);
</script>
