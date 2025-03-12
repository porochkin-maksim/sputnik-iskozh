<template>
    <div>
        <table class="table table-bordered table-striped-columns table-striped ">
            <tr>
                <th>№</th>
                <th>Сумма</th>
                <th>Создан</th>
                <th></th>
            </tr>
            <tr v-for="(payment) in payments">
                <td>{{ payment.id }}</td>
                <td>{{ $formatMoney(payment.cost) }}</td>
                <td>{{ payment.created }}</td>
                <td>
                    <button class="btn btn-sm border-0"
                            v-if="actions.edit"
                            @click="editAction(payment.id)">
                        <i class="fa fa-edit"></i>
                    </button>
                    <history-btn
                        class="btn-link underline-none"
                        :url="payment.historyUrl" />
                    <button class="btn btn-sm border-0"
                            v-if="actions.drop"
                            @click="dropAction(payment.id)">
                        <i class="fa fa-trash text-danger"></i>
                    </button>
                </td>
            </tr>
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
            payments: [],
            actions : {},
        };
    },
    created () {
        this.listAction();
    },
    methods: {
        listAction () {
            let uri = Url.Generator.makeUri(Url.Routes.adminPaymentList, {
                invoiceId: this.invoiceId,
            });

            window.axios[Url.Routes.adminPaymentList.method](uri).then(response => {
                this.actions  = response.data.actions;
                this.payments = response.data.payments;
                this.$emit('update:count', this.payments.length);
            }).catch(response => {
                this.parseResponseErrors(response);
            });
        },
        editAction (id) {
            this.$emit('update:selectedId', id);
        },
        dropAction (id) {
            if (!confirm('Удалить платёж?')) {
                return;
            }
            let uri = Url.Generator.makeUri(Url.Routes.adminPaymentDelete, {
                invoiceId: this.invoiceId,
                id       : id,
            });

            window.axios[Url.Routes.adminAccountDelete.method](
                uri,
            ).then((response) => {
                if (response.data) {
                    this.listAction();
                    this.showInfo('Платёж удалён');
                }
                else {
                    this.showDanger('Платёж не удалён');
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
