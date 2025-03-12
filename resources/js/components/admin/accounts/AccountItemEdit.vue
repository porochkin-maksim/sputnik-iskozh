<template>
    <template v-if="actions.edit">
        <div v-if="!dropped" class="mb-2">
            <div class="input-group input-group-sm w-auto">
                <button class="btn btn-success"
                        @click="saveAction"
                        :disabled="!canSave || loading">
                    <i class="fa"
                       :class="loading ? 'fa-spinner fa-spin' : 'fa-save'"></i>
                </button>
                <span class="input-group-text index">
                {{ id ? id : 'Новый' }}
            </span>
                <div class="input-group-text">
                    <input class="form-check-input mt-0"
                           type="checkbox"
                           v-model="isMember"
                           :id="vueId">
                    &nbsp;
                    <label :for="vueId"
                           class="cursor-pointer">Член СНТ</label>
                </div>
                <input type="text"
                       class="form-control number"
                       placeholder="Номер"
                       v-model="number">
                <span class="input-group-text">
                Площадь (м²)
            </span>
                <input type="number"
                       step="1"
                       class="form-control size"
                       placeholder="Площадь"
                       v-model="size">
                <history-btn :disabled="!historyUrl"
                             class="btn-light border"
                             :url="historyUrl ? historyUrl : ''" />
                <button class="btn btn-danger"
                        @click="dropAction"
                        :disabled="actions?.drop === false || loading">
                    <i class="fa fa-trash"></i>
                </button>
            </div>
        </div>
    </template>
    <template v-else>
        <td>{{ id }}</td>
        <td>{{ isMember ? 'Член СНТ' : 'Не член СНТ' }}</td>
        <td>{{ number }}</td>
        <td>{{ size }}</td>
        <td>
            <history-btn :disabled="!historyUrl"
                         class="btn-link underline-none"
                         :url="historyUrl ? historyUrl : ''" />
        </td>
    </template>
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

export default {
    emits     : ['dropIndex'],
    components: {
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
        this.vueId = 'uuid' + this.$_uid;
        if (this.modelValue) {
            this.id       = this.modelValue.id;
            this.number   = this.modelValue.number;
            this.size     = this.modelValue.size;
            this.isMember = this.modelValue.is_member;

            this.actions    = this.modelValue.actions;
            this.historyUrl = this.modelValue.historyUrl;
        }
        else {
            this.makeAction();
        }
    },
    data () {
        return {
            id        : null,
            number    : null,
            size      : null,
            isMember  : null,
            historyUrl: null,
            actions   : null,

            vueId  : null,
            dropped: false,
            loading: false,
        };
    },
    methods : {
        saveAction () {
            this.loading = true;
            let form     = new FormData();
            form.append('id', this.id);
            form.append('number', this.number);
            form.append('size', parseInt(this.size ? this.size : 0));
            form.append('is_member', !!this.isMember);

            this.clearResponseErrors();
            window.axios[Url.Routes.adminAccountSave.method](
                Url.Routes.adminAccountSave.uri,
                form,
            ).then((response) => {
                let text = this.id ? 'Участок обновлён' : 'Участок ' + response.data.account.id + ' создан';
                this.showInfo(text);

                this.id       = response.data.account.id;
                this.number   = response.data.account.number;
                this.size     = response.data.account.size;
                this.isMember = response.data.account.is_member;

                this.actions    = response.data.account.actions;
                this.historyUrl = response.data.account.historyUrl;
            }).catch(response => {
                let text = response?.data?.message ?
                    response.data.message
                    : 'Не получилось ' + (this.id ? 'сохранить' : 'создать') + ' участок';
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
