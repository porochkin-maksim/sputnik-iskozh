<template>
    <div class="row">
        <div class="col-12 col-md-8 col-lg-6">
            <div class="border bg-light d-flex justify-content-between align-items-center p-2">
                <div>
                    <b>Счётчик {{ counter.number }}</b>
                </div>
                <div class="d-flex flex-md-row flex-column">
                    <button v-if="canAddNewHistory"
                            class="btn btn-sm btn-success mb-md-0 mb-2"
                            @click="addHistoryValue"
                    >Добавить показания
                    </button>
                    <button v-else
                            class="btn btn-sm btn-success mb-md-0 mb-2 disabled"
                            data-bs-toggle="popover"
                            :data-bs-content="'Добавить показания можно будет в следующем месяце'"
                            data-bs-placement="bottom"
                    >Добавить показания
                    </button>
                    <button class="btn btn-sm btn-outline-success ms-2"
                            @click="editIncrement"
                    >Автоприращение
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
                    <button class="btn border-0"
                            disabled
                            v-else>
                        <i class="fa fa-spinner fa-spin"></i> Подгрузка
                    </button>
                </div>
            </template>
        </div>
    </div>
    <div class="row mt-2" v-if="chartData && chartData.length">
        <div class="col-12 col-md-8 col-lg-6">
            <h5 class="text-center">График показаний</h5>
            <counters-chart-block
                :chart-data="chartData"
                :options="chartOptions"
            />
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
            <button class="btn border-0"
                    disabled
                    v-else>
                <i class="fa fa-spinner fa-spin"></i> Добавление
            </button>
        </template>
    </view-dialog>
    <view-dialog v-model:show="showIncrementDialog"
                 v-model:hide="hideIncrementDialog"
                 @hidden="closeIncrementAction"
    >
        <template v-slot:title>Изменение автоприращения</template>
        <template v-slot:body>
            <div class="container-fluid">
                <div class="mt-2">
                    <custom-input v-model="increment"
                                  :errors="errors.increment"
                                  :type="'number'"
                                  :min="0"
                                  :step="1"
                                  :label="'Ежемесячное автоприращение показаний на кВт'"
                                  :required="true"
                                  @focusout="calculateIncrement"
                    />
                </div>
            </div>
        </template>
        <template v-slot:footer>
            <button class="btn btn-success"
                    v-if="!pending"
                    @click="saveIncrementAction"
                    :disabled="!canSubmitIncrementAction">
                Сохранить
            </button>
            <button class="btn border-0"
                    disabled
                    v-else>
                <i class="fa fa-spinner fa-spin"></i> Сохранение
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
import CountersChartBlock from '../../../common/blocks/CountersChartBlock.vue';

export default {
    name      : 'ProfileCounterItem',
    components: {
        CountersChartBlock,
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

            value: null,

            histories: [],
            file     : null,

            mode            : null,
            currentCounterId: null,

            skip : 0,
            total: null,
            limit: 0,

            showIncrementDialog: false,
            hideIncrementDialog: false,
            increment          : null,

            chartData   : [],
            chartOptions: {
                scales: {
                    y: {
                        beginAtZero: true,
                    },
                },
            },
        };
    },
    created () {
        this.increment = this.counter.increment ? this.counter.increment : 0;
        this.listAction();
    },
    methods : {
        loadMore () {
            this.listAction();
        },
        listAction () {
            this.pending = true;
            this.skip += this.limit;
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

                    this.chartData = this.histories
                        .filter(item => item.date && item.delta && !isNaN(parseFloat(item.delta)))
                        .map(item => ({
                            date : item.date,
                            value: parseFloat(item.delta),
                        }))
                        .sort((a, b) => new Date(a.date) - new Date(b.date));
                });
                this.total = response.data.total;
                this.limit = response.data.limit;
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
            let form     = new FormData();
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
            location.reload();
        },
        addHistoryValue () {
            if (!this.canAddNewHistory) {
                return;
            }
            const lastHistory = this.histories[0];
            if (lastHistory) {
                this.value = lastHistory.value + lastHistory.delta;
            }
            else {
                this.value = this.counter.value;
            }
            this.showDialog = true;
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
        editIncrement () {
            this.showIncrementDialog = true;
        },
        closeIncrementAction () {
            this.showIncrementDialog = false;
        },
        calculateIncrement () {
            this.increment = this.increment < 0 ? this.increment * -1 : this.increment;
        },
        saveIncrementAction () {
            if (this.pending) {
                return;
            }
            this.pending = true;
            let form     = new FormData();
            form.append('id', this.counter.id);
            form.append('increment', this.increment);

            window.axios[Url.Routes.profileCountersIncrementSave.method](Url.Routes.profileCountersIncrementSave.uri, form)
                .then(response => {
                    this.onIncrementSuccessSubmit();
                })
                .catch(response => {
                    this.parseResponseErrors(response);
                })
                .then(() => {
                    this.pending = false;
                });
        },
        onIncrementSuccessSubmit () {
            this.showSuccess('Данные сохранены');
            this.showIncrementDialog = false;
            this.hideIncrementDialog = true;
            this.$emit('increment-updated');
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
        canSubmitIncrementAction () {
            return this.increment !== null;
        },
    },
};
</script>