<template>
    <div>
        <table class="table table-sm table-bordered">
            <tbody>
            <tr>
                <th class="text-center">№</th>
                <th class="text-center">Название</th>
                <th class="text-center">Сумма</th>
                <th class="text-center">Файлы</th>
                <th class="text-center">Создан</th>
                <th></th>
            </tr>
            <tr v-for="(payment, index) in payments">
                <td class="text-end">{{ payment.id }}</td>
                <td>{{ payment.name }}</td>
                <td class="text-end">{{ $formatMoney(payment.cost) }}</td>
                <td>
                    <div v-if="payment.files && payment.files.length">
                        <template v-for="(file, index) in payment.files">
                            <file-item
                                :file="file"
                                :edit="true"
                                :index="index"
                                :use-up-sort="index!==0"
                                :use-down-sort="index!==payment.files.length-1"
                            />
                        </template>
                    </div>
                </td>
                <td class="text-center">{{ payment.created }}</td>
                <td>
                    <div class="d-flex justify-content-center">
                        <history-btn
                            class="btn-link underline-none"
                            :url="payment.historyUrl" />
                        <div class="dropdown"
                             v-if="actions.edit || actions.view || actions.drop">
                            <a class="btn btn-sm btn-light border"
                               href="#"
                               role="button"
                               :id="'dropDown'+index+vueId"
                               data-bs-toggle="dropdown"
                               aria-expanded="false">
                                <i class="fa fa-bars"></i>
                            </a>
                            <ul class="dropdown-menu"
                                :aria-labelledby="'dropDown'+vueId">
                                <li v-if="actions.edit">
                                    <a class="dropdown-item cursor-pointer"
                                       @click="editAction(payment.id)"><i class="fa fa-edit"></i> Редактировать</a>
                                </li>
                                <li v-else-if="actions.view">
                                    <a class="dropdown-item cursor-pointer"
                                       @click="editAction(payment.id)"><i class="fa fa-eye"></i> Просмотр</a>
                                </li>
                                <li v-if="actions.drop">
                                    <a class="dropdown-item cursor-pointer text-danger"
                                       @click="dropAction(payment.id)"><i class="fa fa-trash"></i> Удалить</a>
                                </li>
                            </ul>
                        </div>
                    </div>
                </td>
            </tr>
            </tbody>
        </table>
    </div>
</template>

<script>
import ResponseError from '../../../../mixin/ResponseError.js';
import Url           from '../../../../utils/Url.js';
import HistoryBtn    from '../../../common/HistoryBtn.vue';
import FileItem      from '../../../common/files/FileItem.vue';

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

            vueId: null,
        };
    },
    created () {
        this.vueId = 'uuid' + this.$_uid;
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
