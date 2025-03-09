<template>
    <div class="d-flex align-items-center justify-content-between mb-2">
        <div class="d-flex">
            <button class="btn btn-success me-2"
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
    <div>
        <div v-for="(account, index) in accounts">
            <account-item-edit :model-value="account"
                               :index="index"
                               class="mb-2" />
        </div>
    </div>
</template>

<script>
import ResponseError   from '../../../mixin/ResponseError.js';
import Url             from '../../../utils/Url.js';
import AccountItemEdit from './AccountItemEdit.vue';
import HistoryBtn      from '../../common/HistoryBtn.vue';
import SearchSelect    from '../../common/form/SearchSelect.vue';
import Pagination      from '../../common/pagination/Pagination.vue';

export default {
    name      : 'AccountsBlock',
    components: { Pagination, SearchSelect, HistoryBtn, AccountItemEdit },
    mixins    : [
        ResponseError,
    ],
    data () {
        return {
            accounts   : [],
            allAccounts: [],
            historyUrl : null,

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
                this.accounts.push(response.data);
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
    },
};
</script>
