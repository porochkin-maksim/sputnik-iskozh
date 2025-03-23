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
                <input type="text"
                       class="form-control name"
                       placeholder="Название"
                       v-model="name">
                <input type="datetime-local"
                       style="max-width: 200px"
                       class="form-control"
                       placeholder="Начало"
                       v-model="startAt">
                <span class="input-group-text">
                -
            </span>
                <input type="datetime-local"
                       style="max-width: 200px"
                       class="form-control"
                       placeholder="Окончание"
                       v-model="endAt">
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
        <td>{{ name }}</td>
        <td>{{ $formatDateTime(startAt) }}</td>
        <td>{{ $formatDateTime(endAt) }}</td>
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
            this.id      = this.modelValue.id;
            this.name    = this.modelValue.name;
            this.startAt = this.modelValue.startAt;
            this.endAt   = this.modelValue.endAt;

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
            startAt   : null,
            endAt     : null,
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
            form.append('start_at', this.startAt);
            form.append('end_at', this.endAt);

            this.clearResponseErrors();
            window.axios[Url.Routes.adminPeriodSave.method](
                Url.Routes.adminPeriodSave.uri,
                form,
            ).then((response) => {
                let text = this.id ? 'Период обновлён' : 'Период ' + response.data.period.id + ' создан';
                this.showInfo(text);

                this.id      = response.data.period.id;
                this.name    = response.data.period.name;
                this.startAt = response.data.period.startAt;
                this.endAt   = response.data.period.endAt;

                this.actions    = response.data.period.actions;
                this.historyUrl = response.data.period.historyUrl;
            }).catch(response => {
                let text = response?.data?.message ?
                    response.data.message
                    : 'Не получилось ' + (this.id ? 'сохранить' : 'создать') + ' период';
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
            if (!confirm('Удалить период?')) {
                return;
            }

            let uri = Url.Generator.makeUri(Url.Routes.adminPeriodDelete, {
                id: this.id,
            });
            window.axios[Url.Routes.adminPeriodDelete.method](
                uri,
            ).then((response) => {
                this.dropped = response.data;
                if (response.data) {
                    this.showInfo('Период удалён');
                }
                else {
                    this.showDanger('Период не удалён');
                }
            }).catch(response => {
                this.parseResponseErrors(response);
            });
        },
    },
    computed: {
        canSave () {
            return this.name && this.startAt && this.endAt;
        },
    },
};
</script>

<style scoped>
.name {width : 150px;}
</style>
