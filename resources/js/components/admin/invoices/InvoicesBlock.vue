<template>
    <div v-if="loaded && (!periods || !periods.length)">
        <div class="alert alert-warning">
            <p><i class="fa fa-warning"></i> Не найдено ни одного периода</p>
            <a :href="Url.Routes.adminPeriodIndex.uri">
                Создайте период
            </a>
        </div>
    </div>
    <div v-if="periods && periods.length"
         class="d-flex justify-content-between align-items-center mb-2">
        <div class="d-flex">
            <div>
                <button class="btn btn-success me-2"
                        v-if="actions.edit"
                        v-on:click="makeAction">Добавить счёт
                </button>
            </div>
            <template v-if="computedPeriods && computedPeriods.length">
                <simple-select v-model="periodId"
                               :class="'d-inline-block form-select-sm w-auto me-2'"
                               :items="computedPeriods"
                               @change="listAction"
                />
            </template>
            <template v-if="computedTypes && computedTypes.length">
                <simple-select v-model="type"
                               :class="'d-inline-block form-select-sm w-auto me-2'"
                               :items="computedTypes"
                               @change="listAction"
                />
            </template>
            <template v-if="computedAccounts && computedAccounts.length">
                <search-select v-model="accountId"
                               :prop-class="'form-control'"
                               :items="computedAccounts"
                               :placeholder="'Участок...'"
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
    <template v-if="invoices.length"
              class="table">
        <invoices-list :invoices="invoices" />
    </template>
    <invoice-item-edit v-if="invoice && actions.edit"
                       :model-value="invoice"
                       :accounts="accounts"
                       :periods="periods"
                       :types="types"
                       @updated="listAction"
    />
</template>

<script>
import ResponseError   from '../../../mixin/ResponseError.js';
import Url             from '../../../utils/Url.js';
import InvoiceItemEdit from './InvoiceItemEdit.vue';
import HistoryBtn      from '../../common/HistoryBtn.vue';
import Pagination      from '../../common/pagination/Pagination.vue';
import SimpleSelect    from '../../common/form/SimpleSelect.vue';
import SearchSelect    from '../../common/form/SearchSelect.vue';
import InvoicesList    from './InvoicesList.vue';

export default {
    name      : 'InvoicesBlock',
    components: { InvoicesList, SearchSelect, SimpleSelect, Pagination, HistoryBtn, InvoiceItemEdit },
    mixins    : [
        ResponseError,
    ],
    data () {
        return {
            invoice   : null,
            invoices  : [],
            accounts  : [],
            periods   : [],
            types     : [],
            historyUrl: null,

            loaded    : false,
            total     : null,
            perPage   : 25,
            skip      : 0,
            routeState: 0,
            type      : 0,
            periodId  : 0,
            accountId : 0,
            Url       : Url,
            actions   : {},
        };
    },
    created () {
        const urlParams = new URLSearchParams(window.location.search);
        this.perPage    = parseInt(urlParams.get('limit') ? urlParams.get('limit') : 25);
        this.skip       = parseInt(urlParams.get('skip') ? urlParams.get('skip') : 0);
        this.type       = parseInt(urlParams.get('type') ? urlParams.get('type') : 0);
        this.periodId   = parseInt(urlParams.get('period') ? urlParams.get('period') : 0);
        this.accountId  = parseInt(urlParams.get('account') ? urlParams.get('account') : 0);

        this.listAction();
    },
    methods : {
        makeAction () {
            this.invoice = null;
            window.axios[Url.Routes.adminInvoiceCreate.method](Url.Routes.adminInvoiceCreate.uri).then(response => {
                let invoice = response.data;
                if (this.periodId) {
                    invoice.periodId = this.periodId;
                }
                if (this.accountId) {
                    invoice.accountId = this.accountId;
                }
                if (this.type) {
                    invoice.type = this.type;
                }
                this.invoice = invoice;

            }).catch(response => {
                this.parseResponseErrors(response);
            });
        },
        listAction () {
            this.invoices = [];
            let uri       = Url.Generator.makeUri(Url.Routes.adminInvoiceIndex, {}, {
                limit  : this.perPage,
                skip   : this.skip,
                type   : this.type,
                period : this.periodId,
                account: this.accountId,
            });
            window.history.pushState({ state: this.routeState++ }, '', uri);

            window.axios[Url.Routes.adminInvoiceList.method](Url.Routes.adminInvoiceList.uri, {
                params: {
                    limit     : this.perPage,
                    skip      : this.skip,
                    type      : this.type,
                    period_id : this.periodId,
                    account_id: this.accountId,
                },
            }).then(response => {
                this.actions    = response.data.actions;
                this.invoices   = response.data.invoices;
                this.total      = response.data.total;
                this.types      = response.data.types;
                this.periods    = response.data.periods;
                this.accounts   = response.data.accounts;
                this.historyUrl = response.data.historyUrl;
            }).catch(response => {
                this.parseResponseErrors(response);
            }).then(() => {
                this.loaded = true;
            });
        },
        onPaginationUpdate (skip) {
            this.skip = skip;
            this.listAction();
        },
    },
    computed: {
        computedTypes () {
            return [
                {
                    'key'  : 0,
                    'value': 'Все типы',
                },
            ].concat(this.types);
        },
        computedPeriods () {
            return [
                {
                    'key'  : 0,
                    'value': 'Все периоды',
                },
            ].concat(this.periods);
        },
        computedAccounts () {
            return [
                {
                    'key'  : 0,
                    'value': 'Все участки',
                },
            ].concat(this.accounts);
        },
    },
};
</script>
