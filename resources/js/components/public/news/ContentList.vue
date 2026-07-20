<template>
    <div class="public-news-list w-100 position-relative">
        <div v-if="loading" class="public-list-loading">
            <loading-spinner />
        </div>
        <div v-if="short" class="index-news public-news-grid news-list row w-100 ms-0">
            <template v-for="(item, index) in items" :key="index">
                <a class="col-md-6 col-12 text-decoration-none mb-2 px-0"
                   :class="[index%2===0 ? 'pe-md-2' : 'pe-md-0']"
                   :href="item.url"
                >
                    <div class="custom-item news-item card h-100 hover-plate"
                         :class="[item.category === 1 ? 'news-item--announcement' : '']">
                        <div class="title card-body h-100 d-flex flex-column justify-content-between pb-2">
                            <div>
                                <span class="name">
                                    <i v-if="item.category === 1" class="fa fa-bullhorn text-warning me-1"></i>
                                    <i v-else class="fa fa-rss me-1"></i>
                                    {{ item.title ? item.title : 'Без названия' }}
                                </span>
                            </div>
                            <div class="date text-end mt-2">
                                <i class="fa fa-calendar"></i> {{ item.dossier?.publishedAt || item.publishedAt }}
                            </div>
                        </div>
                    </div>
                </a>
            </template>
        </div>
        <template v-else v-for="(item, index) in items">
            <div class="public-news-list__item"
                 :class="[variant === 'announcement' ? 'public-news-list__item--announcement' : '']">
                <news-list-item :news="item"
                                :edit="edit"
                                :is-list="true"
                                @updated="loadList"
                />
            </div>
        </template>
    </div>
    <template v-if="showPagination && !short">
        <div class="mt-3 d-flex justify-content-center">
            <pagination :total="total"
                        :perPage="perPage"
                        :page="currentPage"
                        @update="onPaginationUpdate"
            />
        </div>
    </template>
</template>

<script setup>
import {
    computed,
    onMounted,
    onUnmounted,
    ref,
    watch,
}                           from 'vue';
import { useResponseError } from '@composables/useResponseError';
import Pagination           from '@common/pagination/Pagination.vue';
import LoadingSpinner       from '@common/LoadingSpinner.vue';
import NewsListItem         from './list/NewsItem.vue';

const emit  = defineEmits(['update:canEdit', 'update:itemsCount', 'update:reloadList']);
const props = defineProps({
    fetchFn       : {
        type    : Function,
        required: true,
    },
    short         : {
        type   : Boolean,
        default: false,
    },
    variant       : {
        type   : String,
        default: 'news',
        validator: (value) => ['news', 'announcement'].includes(value),
    },
    canEdit       : {
        type   : Boolean,
        default: false,
    },
    reloadList    : {
        type   : Boolean,
        default: false,
    },
    itemsCount    : {
        type   : Number,
        default: 0,
    },
    showPagination: {
        type   : Boolean,
        default: false,
    },
    limit         : {
        type   : Number,
        default: 10,
    },
    currentPage   : {
        type   : Number,
        default: 1,
    },
});

const { parseResponseErrors } = useResponseError();
const items                   = ref([]);
const edit                    = ref(false);
const total                   = ref(null);
const skip                    = ref(0);
const loading                 = ref(false);
const shouldScrollTop         = ref(false);

const perPage = computed(() => props.limit || 10);

const loadList = () => {
    loading.value = true;
    props.fetchFn({
        limit: perPage.value,
        skip : skip.value,
    }).then(response => {
        total.value = response.data.total;
        items.value = response.data.news;
        edit.value  = response.data.edit;
        emit('update:canEdit', edit.value);
        emit('update:itemsCount', items.value.length);
    }).catch(response => {
        parseResponseErrors(response);
    }).finally(() => {
        loading.value = false;
        if (shouldScrollTop.value) {
            window.scrollTo({ top: 0, behavior: 'smooth' });
            shouldScrollTop.value = false;
        }
    });
};

const onPaginationUpdate = (value) => {
    skip.value            = value;
    shouldScrollTop.value = true;
    const currentPage     = Math.floor(value / perPage.value) + 1;
    const url             = new URL(window.location.href);
    url.searchParams.set('page', String(currentPage));
    window.history.pushState({ page: currentPage }, '', url);
    loadList();
};

const syncFromLocation = () => {
    const page = Math.max(1, Number(new URLSearchParams(window.location.search).get('page') || 1));
    skip.value = (page - 1) * perPage.value;
    loadList();
};

onMounted(() => {
    skip.value = (props.currentPage - 1) * perPage.value;
    loadList();
    window.addEventListener('popstate', syncFromLocation);
});

onUnmounted(() => {
    window.removeEventListener('popstate', syncFromLocation);
});

watch(() => props.reloadList, (value) => {
    if (value) {
        skip.value            = (props.currentPage - 1) * perPage.value;
        shouldScrollTop.value = false;
        loadList();
        emit('update:reloadList', false);
    }
});

watch(() => props.currentPage, (value) => {
    skip.value = (value - 1) * perPage.value;
});
</script>