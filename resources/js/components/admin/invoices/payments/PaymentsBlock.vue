<template>
    <h5>Платежи</h5>
    <div class="d-flex mb-2">
        <button class="btn btn-success"
                v-if="invoice.actions.payments.edit"
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
                 @hidden="closeAction"
                 v-if="payment && (payment.actions.edit || payment.actions.view)"
    >
        <template v-slot:title>{{ payment.id ? (payment.actions.edit ? 'Редактирование платёжа' : 'Просмотр платёжа') : 'Добавление платежа' }}</template>
        <template v-slot:body>
            <div class="container-fluid">
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
                <template v-if="files && files.length">
                    <ul class="list-unstyled mt-2">
                        <li v-for="(file, index) in files"
                            class="mb-2 d-flex justify-content-between">
                            <div>
                                <button class="btn btn-sm btn-danger"
                                        @click="removeFile(index)">
                                    <i class="fa fa-trash"></i>
                                </button>
                                &nbsp;
                                {{ index + 1 }}. {{ file.name }}
                            </div>
                            <span class="text-secondary">
                        {{ (file.size / (1024 * 1024)).toFixed(2) }}MB
                    </span>
                        </li>
                    </ul>
                    <div class="d-flex justify-content-end small">
                        <span :class="[fileSizeExceed ? 'text-danger' : 'text-secondary']">Размер файлов: {{ filesSize }}MB</span>
                    </div>
                </template>
                <input class="d-none"
                       type="file"
                       ref="fileElem"
                       @change="appendFiles"
                       multiple>
            </div>
        </template>
        <template v-slot:footer v-if="payment.actions.edit">
            <div class="d-flex justify-content-between w-100">
                <button class="btn btn-outline-secondary"
                        @click="chooseFiles"
                        v-if="!fileCountExceed">
                    <i class="fa fa-paperclip "></i>&nbsp;Файлы
                </button>
                <button class="btn btn-success"
                        :disabled="!canSave"
                        @click="saveAction">
                    {{ payment.id ? 'Сохранить' : 'Создать' }} платёж
                </button>
            </div>
        </template>
    </view-dialog>
</template>

<script>
import PaymentsList     from './PaymentsList.vue';
import ViewDialog       from '../../../common/ViewDialog.vue';
import ResponseError    from '../../../../mixin/ResponseError.js';
import Url              from '../../../../utils/Url.js';
import TransactionsList from '../transactions/TransactionsList.vue';
import FileItem         from '../../../common/files/FileItem.vue';

export default {
    components: { FileItem, TransactionsList, ViewDialog, PaymentsList },
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
            files        : [],

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
            form.append('comment', String(this.payment.comment ? String(this.payment.comment) : null));
            this.files.forEach((file, index) => {
                form.append('file' + index, file);
            });

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
                this.files      = [];
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
        chooseFiles () {
            this.$refs.fileElem.click();
        },
        appendFiles (event) {
            for (let i = 0; i < event.target.files.length; i++) {
                if (!this.fileCountExceed) {
                    this.files.push(event.target.files[i]);
                }
            }
        },
        removeFile (index) {
            let result = [];
            for (let i = 0; i < this.files.length; i++) {
                if (i !== index) {
                    result.push(this.files[i]);
                }
            }
            this.files = result;
        },
    },
    computed: {
        canSave () {
            return this.payment && this.payment.cost > 0;
        },
        filesSize () {
            let result = 0;
            this.files.forEach(file => {
                result += file.size;
            });
            return (result / (1024 * 1024)).toFixed(2);
        },
        fileSizeExceed () {
            return this.filesSize > 20;
        },
        fileCountExceed () {
            return this.files.length > 4;
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
