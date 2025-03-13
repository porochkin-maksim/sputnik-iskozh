<template>
    <h5>Новые платежи</h5>
    <payments-list v-model:selected-id="selectedId"
                   v-model:reload="reloadList"
    />
    <view-dialog v-model:show="showDialog"
                 v-model:hide="hideDialog"
                 @hidden="closeAction"
                 v-if="payment && (payment.actions.edit || payment.actions.view)"
    >
        <template v-slot:title>{{ payment.actions.edit ? 'Привязка платёжа' : 'Просмотр платёжа' }}</template>
        <template v-slot:body>
            <div class="container-fluid">
                <template v-if="payment.actions.edit">
                    <label>Период</label>
                    <search-select v-if="periods.length"
                                   v-model="periodId"
                                   :prop-class="'form-control mb-2'"
                                   :items="periods"
                                   :placeholder="'Период...'"
                                   @update:model-value="getInvoices"
                    />
                    <template v-if="accounts.length && periodId">
                        <label>Участок</label>
                        <search-select
                            v-model="payment.accountId"
                            :prop-class="'form-control mb-2'"
                            :items="accounts"
                            :placeholder="'Участок...'"
                            @update:model-value="getInvoices"
                        />
                    </template>
                    <template v-if="invoices.length">
                        <label>Счёт</label>
                        <search-select
                            v-model="payment.invoiceId"
                            :prop-class="'form-control mb-2'"
                            :items="invoices"
                            :placeholder="'Счёт...'"
                        />
                    </template>
                </template>
                <label>Стоимость</label>
                <input type="number"
                       step="0.01"
                       class="form-control form-control-sm"
                       :disabled="!payment.actions.edit"
                       v-model="payment.cost"
                />
                <label>Комментарий</label>
                <textarea class="form-control form-control-sm"
                          style="min-height: 200px;"
                          :disabled="!payment.actions.edit"
                          v-model="payment.comment"
                ></textarea>
                <template v-for="(file, index) in payment.files">
                    <file-item
                        :file="file"
                        :edit="true"
                        :index="index"
                        :use-up-sort="index!==0"
                        :use-down-sort="index!==payment.files.length-1"
                        class="mt-2"
                    />
                </template>
            </div>
        </template>
        <template v-slot:footer>
            <button class="btn btn-success"
                    v-if="payment.actions.edit"
                    :disabled="!canSave"
                    @click="saveAction">
                {{ payment.id ? 'Сохранить' : 'Создать' }} платёж
            </button>
        </template>
    </view-dialog>
</template>

<script>
import PaymentsList  from './PaymentsList.vue';
import ViewDialog    from '../../common/ViewDialog.vue';
import ResponseError from '../../../mixin/ResponseError.js';
import Url           from '../../../utils/Url.js';
import FileItem      from '../../common/files/FileItem.vue';
import SearchSelect  from '../../common/form/SearchSelect.vue';

export default {
    name      : 'PaymentsBlock',
    components: { SearchSelect, FileItem, ViewDialog, PaymentsList },
    emits     : ['update:reload'],
    props     : {
        reload: {
            type   : Boolean,
            default: false,
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

            accounts: [],
            invoices: [],
            periods : [],

            periodId: null,

            loading: false,

            showDialog: false,
            hideDialog: false,
        };
    },
    methods : {
        getAction () {
            this.periodId = null;
            this.accounts = [];
            this.invoices = [];
            this.periods  = [];
            let uri       = Url.Generator.makeUri(Url.Routes.adminNewPaymentView, {
                paymentId: this.selectedId,
            });
            window.axios[Url.Routes.adminNewPaymentView.method](uri).then(response => {
                this.payment         = response.data.payment;
                this.accounts        = response.data.accounts;
                this.periods         = response.data.periods;
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
            form.append('account_id', parseFloat(this.payment.accountId));
            form.append('invoice_id', parseFloat(this.payment.invoiceId));

            this.clearResponseErrors();
            window.axios[Url.Routes.adminNewPaymentSave.method](
                Url.Routes.adminNewPaymentSave.uri,
                form,
            ).then((response) => {
                this.showInfo('Платёж привязан');

                this.payment = null;
                this.onSaved();
            }).catch(response => {
                let text = response?.data?.message ?
                    response.data.message
                    : 'Не получилось привязать платёж';
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
            this.closeAction();
            this.reloadList = true;
            this.$emit('update:reload', true);
        },
        getInvoices () {
            this.invoices = [];
            if (!this.periodId || !this.payment.accountId) {
                return;
            }

            let uri = Url.Generator.makeUri(Url.Routes.adminNewPaymentGetInvoices, {
                accountId: this.payment.accountId,
                periodId : this.periodId,
            });
            window.axios[Url.Routes.adminNewPaymentGetInvoices.method](uri).then(response => {
                this.invoices = response.data.invoices;
            }).catch(response => {
                this.parseResponseErrors(response);
            });
        },
    },
    computed: {
        canSave () {
            return this.payment && this.payment.cost > 0 && this.payment.accountId && this.payment.invoiceId;
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
    },
};
</script>
