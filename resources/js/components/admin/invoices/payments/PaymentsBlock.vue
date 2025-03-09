<template>
    <h5>Платежи</h5>
    <div class="d-flex mb-2">
        <button class="btn btn-success"
                v-on:click="makeAction">Добавить платёж
        </button>
    </div>
    <payments-list :invoice-id="invoice.id"
                   v-model:selected-id="selectedId"
                   v-model:reload="reloadList"
                   v-model:count="paymentsCount"
    />
    <view-dialog v-model:show="showDialog"
                 v-model:hide="hideDialog"
                 v-if="payment"
    >
        <template v-slot:title>{{ payment.id ? 'Редактирование платёжа' : 'Добавление платежа' }}</template>
        <template v-slot:body>
            <div class="container-fluid">
                <label>Стоимость</label>
                <input type="number"
                       step="0.01"
                       class="form-control form-control-sm"
                       v-model="payment.cost"
                />
                <label>Комментарий</label>
                <textarea class="form-control form-control-sm"
                          v-model="payment.comment"
                ></textarea>
            </div>
        </template>
        <template v-slot:footer>
            <button class="btn btn-success"
                    :disabled="!canSave"
                    @click="saveAction">
                {{ payment.id ? 'Сохранить' : 'Создать' }} платёж
            </button>
        </template>
    </view-dialog>
</template>

<script>
import PaymentsList     from './PaymentsList.vue';
import ViewDialog       from '../../../common/ViewDialog.vue';
import ResponseError    from '../../../../mixin/ResponseError.js';
import Url              from '../../../../utils/Url.js';
import TransactionsList from '../transactions/TransactionsList.vue';

export default {
    components: { TransactionsList, ViewDialog, PaymentsList },
    emits     : ['update:reload', 'update:count'],
    props     : {
        invoice: {
            type   : Object,
            default: {},
        },
        reload : {
            type   : Boolean,
            default: false,
        },
        count  : {
            type   : Number,
            default: 0,
        },
    },
    mixins    : [
        ResponseError,
    ],
    created () {
        this.vueId = 'uuid' + this.$_uid;
    },
    data () {
        return {
            paymentsCount: 0,
            reloadList   : false,
            paymentId    : null,
            payment      : null,
            selectedId   : null,

            loading: false,

            showDialog: false,
            hideDialog: false,
        };
    },
    methods : {
        makeAction () {
            let uri = Url.Generator.makeUri(Url.Routes.adminPaymentCreate, {
                invoiceId: this.invoice.id,
            });

            window.axios[Url.Routes.adminPaymentCreate.method](uri).then(response => {
                this.payment    = response.data.payment;
                this.showDialog = true;
            }).catch(response => {
                this.parseResponseErrors(response);
            });
        },
        getAction () {
            let uri = Url.Generator.makeUri(Url.Routes.adminPaymentView, {
                invoiceId: this.invoice.id,
                paymentId: this.selectedId,
            });
            window.axios[Url.Routes.adminPaymentView.method](uri).then(response => {
                this.payment         = response.data.payment;
                this.payment.cost    = parseFloat(this.payment.cost).toFixed(2);
                this.payment.comment = this.payment.comment ? String(this.payment.comment) : null;
                this.showDialog      = true;
            }).catch(response => {
                this.parseResponseErrors(response);
            });
        },
        saveAction () {
            this.loading = true;
            let form     = new FormData();
            form.append('id', this.payment.id);
            form.append('cost', parseFloat(this.payment.cost));
            form.append('comment', parseFloat(this.payment.comment ? String(this.payment.comment) : null));

            this.clearResponseErrors();
            let uri = Url.Generator.makeUri(Url.Routes.adminPaymentSave, {
                invoiceId: this.invoice.id,
            });
            window.axios[Url.Routes.adminPaymentSave.method](
                uri,
                form,
            ).then((response) => {
                let text = this.payment.id ? 'Платёж обновлен' : 'Платёж ' + response.data.payment.id + ' создан';
                this.showInfo(text);

                this.payment = null;
                this.onSaved();
            }).catch(response => {
                let text = response?.data?.message ?
                    response.data.message
                    : 'Не получилось ' + (this.id ? 'сохранить' : 'создать') + ' платёж';
                this.showDanger(text);
                this.parseResponseErrors(response);
            }).then(() => {
                this.loading    = false;
                this.selectedId = null;
            });
        },
        closeAction () {
            this.payment    = null;
            this.selectedId = null;
        },
        onSaved () {
            this.reloadList = true;
            this.$emit('update:reload', true);
        },
    },
    computed: {
        canSave () {
            return this.payment && this.payment.cost > 0;
        },
    },
    watch   : {
        reload (value) {
            if (value) {
                this.reloadList = true;
            }
        },
        selectedId () {
            if (this.selectedId) {
                this.getAction();
            }
            else {
                this.payment = null;
            }
        },
        hideDialog () {
            this.closeAction();
        },
        reloadList () {
            this.$emit('update:reload', this.reloadList);
        },
        paymentsCount () {
            this.$emit('update:count', this.paymentsCount);
        },
    },
};
</script>
