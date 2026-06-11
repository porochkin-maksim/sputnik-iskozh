<template>
    <content-list :fetch-fn="fetchFn"
                  :short="short"
                  :reload-list="reloadList"
                  :can-edit="canEdit"
                  :items-count="itemsCount"
                  :show-pagination="showPagination"
                  :limit="limit"
                  :current-page="currentPage"
                  @update:reloadList="emit('update:reloadList', $event)"
                  @update:canEdit="emit('update:canEdit', $event)"
                  @update:itemsCount="emit('update:itemsCount', $event)"
    />
</template>

<script setup>
import { ApiNewsList } from '@api';
import ContentList     from '../ContentList.vue';

const props = defineProps({
    short         : { type: Boolean, default: false },
    canEdit       : { type: Boolean, default: false },
    reloadList    : { type: Boolean, default: false },
    itemsCount    : { type: Number, default: 0 },
    showPagination: { type: Boolean, default: false },
    limit         : { type: Number, default: 10 },
    currentPage   : { type: Number, default: 1 },
});

const emit = defineEmits(['update:canEdit', 'update:itemsCount', 'update:reloadList']);

const fetchFn = ApiNewsList;
</script>