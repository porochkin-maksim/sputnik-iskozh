<template>
    <div>
        <table class="table table-bordered table-striped-columns table-striped ">
            <tr>
                <th>№</th>
                <th>Услуга</th>
                <th>Тариф</th>
                <th>Стоимость</th>
                <th>Оплачено</th>
                <th>Создана</th>
                <th></th>
            </tr>
            <tr v-for="(transaction) in transactions">
                <template v-if="!selectedId || selectedId !== transaction.id">
                    <td>{{ transaction.id }}</td>
                    <td>{{ transaction.service }}</td>
                    <td>{{ $formatMoney(transaction.tariff) }}</td>
                    <td>{{ $formatMoney(transaction.cost) }}</td>
                    <td>{{ $formatMoney(transaction.payed) }}</td>
                    <td>{{ transaction.created }}</td>
                    <td>
                        <button class="btn btn-sm border-0"
                                @click="editAction(transaction.id)">
                            <i class="fa fa-edit"></i>
                        </button>
                        <history-btn
                            class="btn-link underline-none"
                            :url="transaction.historyUrl" />
                        <button class="btn btn-sm border-0"
                                @click="dropAction(transaction.id)">
                            <i class="fa fa-trash text-danger"></i>
                        </button>
                    </td>
                </template>
                <template v-else>
                    <slot name="transaction" />
                </template>
            </tr>
            <slot name="transaction"
                  v-if="!selectedId" />
        </table>
    </div>
</template>

<script>
import ResponseError from '../../../../mixin/ResponseError.js';
import Url           from '../../../../utils/Url.js';
import HistoryBtn    from '../../../common/HistoryBtn.vue';

export default {
    components: { HistoryBtn },
    emits     : ['update:reload', 'update:selectedId', 'update:count'],
    mixins    : [
        ResponseError,
    ],
    props     : [
        'invoiceId',
        'selectedId',
        'reload',
        'count',
    ],
    data () {
        return {
            transactions: [],
        };
    },
    created () {
        this.listAction();
    },
    methods: {
        listAction () {
            let uri = Url.Generator.makeUri(Url.Routes.adminTransactionList, {
                invoiceId: this.invoiceId,
            });

            window.axios[Url.Routes.adminTransactionList.method](uri).then(response => {
                this.transactions = response.data.transactions;
                this.types        = response.data.types;
                this.periods      = response.data.periods;
                this.accounts     = response.data.accounts;

                this.$emit('update:count', this.transactions.length);
            }).catch(response => {
                this.parseResponseErrors(response);
            });
        },
        editAction (id) {
            this.$emit('update:selectedId', id);
        },
        dropAction (id) {
            if (!confirm('Удалить транзакцию?')) {
                return;
            }
            let uri = Url.Generator.makeUri(Url.Routes.adminTransactionDelete, {
                invoiceId: this.invoiceId,
                id       : id,
            });

            window.axios[Url.Routes.adminAccountDelete.method](
                uri,
            ).then((response) => {
                if (response.data) {
                    this.listAction();
                    this.showInfo('Транзакция удалена');
                }
                else {
                    this.showDanger('Транзакция не удалена');
                }
            }).catch(response => {
                this.parseResponseErrors(response);
            });
        },
    },
    watch  : {
        reload (value) {
            if (value === false) {
                return;
            }
            this.listAction();
            this.$emit('update:reload', false);
        },
    },
};
</script>
