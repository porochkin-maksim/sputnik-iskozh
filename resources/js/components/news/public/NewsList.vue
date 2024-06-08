<template>
    <div class="row custom-list list news-list">
        <news-list-item v-for="item in news"
                        :news="item"
                        :edit="edit"
                        @updated="loadList"
        />
    </div>
</template>

<script>
import Url           from '../../../utils/Url.js';
import ResponseError from '../../../mixin/ResponseError.js';
import NewsListItem  from './../NewsListItem.vue';

export default {
    name      : 'NewsList',
    components: {
        NewsListItem,
    },
    mixins    : [
        ResponseError,
    ],
    props     : [
        'viewMode',
        'reloadList',
        'canEdit',
    ],
    data () {
        return {
            showForm: false,

            news: [],
            edit: false,
        };
    },
    mounted () {
        this.loadList();
    },
    methods: {
        loadList () {
            window.axios[Url.Routes.newsList.method](Url.Routes.newsList.uri).then(response => {
                this.news = response.data.news;
                this.edit = response.data.edit;
                this.$emit('update:canEdit', this.edit);
            }).catch(response => {
                this.parseResponseErrors(response);
            });
        },
    },
    watch  : {
        reloadList () {
            if (this.reloadList) {
                this.loadList();
                this.$emit('reloadList', false);
            }
        },
    },
};
</script>