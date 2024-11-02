<template>
    <div class="input-group w-100">
        <button class="btn btn-light border"
                type="submit"
                @click="searchAction"
        >
            <i class="fa fa-search"></i>
        </button>
        <input class="form-control"
               name="q"
               placeholder="Поиск по сайту"
               ref="search"
               v-model="search"
               @keyup.enter="searchAction"
        >
        <button class="btn btn-light border"
                type="button"
                @click="search = null">
            <i class="fa fa-close"></i>
        </button>
    </div>
    <div v-if="search" class="mt-3">
        <template v-if="nothingFound">
            <div class="alert">
                К сожалению ничего не найдено...
            </div>
        </template>
        <template v-else>
            <ul class="nav nav-tabs">
                <li class="nav-item" @click="showNewsTab">
                    <a class="nav-link" :class="showNews ? 'active' : ''">Новости ({{ news.length }})</a>
                </li>
                <li class="nav-item" @click="showFilesTab">
                    <a class="nav-link" :class="showFiles ? 'active' : ''">Файлы ({{ files.length }})</a>
                </li>
            </ul>
        </template>
        <div class="py-2">
            <template v-if="showNews">
                <div v-if="news.length">
                    <div class="custom-list news-list w-100">
                        <template v-for="(item, index) in news">
                            <news-list-item :news="item"
                                            :is-list="true"
                                            :edit="false"
                            />
                            <hr v-if="index !== news.length - 1">
                        </template>
                    </div>
                </div>
            </template>
            <template v-if="showFiles">
                <div v-if="files.length">
                    <div class="custom-list files-list w-100">
                        <template v-for="(file, index) in files">
                            <file-item :file="file"
                                       :index="index"
                                       :class="index!==files.length-1 ? 'mb-1' : ''"
                            />
                        </template>
                    </div>
                </div>
            </template>
        </div>
    </div>
</template>

<script>
import Url           from '../../utils/Url.js';
import CustomInput   from '../common/form/CustomInput.vue';
import ResponseError from '../../mixin/ResponseError.js';
import NewsListItem  from '../news/NewsItem.vue';
import FileItem      from '../news/FileItem.vue';

export default {
    name      : 'SearchBlock',
    components: {
        FileItem,
        NewsListItem,
        CustomInput,
    },
    mixins    : [
        ResponseError,
    ],
    mounted () {
        this.search = new URLSearchParams(window.location.search).get('q');
    },
    created () {

    },
    data () {
        return {
            routeState: 0,
            search    : null,
            progress  : false,
            delay     : null,
            showNews  : false,
            showFiles : false,

            news : [],
            files: [],
        };
    },
    methods: {
        searchAction () {
            if (!this.search) {
                return;
            }
            this.progress = true;
            clearTimeout(this.delay);
            window.axios[Url.Routes.searchSite.method](Url.Routes.searchSite.uri, {
                q: this.search,
            }).then(response => {
                this.news  = response.data.news;
                this.files = response.data.files;
                this.detectShow();
            }).catch(response => {
                this.parseResponseErrors(response);
            }).finally(() => {
                this.progress = false;
            });
        },
        detectShow () {
            if (!this.showNews && !this.showFiles) {
                if (this.news.length) {
                    this.showNewsTab();
                }
                else if (this.files.length) {
                    this.showFilesTab();
                }
            }
        },
        showNewsTab () {
            this.showNews  = true;
            this.showFiles = false;
        },
        showFilesTab () {
            this.showFiles = true;
            this.showNews  = false;
        },
    },
    watch  : {
        search (val) {
            let uri = Url.Routes.search.uri;

            if (val) {
                if (this.delay) {
                    clearTimeout(this.delay);
                }
                this.delay = setTimeout(() => this.searchAction(), 500);
                uri += '?q=' + val;
            }
            window.history.pushState({ state: this.routeState++ }, '', uri);
        },
    },
    computed: {
        nothingFound() {
            return !this.progress && !this.news.length && !this.files.length;
        },
    },
};
</script>