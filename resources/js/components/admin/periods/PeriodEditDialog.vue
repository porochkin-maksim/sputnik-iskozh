<template>
    <view-dialog v-model:show="showDialog"
                 v-model:hide="hideDialog"
                 @hidden="closeDialog"
    >
        <template v-slot:title>
            {{ modelValue?.id ? 'Редактирование периода' : 'Создание периода' }}
        </template>
        <template v-slot:body>
            <div class="container-fluid">
                <div class="mb-3">
                    <label class="form-label">Название</label>
                    <input type="text"
                           class="form-control"
                           v-model="name"
                           :disabled="modelValue?.isClosed">
                </div>
                <div class="mb-3">
                    <label class="form-label">Начало периода</label>
                    <input type="datetime-local"
                           class="form-control"
                           v-model="startAt"
                           :disabled="modelValue?.isClosed">
                </div>
                <div class="mb-3">
                    <label class="form-label">Окончание периода</label>
                    <input type="datetime-local"
                           class="form-control"
                           v-model="endAt"
                           :disabled="modelValue?.isClosed">
                </div>
                <div class="mb-3">
                    <div class="form-check">
                        <input type="checkbox"
                               class="form-check-input"
                               v-model="isClosed"
                               :disabled="!modelValue?.id">
                        <label class="form-check-label">Закрыть период</label>
                    </div>
                </div>
            </div>
        </template>
        <template v-slot:footer>
            <button class="btn btn-success"
                    :disabled="!canSave || loading"
                    @click="saveAction">
                <i class="fa" :class="loading ? 'fa-spinner fa-spin' : 'fa-save'"></i>
                {{ modelValue?.id ? 'Сохранить' : 'Создать' }}
            </button>
        </template>
    </view-dialog>
</template>

<script>
import ResponseError from '../../../mixin/ResponseError.js';
import Url from '../../../utils/Url.js';
import ViewDialog from '../../common/ViewDialog.vue';

export default {
    name: 'PeriodEditDialog',
    components: {
        ViewDialog
    },
    mixins: [ResponseError],
    props: {
        modelValue: {
            type: Object,
            default: null
        },
        show: {
            type: Boolean,
            default: false
        }
    },
    emits: ['update:modelValue', 'update:show'],
    data() {
        return {
            name: null,
            startAt: null,
            endAt: null,
            isClosed: false,
            loading: false,
            hideDialog: false
        };
    },
    computed: {
        showDialog: {
            get() {
                return this.show;
            },
            set(value) {
                this.$emit('update:show', value);
            }
        },
        canSave() {
            return this.name && this.startAt && this.endAt;
        }
    },
    watch: {
        modelValue: {
            handler(newValue) {
                if (newValue) {
                    this.id = newValue.id;
                    this.name = newValue.name;
                    this.startAt = newValue.startAt;
                    this.endAt = newValue.endAt;
                    this.isClosed = newValue.isClosed;
                } else {
                    this.resetForm();
                }
            },
            immediate: true
        }
    },
    methods: {
        resetForm() {
            this.name = null;
            this.startAt = null;
            this.endAt = null;
            this.isClosed = false;
        },
        closeDialog() {
            this.showDialog = false;
            this.resetForm();
        },
        saveAction() {
            if (this.isClosed) {
                if (!confirm('Вы уверены что хотите закрыть период? Это необратимое действие!')) {
                    return;
                }
            }

            this.loading = true;
            let form = new FormData();
            if (this.modelValue?.id) {
                form.append('id', this.modelValue.id);
            }
            form.append('name', this.name);
            form.append('start_at', this.startAt);
            form.append('end_at', this.endAt);
            form.append('is_closed', this.isClosed);

            this.clearResponseErrors();
            window.axios[Url.Routes.adminPeriodSave.method](
                Url.Routes.adminPeriodSave.uri,
                form
            ).then(response => {
                this.$emit('update:modelValue', response.data.period);
                this.showInfo(this.modelValue?.id ? 'Период обновлен' : 'Период создан');
                this.closeDialog();
            }).catch(response => {
                let text = response?.data?.message || 
                    'Не удалось ' + (this.modelValue?.id ? 'обновить' : 'создать') + ' период';
                this.showDanger(text);
                this.parseResponseErrors(response);
            }).finally(() => {
                this.loading = false;
            });
        }
    }
};
</script> 