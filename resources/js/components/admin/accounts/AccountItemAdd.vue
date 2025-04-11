<template>
    <view-dialog v-model:show="showDialog"
                 v-model:hide="hideDialog"
                 @hidden="onCloseDialog"
    >
        <template v-slot:title>Добавление участка</template>
        <template v-slot:body>
            <div class="container-fluid">
                <div>
                    <custom-input v-model="number"
                                  v-if="modelValue.actions.edit"
                                  :required="true"
                                  :label="'Номер участка'"
                    />
                </div>
                <div class="mt-2">
                    <custom-input v-model="size"
                                  v-if="modelValue.actions.edit"
                                  :label="'Площадь (м²)'"
                                  :required="true"
                    />
                </div>
                <div class="mt-2">
                    <custom-input v-model="cadastreNumber"
                                  v-if="modelValue.actions.edit"
                                  :label="'Кадастровый номер'"
                    />
                </div>
                <div class="mt-2">
                    <custom-input v-model="registryDate"
                                  v-if="modelValue.actions.edit"
                                  :label="'Дата регистрации'"
                    />
                </div>
            </div>
        </template>
        <template v-slot:footer>
            <button class="btn btn-success"
                    :disabled="!canSave"
                    @click="saveAction">
                Создать
            </button>
        </template>
    </view-dialog>
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
import ViewDialog     from '../../common/ViewDialog.vue';

export default {
    emits     : ['updated'],
    components: {
        ErrorsList,
        SimpleSelect,
        CustomTextarea,
        CustomCheckbox,
        CustomSelect,
        CustomInput,
        HistoryBtn,
        ViewDialog,
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
            this.number         = this.modelValue.number;
            this.size           = this.modelValue.size;
            this.isMember       = this.modelValue.is_member;
            this.cadastreNumber = this.modelValue.cadastreNumber;
            this.registryDate   = this.modelValue.registryDate;

            this.showDialog = true;
            this.hideDialog = false;
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
            form.append('number', this.number);
            form.append('size', parseInt(this.size ? this.size : 0));
            form.append('is_member', !!this.isMember);
            form.append('cadastreNumber', this.cadastreNumber);
            form.append('registryDate', this.registryDate);

            this.clearResponseErrors();
            window.axios[Url.Routes.adminAccountSave.method](
                Url.Routes.adminAccountSave.uri,
                form,
            ).then((response) => {
                this.showInfo('Участок ' + response.data.account.id + ' создан');

                this.$emit('updated');
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
