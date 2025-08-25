<template>
    <div class="d-flex align-items-center justify-content-between mb-2">
        <div class="d-flex">
            <button class="btn btn-success me-2"
                    v-if="actions.edit"
                    v-on:click="makeAction">Добавить участок
            </button>
            <template v-if="allAccounts && allAccounts.length">
                <div class="d-flex">
                    <div class="input-group input-group-sm">
                        <button class="btn btn-light border"
                                @click="listAction">
                            <i class="fa fa-search"></i>
                        </button>
                        <input class="form-control"
                               v-model="search"
                               name="users_search"
                               placeholder="Поиск"
                               @keyup="listAction"
                               ref="search">
                        <button class="btn btn-light border"
                                type="button"
                                @click="clearSearch">
                            <i class="fa fa-close"></i>
                        </button>
                    </div>
                </div>
            </template>
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
    <div class="table-responsive">
        <table class="table table-sm table-striped table-bordered">
            <thead>
            <tr class="text-center">
                <th class="cursor-pointer text-end" @click="sort('id')">
                    №
                    <i v-if="sortField === 'id'" :class="sortOrder === 'asc' ? 'fa fa-sort-asc' : 'fa fa-sort-desc'"></i>
                    <i v-else class="fa fa-sort"></i>
                </th>
                <th class="cursor-pointer" @click="sort('sort_value')">
                    Номер
                    <i v-if="sortField === 'sort_value'" :class="sortOrder === 'asc' ? 'fa fa-sort-asc' : 'fa fa-sort-desc'"></i>
                    <i v-else class="fa fa-sort"></i>
                </th>
                <th class="cursor-pointer" @click="sort('size')">
                    Площадь (м²)
                    <i v-if="sortField === 'size'" :class="sortOrder === 'asc' ? 'fa fa-sort-asc' : 'fa fa-sort-desc'"></i>
                    <i v-else class="fa fa-sort"></i>
                </th>
                <th>Кадастр</th>
                <th>Выставление счетов</th>
                <th>Пользователи</th>
                <th></th>
            </tr>
            </thead>
            <tbody>
            <tr v-for="account in accounts" class="align-middle">
                <td class="text-end">
                    <a :href="account.viewUrl">
                        {{ account.id }}
                    </a>
                </td>
                <td class="text-end">{{ account.number }}</td>
                <td class="text-end">{{ account.size }}</td>
                <td class="text-center">{{ account.cadastreNumber }}</td>
                <td class="text-center">
                    <i :class="account.isInvoicing ? 'fa fa-check text-success' : ''"></i>
                </td>
                <td class="text-end">
                    <ol v-if="account.users && account.users.length" class="mb-0 ps-0">
                        <template v-for="user in account.users">
                            <li class="d-flex justify-content-between align-items-center">
                                <span>
                                    <template v-if="user?.viewUrl">
                                        <a :href="user.viewUrl">{{ user.fullName }}</a>
                                    </template>
                                    <template v-else>{{ user.fullName }}</template>
                                </span>
                                <span>
                                    <i class="fa fa-user" :class="[user.fractionPercent ? 'text-success' : 'text-light']"></i>&nbsp;{{ user.fractionPercent }}
                                </span>
                            </li>
                        </template>
                    </ol>
                </td>
                <td>
                    <history-btn :disabled="!account.historyUrl"
                                 class="btn-link underline-none"
                                 :url="account.historyUrl ? account.historyUrl : ''" />
                </td>
            </tr>
            </tbody>
        </table>
    </div>
    <account-item-add v-if="account"
                      :model-value="account"
                      @updated="onCreatedEvent" />
</template>

<script>
import ResponseError  from '../../../mixin/ResponseError.js';
import Url            from '../../../utils/Url.js';
import HistoryBtn     from '../../common/HistoryBtn.vue';
import SearchSelect   from '../../common/form/SearchSelect.vue';
import Pagination     from '../../common/pagination/Pagination.vue';
import AccountItemAdd from './AccountItemAdd.vue';
import SimpleSelect   from '../../common/form/SimpleSelect.vue';
import { log10 }      from 'chart.js/helpers';

export default {
    name      : 'AccountsBlock',
    components: { SimpleSelect, AccountItemAdd, Pagination, SearchSelect, HistoryBtn },
    mixins    : [
        ResponseError,
    ],
    data () {
        return {
            account    : null,
            accounts   : [],
            allAccounts: [],
            historyUrl : null,
            actions    : {},

            total     : null,
            perPage   : 25,
            skip      : 0,
            routeState: 0,
            search    : null,

            sortField: null,
            sortOrder: null,
        };
    },
    created () {
        const urlParams = new URLSearchParams(window.location.search);
        this.perPage    = parseInt(urlParams.get('limit') ? urlParams.get('limit') : 25);
        this.skip       = parseInt(urlParams.get('skip') ? urlParams.get('skip') : 0);
        this.search     = urlParams.get('search') ? urlParams.get('search') : '';
        this.sortField  = urlParams.get('sort_field') || 'sort_value';
        this.sortOrder  = urlParams.get('sort_order') || 'asc';

        this.listAction();
    },
    methods: {
        makeAction () {
            window.axios[Url.Routes.adminAccountCreate.method](Url.Routes.adminAccountCreate.uri).then(response => {
                this.account = response.data;
            }).catch(response => {
                this.parseResponseErrors(response);
            });
        },
        listAction () {
            const getParams = {
                limit     : this.perPage,
                skip      : this.skip,
                search    : this.search,
                sort_field: this.sortField,
                sort_order: this.sortOrder,
            };
            const uri       = Url.Generator.makeUri(Url.Routes.adminAccountIndex, {}, getParams);
            window.history.pushState({ state: this.routeState++ }, '', uri);

            Url.RouteFunctions.adminAccountList(getParams).then(response => {
                this.accounts    = [];
                this.actions     = response.data.actions;
                this.allAccounts = response.data.allAccounts;
                this.accounts    = response.data.accounts;
                this.total       = response.data.total;
                this.types       = response.data.types;
                this.historyUrl  = response.data.historyUrl;
            }).catch(response => {
                this.parseResponseErrors(response);
            });
        },
        onPaginationUpdate (skip) {
            this.skip = skip;
            this.listAction();
        },
        onCreatedEvent () {
            this.listAction();
        },
        clearSearch () {
            this.search = '';
            this.listAction();
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
