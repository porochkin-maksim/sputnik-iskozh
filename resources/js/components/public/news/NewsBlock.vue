<template>
    <page-template>
        <template v-slot:main>
            <template v-if="edit">
                <div class="d-flex justify-content-between mb-2">
                    <button class="btn btn-success"
                            v-on:click="showFormAction">Добавить новость
                    </button>
                </div>
            </template>
            <div v-if="showForm">
                <wrapper @close="showForm=false"
                         :container-class="'w-lg-75 w-md-100'">
                    <div class="container-fluid">
                        <news-item-edit :model-value="id"
                                        @updated="createdItem" />
                    </div>
                </wrapper>
            </div>
            <news-list v-model:reloadList="reloadList"
                       v-model:canEdit="edit"
                       :showPagination="true"
                       class="mt-3"
            />
        </template>
    </page-template>
</template>

<script>
import ResponseError from '../../../mixin/ResponseError.js';
import NewsItemEdit  from './NewsItemEdit.vue';
import NewsList      from './NewsList.vue';
import Wrapper       from '../../common/Wrapper.vue';
import PageTemplate  from '../pages/SingleColumnPage.vue';

export default {
    name      : 'NewsBlock',
    components: {
        PageTemplate,
        Wrapper,
        NewsItemEdit,
        NewsList,
    },
    mixins    : [
        ResponseError,
    ],
    data () {
        return {
            showForm  : false,
            reloadList: false,
            edit      : false,
            id        : null,
        };
    },
    methods: {
        showFormAction () {
            this.showForm = !this.showForm;
        },
        createdItem () {
            this.reloadList = true;
            this.showForm   = false;
        },
    },
};
</script>