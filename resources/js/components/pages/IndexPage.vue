<template>
    <page-template>
        <template v-slot:main>
            <div class="mb-lg-0 mb-5">
                <div class="news-list w-100" v-if="news.length">
                    <template v-for="(item, index) in news">
                        <news-list-item :news="item"
                                        :edit="false"
                        />
                        <hr>
                    </template>
                </div>
            </div>
        </template>
    </page-template>
</template>

<script>
import Url          from '../../utils/Url.js';
import PageTemplate from './TwoColumnsPage.vue';
import NewsListItem from '../news/NewsItem.vue';

export default {
    name      : 'IndexPage',
    components: {
        NewsListItem,
        PageTemplate,
    },
    created () {
        this.loadNews();
    },
    data () {
        return {
            Url,
            news: [],
        };
    },
    methods: {
        loadNews () {
            window.axios[Url.Routes.newsList.method](Url.Routes.newsListAll.uri, {
                params: {
                    limit: 5,
                    skip : 0,
                },
            }).then(response => {
                this.news = response.data.news;
            }).catch(response => {
                this.parseResponseErrors(response);
            });
        },
    },
};
</script>