<template>
    <default-page :hide-left="!edit">
        <template v-slot:right>
            <news-list-item :news="news"
                            :edit="edit"
                            @updated="onUpdateItem"
            />
        </template>
    </default-page>
</template>

<script>
import Url          from '../../utils/Url.js';
import NewsListItem from './NewsListItem.vue';
import DefaultPage  from '../pages/DefaultPage.vue';

export default {
    name      : 'NewsItem',
    emits     : ['updated'],
    props     : [
        'news',
        'edit',
    ],
    components: {
        NewsListItem,
        DefaultPage,
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