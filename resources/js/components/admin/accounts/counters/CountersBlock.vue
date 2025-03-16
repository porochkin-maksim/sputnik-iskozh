<template>
    <div>
        <table class="table table-bordered align-middle m-0 w-auto"
               v-if="counters && counters.length">
            <thead>
            <tr class="text-center align-middle">
                <th>Дата фиксации</th>
                <th>Показания (кВт)</th>
                <th>Фото</th>
            </tr>
            </thead>
            <tbody>
            <template v-for="item in counters">
                <tr>
                    <th colspan="2"
                        class="table-info text-center border-end-0">
                        <a href=""
                           class="text-decoration-none"
                           @click.prevent="editAction(item)">
                            <span v-if="item.isInvoicing">
                                <i class="fa fa-check text-success"></i>
                            </span>
                            <span v-else>
                                <i class="fa fa-close text-secondary"></i>
                            </span>
                            &nbsp;Счётчик "{{ item.number }}"
                        </a>
                    </th>
                    <th class="table-info text-center border-start-0">
                        <button class="btn btn-sm btn-success"
                                v-if="this.account.actions.counters.edit"
                                @click="addHistoryValue(item.id)"
                        >Добавить показания
                        </button>
                    </th>
                </tr>
                <tr v-for="history in item.history">
                    <td class="text-center">
                        <div>{{ history.date }}</div>
                        <div>{{ history.days === null ? '' : '+' + history.days + ' дней' }}</div>
                    </td>
                    <td class="text-end">
                        <div>{{ history.value.toLocaleString('ru-RU') }}</div>
                        <div>{{ history.delta === null ? '' : '+' + history.delta.toLocaleString('ru-RU') + 'кВт' }}</div>
                    </td>
                    <td>
                        <file-item :file="history.file"
                                   v-if="history.file"
                                   :edit="false"
                        />
                    </td>
                </tr>
            </template>
            </tbody>
        </table>
    </div>
    <div class="d-flex align-items-center justify-content-between mt-2">
        <div class="d-flex">
            <button class="btn btn-success me-2"
                    v-if="this.account.actions.counters.edit"
                    @click="addCounter">Добавить счётчик
            </button>
        </div>
    </div>
    <view-dialog v-model:show="showDialog"
                 v-model:hide="hideDialog"
                 @hidden="closeAction"
    >
        <template v-slot:title>{{ mode === 1 ? 'Добавление счётчика' : 'Внесение показаний счётчика' }}</template>
        <template v-slot:body>
            <div class="container-fluid">
                <div v-if="mode === 1">
                    <custom-checkbox v-model="isInvoicing"
                                     :label="'Выставлять счета'"
                    />
                    <custom-input v-model="number"
                                  :errors="errors.number"
                                  :type="'text'"
                                  :label="'Серийный номер устройства'"
                                  :required="true"
                    />
                </div>
                <div class="mt-2"
                     v-if="!(counter && counter.id)">
                    <custom-input v-model="value"
                                  :errors="errors.value"
                                  :type="'number'"
                                  :label="'Текущие показания на счётчике'"
                                  :required="true"
                    />
                </div>
                <div class="mt-2"
                     v-if="!(counter && counter.id)">
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
                    v-if="counter && counter.id"
                    @click="saveAction"
                    :disabled="!canSubmitAction">
                Обновить
            </button>
            <button class="btn btn-success"
                    v-else
                    @click="submitAction"
                    :disabled="!canSubmitAction">
                Добавить
            </button>
        </template>
    </view-dialog>
</template>

<script>
import Url            from '../../../../utils/Url.js';
import ResponseError  from '../../../../mixin/ResponseError.js';
import Wrapper        from '../../../common/Wrapper.vue';
import CustomInput    from '../../../common/form/CustomInput.vue';
import CustomCheckbox from '../../../common/form/CustomCheckbox.vue';
import ViewDialog     from '../../../common/ViewDialog.vue';
import FileItem       from '../../../common/files/FileItem.vue';
import SearchSelect   from '../../../common/form/SearchSelect.vue';

export default {
    components: {
        SearchSelect, FileItem, ViewDialog,
        CustomCheckbox,
        CustomInput,
        Wrapper,

    },
    props     : {
        account: Object,
    },
    mixins    : [
        ResponseError,
    ],
    data () {
        return {
            showDialog: false,
            hideDialog: false,

            counter  : null,
            counterId: null,

            isInvoicing: null,
            number     : null,
            value      : null,

            counters: null,
            file    : null,

            mode: null,
        };
    },
    created () {
        this.listAction();
    },
    methods : {
        listAction () {
            let uri = Url.Generator.makeUri(Url.Routes.adminCounterList, {
                accountId: this.account.id,
            });

            window.axios[Url.Routes.adminCounterList.method](uri).then(response => {
                this.counters = response.data.counters;
            }).catch(response => {
                this.parseResponseErrors(response);
            });
        },
        submitAction () {
            if (this.mode === 1) {
                this.createAction();
            }
            else {
                this.addHistoryValueAction();
            }
        },
        createAction () {
            if (!confirm('Добавить счётчик?')) {
                return;
            }
            let form = new FormData();
            form.append('number', this.number);
            form.append('value', this.value);
            form.append('file', this.file);
            form.append('is_invoicing', this.isInvoicing);

            let uri = Url.Generator.makeUri(Url.Routes.adminCounterCreate, {
                accountId: this.account.id,
            });

            window.axios[Url.Routes.adminCounterCreate.method](uri, form).then(response => {
                this.onSuccessSubmit();
            }).catch(response => {
                this.parseResponseErrors(response);
            });
        },
        saveAction () {
            if (!this.counter || !this.counter.id) {
                return;
            }

            let form = new FormData();
            form.append('id', this.counter.id);
            form.append('number', this.number);
            form.append('is_invoicing', this.isInvoicing);

            let uri = Url.Generator.makeUri(Url.Routes.adminCounterSave, {
                accountId: this.account.id,
            });

            window.axios[Url.Routes.adminCounterSave.method](uri, form).then(response => {
                this.onSuccessSubmit();
            }).catch(response => {
                this.parseResponseErrors(response);
            });
        },
        addHistoryValueAction () {
            let form = new FormData();
            form.append('counter_id', this.counterId);
            form.append('value', this.value);
            form.append('file', this.file);

            let uri = Url.Generator.makeUri(Url.Routes.adminCounterAddValue, {
                accountId: this.account.id,
            });
            window.axios[Url.Routes.adminCounterAddValue.method](uri, form).then(response => {
                this.onSuccessSubmit();
            }).catch(response => {
                this.parseResponseErrors(response);
            });
        },
        onSuccessSubmit () {
            this.showDialog = false;
            this.hideDialog = true;
            this.file       = null;
            this.mode       = null;
            this.counterId  = null;
            this.listAction();
        },
        addCounter () {
            this.mode        = 1;
            this.counter     = null;
            this.number      = null;
            this.value       = null;
            this.isInvoicing = true;
            this.showDialog  = true;
        },
        editAction (item) {
            this.mode        = 1;
            this.counter     = item;
            this.number      = item.number;
            this.isInvoicing = item.isInvoicing;
            this.showDialog  = true;
        },
        addHistoryValue (id) {
            this.mode       = 2;
            this.counter    = null;
            this.number     = null;
            this.showDialog = true;
            this.counterId  = id;

            this.counters.forEach(item => {
                if (item.id === id) {
                    this.value = item.history[0].value;
                }
            })
        },
        closeAction () {
            this.showDialog = false;
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
        Url () {
            return Url;
        },
        canSubmitAction () {
            if (this.mode === 1) {
                if (this.counter && this.counter.id) {
                    return this.number;
                }
                return this.number && this.value;
            }
            return this.value;
        },
    },
};
</script>