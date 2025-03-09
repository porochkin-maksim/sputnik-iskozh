<template>
    <h5>Транзакции</h5>
    <div v-if="!transaction || transaction?.id">
        <button class="btn btn-success mb-2"
                v-on:click="makeAction">Добавить транзакцию
        </button>
    </div>
    <transactions-list :invoice-id="invoice.id"
                       v-model:selected-id="selectedId"
                       v-model:reload="reloadList"
                       v-model:count="transactionCount"
    >
        <template v-slot:transaction
                  v-if="transaction">
            <td>
                {{ transaction.id ? transaction.id : '#' }}
            </td>
            <td>
                <div class="input-group input-group-sm">
                    <simple-select v-model="transaction.serviceId"
                                   :disabled="selectedId"
                                   :class="'form-select-sm border-0'"
                                   :items="servicesSelect"
                                   :label="'Услуга'"
                                   @change="onServiceIdChanged"
                    />
                </div>
            </td>
            <td>
                <input type="number"
                       style="max-width: 120px"
                       step="0.01"
                       class="form-control form-control-sm border-0 cost"
                       placeholder="Стоимость"
                       v-model="transaction.tariff">
            </td>
            <td>
                <input type="number"
                       style="max-width: 120px"
                       step="0.01"
                       class="form-control form-control-sm border-0 cost"
                       placeholder="Стоимость"
                       v-model="transaction.cost">

            </td>
            <td>{{ $formatMoney(transaction.payed) }}</td>
            <td>
                {{ transaction.created }}
            </td>
            <td>
                <div>
                    <button class="btn btn-sm border-0"
                            @click="saveAction"
                            :disabled="!canSave || loading">
                        <i class="fa"
                           :class="loading ? 'fa-spinner fa-spin' : 'fa-save'"></i>&nbsp;Сохранить
                    </button>
                    <button class="btn btn-sm border-0"
                            @click="closeAction">
                        <i class="fa fa-close"></i>
                    </button>
                </div>
            </td>
        </template>
    </transactions-list>
</template>

<script>

import SimpleSelect     from '../../../common/form/SimpleSelect.vue';
import TransactionsList from './TransactionsList.vue';
import ResponseError    from '../../../../mixin/ResponseError.js';
import Url              from '../../../../utils/Url.js';

export default {
    components: { TransactionsList, SimpleSelect },
    emits     : ['update:count'],
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
            reloadList      : false,
            transactionId   : null,
            transaction     : null,
            selectedId      : null,

            servicesSelect: [],
            services      : [],

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
            form.append('name', this.transaction.service);

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
            this.selectedId = null;
        },
        onSaved () {
            this.reloadList = true;
            this.$emit('update:reload', true);
        },
    },
    computed: {
        canSave () {
            return this.transaction;
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
        transactionCount () {
            this.$emit('update:count', this.transactionCount);
        },
    },
};
</script>
