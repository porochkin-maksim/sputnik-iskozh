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
            <div class="ms-2">
                <button class="btn btn-success"
                        @click="exportAction">
                    <i class="fa fa-file-excel-o"></i>
                </button>
            </div>
        </div>
        <div class="d-flex">
            <div>
                <pagination :total="total"
                            :perPage="perPage"
                            :page="Math.ceil(this.skip > 0 ? this.skip / this.perPage : 0) + 1"
                            :prop-classes="'pagination-sm mb-0'"
                            @update="onPaginationUpdate"
                />
            </div>
            <div>
                <simple-select v-model="perPage"
                               :class="'d-inline-block form-select-sm w-auto ms-2'"
                               :items="[15,25,50,100]"
                               @change="listAction"
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
    <div class="table-responsive 1">
        <table class="table table-sm table-striped table-bordered">
        <thead>
        <tr class="text-start">
            <th class="cursor-pointer text-end" @click="sort('id')">
                №
                <i v-if="sortField === 'id'" :class="sortOrder === 'asc' ? 'fa fa-sort-asc' : 'fa fa-sort-desc'"></i>
                <i v-else class="fa fa-sort"></i>
            </th>
            <th class="text-end cursor-pointer" @click="sort('account_sort')">
                Участок
                <i v-if="sortField === 'account_sort'" :class="sortOrder === 'asc' ? 'fa fa-sort-asc' : 'fa fa-sort-desc'"></i>
                <i v-else class="fa fa-sort"></i>
            </th>
            <th class="cursor-pointer" @click="sort('last_name')">
                Фамилия
                <i v-if="sortField === 'last_name'" :class="sortOrder === 'asc' ? 'fa fa-sort-asc' : 'fa fa-sort-desc'"></i>
                <i v-else class="fa fa-sort"></i>
            </th>
            <th class="cursor-pointer" @click="sort('first_name')">
                Имя
                <i v-if="sortField === 'first_name'" :class="sortOrder === 'asc' ? 'fa fa-sort-asc' : 'fa fa-sort-desc'"></i>
                <i v-else class="fa fa-sort"></i>
            </th>
            <th class="cursor-pointer" @click="sort('middle_name')">
                Отчество
                <i v-if="sortField === 'middle_name'" :class="sortOrder === 'asc' ? 'fa fa-sort-asc' : 'fa fa-sort-desc'"></i>
                <i v-else class="fa fa-sort"></i>
            </th>
            <th class="cursor-pointer" @click="sort('email')">
                Почта
                <i v-if="sortField === 'email'" :class="sortOrder === 'asc' ? 'fa fa-sort-asc' : 'fa fa-sort-desc'"></i>
                <i v-else class="fa fa-sort"></i>
            </th>
            <th>Телефон</th>
            <th>Членство</th>
        </tr>
        </thead>
        <tbody>
        <tr v-for="(user) in users" class="text-start">
            <td class="text-end">
                <a :href="user.viewUrl">{{ user.id }}</a>
            </td>
            <td class="text-end">
                <template v-if="user.account?.viewUrl">
                    <a :href="user.account.viewUrl">{{ user.accountName }}</a>
                </template>
                <template v-else>{{ user.accountName }}</template>
            </td>
            <td>{{ user.lastName }}</td>
            <td>{{ user.firstName }}</td>
            <td>{{ user.middleName }}</td>
            <td><span :data-copy="user.email" class="text-primary cursor-pointer">{{ user.email }}</span></td>
            <td>{{ user.phone }}</td>
            <td>{{ $formatDate(user.ownershipDate) }}</td>
        </tr>
        </tbody>
    </table>
    </div>
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

            sortField: null,
            sortOrder: null,
        };
    },
    created () {
        const urlParams = new URLSearchParams(window.location.search);
        this.perPage    = parseInt(urlParams.get('limit') ? urlParams.get('limit') : 25);
        this.skip       = parseInt(urlParams.get('skip') ? urlParams.get('skip') : 0);
        this.sortField  = urlParams.get('sort_field') || 'account_sort';
        this.sortOrder  = urlParams.get('sort_order') || 'asc';
        this.search     = urlParams.get('search') || '';

        this.listAction();
        if (this.search) {
            this.searchAction();
        }
    },
    methods: {
        getViewLink (id) {
            return Url.Generator.makeUri(Url.Routes.adminUserView, {
                id: id,
            });
        },
        listAction () {
            let uri = Url.Generator.makeUri(Url.Routes.adminUserIndex, {}, {
                limit     : this.perPage,
                skip      : this.skip,
                sort_field: this.sortField,
                sort_order: this.sortOrder,
                search    : this.search || null,
            });
            window.history.pushState({ state: this.routeState++ }, '', uri);

            this.loading = true;
            window.axios[Url.Routes.adminUserList.method](Url.Routes.adminUserList.uri, {
                params: {
                    limit     : this.perPage,
                    skip      : this.skip,
                    sort_field: this.sortField,
                    sort_order: this.sortOrder,
                    search    : this.search,
                },
            }).then(response => {
                this.users      = [];
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
                this.listAction();
            }, 300);
            this.progress       = true;
        },
        exportAction () {
            window.open(Url.Generator.makeUri(Url.Routes.adminUserExport), '_blank');
        },
        sort (field) {
            if (this.sortField === field) {
                this.sortOrder = this.sortOrder === 'asc' ? 'desc' : 'asc';
            }
            else {
                this.sortOrder = 'asc';
            }
            this.sortField = field;
            this.listAction();
        },
    },
};
</script>