<template>
    <div class="row">
        <div class="col-lg-6 col-12">
            <div class="card mb-2">
                <div class="card-body">
                    <h5>Информация</h5>
                    <template v-if="account.actions.edit">
                        <div class="row">
                            <div class="col-6">
                                <custom-input v-model="account.number"
                                              :required="true"
                                              :disabled="loading"
                                              :label="'Номер участка'"
                                />
                            </div>
                            <div class="col-6">
                                <custom-input v-model="account.size"
                                              :required="true"
                                              :disabled="loading"
                                              :label="'Площадь (м²)'"
                                              :type="'number'"
                                              :min="0"
                                              :step="1"
                                />
                            </div>
                        </div>
                        <div>
                            <div class="mt-2">
                                <custom-checkbox v-model="account.isInvoicing"
                                                 :disabled="loading"
                                                 :label="'Выставлять счета'"
                                                 switch-style
                                />
                            </div>
                            <div>
                                <custom-input v-model="account.cadastreNumber"
                                              :disabled="loading"
                                              :label="'Кадастровый номер'"
                                />
                            </div>
                        </div>
                    </template>
                    <template v-else>
                        <h6>Данные участка</h6>
                        <account-info-list :account="account" />
                    </template>
                    <div class="d-flex align-items-center justify-content-between mt-2">
                        <div class="d-flex">
                            <button class="btn btn-success me-2"
                                    :disabled="!canSave || loading"
                                    v-on:click="saveAction"
                            >
                                <i class="fa"
                                   :class="loading ? 'fa-spinner fa-spin' : 'fa-save'"></i>
                                Сохранить
                            </button>
                        </div>
                        <div class="d-flex">
                            <history-btn
                                class="btn-link underline-none"
                                :url="account.historyUrl" />
                        </div>
                    </div>
                </div>
            </div>
            <div class="card mb-2">
                <div class="card-body">
                    <users-block :account="account" :users="account.users"/>
                </div>
            </div>
        </div>
        <div class="col-lg-6 col-12">
            <div class="card mb-2">
                <div class="card-body">
                    <counters-block :account="account"/>
                </div>
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <invoices-block :account="account" />
                </div>
            </div>
        </div>
    </div>
</template>

<script>
import Url             from '../../../utils/Url.js';
import CustomInput     from '../../common/form/CustomInput.vue';
import CustomCheckbox  from '../../common/form/CustomCheckbox.vue';
import CustomTextarea  from '../../common/form/CustomTextarea.vue';
import CustomSelect    from '../../common/form/CustomSelect.vue';
import ResponseError   from '../../../mixin/ResponseError.js';
import ErrorsList      from '../../common/form/partial/ErrorsList.vue';
import HistoryBtn      from '../../common/HistoryBtn.vue';
import Pagination      from '../../common/pagination/Pagination.vue';
import CountersBlock   from './counters/CountersBlock.vue';
import AccountInfoList from './AccountInfoList.vue';
import InvoicesBlock   from './invoices/InvoicesBlock.vue';
import UsersBlock      from './users/UsersBlock.vue';

export default {
    name      : 'AccountItemView',
    components: {
        UsersBlock,
        InvoicesBlock,
        AccountInfoList,
        CountersBlock,

        Pagination,
        ErrorsList,
        CustomTextarea,
        CustomCheckbox,
        CustomSelect,
        CustomInput,
        HistoryBtn,
    },
    mixins    : [
        ResponseError,
    ],
    props     : [
        'modelValue',
    ],
    created () {
        this.vueId   = 'uuid' + this.$_uid;
        this.account = this.modelValue;
    },
    data () {
        return {
            account: {},
            actions: null,

            vueId  : null,
            loading: false,
        };
    },
    methods : {
        saveAction () {
            this.loading = true;
            let form     = new FormData();
            form.append('id', this.account.id);
            form.append('number', this.account.number);
            form.append('size', parseInt(this.account.size ? this.account.size : 0));
            form.append('is_invoicing', !!this.account.isInvoicing);
            form.append('cadastreNumber', this.account.cadastreNumber);

            this.clearResponseErrors();
            window.axios[Url.Routes.adminAccountSave.method](
                Url.Routes.adminAccountSave.uri,
                form,
            ).then((response) => {
                this.showInfo('Участок обновлён');

                this.actions    = response.data.actions;
                this.historyUrl = response.data.historyUrl;
            }).catch(response => {
                let text = response?.data?.message ?
                    response.data.message
                    : 'Не получилось сохранить участок';
                this.showDanger(text);
                this.parseResponseErrors(response);
            }).then(() => {
                this.loading = false;
            });
        },
        dropAction () {
            if (!this.id) {
                this.dropped = true;
                return;
            }
            if (!confirm('Удалить акаунт?')) {
                return;
            }

            let uri = Url.Generator.makeUri(Url.Routes.adminAccountDelete, {
                id: this.id,
            });
            window.axios[Url.Routes.adminAccountDelete.method](
                uri,
            ).then((response) => {
                this.dropped = response.data;
                if (response.data) {
                    this.showInfo('Участок удалён');
                }
                else {
                    this.showDanger('Участок не удалён');
                }
            }).catch(response => {
                this.parseResponseErrors(response);
            });
        },
    },
    computed: {
        canSave () {
            return this.account.number
                && this.account.size && this.account.size >= 0;
        },
    },
};
</script>
