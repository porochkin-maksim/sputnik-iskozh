<template>
    <h5>Новые показания счётчиков</h5>
    <div class="d-flex align-items-center mb-2"
         v-if="actions.edit && histories.length">
        <button class="btn btn-success"
                :disabled="!canSubmitAction"
                @click="confirmAction"
        >
            Подтвердить выделенные
        </button>
    </div>
    <div>
        <table class="table table-bordered align-middle">
            <thead>
            <tr class="text-center">
                <th v-if="actions.edit && canCheckAction">
                    <div>
                        <input @change="onAllCheck"
                               v-model="allCheck"
                               type="checkbox"
                               class="form-check-input" />
                    </div>
                </th>
                <th>№</th>
                <th>Участок</th>
                <th>Счётчик</th>
                <th>Дата</th>
                <th>Показание</th>
                <th v-if="canCheckAction">Предыдущее</th>
                <th>Выставлять счета</th>
                <th>Файл</th>
                <th></th>
                <th v-if="actions.drop"></th>
            </tr>
            </thead>
            <tbody>
            <tr v-for="(history) in histories">
                <td v-if="actions.edit && canCheckAction">
                    <div>
                        <input @change="onChanged(history.id)"
                               :checked="isChecked(history.id)"
                               type="checkbox"
                               class="form-check-input" />
                    </div>
                </td>
                <td>{{ history.id }}</td>
                <template v-if="history.accountId && history.counterId">
                    <td v-if="history.accountUrl">
                        <a :href='history.accountUrl'>{{ history.accountNumber }}</a>
                    </td>
                    <td v-else>{{ history.accountNumber }}</td>
                    <td>{{ history.counterNumber }}</td>
                </template>
                <template v-else>
                    <td colspan="2"
                        class="text-center">
                        <button class="btn btn-sm border-0"
                                v-if="actions.edit"
                                @click="showLinkDialog(history.id)">
                            <i class="fa fa-link"></i>&nbsp;привязать
                        </button>
                    </td>
                </template>
                <td>{{ history.date }}</td>
                <td class="text-end">
                    {{ history.value }}
                    <template v-if="history.before">
                        <br>
                        +{{ history.value - history.before }}кВт
                    </template>

                </td>
                <td class="text-end" v-if="canCheckAction">{{ history.before }}</td>
                <td class="text-center">
                    <i v-if="history.isInvoicing"
                       class="fa fa-check text-success"></i>
                    <i v-else
                       class="fa fa-close text-danger"></i>
                </td>
                <td>
                    <div v-if="history.file">
                        <a :href="history.file.url"
                           class="text-decoration-none"
                           :data-lightbox="history.file.name"
                           :data-title="history.file.name"
                           target="_blank">{{ history.file.name }}</a>
                    </div>
                </td>
                <td>
                    <history-btn
                        class="btn-link underline-none"
                        :url="history.historyUrl" />
                </td>
                <td v-if="actions.drop">
                    <button class="btn btn-sm text-danger border-0"
                            v-if="actions.drop"
                            @click="dropAction(history.id)">
                        <i class="fa fa-trash"></i>
                    </button>
                </td>
            </tr>
            </tbody>
        </table>
    </div>
    <view-dialog v-model:show="showDialog"
                 v-model:hide="hideDialog"
                 @hidden="closeLinkDialog"
    >
        <template v-slot:title>Привязка показаний</template>
        <template v-slot:body>
            <div class="container-fluid">
                <label>Выберите участок</label>
                <search-select
                    v-model="accountId"
                    :prop-class="'form-control mb-2'"
                    :items="accounts"
                    :placeholder="'Участок...'"
                    @update:model-value="getCounters"
                />
                <template v-if="accountId && counters.length">
                    <label>Выберите счётчик</label>
                    <search-select
                        v-model="counterId"
                        :prop-class="'form-control mb-2'"
                        :items="counters"
                        :placeholder="'Счётчик...'"
                    />
                </template>
                <template v-else-if="accountId && loadedCounters">
                    <div class="alert alert-warning">
                        У участка нет ни одного счётчика
                    </div>
                </template>
            </div>
        </template>
        <template v-slot:footer>
            <button class="btn btn-success"
                    :disabled="!accountId || !counterId"
                    @click="linkAction">
                Привязать
            </button>
        </template>
    </view-dialog>
</template>

<script>
import ResponseError from '../../../mixin/ResponseError.js';
import Url           from '../../../utils/Url.js';
import HistoryBtn    from '../../common/HistoryBtn.vue';
import ViewDialog    from '../../common/ViewDialog.vue';
import SearchSelect  from '../../common/form/SearchSelect.vue';

export default {
    name      : 'CounterHistoryBlock',
    components: { HistoryBtn, ViewDialog, SearchSelect },
    emits     : ['update:reload', 'update:selectedId', 'update:count'],
    mixins    : [
        ResponseError,
    ],
    props     : [],
    data () {
        return {
            histories: [],
            actions  : {},
            allCheck : false,
            checked  : [],

            showDialog: false,
            hideDialog: false,

            historyId: null,
            accountId: null,
            accounts : [],
            counterId: null,
            counters : [],

            loadedCounters: false,
        };
    },
    created () {
        this.getAccounts();
        this.listAction();
    },
    methods : {
        listAction () {
            this.allCheck = false;
            this.checked  = [];
            window.axios[Url.Routes.adminCounterHistoryList.method](Url.Routes.adminCounterHistoryList.uri).then(response => {
                this.actions   = response.data.actions;
                this.histories = response.data.histories;
                this.$emit('update:count', this.histories.length);
            }).catch(response => {
                this.parseResponseErrors(response);
            });
        },
        getAccounts () {
            this.accountId = null;
            this.accounts  = [];
            window.axios[Url.Routes.adminSelectsAccounts.method](Url.Routes.adminSelectsAccounts.uri).then(response => {
                this.accounts = response.data;
            }).catch(response => {
                this.parseResponseErrors(response);
            });
        },
        getCounters () {
            this.counterId = null;
            this.counters  = [];
            if (!this.accountId) {
                return;
            }
            this.loadedCounters = false;
            let uri             = Url.Generator.makeUri(Url.Routes.adminSelectsCounters, {
                accountId: this.accountId,
            });
            window.axios[Url.Routes.adminSelectsCounters.method](uri).then(response => {
                this.counters       = response.data;
                this.loadedCounters = true;
            }).catch(response => {
                this.parseResponseErrors(response);
            });
        },
        onAllCheck () {
            if (this.allCheck) {
                this.histories.forEach(item => {
                    this.checked.push(String(item.id));
                });
            }
            else {
                this.checked = [];
            }
        },
        isChecked (id) {
            return this.checked.includes(String(id));
        },
        onChanged (id) {
            if (this.checked.includes(String(id))) {
                this.checked = this.checked.filter(item => String(item) !== String(id));
            }
            else {
                this.checked.push(String(id));
            }
        },
        showLinkDialog (id) {
            this.historyId  = id;
            this.showDialog = true;
        },
        closeLinkDialog () {
            this.historyId  = null;
            this.showDialog = false;
            this.hideDialog = true;
        },
        linkAction () {
            let form = new FormData();
            form.append('id', this.historyId);
            form.append('account_id', this.accountId);
            form.append('counter_id', this.counterId);

            this.clearResponseErrors();
            window.axios[Url.Routes.adminCounterHistoryLink.method](
                Url.Routes.adminCounterHistoryLink.uri,
                form,
            ).then((response) => {
                this.showInfo('Показания привязаны');
                this.listAction();
                this.closeLinkDialog();
            }).catch(response => {
                let text = response?.data?.message ?
                    response.data.message
                    : 'Не получилось привязать показания';
                this.showDanger(text);
                this.parseResponseErrors(response);
            });
        },
        dropAction (id) {
            if (!confirm(id ? 'Удалить показание?' : 'Удалить выделенные показания?')) {
                return;
            }
            let uri = Url.Generator.makeUri(Url.Routes.adminCounterHistoryDelete, {
                historyId: id,
            });

            window.axios[Url.Routes.adminCounterHistoryDelete.method](
                uri,
            ).then((response) => {
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
        confirmAction () {
            if (!confirm('Подтвердить выделенные показания?')) {
                return;
            }
            let form = new FormData();
            this.checked.forEach(id => {
                form.append('ids[]', id);
            });

            this.clearResponseErrors();
            window.axios[Url.Routes.adminCounterHistoryConfirm.method](
                Url.Routes.adminCounterHistoryConfirm.uri,
                form,
            ).then((response) => {
                this.showInfo('Показания подтверждены');
                this.listAction();
            }).catch(response => {
                let text = response?.data?.message ?
                    response.data.message
                    : 'Не получилось подтвердить показания';
                this.showDanger(text);
                this.parseResponseErrors(response);
            });
        },
    },
    watch   : {
        reload (value) {
            if (value === false) {
                return;
            }
            this.listAction();
            this.$emit('update:reload', false);
        },
    },
    computed: {
        canSubmitAction () {
            return this.checked.length && this.canCheckAction;
        },
        canCheckAction () {
            let result = true;
            this.histories.forEach(history => {
                result = result && history.counterId !== null;
            });

            return result;
        },
    },
};
</script>