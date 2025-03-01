<template>
    <div v-if="!dropped">
        <div class="input-group input-group-sm w-auto">
            <button class="btn btn-success"
                    @click="saveAction"
                    :disabled="!canSave || loading">
                <i class="fa"
                   :class="loading ? 'fa-spinner fa-spin' : 'fa-save'"></i>
            </button>
            <div class="input-group-text">
                <input class="form-check-input mt-0"
                       type="checkbox"
                       v-model="active"
                       :id="vueId"
                       :disabled="actions?.active === false">
                &nbsp;
                <label :for="vueId">Активен</label>
            </div>
            <input type="text"
                   class="form-control name"
                   placeholder="Название"
                   v-model="name">
            <input type="number"
                   style="max-width: 120px"
                   step="0.01"
                   class="form-control cost"
                   placeholder="Стоимость"
                   v-model="cost">
            <simple-select v-model="periodId"
                           class="period"
                           :items="periods"
                           :disabled="id"
                           :label="'Период'"
            />
            <simple-select v-model="type"
                           style="min-width: 230px"
                           :errors="errors.type"
                           :items="actions?.type === false ? types.all : types.available[periodId]"
                           :disabled="actions?.type === false || !periodId"
                           :label="'Тип'"
            />
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
        'types',
        'periods',
    ],
    created () {
        this.vueId = 'uuid' + this.$_uid;
        if (this.modelValue) {
            this.id       = this.modelValue.id;
            this.name     = this.modelValue.name;
            this.periodId = this.modelValue.periodId;
            this.type     = this.modelValue.type;
            this.cost     = this.modelValue.cost;
            this.active   = this.modelValue.active;

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
            name      : null,
            type      : null,
            periodId  : null,
            cost      : null,
            active    : null,
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
            form.append('name', this.name);
            form.append('type', this.type);
            form.append('period_id', this.periodId);
            form.append('cost', parseFloat(this.cost).toFixed(2));
            form.append('active', this.active);

            this.clearResponseErrors();
            window.axios[Url.Routes.adminServiceSave.method](
                Url.Routes.adminServiceSave.uri,
                form,
            ).then((response) => {
                let text = this.id ? 'Услуга обновлена' : 'Услуга создана';
                this.showInfo(text);

                this.id     = response.data.service.id;
                this.name   = response.data.service.name;
                this.type   = response.data.service.type;
                this.cost   = response.data.service.cost;
                this.active = response.data.service.active;

                this.actions    = response.data.service.actions;
                this.historyUrl = response.data.service.historyUrl;
            }).catch(response => {
                let text = response?.data?.message ?
                    response.data.message
                    : 'Не получилось ' + (this.id ? 'сохранить' : 'создать') + ' услугу';
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
            if (!confirm('Удалить услугу?')) {
                return;
            }

            let uri = Url.Generator.makeUri(Url.Routes.adminServiceDelete, {
                id: this.id,
            });
            window.axios[Url.Routes.adminServiceDelete.method](
                uri,
            ).then((response) => {
                this.dropped = response.data;
                if (response.data) {
                    this.showInfo('Услуга удалена');
                }
                else {
                    this.showDanger('Услуга не удалена');
                }
            }).catch(response => {
                this.parseResponseErrors(response);
            });
        },
    },
    computed: {
        canSave () {
            return this.name && this.type && parseFloat(this.cost) >= 0 && this.periodId;
        },
    },
};
</script>

<style scoped>
.period {max-width : 150px;width : 150px;}
</style>