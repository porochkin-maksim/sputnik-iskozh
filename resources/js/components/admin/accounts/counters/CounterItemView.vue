<template>
    <div>
        <div class="d-flex mb-2">
            <button class="btn btn-sm btn-success ms-2"
                    v-if="actions.edit"
                    @click="addHistoryAction(counter)"
                    :disabled="loading"
            >Добавить показания
            </button>
        </div>
        <table class="table table-sm text-center align-middle">
            <thead>
            <tr>
                <th>#</th>
                <th>Дата</th>
                <th>Показания</th>
                <th>Дней</th>
                <th>Дельта</th>
                <th>Статус</th>
                <th>Файл</th>
                <th>Оплачено</th>
                <th>Стоимость</th>
                <th>Тариф</th>
                <th></th>
            </tr>
            </thead>
            <tbody>
            <tr v-for="(history) in histories">
                <td>
                    {{ history.id }}
                </td>
                <td>
                    {{ $formatDate(history.date) }}
                </td>
                <td>
                    {{ history.value }}
                </td>
                <td>
                    {{ history.days === null ? '' : history.days }}
                </td>
                <td>
                    {{ history.delta === null ? '' : history.delta }}
                </td>
                <td>
                    <b :class="history.isVerified ? 'text-success' : 'text-secondary'">&nbsp;{{ history.isVerified ? 'Подтверждено' : 'Не подтверждено' }}</b>
                </td>
                <td>
                    <template v-if="history.file">
                        <file-item :file="history.file"
                                   v-if="history.file"
                                   :edit="false"
                        />
                    </template>
                </td>
                <template v-if="history.claim">
                    <template v-if="history.invoiceUrl">
                        <td>
                            <a :href="history.invoiceUrl"
                               class="text-decoration-none">{{ $formatMoney(history.claim.tariff) }}</a>
                        </td>
                        <td>
                            <a :href="history.invoiceUrl"
                               class="text-decoration-none">{{ $formatMoney(history.claim.cost) }}</a>
                        </td>
                        <td>
                            <a :href="history.invoiceUrl"
                               class="text-decoration-none">{{ $formatMoney(history.claim.payed) }}</a>
                        </td>
                    </template>
                    <template v-else>
                        <td>{{ $formatMoney(history.claim.tariff) }}</td>
                        <td>{{ $formatMoney(history.claim.cost) }}</td>
                        <td>{{ $formatMoney(history.claim.payed) }}</td>
                    </template>
                </template>
                <template v-else-if="history.delta && actions.edit && counter.isInvoicing">
                    <td colspan="3">
                        <button class="btn btn-sm btn-success"
                                @click="addClaimForHistory(history)">
                            Добавить услугу
                        </button>
                    </td>
                </template>
                <template v-else>
                    <td colspan="3"></td>
                </template>
                <td>
                    <div class="dropdown">
                        <a class="btn btn-sm btn-light border"
                           href="#"
                           role="button"
                           :id="'dropDownHistory'+history.id+vueId"
                           data-bs-toggle="dropdown"
                           :disabled="loading"
                           aria-expanded="false"
                        >
                            <i class="fa fa-bars"></i>
                        </a>
                        <ul class="dropdown-menu"
                            :aria-labelledby="'dropDownHistory'+history.id+vueId">
                            <li v-if="account.actions.counters.edit">
                                <a class="dropdown-item cursor-pointer"
                                   @click="editHistoryAction(history)"><i class="fa fa-edit"></i> Редактировать</a>
                            </li>
                            <li v-if="account.actions.counters.drop">
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
            <tr v-if="histories?.length !== 0 && histories?.length < total">
                <td colspan="9">
                    <div class="d-flex justify-content-center">
                        <button class="btn btn-link"
                                v-if="!loading"
                                @click="listAction">
                            Показать ещё
                        </button>
                        <button class="btn border-0"
                                disabled
                                v-else>
                            <i class="fa fa-spinner fa-spin"></i> Подгрузка
                        </button>
                    </div>
                </td>
            </tr>
            </tbody>
        </table>
    </div>
    <counter-history-item
        v-if="selectedHistory"
        :counter="counter"
        :history="selectedHistory"
        @history-updated="onUpdateHistory"
    />
</template>

<script>
import Url                from '../../../../utils/Url.js';
import ResponseError      from '../../../../mixin/ResponseError.js';
import FileItem           from '../../../common/files/FileItem.vue';
import CounterItem        from './CounterItem.vue';
import CounterHistoryItem from './CounterHistoryItem.vue';
import HistoryBtn         from '../../../common/HistoryBtn.vue';

export default {
    name      : 'CounterItemView',
    components: { HistoryBtn, CounterHistoryItem, CounterItem, FileItem },
    props     : {
        modelValue: Object,
    },
    mixins    : [
        ResponseError,
    ],
    data () {
        return {
            vueId   : null,
            limit   : 10,
            lastDate: null,
            total   : 0,
            loading : null,

            counter: null,
            account: null,
            actions: null,

            histories      : [],
            selectedHistory: null,
            selectedCounter: null,
        };
    },
    created () {
        this.vueId   = 'uuid' + this.$_uid;
        this.counter = this.modelValue;
        this.account = this.counter.account;
        this.actions = this.counter.actions;
        this.listAction();
    },
    methods: {
        listAction () {
            this.loading = true;

            let params = {
                limit: this.limit,
                skip : this.histories.length,
            };

            Url.RouteFunctions.adminCounterHistoryList(this.counter.id, params).then(response => {
                response.data.histories.histories?.forEach(history => {
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

                this.total    = this.total ? this.total : response.data.total;
                this.lastDate = this.histories.length > 0 ? this.histories[this.histories.length - 1].date : null;
            }).catch(response => {
                this.parseResponseErrors(response);
            }).then(() => {
                this.loading = false;
            });
        },
        addHistoryAction () {
            this.selectedHistory = {};
        },
        editHistoryAction (history) {
            this.mode            = 2;
            this.selectedHistory = history;
        },
        dropHistoryAction (history) {
            if (!confirm('Удалить показания?')) {
                return;
            }

            Url.RouteFunctions.adminRequestsCounterHistoryDelete(this.counter.id).then(response => {
                if (response.data) {
                    this.listAction();
                    this.showInfo('Показания удалены');
                }
                else {
                    this.showDanger('Показания не удалены');
                }
            }).catch(response => {
                this.parseResponseErrors(response);
            });
        },
        addClaimForHistory (history) {
            Url.RouteFunctions.adminRequestsCounterHistoryCreateClaim(history.id).then(response => {
                if (response.data) {
                    this.listAction();
                    this.showInfo('Услуга создана');
                }
                else {
                    this.showDanger('Услуга не создана');
                }
            }).catch(response => {
                this.parseResponseErrors(response);
            });
        },
        onUpdateHistory () {
            this.histories = [];
            this.listAction();
            this.selectedHistory = null;
        },
    },
};
</script>