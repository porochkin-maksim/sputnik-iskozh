<template>
    <page-template>
        <template v-slot:main>
            <div class="d-flex justify-content-between mb-3"
                 v-if="edit">
                <button class="btn btn-success"
                        v-on:click="showFormAction">Добавить опрос
                </button>
            </div>
            <div v-if="showForm">
                <wrapper @close="showForm=false"
                         :container-class="'w-100'">
                    <div class="container-fluid">
                        <poll-item-edit :model-value="id"
                                        @updated="createdItem" />
                    </div>
                </wrapper>
            </div>
            <div class="custom-list"
                 v-for="(poll, index) in polls">
                <div class="custom-item">
                    <div class="title">
                        <h1>
                            <a href=""
                               class="name">
                                {{ poll.title }}
                            </a>
                        </h1>
                        <div class="date">
                            {{ poll.startAt }} - {{ poll.endAt }}
                        </div>
                    </div>
                </div>
                <div class="btn-group btn-group-sm mt-2"
                     v-if="edit">
                    <button class="btn btn-success"
                            @click="editItem(poll.id)">
                        <i class="fa fa-edit"></i>&nbsp;Редактировать
                    </button>
                </div>
                <hr>
            </div>
        </template>
    </page-template>
</template>

<script>
import Url           from '../../utils/Url.js';
import ResponseError from '../../mixin/ResponseError.js';
import PageTemplate  from '../pages/SingleColumnPage.vue';
import Wrapper       from '../common/Wrapper.vue';
import CustomInput   from '../common/form/CustomInput.vue';
import PollItemEdit  from './PollItemEdit.vue';
import NewsItemEdit  from '../news/NewsItemEdit.vue';

export default {
    name      : 'PollsBlock',
    components: {
        NewsItemEdit,
        PollItemEdit,
        CustomInput,
        Wrapper,
        PageTemplate,
        FileList,
    },
    mixins    : [
        ResponseError,
    ],
    props     : {},
    created () {
        this.loadList();
    },
    data () {
        return {
            perPage: null,
            skip   : null,
            polls  : null,

            edit    : null,
            showForm: false,
            modeEdit: false,

            id: null,
        };
    },
    methods : {
        loadList () {
            window.axios[Url.Routes.pollsList.method](Url.Routes.pollsList.uri, {
                params: {
                    limit: this.perPage,
                    skip : this.skip,
                },
            }).then(response => {
                this.total = response.data.total;
                this.polls = response.data.polls;
                this.edit  = response.data.edit;
            }).catch(response => {
                this.parseResponseErrors(response);
            });
        },
        createdItem () {

        },
        editItem (id) {
            this.id       = id;
            this.modeEdit = true;
            this.showForm = true;
        },
        showFormAction () {
            this.showForm = !this.showForm;
        },
    },
    computed: {},
};
</script>