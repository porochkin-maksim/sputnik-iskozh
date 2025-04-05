<template>
    <div class="row">
        <div class="col-12 col-md-8 col-lg-6">
            <div class="border bg-light d-flex justify-content-between align-items-center p-2">
                <div>
                    <b>Счётчик {{ counter.number }}</b>
                </div>
                <div>
                    <button class="btn btn-sm btn-success"
                            v-if="canAddNewHistory"
                            @click="addHistoryValue"
                    >Добавить показания
                    </button>
                </div>
            </div>
            <template v-for="history in histories">
                <div class="border border-top-0 p-2">
                    <div>
                        <b>Показания:</b> {{ history.value.toLocaleString('ru-RU') }}{{ history.delta === null ? '' : ' (' + history.delta.toLocaleString('ru-RU') + 'кВт)' }}
                    </div>
                    <div class="mt-1">
                        <b>Дата:</b> {{ $formatDate(history.date) }}{{ history.days === null ? '' : ' (+' + history.days + ' дней)' }}
                    </div>
                    <div class="mt-1">
                        <b>Статус:</b>
                        <b :class="history.isVerified ? 'text-success' : 'text-secondary'">&nbsp;{{ history.isVerified ? 'Проверено' : 'Не проверено' }}</b>
                    </div>
                    <div class="mt-1"
                         v-if="history.claim">
                        <b>Оплачено:</b> {{ $formatMoney(history.claim.payed) }}/{{ $formatMoney(history.claim.cost) }} по тарифу {{ $formatMoney(history.claim.tariff) }}
                    </div>
                    <div class="mt-1">
                        <file-item :file="history.file"
                                   v-if="history.file"
                                   :edit="false"
                        />
                    </div>
                </div>
            </template>
            <template v-if="canLoadMore">
                <div class="d-flex justify-content-center border border-top-0 p-2">
                    <button class="btn btn-link"
                            v-if="!pending"
                            @click="loadMore">
                        Показать ещё
                    </button>
                    <button class="btn border-0" disabled v-else>
                        <i class="fa fa-spinner fa-spin"></i> Подгрузка
                    </button>
                </div>
            </template>
        </div>
    </div>
    <view-dialog v-model:show="showDialog"
                 v-model:hide="hideDialog"
                 @hidden="closeAction"
    >
        <template v-slot:title>Внесение показаний счётчика</template>
        <template v-slot:body>
            <div class="container-fluid">
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
                    v-if="!pending"
                    @click="submitAction"
                    :disabled="!canSubmitAction">
                Добавить
            </button>
            <button class="btn border-0" disabled v-else>
                <i class="fa fa-spinner fa-spin"></i> Добавление
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
    name      : 'ProfileCounterItem',
    components: {
        SearchSelect, FileItem, ViewDialog,
        CustomCheckbox,
        CustomInput,
        Wrapper,
    },
    props     : [
        'counter',
    ],
    mixins    : [
        ResponseError,
    ],
    data () {
        return {
            loaded : false,
            pending: false,

            showDialog: false,
            hideDialog: false,

            id   : null,
            value: null,

            histories: [],
            file     : null,

            mode            : null,
            currentCounterId: null,

            skip : 0,
            total: null,
            limit: 0,
        };
    },
    created () {
        this.listAction();
    },
    methods : {
        loadMore () {
            this.listAction();
        },
        listAction () {
            this.pending = true;
            window.axios[Url.Routes.profileCounterHistoryList.method](Url.Routes.profileCounterHistoryList.uri, {
                counter_id: this.counter.id,
                skip      : this.skip,
            }).then(response => {
                response.data.histories?.forEach(history => {
                    let exists = false;
                    this.histories.forEach(item => {
                        if (item.id === history.id) {
                            exists = true;
                        }
                    });
                    if (!exists) {
                        this.histories.push(history);
                    }
                });
                this.total = response.data.total;
                this.limit = response.data.limit;
                this.skip += this.limit;
            }).catch(response => {
                this.parseResponseErrors(response);
            }).then(() => {
                this.loaded  = true;
                this.pending = false;
            });
        },
        submitAction () {
            if (this.pending) {
                return;
            }
            this.addHistoryValueAction();
        },
        addHistoryValueAction () {
            this.pending = true;
            let form = new FormData();
            form.append('counter_id', this.counter.id);
            form.append('value', this.value);
            form.append('file', this.file);

            window.axios[Url.Routes.profileCounterAddValue.method](Url.Routes.profileCounterAddValue.uri, form).then(response => {
                this.onSuccessSubmit();
            }).catch(response => {
                this.parseResponseErrors(response);
            }).then(() => {
                this.pending = false;
            });
        },
        onSuccessSubmit () {
            this.showDialog = false;
            this.hideDialog = true;
            this.file       = null;
            this.mode       = null;
            this.listAction();
        },
        addHistoryValue (item) {
            this.value      = item.value;
            this.showDialog = true;
            this.id         = item.id;
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
            return this.value && this.file;
        },
        canAddNewHistory () {
            if (!this.loaded || !this.histories) {
                return false;
            }
            const lastHistory = this.histories[0];
            if (lastHistory) {
                return lastHistory.actions.create;
            }
            return true;
        },
        canLoadMore () {
            return this.total && this.skip < this.total;
        },
    },
};
</script>