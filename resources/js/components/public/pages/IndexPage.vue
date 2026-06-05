<template>
    <page-template>
        <template v-slot:main>
            <template v-if="news.length > 0">
                <div class="index-news public-news-grid news-list row w-100 ms-0">
                    <template v-for="(item, index) in news" :key="index">
                        <a class="col-md-6 col-12 text-decoration-none mb-2 px-0"
                           :class="[index%2===0 ? 'pe-md-2' : 'pe-md-0']"
                           :href="item.url"
                        >
                            <div class="custom-item news-item card h-100 hover-plate">
                                <div class="title card-body h-100 d-flex flex-column justify-content-between pb-2">
                                    <div>
                                        <span class="name">
                                            {{ item.title ? item.title : 'Без названия' }}
                                        </span>
                                    </div>
                                    <div class="date text-end mt-2">
                                        <i class="fa fa-calendar"></i> {{ item.publishedAt }}
                                    </div>
                                </div>
                            </div>
                        </a>
                    </template>
                </div>
            </template>
        </template>
        <template v-slot:sub>
            <form :action="searchUrl"
                  method="get">
                <div class="input-group">
                    <button class="btn btn-light border"
                            type="submit">
                        <i class="fa fa-search"></i>
                    </button>
                    <inline-input v-model="search"
                                  name="q"
                                  placeholder="Поиск по сайту"
                                  :grouped="true"
                                  :input-class="'flex-grow-1 rounded-0 border-0 shadow-none'" />
                    <button class="btn btn-light border"
                            type="button"
                            @click="search = null">
                        <i class="fa fa-close"></i>
                    </button>
                </div>
            </form>
            <template v-if="lockedNews && lockedNews.length">
                <div class="side-news public-news-grid news-list row w-100 ms-0 mt-2">
                    <template v-for="(item, index) in lockedNews" :key="index">
                        <a class="col-md-6 col-lg-12 col-12 text-decoration-none pe-lg-0 mb-2 px-0"
                           :class="[index%2===0 ? 'pe-md-2' : 'pe-md-0']"
                           :href="item.url"
                        >
                            <div class="custom-item news-item card h-100 hover-plate">
                                <div class="title card-body h-100 d-flex flex-column justify-content-between p-2 pb-1">
                                    <span class="name">
                                        <i class="fa fa-bolt text-warning"></i>&nbsp;
                                        {{ item.title ? item.title : 'Без названия' }}
                                    </span>
                                    <div class="date text-end mt-2">
                                        <i class="fa fa-calendar"></i> {{ item.publishedAt }}
                                    </div>
                                </div>
                            </div>
                        </a>
                    </template>
                </div>
            </template>

            <div class="mb-2">
                <state-schedule :schedule="schedule" />
            </div>

            <template v-if="qrPayment && qrPayment.url">
                <a :href="qrPayment.url"
                   class="d-flex flex-column justify-content-center align-items-center"
                   data-lightbox="qr_payment">
                    <b>QR-код для оплат</b>
                    <img :src="qrPayment.url"
                         style="width:200px;height:200px"
                         alt="QR код">
                </a>
                <br>
                <div class="text-center mb-2">
                    <a :href="contactsUrl">
                        Подробнее в разделе <b>"{{ contactsLabel }}"</b>
                    </a>
                </div>
            </template>
        </template>
    </page-template>
</template>

<script setup>
import {
    computed,
    onMounted,
    ref,
}                           from 'vue';
import InlineInput          from '@common/form/InlineInput.vue';
import PageTemplate         from './TwoColumnsPage.vue';
import StateSchedule        from '../StateSchedule.vue';
import {
    ApiNewsListIndex,
    ApiNewsListLocked,
}                           from '@api';
import {
    routeMeta,
    routeUri,
}                           from '@utils/routeUri.js';
import { useResponseError } from '@composables/useResponseError';

defineProps({
    qrPayment: null,
    schedule : {
        type   : Array,
        default: [],
    },
});

const news                    = ref([]);
const lockedNews              = ref([]);
const search                  = ref(null);
const { parseResponseErrors } = useResponseError();
const normalizeList           = (value) => {
    const toPlainItem = (item) => ({
        id         : item?.id ?? null,
        title      : typeof item?.title === 'string' ? item.title : '',
        publishedAt: typeof item?.publishedAt === 'string' ? item.publishedAt : '',
        url        : typeof item?.url === 'string' ? item.url : '#',
    });

    if (Array.isArray(value)) {
        return value.map(toPlainItem);
    }

    if (Array.isArray(value?.items)) {
        return value.items.map(toPlainItem);
    }

    if (Array.isArray(value?.data)) {
        return value.data.map(toPlainItem);
    }

    return [];
};

function loadLockedNews () {
    ApiNewsListLocked().then(response => {
        lockedNews.value = normalizeList(response.data.news);
    }).catch(response => {
        parseResponseErrors(response);
    });
}

function loadNews () {
    ApiNewsListIndex().then(response => {
        news.value = normalizeList(response.data.news);
    }).catch(response => {
        parseResponseErrors(response);
    });
}

const contactsLabel = computed(() => routeMeta('contacts').displayName);
const contactsUrl   = computed(() => routeUri('contacts'));
const searchUrl     = computed(() => routeUri('search'));

onMounted(() => {
    loadLockedNews();
    loadNews();
});
</script>
