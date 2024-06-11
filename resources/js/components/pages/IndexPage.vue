<template>
    <default-page>
        <template v-slot:sub>
            <h5 v-if="filesCount">
                <a :href="Url.Routes.filesIndex.uri">
                    Последние файлы
                </a>
            </h5>
            <file-list :limit="5"
                       v-model:count="filesCount" />
        </template>
        <template v-slot:main>
            <h1 v-if="newsCount">
                <a :href="Url.Routes.newsIndex.uri">
                    Новости
                </a>
            </h1>
            <news-list :limit="3"
                       v-model:items-count="newsCount" />
        </template>
    </default-page>
</template>

<script>
import Url         from '../../utils/Url.js';
import DefaultPage from '../pages/DefaultPage.vue';
import NewsList    from '../news/NewsList.vue';
import FileList    from '../files/FileList.vue';

export default {
    name      : 'IndexPage',
    components: {
        DefaultPage,
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