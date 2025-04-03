<template>
    <div v-if="counters && counters.length">
        <template v-for="item in counters">
            <div class="row justify-content-center">
                <div class="col-12 col-md-8 col-lg-6 border bg-light d-flex justify-content-between align-items-center p-2">
                    <div>
                        <b>Счётчик {{ item.number }}</b>
                    </div>
                    <div>
                        <button class="btn btn-sm btn-success"
                                v-if="currentCounterId === item.id && canAddNewHistory(item)"
                                @click="addHistoryValue(item)"
                        >Добавить показания
                        </button>
                        <button class="btn btn-light border-secondary btn-sm"
                                v-if="currentCounterId !== item.id"
                                @click="toggleCounterBlock(item.id)">
                            Подробнее <i class="fa fa-chevron-down"></i>
                        </button>
                    </div>
                </div>
            </div>
            <template v-if="currentCounterId === item.id">
                <template v-for="history in item.history">
                    <div class="row justify-content-center">
                        <div class="col-12 col-md-8 col-lg-6 border border-top-0  py-2">
                            <div>
                                <b>Показания:</b> {{ history.value.toLocaleString('ru-RU') }}{{ history.delta === null ? '' : ' (' + history.delta.toLocaleString('ru-RU') + 'кВт)' }}
                            </div>
                            <div class="mt-2">
                                <b>Дата:</b> {{ $formatDate(history.date) }}{{ history.days === null ? '' : ' (+' + history.days + ' дней)' }}
                            </div>
                            <div class="mt-2">
                                <b>Статус:</b> <b :class="history.isVerified ? 'text-success' : 'text-secondary'"> {{ history.isVerified ? 'Проверено' : 'Не проверено' }}</b>
                            </div>
                            <div class="mt-2"
                                 v-if="history.transaction">
                                <b>Оплачено:</b> {{ $formatMoney(history.transaction.payed) }}/{{ $formatMoney(history.transaction.cost) }} по тарифу {{ $formatMoney(history.transaction.tariff) }}
                            </div>
                            <div class="mt-2">
                                <file-item :file="history.file"
                                           v-if="history.file"
                                           :edit="false"
                                />
                            </div>
                        </div>
                    </div>
                </template>
            </template>
        </template>
    </div>
    <div class="row justify-content-center">
        <div class="col-12 col-md-8 col-lg-6 border bg-light d-flex justify-content-between align-items-center p-2">
            <button class="btn btn-success me-2"
                    v-if="showAddCounterButton"
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
                    <custom-input v-model="number"
                                  :errors="errors.number"
                                  :type="'text'"
                                  :label="'Серийный номер устройства'"
                                  :required="true"
                    />
                </div>
                <div class="mt-2">
                    <custom-input v-model="value"
                                  :errors="errors.value"
                                  :type="'number'"
                                  :label="'Текущие показания на счётчике'"
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
    name      : 'ProfileCountersBlock',
    components: {
        SearchSelect, FileItem, ViewDialog,
        CustomCheckbox,
        CustomInput,
        Wrapper,

    },
    mixins    : [
        ResponseError,
    ],
    data () {
        return {
            showDialog: false,
            hideDialog: false,

            id    : null,
            number: null,
            value : null,

            counters: null,
            file    : null,

            mode            : null,
            currentCounterId: null,
        };
    },
    created () {
        this.listAction();
    },
    methods : {
        listAction () {
            window.axios[Url.Routes.profileCounterList.method](Url.Routes.profileCounterList.uri).then(response => {
                this.counters         = response.data.counters;
                if (this.id !== null) {
                    this.currentCounterId = this.id;
                }
                else {
                    this.currentCounterId = this.counters.length > 0 ? this.counters[0].id : null;
                }
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
            if (!confirm('Номер счётчика невозможно будет изменить. Продолжить?')) {
                return;
            }
            let form = new FormData();
            form.append('number', this.number);
            form.append('value', this.value);
            form.append('file', this.file);

            window.axios[Url.Routes.profileCounterCreate.method](Url.Routes.profileCounterCreate.uri, form).then(response => {
                this.onSuccessSubmit();
            }).catch(response => {
                this.parseResponseErrors(response);
            });
        },
        addHistoryValueAction () {
            let form = new FormData();
            form.append('counter_id', this.id);
            form.append('value', this.value);
            form.append('file', this.file);

            window.axios[Url.Routes.profileCounterAddValue.method](Url.Routes.profileCounterAddValue.uri, form).then(response => {
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
            this.listAction();
        },
        addCounter () {
            this.mode       = 1;
            this.showDialog = true;
        },
        addHistoryValue (item) {
            this.value = item.value;

            this.mode       = 2;
            this.showDialog = true;
            this.id         = item.id;
        },
        canAddNewHistory (item) {
            const lastHistory = item.history[0];
            if (lastHistory) {
                return lastHistory.actions.create;
            }
            return true;
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
        toggleCounterBlock (id) {
            this.currentCounterId = this.currentCounterId === id ? null : id;
        },
    },
    computed: {
        Url () {
            return Url;
        },
        showAddCounterButton () {
            return !this.counters || !this.counters.length || this.counters.length <= 2;
        },
        canSubmitAction () {
            if (this.mode === 1) {
                return this.number && this.value && this.file;
            }
            return this.value && this.file;
        },
    },
};
</script>