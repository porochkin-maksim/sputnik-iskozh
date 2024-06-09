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
            <div class="title">
                <div class="date">{{ news.dossier.publishedAt }}</div>
                <b class="name"
                   :class="!news.title ? 'no-name' : ''">
                    {{ news.title ? news.title : 'Без названия' }}
                </b>
            </div>

            <bs-slider :images="images" :id="sliderId" class="mt-3"/>

            <div class="article ql-editor"
                 v-html="news.article"></div>
        </div>

        <div class="footer">
            <div class="files">
                <div v-for="file in news.files"
                     class="file">
                    <file-list-item
                                    v-if="!file.isImage || edit"
                                    :file="file"
                                    :edit="edit"
                                    :mode="FileItemMode.LINE"
                                    @updated="updatedItem"
                    />
                </div>
            </div>

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

            <div class="d-none">
                <input type="file"
                       ref="fileElem"
                       @change="uploadFile">
            </div>
        </div>
    </div>
</template>

<script>
import NewsItemEdit             from './NewsItemEdit.vue';
import Wrapper                  from '../common/Wrapper.vue';
import CustomInput              from '../common/CustomInput.vue';
import Url                      from '../../utils/Url.js';
import FileListItem             from '../files/FileListItem.vue';
import { MODE as FileItemMode } from '../files/FileListItem.vue';
import BsSlider                 from '../common/BsSlider.vue';


export default {
    emits     : ['updated'],
    props     : [
        'news',
        'edit',
    ],
    components: {
        BsSlider,
        FileListItem,
        CustomInput,
        Wrapper,
        NewsItemEdit,
    },
    data () {
        return {
            FileItemMode,
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
    computed: {
        images() {
            let images = [];
            if (this.news && this.news.files) {
                this.news.files.forEach(function (file) {
                    if (file.isImage) {
                        images.push(file);
                    }
                });
            }
            return images;
        },
        sliderId() {
            return 'news' + this.id;
        },
    },
};
</script>