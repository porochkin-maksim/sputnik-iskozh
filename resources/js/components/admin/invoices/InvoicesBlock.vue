<template>
    <div v-if="loaded && (!periods || !periods.length)">
        <div class="alert alert-warning">
            <p><i class="fa fa-warning"></i> Не найдено ни одного периода</p>
            <a :href="Url.Routes.adminPeriodIndex.uri">
                Создайте период
            </a>
        </div>
    </div>
    <template v-if="periods && periods.length">
        <div class="d-flex justify-content-between align-items-center mb-2">
            <div class="d-flex">
                <button class="btn btn-success"
                        v-if="actions.edit"
                        v-on:click="makeAction">Добавить счёт
                </button>
                <button class="btn btn-success ms-2"
                        v-if="actions.edit && periodId"
                        v-on:click="makeRegularAction">Выставить регулярные счета
                </button>
            </div>
            <div class="d-flex">
                <div>
                    <pagination :total="total"
                                :perPage="perPage"
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
                <div>
                    <history-btn
                        class="btn-link underline-none"
                        :url="historyUrl" />
                </div>
            </div>
        </div>
        <div class="d-flex justify-content-between">
            <div class="d-flex mb-2">
                <template v-if="computedPeriods && computedPeriods.length">
                    <simple-select v-model="periodId"
                                   :class="'d-inline-block form-select-sm w-auto'"
                                   :items="computedPeriods"
                                   @change="listAction"
                    />
                </template>
                <template v-if="computedTypes && computedTypes.length">
                    <simple-select v-model="type"
                                   :class="'d-inline-block form-select-sm w-auto ms-2'"
                                   :items="computedTypes"
                                   @change="listAction"
                    />
                </template>
                <template v-if="computedAccounts && computedAccounts.length">
                    <div class="d-flex ms-2">
                        <div class="input-group input-group-sm">
                            <input class="form-control"
                                   v-model="searchAccount"
                                   name="users_search"
                                   placeholder="Участок..."
                                   @keyup="listAction"
                                   ref="search">
                            <button class="btn btn-light border"
                                    type="button"
                                    @click="searchAccount=null;listAction">
                                <i class="fa fa-close"></i>
                            </button>
                        </div>
                    </div>
                </template>
                <template v-if="computedPayedStatus && computedPayedStatus.length">
                    <simple-select v-model="payedStatus"
                                   :class="'d-inline-block form-select-sm w-auto ms-2'"
                                   :items="computedPayedStatus"
                                   @change="listAction"
                    />
                </template>
            </div>
            <div>
                <button class="btn btn-success"
                        @click="exportAction">
                    <i class="fa fa-file-excel-o"></i>
                </button>
            </div>
        </div>
    </template>
    <summary-block :account-id="parseInt(accountId)"
                   :account-search="searchAccount"
                   :type="parseInt(type)"
                   :period-id="parseInt(periodId)" />
    <invoices-list
        :invoices="invoices"
        :sort-field="sortField"
        :sort-order="sortOrder"
        @sort="onSort"
    />
    <invoice-item-edit v-if="invoice && actions.edit"
                       :model-value="invoice"
                       :accounts="accounts"
                       :periods="activePeriods"
                       :types="activeTypes"
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
import CustomCheckbox  from '../../common/form/CustomCheckbox.vue';
import SummaryBlock    from '../../common/blocks/SummaryBlock.vue';

export default {
    name      : 'InvoicesBlock',
    components: {
        CustomCheckbox,
        InvoicesList,
        SearchSelect,
        SimpleSelect,
        Pagination,
        HistoryBtn,
        InvoiceItemEdit,
        SummaryBlock,
    },
    mixins    : [
        ResponseError,
    ],
    data () {
        return {
            invoice      : null,
            invoices     : [],
            accounts     : [],
            periods      : [],
            activePeriods: [],
            types        : [],
            activeTypes  : [],
            historyUrl   : null,

            loaded       : false,
            total        : null,
            perPage      : 25,
            skip         : 0,
            routeState   : 0,
            type         : 0,
            periodId     : null,
            payedStatus  : null,
            accountId    : 0,
            searchAccount: null,
            Url          : Url,
            actions      : {},
            summary      : null,

            // Добавляем параметры сортировки
            sortField: 'id',
            sortOrder: 'desc',
        };
    },
    created () {
        const urlParams    = new URLSearchParams(window.location.search);
        this.perPage       = parseInt(urlParams.get('limit') || 25);
        this.skip          = parseInt(urlParams.get('skip') || 0);
        this.type          = parseInt(urlParams.get('type') || 0);
        this.periodId      = parseInt(urlParams.get('period') || 0);
        this.payedStatus   = urlParams.get('status') || 'all';
        this.sortField     = urlParams.get('sort_field') || 'id';
        this.sortOrder     = urlParams.get('sort_order') || 'desc';
        this.searchAccount = urlParams.get('search') || null;
        this.listAction();
    },
    methods : {
        makeRegularAction () {
            let uri = Url.Generator.makeUri(Url.Routes.adminInvoiceGetAccountsCountWithoutRegular, {
                periodId: this.periodId,
            });
            window.axios[Url.Routes.adminInvoiceGetAccountsCountWithoutRegular.method](uri).then(response => {
                const count = response.data;
                if (count === 0) {
                    alert('Нет ни одного участка для выставления регулярного счёта в периоде');
                    return;
                }
                else if (!confirm('Выставить регулярные счета всем участкам в периоде, у которых ещё нет таких счетов? (' + count + 'шт)')) {
                    return;
                }

                uri = Url.Generator.makeUri(Url.Routes.adminInvoiceCreateRegularInvoices, {
                    periodId: this.periodId,
                });
                window.axios[Url.Routes.adminInvoiceCreateRegularInvoices.method](uri).then(response => {
                    this.listAction();
                }).catch(response => {
                    this.parseResponseErrors(response);
                });
            }).catch(response => {
                this.parseResponseErrors(response);
            });
        },
        makeAction () {
            this.invoice = null;
            window.axios[Url.Routes.adminInvoiceCreate.method](Url.Routes.adminInvoiceCreate.uri).then(response => {
                let invoice = response.data;
                if (this.periodId) {
                    invoice.periodId = this.periodId;
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
            let uri = Url.Generator.makeUri(Url.Routes.adminInvoiceIndex, {}, {
                limit     : this.perPage,
                skip      : this.skip,
                type      : this.type,
                period    : this.periodId,
                account   : this.accountId,
                search    : this.searchAccount,
                status    : this.payedStatus,
                sort_field: this.sortField,
                sort_order: this.sortOrder,
            });
            window.history.pushState({ state: this.routeState++ }, '', uri);

            window.axios[Url.Routes.adminInvoiceList.method](Url.Routes.adminInvoiceList.uri, {
                params: {
                    limit       : this.perPage,
                    skip        : this.skip,
                    type        : this.type,
                    period_id   : this.periodId,
                    account_id  : this.accountId,
                    account     : this.searchAccount,
                    payed_status: this.payedStatus,
                    sort_field  : this.sortField,
                    sort_order  : this.sortOrder,
                },
            }).then(response => {
                this.actions       = response.data.actions;
                this.invoices      = response.data.invoices;
                this.total         = response.data.total;
                this.types         = response.data.types;
                this.activeTypes   = response.data.activeTypes;
                this.periods       = response.data.periods;
                this.activePeriods = response.data.activePeriods;
                this.accounts      = response.data.accounts;
                this.historyUrl    = response.data.historyUrl;

                if (!this.periodId && this.periods.length) {
                    this.periodId = this.periods[0].key;
                    this.listAction();
                }
            }).catch(response => {
                this.parseResponseErrors(response);
            }).then(() => {
                this.loaded = true;
            });
        },
        exportAction () {
            window.open(Url.Generator.makeUri(Url.Routes.adminInvoiceExport, {}, {
                type   : this.type,
                period : this.periodId,
                account: this.searchAccount,
                status : this.payedStatus,
            }), '_blank');
        },
        onPaginationUpdate (skip) {
            this.skip = skip;
            this.listAction();
        },
        onSort ({ field, order }) {
            this.sortField = field;
            this.sortOrder = order;
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
        computedPayedStatus () {
            return [
                {
                    'key'  : 'all',
                    'value': 'Все статусы',
                },
                {
                    'key'  : 'payed',
                    'value': 'Оплаченные',
                },
                {
                    'key'  : 'unpayed',
                    'value': 'Неоплаченные',
                },
                {
                    'key'  : 'partial',
                    'value': 'Частично оплаченные',
                },
            ];
        },
    },
};
</script>
