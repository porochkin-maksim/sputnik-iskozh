<template>
    <div v-if="payments && payments.length">
        <table class="table table-bordered table-striped-columns align-middle table-striped ">
            <tr>
                <th>№</th>
                <th>Сумма</th>
                <th>Создан</th>
                <th>Файл</th>
                <th></th>
            </tr>
            <tr v-for="(payment) in payments">
                <td>{{ payment.id }}</td>
                <td>{{ $formatMoney(payment.cost) }}</td>
                <td>{{ payment.created }}</td>
                <td>
                    <template v-for="(file, index) in payment.files">
                        <file-item :file="file"
                                   :edit="true"
                                   :index="index"
                                   :use-up-sort="index!==0"
                                   :use-down-sort="index!==payment.files.length-1"
                        />
                    </template>
                </td>
                <td>
                    <button class="btn btn-sm border-0"
                            v-if="actions.edit"
                            @click="editAction(payment.id)">
                        <i class="fa fa-link"></i>&nbsp;Привязать
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
import ResponseError from '../../../mixin/ResponseError.js';
import Url           from '../../../utils/Url.js';
import HistoryBtn    from '../../common/HistoryBtn.vue';
import FileItem      from '../../common/files/FileItem.vue';

export default {
    components: { FileItem, HistoryBtn },
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
            window.axios[Url.Routes.adminNewPaymentList.method](Url.Routes.adminNewPaymentList.uri).then(response => {
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
            let uri = Url.Generator.makeUri(Url.Routes.adminNewPaymentDelete, {
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
