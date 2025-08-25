<template>
    <div class="row">
        <div class="col-lg-6 col-12">
            <div class="card mb-2">
                <div class="card-body">
                    <h5>Информация</h5>
                    <template v-if="account.actions.edit">
                        <div>
                            <custom-input v-model="account.number"
                                          :required="true"
                                          :label="'Номер участка'"
                            />
                        </div>
                        <div class="mt-2">
                            <custom-input v-model="account.size"
                                          :label="'Площадь (м²)'"
                                          :type="'number'"
                                          :min="0"
                                          :step="1"
                                          :required="true"
                            />
                        </div>
                        <div class="mt-2">
                            <custom-checkbox v-model="account.isInvoicing"
                                             :label="'Выставлять счета'"
                            />
                        </div>
                        <div>
                            <custom-input v-model="account.cadastreNumber"
                                          :label="'Кадастровый номер'"
                                          :required="true"
                            />
                        </div>
                        <div class="mt-2">
                            <custom-input v-model="account.registryDate"
                                          :label="'Дата регистрации'"
                                          :type="'date'"
                                          @change="clearError('registryDate')"
                            />
                        </div>
                    </template>
                    <template v-else>
                        <h6>Данные участка</h6>
                        <account-info-list :account="account" />
                    </template>
                    <div class="d-flex align-items-center justify-content-between mt-2">
                        <div class="d-flex">
                            <div class="d-flex"
                                 v-if="account.actions.edit">
                                <button class="btn btn-success me-2"
                                        v-if="!loading"
                                        :disabled="!canSave"
                                        v-on:click="saveAction">Сохранить участок
                                </button>
                                <button class="btn border-0"
                                        disabled
                                        v-else>
                                    <i class="fa fa-spinner fa-spin"></i> Сохранение
                                </button>
                            </div>
                        </div>
                        <div class="d-flex">
                            <history-btn
                                class="btn-link underline-none"
                                :url="account.historyUrl" />
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-lg-6 col-12">
            <div class="card mb-2"
                 v-if="account.users && account.users.length">
                <div class="card-body">
                    <h5>Пользователи</h5>
                    <ol class=" mb-0 ps-3">
                        <li v-for="user in account.users"
                            class="">
                            <a :href="user.viewUrl"
                               v-if="user.actions.view">{{ user.fullName }}</a>
                            <span v-else>{{ user.fullName }}</span>
                            &nbsp;<i class="fa fa-user" :class="[user.fractionPercent ? 'text-success' : 'text-light']"></i>&nbsp;{{ user.fractionPercent }}
                        </li>
                    </ol>
                </div>
            </div>
            <div class="card mb-2">
                <div class="card-body">
                    <counters-block :account="account" />
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
import SimpleSelect    from '../../common/form/SimpleSelect.vue';
import ErrorsList      from '../../common/form/partial/ErrorsList.vue';
import HistoryBtn      from '../../common/HistoryBtn.vue';
import Pagination      from '../../common/pagination/Pagination.vue';
import SearchSelect    from '../../common/form/SearchSelect.vue';
import CountersBlock   from './counters/CountersBlock.vue';
import AccountInfoList from './AccountInfoList.vue';
import InvoicesBlock   from './invoices/InvoicesBlock.vue';

export default {
    name      : 'AccountItemView',
    components: {
        InvoicesBlock,
        AccountInfoList,
        CountersBlock,
        SearchSelect, Pagination,
        ErrorsList,
        SimpleSelect,
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
            form.append('registryDate', this.account.registryDate);

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
