<template>
    <page-template>
        <template v-slot:main>
            <div class="public-article-shell">
                <news-item :news="localNews"
                           :edit="canEdit"
                           @updated="onUpdateItem"
                />
            </div>
        </template>
    </page-template>
</template>

<script setup>
import {
    onMounted,
    ref,
}                           from 'vue';
import NewsItem             from './list/NewsItem.vue';
import PageTemplate         from '@components/public/pages/SingleColumnPage.vue';
import { ApiNewsEdit }      from '@api';
import { routeUri }         from '@utils/routeUri.js';
import { useResponseError } from '@composables/useResponseError';
import { usePermissions }   from '@composables/usePermissions.js';

const { parseResponseErrors } = useResponseError();
const { has }                 = usePermissions();

const props = defineProps({
    news: {
        type    : Object,
        required: true,
    },
});

const localNews = ref(props.news);
const canEdit   = has('news', 'edit');

const reloadItem = () => {
    ApiNewsEdit(localNews.value.id).then(response => {
        localNews.value = response.data.news;
    }).catch(response => {
        parseResponseErrors(response);
    });
};

const onUpdateItem = (isDeleted) => {
    if (isDeleted) {
        location.href = routeUri('newsIndex');
    }
    else {
        reloadItem();
    }
};

onMounted(() => {
    localNews.value = props.news;
});
</script>
