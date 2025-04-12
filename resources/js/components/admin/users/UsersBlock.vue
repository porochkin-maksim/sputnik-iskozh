<template>
    <div class="d-flex justify-content-between align-items-center mb-2">
        <div class="d-flex">
            <a class="btn btn-success me-2"
               v-if="actions.edit"
               :href="getViewLink(null)">Добавить пользователя
            </a>
            <div>
                <div class="input-group input-group-sm">
                    <button class="btn btn-light border"
                            @click="searchAction">
                        <i class="fa fa-search"></i>
                    </button>
                    <input class="form-control"
                           v-model="search"
                           name="users_search"
                           placeholder="Поиск"
                           @keyup="searchAction"
                           ref="search">
                    <button class="btn btn-light border"
                            type="button"
                            @click="listAction">
                        <i class="fa fa-close"></i>
                    </button>
                </div>
            </div>
        </div>
        <div class="d-flex">
            <div>
                <pagination :total="total"
                            :perPage="perPage"
                            :prop-classes="'pagination-sm mb-0'"
                            @update="onPaginationUpdate"
                />
            </div>
            <div class=" d-flex align-items-center justify-content-center mx-2">
                Всего: {{ total }}
            </div>
            <history-btn
                class="btn-link underline-none"
                :url="historyUrl" />
        </div>
    </div>
    <table class="table table-sm">
        <thead>
        <tr class="text-start">
            <th>№</th>
            <th>Фамилия</th>
            <th>Имя</th>
            <th>Отчество</th>
            <th>Почта</th>
            <th>Телефон</th>
            <th class="text-center">Членство</th>
            <th class="text-end">Участок</th>
        </tr>
        </thead>
        <tbody>
        <tr v-for="(user) in users" class="text-start">
            <td>
                <a :href="user.viewUrl">{{ user.id }}</a>
            </td>
            <td>{{ user.lastName }}</td>
            <td>{{ user.firstName }}</td>
            <td>{{ user.middleName }}</td>
            <td><span :data-copy="user.email" class="text-primary cursor-pointer">{{ user.email }}</span></td>
            <td>{{ user.phone }}</td>
            <td class="text-center">{{ user.ownershipDate ? 'Да' : 'Нет' }}</td>
            <td class="text-end">
                <template v-if="user.account?.viewUrl">
                    <a :href="user.account.viewUrl">{{ user.accountName }}</a>
                </template>
                <template v-else>{{ user.accountName }}</template>
            </td>
        </tr>
        </tbody>
    </table>
</template>

<script>
import ResponseError from '../../../mixin/ResponseError.js';
import Url           from '../../../utils/Url.js';
import HistoryBtn    from '../../common/HistoryBtn.vue';
import Pagination    from '../../common/pagination/Pagination.vue';
import SimpleSelect  from '../../common/form/SimpleSelect.vue';
import SearchSelect  from '../../common/form/SearchSelect.vue';
import CustomInput   from '../../common/form/CustomInput.vue';

export default {
    name      : 'UsersBlock',
    components: { CustomInput, SearchSelect, SimpleSelect, Pagination, HistoryBtn },
    mixins    : [
        ResponseError,
    ],
    props     : {
        permissions: {
            type   : Object,
            default: {},
        },
    },
    data () {
        return {
            vueId  : null,
            loading: false,

            user      : null,
            users     : [],
            historyUrl: null,
            actions   : {},

            total     : null,
            perPage   : 25,
            skip      : 0,
            routeState: 0,
            type      : 0,
            periodId  : 0,
            accountId : 0,
            Url       : Url,

            search        : null,
            searchProgress: null,
        };
    },
    created () {
        const urlParams = new URLSearchParams(window.location.search);
        this.perPage    = parseInt(urlParams.get('limit') ? urlParams.get('limit') : 25);
        this.skip       = parseInt(urlParams.get('skip') ? urlParams.get('skip') : 0);

        this.listAction();
    },
    methods: {
        getViewLink (id) {
            return Url.Generator.makeUri(Url.Routes.adminUserView, {
                id: id,
            });
        },
        listAction () {
            this.search = null;
            this.users  = [];
            let uri     = Url.Generator.makeUri(Url.Routes.adminUserIndex, {}, {
                limit: this.perPage,
                skip : this.skip,
            });
            window.history.pushState({ state: this.routeState++ }, '', uri);

            this.loading = true;
            window.axios[Url.Routes.adminUserList.method](Url.Routes.adminUserList.uri, {
                params: {
                    limit: this.perPage,
                    skip : this.skip,
                },
            }).then(response => {
                this.total      = response.data.total;
                this.actions    = response.data.actions;
                this.users      = response.data.users;
                this.historyUrl = response.data.historyUrl;
            }).catch(response => {
                this.parseResponseErrors(response);
            }).then(() => {
                this.loading = false;
            });
        },
        onPaginationUpdate (skip) {
            this.skip = skip;
            this.listAction();
        },
        searchAction () {
            if (!this.search) {
                return;
            }
            clearTimeout(this.searchProgress);
            this.searchProgress = setTimeout(() => {
                window.axios[Url.Routes.adminUserSearch.method](Url.Routes.adminUserSearch.uri, {
                    params: {
                        q: this.search,
                    },
                }).then(response => {
                    this.total = response.data.total;
                    this.users = response.data.users;
                }).catch(response => {
                    this.parseResponseErrors(response);
                }).finally(() => {
                    this.searchProgress = null;
                });
            }, 300);
            this.progress       = true;
        },
    },
};
</script>