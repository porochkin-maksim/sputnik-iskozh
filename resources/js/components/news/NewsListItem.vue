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
            <div class="d-flex justify-content-between">
                <div class="name"
                     :class="!news.title ? 'no-name' : ''">
                    {{ news.title ? news.title : 'Без названия' }}
                </div>
                <div class="date">{{ news.dossier.publishedAt }}</div>
            </div>
            <div class="article ql-editor"
                 v-html="news.article"></div>
        </div>

        <div class="footer">
            <div class="slider" v-if="images.length">
                <div :id="sliderId" class="carousel slide" data-bs-ride="carousel">
                    <div class="carousel-indicators">
                        <button v-for="(img, index) in images"
                                type="button" :data-bs-target="'#'+sliderId"
                                :class="index === 0 ? 'active' : ''" :aria-current="index === 0"
                                :data-bs-slide-to="index" :aria-label="'Slide ' + index"
                        ></button>
                    </div>
                    <div class="carousel-inner">
                        <a v-for="(img, index) in images"
                           :href="img.url" :data-lightbox="sliderId" :data-title="img.name"
                           class="carousel-item"
                           :class="index === 0 ? 'active' : ''"
                        >
                            <img :src="img.url" class="d-block mx-auto" alt="">
                        </a>
                    </div>
                    <button class="carousel-control-prev" type="button" :data-bs-target="'#'+sliderId" data-bs-slide="prev">
                        <span class="carousel-control-prev-icon" aria-hidden="true"></span>
                        <span class="visually-hidden">Previous</span>
                    </button>
                    <button class="carousel-control-next" type="button" :data-bs-target="'#'+sliderId" data-bs-slide="next">
                        <span class="carousel-control-next-icon" aria-hidden="true"></span>
                        <span class="visually-hidden">Next</span>
                    </button>
                </div>
            </div>

            <div class="files">
                <div v-for="file in news.files"
                     class="file">
                    <file-list-item
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


export default {
    emits     : ['updated'],
    props     : [
        'news',
        'edit',
    ],
    components: {
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
            return 'news' + this.id + this.images.length;
        },
    },
};
</script>