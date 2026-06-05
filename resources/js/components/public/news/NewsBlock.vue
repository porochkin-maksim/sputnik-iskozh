<template>
    <page-template>
        <template v-slot:main>
            <template v-if="edit">
                <div class="public-action-bar mb-2">
                    <a class="btn btn-success public-action-bar__button"
                       href="/news/form">
                        Добавить новость
                    </a>
                </div>
            </template>
            <news-list v-model:reloadList="reloadList"
                       v-model:canEdit="edit"
                       :showPagination="true"
                       :current-page="currentPage"
                       class="mt-3"
            />
        </template>
    </page-template>
</template>

<script setup>
import { ref }      from 'vue';
import NewsList     from './list/NewsList.vue';
import PageTemplate from '@components/public/pages/SingleColumnPage.vue';

const props      = defineProps({
    currentPage: {
        type   : Number,
        default: 1,
    },
});

const reloadList = ref(false);
const edit       = ref(false);
</script>