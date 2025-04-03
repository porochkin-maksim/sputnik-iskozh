<template>
    <div>
        <h5>Счётчики</h5>
        <table class="table table-bordered align-middle m-0 w-auto"
               v-if="counters && counters.length">
            <tbody>
            <template v-for="counter in counters">
                <tr class="table-secondary">
                    <th colspan="6"
                        class="text-center border-end-0">
                        <a href=""
                           v-if="this.account.actions.counters.edit"
                           class="text-decoration-none"
                           @click.prevent="editCounterAction(counter)">
                            <span v-if="counter.isInvoicing">
                                <i class="fa fa-file-text text-success"></i>
                            </span>
                            <span v-else>
                                <i class="fa fa-file-text-o text-secondary"></i>
                            </span>
                            &nbsp;Счётчик "{{ counter.number }}"
                        </a>
                        <div v-else>
                            <span v-if="counter.isInvoicing">
                                <i class="fa fa-file-text text-success"></i>
                            </span>
                            <span v-else>
                                <i class="fa fa-file-text-o text-secondary"></i>
                            </span>
                            &nbsp;Счётчик "{{ counter.number }}"
                        </div>
                    </th>
                    <th class="text-end border-start-0"
                        colspan="2">
                        <button class="btn btn-sm btn-success"
                                v-if="this.account.actions.counters.edit"
                                @click="addHistoryAction(counter)"
                        >Добавить показания
                        </button>
                    </th>
                </tr>
                <tr class="text-center align-middle">
                    <th>Дата фиксации</th>
                    <th>Показания (кВт)</th>
                    <th>Подтверждены</th>
                    <th>Тариф</th>
                    <th>Стоимость</th>
                    <th>Оплачено</th>
                    <th>Фото</th>
                    <th>
                        <div class="dropdown">
                            <a class="btn btn-sm btn-light border"
                               href="#"
                               role="button"
                               :id="'dropDownCounter'+counter.id+vueId"
                               data-bs-toggle="dropdown"
                               aria-expanded="false">
                                <i class="fa fa-bars"></i>
                            </a>
                            <ul class="dropdown-menu"
                                :aria-labelledby="'dropDownCounter'+counter.id+vueId">
                                <li v-if="this.account.actions.counters.drop">
                                    <a class="dropdown-item cursor-pointer text-danger"
                                       @click="dropCounterAction(counter)"><i class="fa fa-trash"></i> Удалить</a>
                                </li>
                                <li>
                                    <history-btn
                                        class="dropdown-item btn btn-link text-decoration-none"
                                        :url="counter.historyUrl" />
                                </li>
                            </ul>
                        </div>
                    </th>
                </tr>
                <tr v-for="history in counter.history.histories">
                    <td class="text-center">
                        <div>{{ $formatDate(history.date) }}</div>
                        <div>{{ history.days === null ? '' : '+' + history.days + ' дней' }}</div>
                    </td>
                    <td class="text-end">
                        <div>{{ history.value?.toLocaleString('ru-RU') }}</div>
                        <div>{{ history.delta === null ? '' : '+' + history.delta?.toLocaleString('ru-RU') + 'кВт' }}</div>
                    </td>
                    <td class="text-center">
                        <span v-if="history.isVerified"><i class="fa fa-check text-success"></i></span>
                        <span v-else><i class="fa fa-close text-secondary"></i></span>
                    </td>
                    <template v-if="history.transaction">
                        <template v-if="history.invoiceUrl">
                            <td class="text-end"><a :href="history.invoiceUrl" class="text-decoration-none">{{ $formatMoney(history.transaction.tariff) }}</a></td>
                            <td class="text-end"><a :href="history.invoiceUrl" class="text-decoration-none">{{ $formatMoney(history.transaction.cost) }}</a></td>
                            <td class="text-end"><a :href="history.invoiceUrl" class="text-decoration-none">{{ $formatMoney(history.transaction.payed) }}</a></td>
                        </template>
                        <template v-else>
                            <td class="text-end">{{ $formatMoney(history.transaction.tariff) }}</td>
                            <td class="text-end">{{ $formatMoney(history.transaction.cost) }}</td>
                            <td class="text-end">{{ $formatMoney(history.transaction.payed) }}</td>
                        </template>
                    </template>
                    <template v-else>
                        <template v-if="history.delta && this.account.actions.counters.edit && counter.isInvoicing">
                            <td class="text-center"
                                colspan="3">
                                <button class="btn btn-sm btn-success"
                                        v-if="this.account.actions.counters.edit && counter.isInvoicing"
                                        @click="addTransactionForHistory(history)">
                                    Добавить транзакцию
                                </button>
                            </td>
                        </template>
                        <template v-else>
                            <td class="text-center"
                                colspan="3">{{ counter.isInvoicing ? '' : 'Не выставляется' }}
                            </td>
                        </template>
                    </template>
                    <td>
                        <file-item :file="history.file"
                                   v-if="history.file"
                                   :edit="false"
                        />
                    </td>
                    <td class="text-center">
                        <div class="dropdown">
                            <a class="btn btn-sm btn-light border"
                               href="#"
                               role="button"
                               :id="'dropDownHistory'+history.id+vueId"
                               data-bs-toggle="dropdown"
                               aria-expanded="false">
                                <i class="fa fa-bars"></i>
                            </a>
                            <ul class="dropdown-menu"
                                :aria-labelledby="'dropDownHistory'+history.id+vueId">
                                <li v-if="this.account.actions.counters.edit">
                                    <a class="dropdown-item cursor-pointer"
                                       @click="editHistoryAction(history)"><i class="fa fa-edit"></i> Редактировать</a>
                                </li>
                                <li v-if="this.account.actions.counters.drop">
                                    <a class="dropdown-item cursor-pointer text-danger"
                                       @click="dropHistoryAction(history)"><i class="fa fa-trash"></i> Удалить</a>
                                </li>
                                <li>
                                    <history-btn
                                        class="dropdown-item btn btn-link text-decoration-none"
                                        :url="history.historyUrl" />
                                </li>
                            </ul>
                        </div>
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
                    @click="addCounterAction">Добавить счётчик
            </button>
        </div>
    </div>
    <counter-item
        v-if="selectedCounter && mode===1"
        :account="account"
        :counter="selectedCounter"
        @counter-updated="onUpdateCounter"
    />
    <counter-history-item
        v-if="selectedCounter && mode===2"
        :counter="selectedCounter"
        :history="selectedHistory"
        @history-updated="onUpdateHistory"
    />
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
import CounterItem        from './CounterItem.vue';
import HistoryBtn         from '../../../common/HistoryBtn.vue';

export default {
    components: {
        HistoryBtn,
        CounterItem,
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
    },
    mixins    : [
        ResponseError,
    ],
    data () {
        return {
            vueId: null,

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

            selectedCounter: null,
            selectedHistory: null,
        };
    },
    created () {
        this.vueId = 'uuid' + this.$_uid;
        this.listAction();
    },
    methods: {
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
        addCounterAction () {
            this.mode            = 1;
            this.selectedCounter = {};
        },
        editCounterAction (counter) {
            this.mode            = 1;
            this.selectedCounter = counter;
        },
        onSuccessSubmit () {
            this.listAction();
        },
        editHistoryAction (history) {
            this.mode            = 2;
            this.selectedCounter = this.getCounter(history.counterId);
            this.selectedHistory = history;
        },
        addHistoryAction (counter) {
            this.mode            = 2;
            this.selectedCounter = counter;
            this.selectedHistory = null;
        },
        addTransactionForHistory (history) {
            if (!confirm('Добавить транзакцию?')) {
                return;
            }

            let uri = Url.Generator.makeUri(Url.Routes.adminCounterHistoryCreateTransaction, {
                historyId: history.id,
            });
            window.axios[Url.Routes.adminCounterHistoryCreateTransaction.method](uri).then(response => {
                this.onSuccessSubmit();
                this.showInfo('Транзакция создана');
            }).catch(response => {
                this.parseResponseErrors(response);
            });
        },
        dropCounterAction (counter) {
            const reason = prompt('Укажите причну удаления');

            if (!reason) {
                return;
            }

            let form = new FormData();
            form.append('comment', reason);

            let uri = Url.Generator.makeUri(Url.Routes.adminCounterDelete, {
                accountId: this.account.id,
                counterId: counter.id,
            });

            window.axios[Url.Routes.adminCounterDelete.method](uri, form).then(response => {
                this.onSuccessSubmit();
                this.showInfo('Счётчик удалён');
            }).catch(response => {
                this.parseResponseErrors(response);
            });
        },
        dropHistoryAction (history) {
            if (!confirm('Удалить показания?')) {
                return;
            }
            let uri = Url.Generator.makeUri(Url.Routes.adminCounterHistoryDelete, {
                historyId: history.id,
            });
            window.axios[Url.Routes.adminCounterHistoryDelete.method](uri).then(response => {
                this.onSuccessSubmit();
                this.showInfo('Показания удалены');
            }).catch(response => {
                this.parseResponseErrors(response);
            });
        },
        onUpdateHistory () {
            this.selectedCounter = null;
            this.selectedHistory = null;
            this.listAction();
        },
        onUpdateCounter () {
            this.selectedCounter = null;
            this.selectedHistory = null;
            this.listAction();
        },
        getCounter (id) {
            return this.counters.find(item => item.id === id);
        },
    },
};
</script>