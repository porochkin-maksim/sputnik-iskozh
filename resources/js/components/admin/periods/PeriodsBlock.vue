<template>
    <div class="d-flex align-items-center justify-content-between mb-2">
        <div>
            <button class="btn btn-success"
                    v-if="actions.edit"
                    @click="showCreateDialog">
                <i class="fa fa-plus"></i> Добавить период
            </button>
        </div>
        <history-btn
            class="btn-link underline-none"
            :url="historyUrl" />
    </div>

    <div class="table-responsive">
        <table class="table table-sm table-hover align-middle">
            <thead>
            <tr>
                <th>ID</th>
                <th>Название</th>
                <th>Начало</th>
                <th>Окончание</th>
                <th>Статус</th>
                <th class="text-center">Действия</th>
            </tr>
            </thead>
            <tbody>
            <tr v-for="(period, index) in periods"
                :key="period.id">
                <td>{{ period.id }}</td>
                <td>{{ period.name }}</td>
                <td>{{ $formatDate(period.startAt) }}</td>
                <td>{{ $formatDate(period.endAt) }}</td>
                <td>
                    <span v-if="period.isClosed"
                          class="badge bg-primary">
                        <i class="fa fa-check"></i> Закрыт
                    </span>
                    <span v-else
                          class="badge bg-success">
                        <i class="fa fa-clock-o"></i> Активен
                    </span>
                </td>
                <td>
                    <div class="d-flex justify-content-center">
                        <history-btn
                            class="btn-link underline-none"
                            :url="period.historyUrl" />
                        <div class="dropdown">
                            <a class="btn btn-sm btn-light border"
                               href="#"
                               role="button"
                               :id="'dropDown'+index+vueId"
                               data-bs-toggle="dropdown"
                               :class="{'disabled opacity-50': !(actions.edit && actions.drop) || period.isClosed}"
                               aria-expanded="false">
                                <i class="fa fa-bars"></i>
                            </a>
                            <ul class="dropdown-menu"
                                :aria-labelledby="'dropDown'+index+vueId">
                                <li v-if="actions.edit && !period.isClosed">
                                    <a class="dropdown-item cursor-pointer"
                                       @click="showEditDialog(period)">
                                        <i class="fa fa-edit"></i> Редактировать
                                    </a>
                                </li>
                                <li v-if="actions.drop && !period.isClosed">
                                    <a class="dropdown-item cursor-pointer text-danger"
                                       @click="deleteAction(period)">
                                        <i class="fa fa-trash"></i> Удалить
                                    </a>
                                </li>
                            </ul>
                        </div>
                    </div>
                </td>
            </tr>
            </tbody>
        </table>
    </div>

    <period-edit-dialog
        v-model:model-value="selectedPeriod"
        v-model:show="showDialog"
        @update:model-value="onPeriodUpdated" />
</template>

<script>
import ResponseError    from '../../../mixin/ResponseError.js';
import Url              from '../../../utils/Url.js';
import HistoryBtn       from '../../common/HistoryBtn.vue';
import PeriodEditDialog from './PeriodEditDialog.vue';

export default {
    name      : 'PeriodsBlock',
    components: {
        HistoryBtn,
        PeriodEditDialog,
    },
    mixins    : [ResponseError],
    data () {
        return {
            periods       : [],
            historyUrl    : null,
            actions       : {},
            selectedPeriod: null,
            showDialog    : false,
            vueId         : null,
        };
    },
    created () {
        this.vueId = 'uuid' + this.$_uid;
        this.listAction();
    },
    methods: {
        listAction () {
            window.axios[Url.Routes.adminPeriodList.method](Url.Routes.adminPeriodList.uri)
                .then(response => {
                    this.periods    = response.data.periods || [];
                    this.actions    = response.data.actions;
                    this.historyUrl = response.data.historyUrl;
                })
                .catch(response => {
                    this.parseResponseErrors(response);
                });
        },
        showCreateDialog () {
            window.axios[Url.Routes.adminPeriodCreate.method](Url.Routes.adminPeriodCreate.uri).then(response => {
                this.selectedPeriod = response.data;
                this.showDialog     = true;
            }).catch(response => {
                this.parseResponseErrors(response);
            });
        },
        showEditDialog (period) {
            if (!this.actions.edit || period.isClosed) {
                return;
            }
            this.selectedPeriod = period;
            this.showDialog     = true;
        },
        onPeriodUpdated () {
            this.listAction();
        },
        deleteAction (period) {
            if (!confirm('Удалить период?')) {
                return;
            }

            window.axios[Url.Routes.adminPeriodDelete.method](
                Url.Routes.adminPeriodDelete.uri + '/' + period.id,
            ).then(() => {
                this.periods = this.periods.filter(p => p.id !== period.id);
                this.showInfo('Период удален');
            }).catch(response => {
                this.parseResponseErrors(response);
            });
        },
    },
};
</script>

<style scoped>
.cursor-pointer {
    cursor : pointer;
}

.dropdown-item {
    cursor : pointer;
}

.disabled {
    pointer-events : none;
}
</style>