<template>
    <page-template>
        <template v-slot:main>
            <template v-if="edit">
                <div class="public-action-bar mb-2">
                    <a class="btn btn-success public-action-bar__button"
                       :href="formUrl">Добавить объявление
                    </a>
                </div>
            </template>
            <announcements-list v-model:reloadList="reloadList"
                                v-model:canEdit="edit"
                                :showPagination="true"
                                :current-page="currentPage"
                                class="mt-3"
            />
        </template>
    </page-template>
</template>

<script setup>
import { ref }           from 'vue';
import AnnouncementsList from './AnnouncementsList.vue';
import PageTemplate      from '@components/public/pages/SingleColumnPage.vue';
import { routeUri }      from '@utils/routeUri.js';

const props      = defineProps({
    currentPage: {
        type   : Number,
        default: 1,
    },
});

const reloadList = ref(false);
const edit       = ref(false);

const formUrl = routeUri('newsForm', { id: '' }, { category: 1 });
</script>
