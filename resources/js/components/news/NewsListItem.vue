<template>
    <div v-if="modeEdit">
        <wrapper @close="modeEdit=false"
                 :container-class="'w-lg-75 w-md-100'">
            <div class="container-fluid">
                <news-item-edit :model-value="id"
                                @updated="updatedItem()" />
            </div>
        </wrapper>
    </div>
    <div class="list-item">
        <div class="body">
            <div>{{ news.id }}</div>
            <div class="d-flex justify-content-between">
                <div class="name"
                     :class="!news.name ? 'no-name' : ''">
                    {{ news.name ? news.name : 'Без названия' }}
                </div>
                <div class="date">&nbsp;<i class="fa fa-upload"></i> {{ news.dossier.updatedAt }}</div>
            </div>
            <div class="d-flex justify-content-between">
                <div class="category">{{ news.dossier.category }}</div>
                <div class="period">{{ news.dossier.period }}</div>
            </div>
        </div>

        <div class="footer">
            <div class="btn-group btn-group-sm"
                 v-if="edit">
                <button class="btn btn-primary"
                        @click="modeEdit=1">
                    <i class="fa fa-edit"></i>&nbsp;Редактировать
                </button>
                <button class="btn btn-info"
                        @click="chooseFile">
                    <i class="fa fa-paperclip "></i>&nbsp;Файл
                </button>
                <button class="btn btn-danger"
                        @click="deleteNews">
                    <i class="fa fa-trash "></i>&nbsp;Удалить
                </button>
            </div>
            <div class="files">
                <div v-for="file in news.files"
                     class="file">
                    <div class="d-flex justify-content-between align-items-center">
                        <a :href="file.url"
                           target="_blank"><i class="fa fa-file"></i>&nbsp;{{ file.name }}</a>

                        <div class="btn-group btn-group-sm"
                             v-if="edit">
                            <button class="btn btn-danger"
                                    @click="deleteFile(file.id)">
                                <i class="fa fa-trash"></i>
                            </button>
                        </div>
                    </div>
                </div>
            </div>

            <div class="d-none">
                <input type="file"
                       ref="fileElem"
                       @change="uploadFile">
            </div>
        </div>
    </div>
</template>

<script>
import NewsItemEdit from './NewsItemEdit.vue';
import Wrapper      from '../common/Wrapper.vue';
import CustomInput  from '../common/CustomInput.vue';
import Url          from '../../utils/Url.js';

export default {
    name      : 'NewsListItem',
    emits     : ['updated'],
    props     : [
        'news',
        'edit',
    ],
    components: {
        CustomInput,
        Wrapper,
        NewsItemEdit,
    },
    created () {

    },
    data () {
        return {
            modeEdit: false,

            id: this.news.id,
        };
    },
    methods: {
        updatedItem () {
            this.$emit('updated', true);
            this.modeEdit = false;
        },
        deleteNews () {
            if (!confirm('Удалить новость?')) {
                return;
            }

            let uri = Url.Generator.makeUri(Url.Routes.newsDelete, {
                id: this.id,
            });

            window.axios[Url.Routes.newsDelete.method](uri).then(response => {
                this.updatedItem();
            }).catch(response => {
                this.parseResponseErrors(response);
            });
        },
        chooseFile () {
            this.$refs.fileElem.click();
        },
        uploadFile (event) {
            let form = new FormData();
            form.append('file', event.target.files[0]);

            let uri = Url.Generator.makeUri(Url.Routes.newsFileUpload, {
                id: this.news.id,
            });

            window.axios[Url.Routes.newsFileUpload.method](
                uri,
                form,
            ).then(response => {
                if (response.data) {
                    this.updatedItem();
                }
            }).catch(response => {
                this.parseResponseErrors(response);
            });
        },
        deleteFile (id) {
            if (!confirm('Удалить файл?')) {
                return;
            }

            let uri = Url.Generator.makeUri(Url.Routes.newsFileDelete, {
                id: id,
            });

            window.axios[Url.Routes.newsFileDelete.method](uri).then(response => {
                this.updatedItem();
            }).catch(response => {
                this.parseResponseErrors(response);
            });
        },
    },
    watch  : {
        news: {
            handler (val) {
                this.id = val.id;
            },
            deep: true,
        },

    },
};
</script>