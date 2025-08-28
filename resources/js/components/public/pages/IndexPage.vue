<template>
    <page-template>
        <template v-slot:main>
            <template v-if="news.length">
                <div class="index-news custom-list news-list row w-100 ms-0">
                    <template v-for="(item, index) in news">
                        <a class="col-md-6 col-12 text-decoration-none mb-2 px-0"
                               :class="[index%2===0 ? 'pe-md-2' : 'pe-md-0']"
                               :href="item.url"
                            >
                            <div class="custom-item news-item card h-100">
                                <div class="title card-body h-100 d-flex flex-column justify-content-between pb-2">
                                    <div>
                                        <a class="name">
                                            {{ item.title ? item.title : 'Без названия' }}
                                        </a>
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
            <form :action="Url.Routes.search.uri" method="get">
                <div class="input-group">
                    <button class="btn btn-light border" type="submit">
                        <i class="fa fa-search"></i>
                    </button>
                    <input class="form-control" v-model="search" name="q" placeholder="Поиск по сайту" ref="search">
                    <button class="btn btn-light border" type="button" @click="search = null">
                        <i class="fa fa-close"></i>
                    </button>
                </div>
            </form>
            <template v-if="lockedNews && lockedNews.length">
                <div class="side-news custom-list news-list row w-100 ms-0 mt-2">
                    <template v-for="(item, index) in lockedNews">
                        <a class="col-md-6 col-lg-12 col-12 text-decoration-none pe-lg-0 mb-2 px-0"
                           :class="[index%2===0 ? 'pe-md-2' : 'pe-md-0']"
                           :href="item.url"
                        >
                            <div class="custom-item news-item card h-100">
                                <div class="title card-body h-100 d-flex flex-column justify-content-between p-2 pb-1">
                                    <a class="name">
                                        <i class="fa fa-bolt text-warning"></i>&nbsp;
                                        {{ item.title ? item.title : 'Без названия' }}
                                    </a>
                                    <div class="date text-end mt-2">
                                        <i class="fa fa-calendar"></i> {{ item.publishedAt }}
                                    </div>
                                </div>
                            </div>
                        </a>
                    </template>
                </div>
            </template>
            <table class="table table-bordered small">
                <tbody>
                <tr>
                    <th>График работы</th>
                </tr>
                <tr :class="!isWinter ? 'table-info' : ''">
                    <th>1 апреля - 31 октября</th>
                </tr>
                <tr :class="!isWinter ? 'table-info' : ''">
                    <td>Каждые четверг и воскресенье 12:00-14:00</td>
                </tr>
                <tr :class="isWinter ? 'table-info' : ''">
                    <th>1 ноября - 31 марта</th>
                </tr>
                <tr :class="isWinter ? 'table-info' : ''">
                    <td>Каждые 1-ое и 3-е воскресенье месяца 12:00-14:00</td>
                </tr>
                </tbody>
            </table>
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
                    <a :href="Url.Routes.contacts.uri">
                        Подробнее в разделе <b>"{{ Url.Routes.contacts.displayName }}"</b>
                    </a>
                </div>
            </template>
        </template>
    </page-template>
</template>

<script>
import Url          from '../../../utils/Url.js';
import PageTemplate from './TwoColumnsPage.vue';
import NewsListItem from '../news/list/NewsItem.vue';
import BsSlider     from '../../common/BsSlider.vue';
import FileItem     from '../news/FileItem.vue';
import { newsListIndex } from '../../../routes-functions.js';

export default {
    name      : 'IndexPage',
    components: {
        FileItem, BsSlider,
        NewsListItem,
        PageTemplate,
    },
    props: {
        qrPayment: null,
    },
    async created () {
        this.loadLockedNews();
        this.loadNews();
    },
    data () {
        return {
            Url,
            news      : [],
            lockedNews: [],
            search    : null,
        };
    },
    methods : {
        loadLockedNews () {
            Url.RouteFunctions.newsListLocked().then(response => {
                this.lockedNews = response.data.news;
            }).catch(response => {
                this.parseResponseErrors(response);
            });
        },
        loadNews () {
            Url.RouteFunctions.newsListIndex().then(response => {
                this.news = response.data.news;
            }).catch(response => {
                this.parseResponseErrors(response);
            });
        },
    },
    computed: {
        isWinter () {
            let date = new Date();
            return date.getMonth() >= 10 || date.getMonth() <= 2;
        },
    },
};
</script>