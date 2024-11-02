<template>
    <page-template>
        <template v-slot:main>
            <div v-if="news.length">
                <h1 class="title">
                    <a :href="Url.Routes.newsIndex.uri"
                       class="text-decoration-none">Новости</a>
                </h1>
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
            <hr>
            <template v-if="lockedNews && lockedNews.length">
                <h1 class="title">
                    Важное
                </h1>
                <div class="side-news">
                    <template v-for="(item, index) in lockedNews">
                        <div class="custom-item news-item w-100">
                            <div class="title">
                                <div class="date">{{ item.dossier.publishedAt }}</div>
                                <a class="name"
                                   :href="item.url">
                                    {{ item.title ? item.title : 'Без названия' }}
                                </a>
                            </div>
                        </div>
                        <hr v-if="index !== item.length - 1">
                    </template>
                </div>
            </template>
            <table class="table table-borderless small">
                <tbody>
                <tr class="border-bottom">
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
            <hr>
            <h4>
                <a :href="Url.Routes.proposal.uri"
                   class="btn btn-sm btn-success w-lg-100">
                    <i class="fa fa-envelope"></i>&nbsp;Написать&nbsp;предложение
                </a>
            </h4>
        </template>
    </page-template>
</template>

<script>
import Url          from '../../utils/Url.js';
import PageTemplate from './TwoColumnsPage.vue';
import NewsListItem from '../news/NewsItem.vue';

export default {
    name      : 'IndexPage',
    components: {
        NewsListItem,
        PageTemplate,
    },
    created () {
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
            window.axios[Url.Routes.newsListLocked.method](Url.Routes.newsListLocked.uri, {}).then(response => {
                this.lockedNews = response.data.news;
            }).catch(response => {
                this.parseResponseErrors(response);
            });
        },
        loadNews () {
            window.axios[Url.Routes.newsListAll.method](Url.Routes.newsListAll.uri, {
                params: {
                    limit: 5,
                    skip : 0,
                },
            }).then(response => {
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