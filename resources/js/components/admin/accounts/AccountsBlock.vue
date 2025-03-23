<template>
    <div class="d-flex align-items-center justify-content-between mb-2">
        <div class="d-flex">
            <button class="btn btn-success me-2"
                    v-if="actions.edit"
                    v-on:click="makeAction">Добавить участок
            </button>
            <template v-if="allAccounts && allAccounts.length">
                <search-select v-model="accountId"
                               :prop-class="'form-control'"
                               :items="allAccounts"
                               :placeholder="'Участок'"
                               @update:model-value="listAction"
                />
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
            <history-btn
                class="btn-link underline-none"
                :url="historyUrl" />
        </div>
    </div>
    <table class="table table-sm">
        <thead>
        <tr>
            <th>ID</th>
            <th>Членство</th>
            <th>Номер</th>
            <th>Площадь (м²)</th>
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
            <td>{{ account.isMember ? 'Член СНТ' : 'Не член СНТ' }}</td>
            <td>{{ account.number }}</td>
            <td>{{ account.size }}</td>
            <td>
                <history-btn :disabled="!account.historyUrl"
                             class="btn-link underline-none"
                             :url="account.historyUrl ? account.historyUrl : ''" />
            </td>
        </tr>
        </tbody>
    </table>
    <account-item-add v-if="account" :model-value="account" @updated="onCreatedEvent"/>
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
            accountId : 0,
        };
    },
    created () {
        const urlParams = new URLSearchParams(window.location.search);
        this.perPage    = parseInt(urlParams.get('limit') ? urlParams.get('limit') : 25);
        this.skip       = parseInt(urlParams.get('skip') ? urlParams.get('skip') : 0);
        this.accountId  = parseInt(urlParams.get('account') ? urlParams.get('account') : 0);

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
                limit  : this.perPage,
                skip   : this.skip,
                account: this.accountId,
            });
            window.history.pushState({ state: this.routeState++ }, '', uri);

            window.axios[Url.Routes.adminAccountList.method](Url.Routes.adminAccountList.uri, {
                params: {
                    limit     : this.perPage,
                    skip      : this.skip,
                    account_id: this.accountId,
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
            this.account = null;
            this.listAction();
        },
    },
};
</script>
