<template>
    <template v-if="showPagination">
        <pagination :total="total"
                    :perPage="perPage"
                    @update="onPaginationUpdate"
        />
    </template>
    <div class="row custom-list list news-list">
        <news-list-item v-for="item in news"
                        :news="item"
                        :edit="edit"
                        @updated="loadList"
        />
    </div>
    <template v-if="showPagination && news.length > 3">
        <pagination :total="total"
                    :perPage="perPage"
                    @update="onPaginationUpdate"
        />
    </template>
</template>

<script>
import Url           from '../../utils/Url.js';
import Pagination    from '../common/pagination/Pagination.vue';
import ResponseError from '../../mixin/ResponseError.js';
import NewsListItem  from './NewsListItem.vue';

export default {
    components: {
        NewsListItem,
        Pagination,
    },
    mixins    : [
        ResponseError,
    ],
    props     : [
        'viewMode',
        'reloadList',
        'canEdit',
        'itemsCount',
        'showPagination',
        'limit',
    ],
    data () {
        return {
            showForm: false,

            news: [],
            edit: false,

            total: null,
            skip : 0,
        };
    },
    mounted () {
        this.loadList();
    },
    methods : {
        loadList () {
            window.axios[Url.Routes.newsList.method](Url.Routes.newsList.uri, {
                params: {
                    limit: this.perPage,
                    skip : this.skip,
                },
            }).then(response => {
                this.total = response.data.total;
                this.news  = response.data.news;
                this.edit  = response.data.edit;

                this.$emit('update:canEdit', this.edit);
                this.$emit('update:itemsCount', this.news.length);
            }).catch(response => {
                this.parseResponseErrors(response);
            });
        },
        onPaginationUpdate (skip) {
            this.skip = skip;
            this.loadList();
        },
    },
    watch   : {
        reloadList () {
            if (this.reloadList) {
                this.loadList();
                this.$emit('reloadList', false);
            }
        },
    },
    computed: {
        perPage () {
            return this.limit ? this.limit : 10;
        },
    },
};
</script>