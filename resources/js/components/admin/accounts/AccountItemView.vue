<template>
    <div class="d-flex align-items-center justify-content-between mb-2">
        <div class="d-flex">
            <button class="btn btn-success me-2"
                    v-if="account.actions.edit"
                    v-on:click="saveAction">Сохранить участок
            </button>
        </div>
        <div class="d-flex">
            <history-btn
                class="btn-link underline-none"
                :url="account.historyUrl" />
        </div>
    </div>
    <div class="row">
        <div class="col-4">
            <template v-if="account.actions.edit">
                <custom-checkbox v-model="account.isMember"
                                 :label="'Член СНТ'"
                />
                <div>
                    <custom-input v-model="account.number"
                                  :required="true"
                                  :label="'Номер участка'"
                    />
                </div>
                <div class="mt-2">
                    <custom-input v-model="account.size"
                                  :label="'Площадь (м²)'"
                                  :required="true"
                    />
                </div>
            </template>
            <template v-else>
                <h6>Данные участка</h6>
                <ul class="list-group">
                    <li class="list-group-item">{{ account.isMember ? 'Член СНТ' : 'Не член СНТ' }}</li>
                    <li class="list-group-item">Номер участка {{ account.number }}</li>
                    <li class="list-group-item">Площадь {{ account.size }}(м²)</li>
                </ul>
            </template>
        </div>
    </div>
    <div class="row">
        <div class="col-6">
            <div class="mt-2"
                 v-if="account.users && account.users.length">
                <h6>Пользователи</h6>
                <ul class="list-group">
                    <li v-for="user in account.users"
                        class="list-group-item">
                        <a :href="user.viewUrl"
                           v-if="user.actions.view">{{ user.fullName }}</a>
                        <span v-else>{{ user.fullName }}</span>
                    </li>
                </ul>
            </div>
        </div>
    </div>
    <div class="mt-2 border-top">
        <counters-block :account="account" />
    </div>
</template>

<script>
import Url            from '../../../utils/Url.js';
import CustomInput    from '../../common/form/CustomInput.vue';
import CustomCheckbox from '../../common/form/CustomCheckbox.vue';
import CustomTextarea from '../../common/form/CustomTextarea.vue';
import CustomSelect   from '../../common/form/CustomSelect.vue';
import ResponseError  from '../../../mixin/ResponseError.js';
import SimpleSelect   from '../../common/form/SimpleSelect.vue';
import ErrorsList     from '../../common/form/partial/ErrorsList.vue';
import HistoryBtn     from '../../common/HistoryBtn.vue';
import Pagination     from '../../common/pagination/Pagination.vue';
import SearchSelect   from '../../common/form/SearchSelect.vue';
import CountersBlock  from './counters/CountersBlock.vue';

export default {
    name      : 'AccountItemView',
    components: {
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
            form.append('is_member', !!this.account.isMember);

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
            return this.number;
        },
    },
};
</script>

<style scoped>
.index {width : 80px;}

.size {width : 50px;}
</style>
