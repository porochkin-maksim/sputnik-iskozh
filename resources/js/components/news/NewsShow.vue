<template>
    <page-template>
        <template v-slot:main>
            <news-item :news="localNews"
                       :edit="localEdit"
                       @updated="onUpdateItem"
            />
        </template>
    </page-template>
</template>

<script>
import Url          from '../../utils/Url.js';
import NewsItem     from './NewsItem.vue';
import PageTemplate from '../pages/SingleColumnPage.vue';

export default {
    name      : 'NewsShow',
    props     : [
        'news',
        'edit',
    ],
    components: {
        NewsItem,
        PageTemplate,
    },
    data () {
        return {
            localNews: null,
            localEdit: false,
        };
    },
    created () {
        this.localNews = this.news;
        this.localEdit = this.edit;
    },
    methods: {
        onUpdateItem (isDeleted) {
            if (isDeleted) {
                location.href = Url.Routes.newsIndex.uri;
            }
            else {
                this.reloadItem();
            }
        },
        reloadItem() {
            let uri = Url.Generator.makeUri(Url.Routes.newsEdit, {
                id: this.localNews.id,
            });

            window.axios[Url.Routes.newsEdit.method](uri).then(response => {
                this.localNews = response.data.news;
                this.localEdit = response.data.edit;
            }).catch(response => {
                this.parseResponseErrors(response);
            });
        },
    },
};
</script>