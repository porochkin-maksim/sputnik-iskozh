<template>
    <page-template>
        <template v-slot:sub>
            <file-list :limit="5"
                       v-model:count="filesCount" />
        </template>
        <template v-slot:main>
            <h1 v-if="newsCount">
                <a :href="Url.Routes.newsIndex.uri">
                    Новости
                </a>
            </h1>
            <news-list :limit="5"
                       v-model:items-count="newsCount" />
        </template>
    </page-template>
</template>

<script>
import Url         from '../../utils/Url.js';
import PageTemplate from './TwoColumnsPage.vue';
import NewsList    from '../news/NewsList.vue';
import FileList    from '../files/FileList.vue';

export default {
    name      : 'IndexPage',
    components: {
        PageTemplate,
        FileList,
        NewsList,
    },
    data () {
        return {
            Url,
            newsCount : 0,
            filesCount: 0,
        };
    },
    methods: {},
};
</script>