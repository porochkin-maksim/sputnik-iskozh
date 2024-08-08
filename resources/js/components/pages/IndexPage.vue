<template>
    <page-template>
        <template v-slot:sub>
            <file-list :limit="10"
                       v-model:count="filesCount" />
        </template>
        <template v-slot:main>
            <div class="mb-lg-0 mb-5">
                <news-list :limit="5"
                           v-model:items-count="newsCount" />
                <hr class="d-block d-lg-none">
            </div>
        </template>
    </page-template>
</template>

<script>
import Url          from '../../utils/Url.js';
import PageTemplate from './TwoColumnsPage.vue';
import NewsList     from '../news/NewsList.vue';
import FileList     from '../files/FileList.vue';

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