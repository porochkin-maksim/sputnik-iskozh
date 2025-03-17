<template>
    <h5>Транзакции</h5>
    <div>
        <button class="btn btn-success mb-2"
                v-if="invoice.actions.transactions.edit"
                v-on:click="makeAction">Добавить транзакцию
        </button>
    </div>
    <transactions-list :invoice-id="invoice.id"
                       v-model:selected-id="selectedId"
                       v-model:reload="reloadList"
                       v-model:count="transactionCount"
    />
    <view-dialog v-model:show="showDialog"
                 v-model:hide="hideDialog"
                 @hidden="closeAction"
                 v-if="transaction && (transaction.actions.edit || transaction.actions.view)"
    >
        <template v-slot:title>{{ transaction.id ? (transaction.actions.edit ? 'Редактирование транзакции' : 'Просмотр транзакции') : 'Добавление транзакции' }}</template>
        <template v-slot:body>
            <div class="container-fluid">
                <label>Услуга</label>
                <div class="input-group input-group-sm">
                    <simple-select v-model="transaction.serviceId"
                                   :disabled="selectedId"
                                   :class="'form-select-sm border'"
                                   :items="servicesSelect"
                                   :label="'Услуга'"
                                   @change="onServiceIdChanged"
                    />
                </div>
                <label>Своё название услуги</label>
                <input type="text"
                       class="form-control form-control-sm"
                       :disabled="!transaction.actions.edit"
                       v-model="transaction.name"
                />
                <label>Тариф</label>
                <input type="number"
                       step="0.01"
                       class="form-control form-control-sm"
                       :disabled="!transaction.actions.edit"
                       v-model="transaction.tariff"
                />
                <label>Стоимость</label>
                <input type="number"
                       step="0.01"
                       class="form-control form-control-sm"
                       :disabled="!transaction.actions.edit"
                       v-model="transaction.cost"
                />
            </div>
        </template>
        <template v-slot:footer v-if="transaction.actions.edit">
            <div class="d-flex justify-content-between w-100">
                <div></div>
                <button class="btn btn-success"
                        :disabled="!canSave"
                        @click="saveAction">
                    {{ transaction.id ? 'Сохранить' : 'Создать' }}
                </button>
            </div>
        </template>
    </view-dialog>
</template>

<script>

import SimpleSelect     from '../../../common/form/SimpleSelect.vue';
import TransactionsList from './TransactionsList.vue';
import ResponseError    from '../../../../mixin/ResponseError.js';
import Url              from '../../../../utils/Url.js';
import FileItem         from '../../../common/files/FileItem.vue';
import ViewDialog       from '../../../common/ViewDialog.vue';

export default {
    components: { ViewDialog, FileItem, TransactionsList, SimpleSelect },
    emits     : ['update:count', 'update:reload'],
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
            transactionCount: 0,
            actions         : {},
            reloadList      : false,
            transactionId   : null,
            transaction     : null,
            selectedId      : null,

            servicesSelect: [],
            services      : [],

            showDialog: false,
            hideDialog: false,

            loading: false,
        };
    },
    methods : {
        makeAction () {
            this.selectedId = null;
            let uri         = Url.Generator.makeUri(Url.Routes.adminTransactionCreate, {
                invoiceId: this.invoice.id,
            });

            window.axios[Url.Routes.adminTransactionCreate.method](uri).then(response => {
                this.servicesSelect = response.data.servicesSelect;
                this.services       = response.data.services;
                if (this.servicesSelect.length) {
                    this.transaction        = response.data.transaction;
                    this.transaction.tariff = parseFloat(this.transaction.tariff).toFixed(2);
                    this.transaction.cost   = parseFloat(this.transaction.cost).toFixed(2);
                    this.transaction.payed  = parseFloat(this.transaction.payed).toFixed(2);
                    this.onServiceIdChanged(this.transaction.serviceId);
                    this.showDialog = true;
                }
                else {
                    this.transaction = null;
                    this.showInfo('Невозможно добавить транзакцию. Нет доступных услуг для добавления.');
                }
            }).catch(response => {
                this.parseResponseErrors(response);
            });
        },
        getAction () {
            let uri = Url.Generator.makeUri(Url.Routes.adminTransactionView, {
                invoiceId    : this.invoice.id,
                transactionId: this.selectedId,
            });
            window.axios[Url.Routes.adminTransactionView.method](uri).then(response => {
                this.servicesSelect     = response.data.servicesSelect;
                this.transaction        = response.data.transaction;
                this.transaction.tariff = parseFloat(this.transaction.tariff).toFixed(2);
                this.transaction.cost   = parseFloat(this.transaction.cost).toFixed(2);
                this.transaction.payed  = parseFloat(this.transaction.payed).toFixed(2);

                this.showDialog = true;
            }).catch(response => {
                this.parseResponseErrors(response);
            });
        },
        saveAction () {
            this.loading = true;
            let form     = new FormData();
            form.append('id', this.transaction.id);
            form.append('invoice_id', this.invoice.id);
            form.append('service_id', this.transaction.serviceId);
            form.append('tariff', parseFloat(this.transaction.tariff));
            form.append('cost', parseFloat(this.transaction.cost));
            form.append('name', this.transaction.name);

            this.clearResponseErrors();
            let uri = Url.Generator.makeUri(Url.Routes.adminTransactionSave, {
                invoiceId: this.invoice.id,
            });
            window.axios[Url.Routes.adminTransactionSave.method](
                uri,
                form,
            ).then((response) => {
                let text = this.transaction.id ? 'Транзакция обновлена' : 'Транзакция ' + response.data.transaction.id + ' создана';
                this.showInfo(text);

                this.transaction = null;
                this.onSaved();
            }).catch(response => {
                let text = response?.data?.message ?
                    response.data.message
                    : 'Не получилось ' + (this.id ? 'сохранить' : 'создать') + ' транзакцию';
                this.showDanger(text);
                this.parseResponseErrors(response);
            }).then(() => {
                this.loading    = false;
                this.selectedId = null;
            });
        },
        onServiceIdChanged (id) {
            Object.values(this.services).forEach(service => {
                if (parseInt(service.id) === parseInt(id)) {
                    this.transaction.tariff = parseFloat(service.cost).toFixed(2);
                    this.transaction.cost   = parseFloat(service.cost).toFixed(2);
                }
            });
        },
        closeAction () {
            this.transaction = null;
            this.selectedId  = null;
        },
        onSaved () {
            this.reloadList = true;
            this.$emit('update:reload', true);
        },
    },
    computed: {
        canSave () {
            return this.transaction && this.transaction.serviceId && this.transaction.cost > 0;
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
                this.transaction = null;
            }
        },
        hideDialog () {
            this.closeAction();
        },
        reloadList () {
            this.$emit('update:reload', this.reloadList);
        },
        transactionsCount () {
            this.$emit('update:count', this.transactionsCount);
        },
    },
};
</script>
