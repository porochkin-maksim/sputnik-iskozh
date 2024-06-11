<template>
    <page-template>
        <template v-slot:main>
            <news-list-item :news="news"
                            :edit="edit"
                            @updated="onUpdateItem"
            />
        </template>
    </page-template>
</template>

<script>
import Url          from '../../utils/Url.js';
import NewsListItem from './NewsListItem.vue';
import PageTemplate  from '../pages/TwoColumnsPage.vue';

export default {
    name      : 'NewsItem',
    emits     : ['updated'],
    props     : [
        'news',
        'edit',
    ],
    components: {
        NewsListItem,
        PageTemplate,
    },
    methods   : {
        onUpdateItem (isDeleted) {
            if (isDeleted) {
                location.href = Url.Routes.newsIndex.uri;
            }
            else {
                location.reload();
            }
        },
    },
};
</script>