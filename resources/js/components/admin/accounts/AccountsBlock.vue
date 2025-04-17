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
                                @click="search=null;listAction">
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
        <tr class="text-center">
            <th>ID</th>
            <th>Номер</th>
            <th class="text-end">Площадь (м²)</th>
            <th>Кадастр</th>
            <th>Выставление счетов</th>
            <th></th>
        </tr>
        </thead>
        <tbody>
        <tr v-for="account in accounts"
            class="align-middle">
            <td>
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
            <td>
                <history-btn :disabled="!account.historyUrl"
                             class="btn-link underline-none"
                             :url="account.historyUrl ? account.historyUrl : ''" />
            </td>
        </tr>
        </tbody>
    </table>
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

export default {
    name      : 'AccountsBlock',
    components: { AccountItemAdd, Pagination, SearchSelect, HistoryBtn },
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
        };
    },
    created () {
        const urlParams = new URLSearchParams(window.location.search);
        this.perPage    = parseInt(urlParams.get('limit') ? urlParams.get('limit') : 25);
        this.skip       = parseInt(urlParams.get('skip') ? urlParams.get('skip') : 0);
        this.search     = urlParams.get('search') ? urlParams.get('search') : '';

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
            this.accounts = [];
            let uri       = Url.Generator.makeUri(Url.Routes.adminAccountIndex, {}, {
                limit : this.perPage,
                skip  : this.skip,
                search: this.search,
            });
            window.history.pushState({ state: this.routeState++ }, '', uri);

            window.axios[Url.Routes.adminAccountList.method](Url.Routes.adminAccountList.uri, {
                params: {
                    limit : this.perPage,
                    skip  : this.skip,
                    search: this.search,
                },
            }).then(response => {
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
    },
};
</script>
