<template>
    <h4>Детали счёта №{{ localInvoice.id }} для «{{ localInvoice.account.number }}» ({{ localInvoice.typeName }}) </h4>
    <div class="mb-2 d-flex align-items-center justify-content-between">
        <div>
            <a class="btn btn-sm border text-decoration-none me-2"
               v-if="localInvoice.account.viewUrl"
               :href="localInvoice.account.viewUrl">
                Перейти в участок {{ localInvoice.account.number }} (Площадь {{ localInvoice.account.size }}м²)
            </a>
            <history-btn
                class="btn-link underline-none"
                :url="localInvoice.historyUrl" />
        </div>
        <button class="btn btn-sm text-danger"
                v-if="canDrop"
                @click="dropAction">
            <i class="fa fa-trash"></i>
        </button>
    </div>
    <div class="alert p-2 mb-2"
         v-if="actions.view"
         :class="[invoice.cost !== 0 && localInvoice.isPayed ? 'alert-success' : 'alert-secondary']">
        <i class="fa fa-check text-success"
           v-if="localInvoice.isPayed"></i>
        <i class="fa fa-close text-secondary"
           v-else></i>
        &nbsp;
        <span>
            Оплачено: {{ $formatMoney(localInvoice.payed ? localInvoice.payed : 0) }} / {{ $formatMoney(localInvoice.cost ? localInvoice.cost : 0) }}

            <template v-if="localInvoice.delta !== 0">
                &nbsp;(Долг {{ $formatMoney(localInvoice.delta) }})
            </template>
        </span>
    </div>
    <claim-block :invoice="invoice"
                 v-if="actions.claims.view"
                 v-model:count="claimsCount"
                 v-model:reload="reload" />
    <div class="border-top my-2"></div>
    <payments-block :invoice="invoice"
                    v-if="actions.payments.view"
                    v-model:count="paymentsCount"
                    v-model:reload="reload" />
    <div class="border-top my-2"></div>
    <counters-block v-if="account"
                    :account="account"/>
</template>

<script>
import ResponseError    from '../../../mixin/ResponseError.js';
import HistoryBtn       from '../../common/HistoryBtn.vue';
import ClaimBlock       from './claims/ClaimBlock.vue';
import PaymentsBlock    from './payments/PaymentsBlock.vue';
import Url              from '../../../utils/Url.js';
import SearchSelect     from '../../common/form/SearchSelect.vue';
import CountersBlock    from '../accounts/counters/CountersBlock.vue';
import AccountActions   from '../accounts/AccountActions.js';

export default {
    name      : 'InvoiceItemView',
    components: {
        CountersBlock,
        SearchSelect,
        ClaimBlock,
        PaymentsBlock,
        HistoryBtn,
    },
    mixins    : [
        ResponseError,
        AccountActions,
    ],
    props     : {
        invoice: {
            type   : Object,
            default: {},
        },
    },
    created () {
        this.localInvoice = this.invoice;
        this.actions      = this.invoice.actions;
        this.vueId        = 'uuid' + this.$_uid;
        this.getAction();
        this.getAccountAction(this.invoice.accountId);
    },
    data () {
        return {
            localInvoice : {},
            actions      : {},
            reload       : false,
            claimsCount  : 0,
            paymentsCount: 0,
        };
    },
    methods : {
        getAction () {
            let uri = Url.Generator.makeUri(Url.Routes.adminInvoiceGet, {
                id: this.invoice.id,
            });
            window.axios[Url.Routes.adminInvoiceGet.method](uri).then(response => {
                this.localInvoice = response.data;
                this.actions      = this.localInvoice.actions;
            }).catch(response => {
                this.parseResponseErrors(response);
            });
        },
        dropAction () {
            if (!confirm('Удалить счёт?')) {
                return;
            }

            let uri = Url.Generator.makeUri(Url.Routes.adminInvoiceDelete, {
                id: this.invoice.id,
            });
            window.axios[Url.Routes.adminInvoiceDelete.method](
                uri,
            ).then((response) => {
                this.dropped = response.data;
                if (response.data) {
                    this.showInfo('Счёт удалён');
                    setTimeout(() => {
                        location.href = Url.Routes.adminInvoiceIndex.uri;
                    }, 1000);
                }
                else {
                    this.showDanger('Счёт не удалён');
                }
            }).catch(response => {
                this.parseResponseErrors(response);
            });
        },
    },
    computed: {
        canDrop () {
            return this.actions.drop
                && this.claimsCount === 0
                && this.paymentsCount === 0;
        },
    },
    watch   : {
        reload (value) {
            if (value) {
                this.getAction();
                setTimeout(() => {
                    this.reload = false;
                }, 100);
            }
        },
    },
};
</script>
