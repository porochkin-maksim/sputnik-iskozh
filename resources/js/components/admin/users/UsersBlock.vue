<template>
    <div>
        <users-toolbar
            :can-create="canCreate"
            :get-view-link="getViewLink"
            :history-url="historyUrl"
            :is-loading="isLoading"
            :per-page="perPage"
            :current-page="currentPage"
            :search="search"
            :total="total"
            @search="searchAction"
            @clear-search="clearSearchAction"
            @export="exportAction"
            @pagination-update="onPaginationUpdate"
            @per-page-change="onPerPageChange"
            @update:search="search = $event"
            @update:perPage="perPage = $event"
        />

        <users-filters
            :is-member="isMember"
            :is-not-member="isNotMember"
            :is-deleted="isDeleted"
            :has-verified-email="hasVerifiedEmail"
            :is-loading="isLoading"
            @update:is-member="isMember = $event"
            @update:is-not-member="isNotMember = $event"
            @update:is-deleted="isDeleted = $event"
            @update:has-verified-email="hasVerifiedEmail = $event"
        />

        <loading-spinner
            v-if="isLoading && !users.length"
            size="lg"
            color="primary"
            text="Загрузка пользователей..."
            wrapper-class="py-5"
        />

        <users-table
            v-else
            :users="users"
            :sort-field="sortField"
            :sort-order="sortOrder"
            :format-date="formatDate"
            @sort="sort"
            @copy-email="copyToClipboard"
        />
    </div>
</template>

<script setup>
import LoadingSpinner    from '@common/LoadingSpinner.vue';
import { useUsersBlock } from './users-block/useUsersBlock';
import UsersFilters      from './users-block/UsersFilters.vue';
import UsersTable        from './users-block/UsersTable.vue';
import UsersToolbar      from './users-block/UsersToolbar.vue';

const {
          canCreate,
          clearSearchAction,
          copyToClipboard,
          currentPage,
          exportAction,
          formatDate,
          getViewLink,
          historyUrl,
          isDeleted,
          isLoading,
          isMember,
          isNotMember,
          hasVerifiedEmail,
          onPaginationUpdate,
          onPerPageChange,
          perPage,
          search,
          searchAction,
          sort,
          sortField,
          sortOrder,
          total,
          users,
      } = useUsersBlock();
</script>
