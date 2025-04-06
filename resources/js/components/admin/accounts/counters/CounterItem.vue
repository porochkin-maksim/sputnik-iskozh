<template>
    <view-dialog v-model:show="showDialog"
                 v-model:hide="hideDialog"
                 @hidden="closeAction"
    >
        <template v-slot:title>{{ localCounter?.id ? 'Редактирование счётчика' : 'Добавление счётчика' }}</template>
        <template v-slot:body>
            <div class="container-fluid">
                <div>
                    <custom-input v-model="localCounter.number"
                                  :errors="errors.number"
                                  :type="'text'"
                                  :label="'Серийный номер устройства'"
                                  :required="true"
                    />
                </div>
                <div class="mt-2">
                    <custom-checkbox v-model="localCounter.isInvoicing"
                                     :label="'Выставлять счета'"
                    />
                </div>
                <div class="mt-2">
                    <custom-input v-model="localCounter.increment"
                                  :errors="errors.increment"
                                  :type="'number'"
                                  :min="0"
                                  :step="1"
                                  :label="'Ежемесячное автоприращение показаний на кВт'"
                                  :required="true"
                                  @focusout="calculateIncrement"
                    />
                </div>
                <div class="mt-2"
                     v-if="!localCounter?.id">
                    <custom-input v-model="localCounter.value"
                                  :errors="errors.value"
                                  :type="'number'"
                                  :label="'Текущие показания на счётчике'"
                                  :required="true"
                    />
                </div>
                <div class="mt-2"
                     v-if="!localCounter?.id">
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
                    @click="saveAction"
                    :disabled="!canSubmitAction">
                {{ localCounter?.id ? 'Сохранить' : 'Добавить' }}
            </button>
        </template>
    </view-dialog>
</template>

<script>
import Url                from '../../../../utils/Url.js';
import ResponseError      from '../../../../mixin/ResponseError.js';
import Wrapper            from '../../../common/Wrapper.vue';
import CustomInput        from '../../../common/form/CustomInput.vue';
import CustomCheckbox     from '../../../common/form/CustomCheckbox.vue';
import ViewDialog         from '../../../common/ViewDialog.vue';
import FileItem           from '../../../common/files/FileItem.vue';
import SearchSelect       from '../../../common/form/SearchSelect.vue';
import CounterHistoryItem from './CounterHistoryItem.vue';

export default {
    emits     : ['counterUpdated'],
    components: {
        CounterHistoryItem,
        SearchSelect,
        FileItem,
        ViewDialog,
        CustomCheckbox,
        CustomInput,
        Wrapper,
    },
    props     : {
        account: Object,
        counter: {
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

            localCounter: {},

            file: null,
        };
    },
    created () {
        this.vueId = 'uuid' + this.$_uid;

        this.localCounter = this.counter ? Object.assign({}, this.counter) : {};

        this.showDialog = true;
    },
    methods : {
        saveAction () {
            let form = new FormData();
            form.append('id', this.localCounter.id);
            form.append('number', this.localCounter.number);
            form.append('is_invoicing', this.localCounter.isInvoicing);
            form.append('value', this.localCounter.value);
            form.append('file', this.file);
            form.append('increment', this.localCounter.increment);

            let route = this.localCounter?.id ? Url.Routes.adminCounterSave : Url.Routes.adminCounterCreate;

            let uri = Url.Generator.makeUri(route, {
                accountId: this.account.id,
            });

            window.axios[route.method](uri, form).then(response => {
                this.onSuccessSubmit();
                if (this.counter && this.counter.id) {
                    this.showInfo('Счётчик обновлён');
                }
                else {
                    this.showInfo('Счётчик добавлен');
                }
            }).catch(response => {
                this.parseResponseErrors(response);
            });
        },
        onSuccessSubmit () {
            this.showDialog = false;
            this.hideDialog = true;
            this.file       = null;
            this.$emit('counterUpdated');
        },
        closeAction () {
            this.showDialog = false;
            this.$emit('counterUpdated');
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
        calculateIncrement () {
            this.localCounter.increment = this.localCounter.increment < 0 ? this.localCounter.increment * -1 : this.localCounter.increment;
        },
    },
    computed: {
        canSubmitAction () {
            return this.localCounter.number && (this.localCounter.id || this.localCounter.value);
        },
    },
};
</script>