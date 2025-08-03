<template>
    <view-dialog v-model:show="showDialog"
                 v-model:hide="hideDialog"
    >
        <template v-slot:title>{{ id ? 'Редактирование счёта' : 'Добавление счёта' }}</template>
        <template v-slot:body>
            <div class="container-fluid">
                <custom-input v-model="name"
                               class="form-select form-select-sm"
                               :label="'Название (опционально)'"
                />
                <label>Тип</label>
                <simple-select v-model="type"
                               class="form-select form-select-sm"
                               :items="types"
                               :label="'Тип'"
                />
                <label>Период</label>
                <simple-select v-model="periodId"
                               class="form-select form-select-sm"
                               :items="periods"
                               :label="'Период'"
                />
                <label>Участок</label>
                <search-select :items="accounts"
                               :prop-class="'form-control'"
                               v-model="accountId"
                />
            </div>
        </template>
        <template v-slot:footer>
            <button class="btn btn-success"
                    :disabled="!canSave"
                    @click="saveAction">
                {{ id ? 'Сохранить' : 'Создать' }} счёт
            </button>
        </template>
    </view-dialog>
</template>

<script>
import Url           from '../../../utils/Url.js';
import SimpleSelect  from '../../common/form/SimpleSelect.vue';
import CustomInput   from '../../common/form/CustomInput.vue';
import ErrorsList    from '../../common/form/partial/ErrorsList.vue';
import SearchSelect  from '../../common/form/SearchSelect.vue';
import ViewDialog    from '../../common/ViewDialog.vue';
import ResponseError from '../../../mixin/ResponseError.js';

export default {
    emits     : ['update:modelValue', 'updated'],
    components: {
        ViewDialog,
        SearchSelect,
        ErrorsList,
        SimpleSelect,
        CustomInput,
    },
    mixins    : [
        ResponseError,
    ],
    props     : [
        'modelValue',
        'accounts',
        'periods',
        'types',
    ],
    created () {
        this.vueId     = 'uuid' + this.$_uid;
        this.id        = this.modelValue.id;
        this.periodId  = this.modelValue.periodId;
        this.accountId = this.modelValue.accountId;
        this.type      = this.modelValue.type;
        this.name      = this.modelValue.name;

        this.showDialog = true;
    },
    data () {
        return {
            showDialog: false,
            hideDialog: false,

            id       : null,
            periodId : null,
            accountId: null,
            type     : null,

            vueId  : null,
            loading: false,
        };
    },
    methods : {
        saveAction () {
            this.loading = true;
            let form     = new FormData();
            form.append('id', this.id);
            form.append('period_id', this.periodId);
            form.append('account_id', this.accountId);
            form.append('type', this.type);
            form.append('name', this.name);

            this.clearResponseErrors();
            window.axios[Url.Routes.adminInvoiceSave.method](
                Url.Routes.adminInvoiceSave.uri,
                form,
            ).then((response) => {
                let text = this.id ? 'Счёт обновлён' : 'Счёт ' + response.data.invoice.id + ' создан';
                this.showInfo(text);
                this.$emit('updated');
                this.hideDialog = true;
            }).catch(response => {
                let text = response?.data?.message ?
                    response.data.message
                    : 'Не получилось ' + (this.id ? 'сохранить' : 'создать') + ' счёт';
                this.showDanger(text);
                this.parseResponseErrors(response);
            }).then(() => {
                this.loading = false;
            });
        },
    },
    computed: {
        canSave () {
            return this.type && this.periodId && this.accountId;
        },
    },
};
</script>
