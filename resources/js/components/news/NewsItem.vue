<template>
    <div v-if="modeEdit">
        <wrapper @close="modeEdit=false"
                 :container-class="'w-100'">
            <div class="container-fluid">
                <news-item-edit :model-value="id"
                                @updated="updatedItem()" />
            </div>
        </wrapper>
    </div>
    <div :style="[articleStyle]" class="position-relative">
        <div class="news-item w-100"
             :ref="(el) => (newsElementRef = el)">
            <div class="title">
                <h1>
                    <a class="name"
                       :href="news.url">
                        {{ news.title ? news.title : 'Без названия' }}
                    </a>
                </h1>
                <div class="date">{{ news.dossier.publishedAt }}</div>
            </div>

            <div class="body">
                <bs-slider :images="images"
                           :id="sliderId"
                           class="mt-3" />
                <div class="article ql-editor px-0"
                     v-html="news.article"
                     v-if="news.article"
                ></div>
            </div>

            <div class="footer">
                <div v-if="news.files.length"
                     class="mt-2">
                    <template v-for="(file, index) in news.files">
                        <file-item v-if="!file.isImage || edit"
                                   :file="file"
                                   :edit="edit"
                                   :index="index"
                                   :use-up-sort="index!==0"
                                   :use-down-sort="index!==news.files.length-1"
                                   :class="index!==news.files.length-1 ? 'mb-1' : ''"
                                   @updated="onUpdatedFile"
                        />
                    </template>
                </div>

                <div class="btn-group btn-group-sm mt-2"
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
            <div class="news-item-wrapper"
                 v-if="showExpand"
                 @click="forceExpandArticle=true">
                <button class="btn btn-link">
                    Показать ещё
                </button>
            </div>
        </div>
    </div>
</template>

<script>
import Url          from '../../utils/Url.js';
import Wrapper      from '../common/Wrapper.vue';
import CustomInput  from '../common/form/CustomInput.vue';
import NewsItemEdit from './NewsItemEdit.vue';
import BsSlider     from '../common/BsSlider.vue';
import FileItem     from './FileItem.vue';

export default {
    emits     : ['updated'],
    props     : [
        'news',
        'edit',
        'isList',
    ],
    components: {
        BsSlider,
        FileItem,
        CustomInput,
        Wrapper,
        NewsItemEdit,
    },
    data () {
        return {
            modeEdit          : false,
            newsElementRef    : null,
            forceExpandArticle: false,

            id: this.news.id,
            maxArticleHeight: 680,
        };
    },
    methods : {
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
                form.append(event.target.files[i].name, event.target.files[i]);
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
        onUpdatedFile () {
            this.updatedItem();
        },
    },
    watch   : {
        news: {
            handler (val) {
                this.id = val.id;
            },
            deep: true,
        },

    },
    computed: {
        images () {
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
        sliderId () {
            return 'newsSlider-' + this.id;
        },
        articleStyle () {
            if (this.showExpand) {
                return 'max-height:' + this.maxArticleHeight + 'px;overflow-y:hidden;';
            }
            return '';
        },
        showExpand () {
            return this.isList && this.calculateHeight >= this.maxArticleHeight && !this.forceExpandArticle;
        },
        calculateHeight () {
            return parseInt(this.newsElementRef?.clientHeight);
        },
    },
};
</script>