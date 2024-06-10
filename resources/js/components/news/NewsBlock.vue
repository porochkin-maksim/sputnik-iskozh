<template>
    <default-page>
        <template v-slot:sub
                  v-if="edit">
            <div class="d-flex justify-content-between">
                <button class="btn btn-primary"
                        v-on:click="showFormAction">Добавить новость
                </button>
            </div>
        </template>
        <template v-slot:main>
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
                       class="mt-3"
            />
        </template>
    </default-page>
</template>

<script>
import ResponseError from '../../mixin/ResponseError.js';
import NewsItemEdit  from './NewsItemEdit.vue';
import NewsList      from './NewsList.vue';
import Wrapper       from '../common/Wrapper.vue';
import DefaultPage   from '../pages/DefaultPage.vue';

export default {
    name      : 'NewsBlock',
    components: {
        DefaultPage,
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