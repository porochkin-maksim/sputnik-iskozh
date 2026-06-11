<template>
    <div class="search-block public-search-card">
        <div class="input-group w-100 public-search-block__input">
            <button class="btn btn-light border public-search-block__icon-btn"
                    type="submit"
                    @click="searchAction"
                    aria-label="Поиск"
            >
                <i class="fa fa-search"></i>
            </button>
            <inline-input v-model="search"
                          name="q"
                          placeholder="Поиск по сайту"
                          :grouped="true"
                          :input-class="'flex-grow-1 rounded-0 border-0 shadow-none'"
                          ref="searchInput"
                          @submit="searchAction" />
            <button class="btn btn-light border public-search-block__icon-btn"
                    type="button"
                    @click="search = null"
                    aria-label="Очистить поиск">
                <i class="fa fa-close"></i>
            </button>
        </div>
        <div v-if="search" class="mt-3">
            <template v-if="nothingFound">
                <div class="alert alert-light border mb-0 public-search-block__empty">
                    К сожалению ничего не найдено...
                </div>
            </template>
            <template v-else>
                <ul class="nav nav-tabs public-search-tabs">
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
                        <div class="public-search-results news-list w-100">
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
                        <div class="public-search-results files-list w-100">
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
    </div>
</template>

<script setup>
import {
    computed,
    onMounted,
    ref,
    watch,
}                           from 'vue';
import InlineInput          from '@common/form/InlineInput.vue';
import { ApiSearchSite }    from '@api';
import { useResponseError } from '@composables/useResponseError';
import { routeUri }         from '@utils/routeUri.js';
import NewsListItem         from '../news/list/NewsItem.vue';
import FileItem             from '../news/FileItem.vue';

const { parseResponseErrors } = useResponseError();

const routeState = ref(0);
const search     = ref(null);
const searchInput = ref(null);
const progress   = ref(false);
const delay      = ref(null);
const showNews   = ref(false);
const showFiles  = ref(false);
const news       = ref([]);
const files      = ref([]);

const nothingFound = computed(() => !progress.value && !news.value.length && !files.value.length);

const searchAction = () => {
    if (!search.value) {
        return;
    }

    progress.value = true;
    clearTimeout(delay.value);

    ApiSearchSite({}, {
        q: search.value,
    }).then(response => {
        news.value  = response.data.news;
        files.value = response.data.files;
        detectShow();
    }).catch(response => {
        parseResponseErrors(response);
    }).finally(() => {
        progress.value = false;
    });
};

const detectShow = () => {
    if (!showNews.value && !showFiles.value) {
        if (news.value.length) {
            showNewsTab();
        }
        else if (files.value.length) {
            showFilesTab();
        }
    }
};

const showNewsTab = () => {
    showNews.value  = true;
    showFiles.value = false;
};

const showFilesTab = () => {
    showFiles.value = true;
    showNews.value  = false;
};

watch(search, (val) => {
    let uri = routeUri('search');

    if (val) {
        if (delay.value) {
            clearTimeout(delay.value);
        }

        delay.value = setTimeout(() => searchAction(), 500);
        uri         = routeUri('search', {}, { q: val });
    }

    window.history.pushState({ state: routeState.value++ }, '', uri);
});

onMounted(() => {
    search.value = new URLSearchParams(window.location.search).get('q');
});
</script>
