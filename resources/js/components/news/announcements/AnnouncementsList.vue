<template>
    <template v-if="showPagination">
        <div class="d-flex justify-content-end">
            <pagination :total="total"
                        :perPage="perPage"
                        @update="onPaginationUpdate"
            />
        </div>
    </template>
    <div class="news-list w-100">
        <template v-for="(item, index) in news">
            <news-list-item :news="item"
                            :edit="edit"
                            :is-list="true"
                            @updated="loadList"
            />
            <hr>
        </template>
    </div>
    <template v-if="showPagination && news.length > 3">
        <div class="mt-3 d-flex justify-content-end">
            <pagination :total="total"
                        :perPage="perPage"
                        @update="onPaginationUpdate"
            />
        </div>
    </template>
</template>

<script>
import Url           from '../../../utils/Url.js';
import ResponseError from '../../../mixin/ResponseError.js';
import Pagination    from '../../common/pagination/Pagination.vue';
import NewsListItem  from './../NewsItem.vue';

export default {
    components: {
        NewsListItem,
        Pagination,
    },
    mixins    : [
        ResponseError,
    ],
    props     : [
        'canEdit',
        'reloadList',
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
            window.axios[Url.Routes.newsList.method](Url.Routes.announcementsList.uri, {
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
                this.$emit('update:reloadList', false);
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