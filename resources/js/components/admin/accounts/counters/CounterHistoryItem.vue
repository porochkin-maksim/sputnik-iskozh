<template>
    <view-dialog v-model:show="showDialog"
                 v-model:hide="hideDialog"
                 @hidden="closeAction"
    >
        <template v-slot:title>{{ localHistory?.id ? 'Изменение показаний счётчика' : 'Внесение показаний счётчика' }}</template>
        <template v-slot:body>
            <div class="container-fluid">
                <div v-if="counter.isInvoicing"
                     class="alert alert-info">
                    <template v-if="localHistory?.id">
                        При обновлении показаний будет автоматически пересчитана услуга к регулярному счёту текущего периода, либо будет создан новый доходный счёт
                    </template>
                    <template v-else>
                        При добавлении показаний будет автоматически создана услуга к регулярному счёту текущего периода, либо будет создан новый доходный счёт
                    </template>
                </div>
                <div class="mt-2">
                    <custom-input v-model="localHistory.value"
                                  :errors="errors.value"
                                  :type="'number'"
                                  :label="'Текущие показания на счётчике'"
                                  :required="true"
                    />
                </div>
                <div class="mt-2">
                    <custom-input v-model="localHistory.date"
                                  :errors="errors.date"
                                  :type="'date'"
                                  :label="'Дата показаний'"
                                  :required="true"
                    />
                </div>
                <div class="mt-2">
                    <div v-if="file">
                        <button class="btn btn-sm btn-danger"
                                @click="removeFile">
                            <i class="fa fa-trash"></i>
                        </button>
                        &nbsp;
                        {{ file.name }}
                    </div>
                    <template v-else>
                        <button class="btn btn-outline-secondary"
                                @click="chooseFile"
                                v-if="!file">
                            <i class="fa fa-paperclip "></i>&nbsp;Фото счётчика
                        </button>
                        <input class="d-none"
                               type="file"
                               ref="fileElem"
                               accept="image/*"
                               @change="appendFile"
                        />
                    </template>
                </div>
            </div>
        </template>
        <template v-slot:footer>
            <button class="btn btn-success"
                    :disabled="!canSubmitAction"
                    @click="saveAction">
                {{ localHistory?.id ? 'Сохранить' : 'Добавить' }}
            </button>
        </template>
    </view-dialog>
</template>
<script>
import CustomInput    from '../../../common/form/CustomInput.vue';
import ViewDialog     from '../../../common/ViewDialog.vue';
import CustomCheckbox from '../../../common/form/CustomCheckbox.vue';
import SearchSelect   from '../../../common/form/SearchSelect.vue';
import FileItem       from '../../../common/files/FileItem.vue';
import Wrapper        from '../../../common/Wrapper.vue';
import ResponseError  from '../../../../mixin/ResponseError.js';
import Url            from '../../../../utils/Url.js';

export default {
    emits     : ['historyUpdated'],
    components: {
        SearchSelect,
        FileItem,
        ViewDialog,
        CustomCheckbox,
        CustomInput,
        Wrapper,
    },
    props     : {
        counter: {
            type   : Object,
            default: null,
        },
        history: {
            type   : Object,
            default: null,
        },
    },
    mixins    : [
        ResponseError,
    ],
    data () {
        return {
            loading: false,

            vueId: null,

            showDialog: false,
            hideDialog: false,

            localHistory: {},

            file: null,
        };
    },
    created () {
        this.vueId = 'uuid' + this.$_uid;

        const date        = new Date();
        this.localHistory = this.history ? Object.assign({}, this.history) : { date: date.toISOString().split('T')[0] };
        if (!this.history) {
            this.localHistory.value = this.counter.value;
        }
        this.showDialog = true;
    },
    methods : {
        saveAction () {
            this.loading = true;

            let form = new FormData();
            form.append('counter_id', this.counter.id);
            form.append('id', this.localHistory.id);
            form.append('value', this.localHistory.value);
            form.append('date', this.localHistory.date);
            form.append('file', this.file);

            let uri = Url.Generator.makeUri(Url.Routes.adminCounterAddValue, {
                accountId: this.counter.accountId,
            });
            window.axios[Url.Routes.adminCounterAddValue.method](uri, form).then(response => {
                this.onSuccessSubmit();
                if (this.localHistory?.id) {
                    this.showInfo('Показания обновлены');
                }
                else {
                    this.showInfo('Показания добавлены');
                }
            }).catch(response => {
                this.parseResponseErrors(response);
            }).then(() => {
                this.loading = false;
            });
        },
        onSuccessSubmit () {
            this.showDialog = false;
            this.hideDialog = true;
            this.file       = null;
            this.$emit('historyUpdated');
        },
        closeAction () {
            this.showDialog = false;
            this.$emit('historyUpdated');
        },
        chooseFile () {
            this.$refs.fileElem.click();
        },
        appendFile (event) {
            this.file = event.target.files[0];
        },
        removeFile () {
            this.file = null;
        },
    },
    computed: {
        canSubmitAction () {
            return !this.loading && this.localHistory.value && this.localHistory.date;
        },
    },
};
</script>