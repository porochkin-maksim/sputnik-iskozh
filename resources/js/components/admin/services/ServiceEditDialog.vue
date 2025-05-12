<template>
    <view-dialog v-model:show="showDialog"
                 v-model:hide="hideDialog"
                 @hidden="closeDialog"
    >
        <template v-slot:title>
            {{ modelValue?.id ? 'Редактирование услуги' : 'Создание услуги' }}
        </template>
        <template v-slot:body>
            <div class="container-fluid">
                <div class="form-check d-none">
                    <input type="checkbox"
                           class="form-check-input"
                           v-model="active"
                           :disabled="!actions?.active">
                    <label class="form-check-label">Активна</label>
                </div>
                <label>Название</label>
                <input type="text"
                       class="form-control"
                       v-model="name">
                <label>Стоимость</label>
                <input type="number"
                       step="0.01"
                       class="form-control"
                       v-model="cost">
                <label>Период</label>
                <simple-select v-model="periodId"
                               class="form-control"
                               :items="periods"
                               :disabled="!actions?.period" />
                <label>Тип</label>
                <simple-select v-model="type"
                               class="form-control"
                               :items="actions && actions.type === false ? types.all : (types.available && types.available[periodId] || [])"
                               :disabled="!actions?.type" />
            </div>
        </template>
        <template v-slot:footer>
            <button class="btn btn-success"
                    :disabled="!canSave || loading"
                    @click="saveAction">
                <i class="fa"
                   :class="loading ? 'fa-spinner fa-spin' : 'fa-save'"></i>
                {{ modelValue?.id ? 'Сохранить' : 'Создать' }}
            </button>
        </template>
    </view-dialog>
</template>

<script>
import ResponseError from '../../../mixin/ResponseError.js';
import Url           from '../../../utils/Url.js';
import ViewDialog    from '../../common/ViewDialog.vue';
import SimpleSelect  from '../../common/form/SimpleSelect.vue';

export default {
    name      : 'ServiceEditDialog',
    components: {
        ViewDialog,
        SimpleSelect,
    },
    mixins    : [ResponseError],
    props     : {
        modelValue: {
            type   : Object,
            default: null,
        },
        show      : {
            type   : Boolean,
            default: false,
        },
        types     : {
            type    : Object,
            required: true,
        },
        periods   : {
            type    : Array,
            required: true,
        },
    },
    emits     : ['update:modelValue', 'update:show'],
    data () {
        return {
            id        : null,
            name      : null,
            type      : null,
            periodId  : null,
            cost      : null,
            active    : false,
            loading   : false,
            hideDialog: false,
            actions   : null,
        };
    },
    computed: {
        showDialog: {
            get () {
                return this.show;
            },
            set (value) {
                this.$emit('update:show', value);
            },
        },
        canSave () {
            return this.name && this.type && parseFloat(this.cost) >= 0 && this.periodId;
        },
    },
    watch   : {
        modelValue: {
            handler (newValue) {
                if (newValue) {
                    this.id       = newValue.id;
                    this.name     = newValue.name;
                    this.type     = newValue.type;
                    this.periodId = newValue.periodId;
                    this.cost     = newValue.cost;
                    this.active   = newValue.active;
                    this.actions  = newValue.actions;
                }
                else {
                    this.resetForm();
                }
            },
            immediate: true,
        },
    },
    methods : {
        resetForm () {
            this.id       = null;
            this.name     = null;
            this.type     = null;
            this.periodId = null;
            this.cost     = null;
            this.active   = false;
            this.actions  = null;
        },
        closeDialog () {
            this.showDialog = false;
            this.resetForm();
        },
        saveAction () {
            this.loading = true;
            let form     = new FormData();
            if (this.modelValue?.id) {
                form.append('id', this.modelValue.id);
            }
            form.append('name', this.name);
            form.append('type', this.type);
            form.append('period_id', this.periodId);
            form.append('cost', parseFloat(this.cost).toFixed(2));
            form.append('active', this.active);

            this.clearResponseErrors();
            window.axios[Url.Routes.adminServiceSave.method](
                Url.Routes.adminServiceSave.uri,
                form,
            ).then(response => {
                this.$emit('update:modelValue', response.data.service);
                this.showInfo(this.modelValue?.id ? 'Услуга обновлена' : 'Услуга создана');
                this.closeDialog();
            }).catch(response => {
                let text = response?.data?.message ||
                    'Не удалось ' + (this.modelValue?.id ? 'обновить' : 'создать') + ' услугу';
                this.showDanger(text);
                this.parseResponseErrors(response);
            }).finally(() => {
                this.loading = false;
            });
        },
    },
};
</script> 