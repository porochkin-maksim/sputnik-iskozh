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
    <div class="list-item news-item">
        <div class="title">
            <div class="date">{{ news.dossier.publishedAt }}</div>
            <a class="name"
               :href="news.url"
               :class="!news.title ? 'no-name' : ''">
                {{ news.title ? news.title : 'Без названия' }}
            </a>
        </div>

        <div class="body">
            <bs-slider :images="images" :id="sliderId" class="mt-3"/>
            <div class="article ql-editor"
                 v-html="news.article"></div>
        </div>

        <div class="footer">
            <div class="files">
                <div v-for="(file, index) in news.files"
                     class="file my-2">
                    <file-list-item
                                    v-if="!file.isImage || edit"
                                    :file="file"
                                    :edit="edit"
                                    :index="index"
                                    :use-up-sort="index!==0"
                                    :use-down-sort="index!==news.files.length-1"
                                    :mode="FileItemMode.LINE"
                                    @updated="onUpdatedFile"
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
                        @click="chooseFiles">
                    <i class="fa fa-paperclip "></i>&nbsp;Файлы
                </button>
                <button class="btn btn-danger"
                        @click="deleteNews">
                    <i class="fa fa-trash "></i>&nbsp;Удалить
                </button>
            </div>

            <div class="d-none">
                <input type="file"
                       ref="fileElem"
                       multiple
                       @change="uploadFiles">
            </div>
        </div>
    </div>
</template>

<script>
import Url                      from '../../utils/Url.js';
import NewsItemEdit             from './NewsItemEdit.vue';
import Wrapper                  from '../common/Wrapper.vue';
import CustomInput              from '../common/form/CustomInput.vue';
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
        updatedItem (isDeleted = false) {
            this.$emit('updated', isDeleted);
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
        chooseFiles () {
            this.$refs.fileElem.click();
        },
        uploadFiles (event) {
            let form = new FormData();
            for (let i = 0; i < event.target.files.length; i++) {
                form.append(event.target.files[i].name, event.target.files[i])
            }

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
        onUpdatedFile() {
            this.updatedItem();
        },
        deleteFile (id) {
            if (!confirm('Удалить файл?')) {
                return;
            }

            let uri = Url.Generator.makeUri(Url.Routes.newsFileDelete, {
                id: id,
            });

            window.axios[Url.Routes.newsFileDelete.method](uri).then(response => {
                this.updatedItem(true);
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