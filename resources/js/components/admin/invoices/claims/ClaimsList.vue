<template>
    <div>
        <table class="table table-sm table-bordered">
            <tbody>
            <tr>
                <th class="text-center">№</th>
                <th class="text-center">Услуга</th>
                <th class="text-center">Тариф</th>
                <th class="text-center">Стоимость</th>
                <th class="text-center">Оплачено</th>
                <th class="text-center">Долг</th>
                <th class="text-center">Создана</th>
                <th></th>
            </tr>
            <tr v-for="(claim, index) in claims">
                <td class="text-end">{{ claim.id }}</td>
                <td>{{ claim.service }}</td>
                <td class="text-end">{{ $formatMoney(claim.tariff) }}</td>
                <td class="text-end">{{ $formatMoney(claim.cost) }}</td>
                <td class="text-end">{{ $formatMoney(claim.payed) }}</td>
                <td class="text-end">{{ $formatMoney(claim.delta) }}</td>
                <td class="text-center">{{ claim.created }}</td>
                <td>
                    <div class="d-flex justify-content-center">
                        <history-btn
                            class="btn-link underline-none"
                            :url="claim.historyUrl" />
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
                                       @click="editAction(claim.id)"><i class="fa fa-edit"></i> Редактировать</a>
                                </li>
                                <li v-else-if="actions.view">
                                    <a class="dropdown-item cursor-pointer"
                                       @click="editAction(claim.id)"><i class="fa fa-eye"></i> Просмотр</a>
                                </li>
                                <li v-if="actions.drop">
                                    <a class="dropdown-item cursor-pointer text-danger"
                                       @click="dropAction(claim.id)"><i class="fa fa-trash"></i> Удалить</a>
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
            claims : [],
            actions: {},
            vueId  : null,
        };
    },
    created () {
        this.vueId = 'uuid' + this.$_uid;
        this.listAction();
    },
    methods: {
        listAction () {
            let uri = Url.Generator.makeUri(Url.Routes.adminClaimList, {
                invoiceId: this.invoiceId,
            });

            window.axios[Url.Routes.adminClaimList.method](uri).then(response => {
                this.actions  = response.data.actions;
                this.claims   = response.data.claims;
                this.types    = response.data.types;
                this.periods  = response.data.periods;
                this.accounts = response.data.accounts;

                this.$emit('update:count', this.claims.length);
            }).catch(response => {
                this.parseResponseErrors(response);
            });
        },
        editAction (id) {
            this.$emit('update:selectedId', id);
        },
        dropAction (id) {
            if (!confirm('Удалить услугу?')) {
                return;
            }
            let uri = Url.Generator.makeUri(Url.Routes.adminClaimDelete, {
                invoiceId: this.invoiceId,
                id       : id,
            });

            window.axios[Url.Routes.adminAccountDelete.method](
                uri,
            ).then((response) => {
                if (response.data) {
                    this.listAction();
                    this.showInfo('Услуга удалена');
                }
                else {
                    this.showDanger('Услуга не удалена');
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