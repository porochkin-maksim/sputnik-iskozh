<template>
    <h5>Услуги</h5>
    <div>
        <button class="btn btn-success mb-2"
                v-if="invoice.actions.claims.edit"
                v-on:click="makeAction">Добавить услугу
        </button>
    </div>
    <claims-list :invoice-id="invoice.id"
                 v-model:selected-id="selectedId"
                 v-model:reload="reloadList"
                 v-model:count="claimCount"
                 @update:count="onUpdatedCount"
    />
    <view-dialog v-model:show="showDialog"
                 v-model:hide="hideDialog"
                 @hidden="closeAction"
                 v-if="claim && (claim.actions.edit || claim.actions.view)"
    >
        <template v-slot:title>{{ claim.id ? (claim.actions.edit ? 'Редактирование услуги' : 'Просмотр услуги') : 'Добавление услуги' }}</template>
        <template v-slot:body>
            <div class="container-fluid">
                <label>Услуга</label>
                <div class="input-group input-group-sm">
                    <simple-select v-model="claim.serviceId"
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
                       :disabled="!claim.actions.edit"
                       v-model="claim.name"
                />
                <label>Тариф</label>
                <input type="number"
                       step="0.01"
                       class="form-control form-control-sm"
                       :disabled="!claim.actions.edit"
                       v-model="claim.tariff"
                       @change="onTariffChanged"
                />
                <label>Стоимость</label>
                <input type="number"
                       step="0.01"
                       class="form-control form-control-sm"
                       :disabled="!claim.actions.edit"
                       v-model="claim.cost"
                       @change="onCostChanged"
                />
            </div>
        </template>
        <template v-slot:footer
                  v-if="claim.actions.edit">
            <div class="d-flex justify-content-between w-100">
                <div></div>
                <button class="btn btn-success"
                        :disabled="!canSave"
                        @click="saveAction">
                    {{ claim.id ? 'Сохранить' : 'Создать' }}
                </button>
            </div>
        </template>
    </view-dialog>
</template>

<script>

import SimpleSelect  from '../../../common/form/SimpleSelect.vue';
import ClaimsList    from './ClaimsList.vue';
import ResponseError from '../../../../mixin/ResponseError.js';
import Url           from '../../../../utils/Url.js';
import FileItem      from '../../../common/files/FileItem.vue';
import ViewDialog    from '../../../common/ViewDialog.vue';

export default {
    components: { ViewDialog, FileItem, ClaimsList, SimpleSelect },
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
            claimCount: 0,
            actions   : {},
            reloadList: false,
            claimId   : null,
            claim     : null,
            selectedId: null,

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
            let uri         = Url.Generator.makeUri(Url.Routes.adminClaimCreate, {
                invoiceId: this.invoice.id,
            });

            window.axios[Url.Routes.adminClaimCreate.method](uri).then(response => {
                this.servicesSelect = response.data.servicesSelect;
                this.services       = response.data.services;
                if (this.servicesSelect.length) {
                    this.claim        = response.data.claim;
                    this.claim.tariff = parseFloat(this.claim.tariff).toFixed(2);
                    this.claim.cost   = parseFloat(this.claim.cost).toFixed(2);
                    this.claim.payed  = parseFloat(this.claim.payed).toFixed(2);
                    this.onServiceIdChanged(this.claim.serviceId);
                    this.showDialog = true;
                }
                else {
                    this.claim = null;
                    this.showInfo('Невозможно добавить услугу. Нет доступных услуг для добавления.');
                }
            }).catch(response => {
                this.parseResponseErrors(response);
            });
        },
        getAction () {
            let uri = Url.Generator.makeUri(Url.Routes.adminClaimView, {
                invoiceId: this.invoice.id,
                claimId  : this.selectedId,
            });
            window.axios[Url.Routes.adminClaimView.method](uri).then(response => {
                this.servicesSelect = response.data.servicesSelect;
                this.claim          = response.data.claim;
                this.claim.tariff   = parseFloat(this.claim.tariff).toFixed(2);
                this.claim.cost     = parseFloat(this.claim.cost).toFixed(2);
                this.claim.payed    = parseFloat(this.claim.payed).toFixed(2);

                this.showDialog = true;
            }).catch(response => {
                this.parseResponseErrors(response);
            });
        },
        saveAction () {
            this.loading = true;
            let form     = new FormData();
            form.append('id', this.claim.id);
            form.append('invoice_id', this.invoice.id);
            form.append('service_id', this.claim.serviceId);
            form.append('tariff', parseFloat(this.claim.tariff));
            form.append('cost', parseFloat(this.claim.cost));
            form.append('name', this.claim.name);

            this.clearResponseErrors();
            let uri = Url.Generator.makeUri(Url.Routes.adminClaimSave, {
                invoiceId: this.invoice.id,
            });
            window.axios[Url.Routes.adminClaimSave.method](
                uri,
                form,
            ).then((response) => {
                let text = this.claim.id ? 'Услуга обновлена' : 'Услуга ' + response.data.claim.id + ' создана';
                this.showInfo(text);

                this.claim = null;
                this.onSaved();
            }).catch(response => {
                let text = response?.data?.message ?
                    response.data.message
                    : 'Не получилось ' + (this.id ? 'сохранить' : 'создать') + ' услугу';
                this.showDanger(text);
                this.parseResponseErrors(response);
            }).then(() => {
                this.loading = false;
                this.selectedId = null;
            });
        },
        onServiceIdChanged (id) {
            Object.values(this.services).forEach(service => {
                if (parseInt(service.id) === parseInt(id)) {
                    this.claim.tariff = parseFloat(service.cost).toFixed(2);
                    this.claim.cost   = parseFloat(service.cost).toFixed(2);
                }
            });
        },
        closeAction () {
            this.claim      = null;
            this.selectedId = null;
        },
        onSaved () {
            this.reloadList = true;
            this.$emit('update:reload', true);
        },
        onUpdatedCount (value) {
            this.claimCount = value;
            this.$emit('update:count', this.claimCount);
        },
        onCostChanged() {
            if (this.claim.tariff > this.claim.cost) {
                this.claim.tariff = this.claim.cost;
            }
        },
        onTariffChanged() {
            if (this.claim.tariff > this.claim.cost) {
                this.claim.cost = this.claim.tariff;
            }
        },
    },
    computed: {
        canSave () {
            return this.claim && this.claim.serviceId && this.claim.cost >= 0;
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
                this.claim = null;
            }
        },
        hideDialog () {
            this.closeAction();
        },
        reloadList () {
            this.$emit('update:reload', this.reloadList);
        },
        claimCount () {
            this.$emit('update:count', this.claimCount);
        },
    },
};
</script> 