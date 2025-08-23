<template>
    <div>
        <h5>Счётчики</h5>
        <table class="table align-middle m-0 text-center"
               v-if="counters && counters.length">
            <thead>
            <tr>
                <th>Номер</th>
                <th>Показание</th>
                <th>Дата</th>
                <th>Счета</th>
                <th>Авто показания</th>
                <th></th>
            </tr>
            </thead>
            <tbody>
            <template v-for="counter in counters">
                <tr>
                    <td><a :href="counter.viewUrl">{{ counter.number }}</a></td>
                    <td>{{ counter.value }}</td>
                    <td>{{ counter.date }}</td>
                    <td>{{ counter.isInvoicing ? 'да' : 'нет' }}</td>
                    <td>{{ counter.increment ? counter.increment : '-' }}</td>
                    <td>
                        <div class="dropdown">
                            <a class="btn btn-sm btn-light border"
                               href="#"
                               role="button"
                               :id="'dropDown'+counter.id+vueId"
                               data-bs-toggle="dropdown"
                               aria-expanded="false">
                                <i class="fa fa-bars"></i>
                            </a>
                            <ul class="dropdown-menu"
                                :aria-labelledby="'dropDown'+counter.id+vueId">
                                <li>
                                    <a class="dropdown-item cursor-pointer"
                                       @click.prevent="editCounterAction(counter)">
                                        <i class="fa fa-edit"></i> Редактировать
                                    </a>
                                </li>
                                <li v-if="counter.actions.drop">
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
                    </td>
                </tr>
            </template>
            </tbody>
        </table>
    </div>
    <div class="d-flex align-items-center justify-content-between mt-2">
        <div class="d-flex">
            <button class="btn btn-success me-2"
                    v-if="this.account.actions.counters.edit && !period?.isClosed"
                    @click="addCounterAction">Добавить счётчик
            </button>
        </div>
    </div>
    <counter-item
        :account="account"
        :period="period"
        :counter="selectedCounter"
        :show-form="showCounterForm"
        @counter-updated="onUpdateCounter"
    />
</template>

<script>
import Url                   from '../../../../utils/Url.js';
import ResponseError         from '../../../../mixin/ResponseError.js';
import Wrapper               from '../../../common/Wrapper.vue';
import CustomInput           from '../../../common/form/CustomInput.vue';
import CustomCheckbox        from '../../../common/form/CustomCheckbox.vue';
import ViewDialog            from '../../../common/ViewDialog.vue';
import FileItem              from '../../../common/files/FileItem.vue';
import SearchSelect          from '../../../common/form/SearchSelect.vue';
import CounterItem           from './CounterItem.vue';
import HistoryBtn            from '../../../common/HistoryBtn.vue';
import CounterItemChartBlock from './CounterItemChartBlock.vue';

export default {
    components: {
        CounterItemChartBlock,
        HistoryBtn,
        CounterItem,
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

            selectedCounter: null,
            showCounterForm: null,

            period: null,
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
                this.period   = response.data.period;
            }).catch(response => {
                this.parseResponseErrors(response);
            });
        },
        addCounterAction () {
            this.selectedCounter = {};
            this.showCounterForm = true;
        },
        editCounterAction (counter) {
            this.selectedCounter = counter;
            this.showCounterForm = true;
        },
        dropCounterAction (counter) {
            if (!confirm('Удалить счётчик?')) {
                return;
            }

            let uri = Url.Generator.makeUri(Url.Routes.adminCounterDelete, {
                accountId: this.account.id,
                counterId: counter.id,
            });

            window.axios[Url.Routes.adminCounterDelete.method](
                uri,
            ).then((response) => {
                if (response.data) {
                    this.listAction();
                    this.showInfo('Счётчик удалён');
                }
                else {
                    this.showDanger('Счётчик не удалён');
                }
            }).catch(response => {
                this.parseResponseErrors(response);
            });
        },
        onUpdateCounter () {
            this.listAction();
            this.showCounterForm = false;
        },
    },
};
</script>