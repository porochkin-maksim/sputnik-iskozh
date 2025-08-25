<template>
    <div>
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
                                @click="clearSearchAction">
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
    </div>
    <div class="mt-2">
        <div class="d-flex justify-content-between align-items-center mb-2">
            <div class="d-flex align-items-center">
                <div>
                    <custom-checkbox v-model="isMember"
                                     :label="'Члены СНТ'"
                    />
                </div>
                <div class="ms-2">
                    <custom-checkbox v-model="isNotMember"
                                     :label="'Не члены СНТ'"
                    />
                </div>
                <div class="ms-2">
                    <custom-checkbox v-model="isDeleted"
                                     :label="'Удалён'"
                    />
                </div>
            </div>
        </div>
    </div>
    <div class="table-responsive 1">
        <table class="table table-sm table-striped table-bordered">
            <thead>
            <tr class="text-start">
                <th class="cursor-pointer text-end"
                    @click="sort('id')">
                    №
                    <i v-if="sortField === 'id'"
                       :class="sortOrder === 'asc' ? 'fa fa-sort-asc' : 'fa fa-sort-desc'"></i>
                    <i v-else
                       class="fa fa-sort"></i>
                </th>
                <th class="text-end">
                    Участок
                </th>
                <th class="cursor-pointer"
                    @click="sort('last_name')">
                    Фамилия
                    <i v-if="sortField === 'last_name'"
                       :class="sortOrder === 'asc' ? 'fa fa-sort-asc' : 'fa fa-sort-desc'"></i>
                    <i v-else
                       class="fa fa-sort"></i>
                </th>
                <th class="cursor-pointer"
                    @click="sort('first_name')">
                    Имя
                    <i v-if="sortField === 'first_name'"
                       :class="sortOrder === 'asc' ? 'fa fa-sort-asc' : 'fa fa-sort-desc'"></i>
                    <i v-else
                       class="fa fa-sort"></i>
                </th>
                <th class="cursor-pointer"
                    @click="sort('middle_name')">
                    Отчество
                    <i v-if="sortField === 'middle_name'"
                       :class="sortOrder === 'asc' ? 'fa fa-sort-asc' : 'fa fa-sort-desc'"></i>
                    <i v-else
                       class="fa fa-sort"></i>
                </th>
                <th class="cursor-pointer"
                    @click="sort('email')">
                    Почта
                    <i v-if="sortField === 'email'"
                       :class="sortOrder === 'asc' ? 'fa fa-sort-asc' : 'fa fa-sort-desc'"></i>
                    <i v-else
                       class="fa fa-sort"></i>
                </th>
                <th>Телефон</th>
                <th>Членство</th>
                <th>Право</th>
            </tr>
            </thead>
            <tbody>
            <tr v-for="(user) in users"
                class="text-start">
                <td class="text-end">
                    <a :href="user.viewUrl">{{ user.id }}</a>
                </td>
                <td>
                    <template v-for="account in user.accounts">
                        <div class="d-flex justify-content-between align-items-center">
                            <span><i class="fa fa-user"
                                     :class="[account.fractionPercent ? 'text-success' : 'text-light']"></i>&nbsp;{{ account.fractionPercent }}&nbsp;</span>
                            <span>
                                <template v-if="account?.viewUrl">
                                    <a :href="account.viewUrl">{{ account.number }}</a>
                                </template>
                                <template v-else>{{ account.number }}</template>
                            </span>
                        </div>
                    </template>
                </td>
                <td>{{ user.lastName }}</td>
                <td>{{ user.firstName }}</td>
                <td>{{ user.middleName }}</td>
                <td><span :data-copy="user.email"
                          class="text-primary cursor-pointer">{{ user.email }}</span></td>
                <td>{{ user.phone }}</td>
                <td>{{ $formatDate(user.membershipDate) }}</td>
            </tr>
            </tbody>
        </table>
    </div>
</template>

<script>
import ResponseError  from '../../../mixin/ResponseError.js';
import Url            from '../../../utils/Url.js';
import HistoryBtn     from '../../common/HistoryBtn.vue';
import Pagination     from '../../common/pagination/Pagination.vue';
import SimpleSelect   from '../../common/form/SimpleSelect.vue';
import SearchSelect   from '../../common/form/SearchSelect.vue';
import CustomInput    from '../../common/form/CustomInput.vue';
import CustomCheckbox from '../../common/form/CustomCheckbox.vue';

export default {
    name      : 'UsersBlock',
    components: { CustomCheckbox, CustomInput, SearchSelect, SimpleSelect, Pagination, HistoryBtn },
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

            total      : null,
            perPage    : 25,
            skip       : 0,
            routeState : 0,
            type       : 0,
            periodId   : 0,
            accountId  : 0,
            isMember   : null,
            isNotMember: null,
            isDeleted  : null,
            Url        : Url,

            search        : null,
            searchProgress: null,

            sortField: null,
            sortOrder: null,
        };
    },
    created () {
        const urlParams  = new URLSearchParams(window.location.search);
        this.perPage     = parseInt(urlParams.get('limit') ? urlParams.get('limit') : 25);
        this.skip        = parseInt(urlParams.get('skip') ? urlParams.get('skip') : 0);
        this.sortField   = urlParams.get('sort_field') || 'id';
        this.sortOrder   = urlParams.get('sort_order') || 'asc';
        this.search      = urlParams.get('search') || '';
        this.isMember    = urlParams.get('isMember') === 'true' || null;
        this.isNotMember = urlParams.get('isMember') === 'false' || null;
        this.isDeleted   = urlParams.get('isDeleted') === 'true' || null;

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
        makeGetParams () {
            return {
                limit     : this.perPage,
                skip      : this.skip,
                sort_field: this.sortField,
                sort_order: this.sortOrder,
                search    : this.search || null,
                isMember  : this.isMember ? 'true' : this.isNotMember ? 'false' : null,
                isDeleted : this.isDeleted ? 'true' : null,
            };
        },
        listAction () {
            const getParams = this.makeGetParams();

            const uri = Url.Generator.makeUri(Url.Routes.adminUserIndex, {}, getParams);
            window.history.pushState({ state: this.routeState++ }, '', uri);

            this.loading = true;
            Url.RouteFunctions.adminUserList(getParams).then(response => {
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
            clearTimeout(this.searchProgress);
            this.searchProgress = setTimeout(() => {
                this.listAction();
            }, 300);
            this.progress       = true;
        },
        clearSearchAction () {
            this.search = null;
            this.searchAction();
        },
        exportAction () {
            const getParams = this.makeGetParams();
            const uri       = Url.Generator.makeUri(Url.Routes.adminUserExport, {}, getParams);
            window.open(uri, '_blank');
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
    watch  : {
        isMember () {
            if (this.isMember) {
                this.isNotMember = null;
                this.listAction();
            }
            else if (!this.isMember && !this.isNotMember) {
                this.listAction();
            }
        },
        isNotMember () {
            if (this.isNotMember) {
                this.isMember = null;
                this.listAction();
            }
            else if (!this.isMember && !this.isNotMember) {
                this.listAction();
            }
        },
        isDeleted () {
            this.listAction();
        },
    },
};
</script>