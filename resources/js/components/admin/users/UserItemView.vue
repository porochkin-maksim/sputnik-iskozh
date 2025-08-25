<template>
    <div class="d-flex justify-content-between align-items-center mb-2">
        <div class="d-flex">
            <button class="btn btn-success me-2"
                    v-if="actions.edit"
                    @click="saveAction"
                    :disabled="loading"
            >
                <i class="fa"
                   :class="loading ? 'fa-spinner fa-spin' : 'fa-save'"></i>
                {{ localUser.id ? 'Сохранить' : 'Создать' }}
            </button>
            <a class="btn btn-outline-primary me-2"
               v-if="actions.edit && localUser.id"
               :href="getCreateLink()">Добавить пользователя
            </a>
        </div>
        <div class="d-flex">
            <history-btn
                :disabled="!localUser.id"
                class="btn-link underline-none"
                :url="historyUrl" />
        </div>
    </div>
    <div class="row">
        <div class="col-6">
            <div class="form">
                <table class="table table-responsive align-middle"
                       :class="localUser.actions.edit ? 'table-borderless' : ''">
                    <tbody>
                    <tr>
                        <th>Фамилия</th>
                        <td>
                            <custom-input v-model="localUser.lastName"
                                          v-if="localUser.actions.edit"
                                          :disabled="loading"
                                          @change="clearError('lastName')"
                            />
                            <span v-else>{{ localUser.lastName }}</span>
                        </td>
                    </tr>
                    <tr>
                        <th>Имя</th>
                        <td>
                            <custom-input v-model="localUser.firstName"
                                          v-if="localUser.actions.edit"
                                          :disabled="loading"
                                          @change="clearError('firstName')"
                            />
                            <span v-else>{{ localUser.firstName }}</span>
                        </td>
                    </tr>
                    <tr>
                        <th>Отчество</th>
                        <td>
                            <custom-input v-model="localUser.middleName"
                                          v-if="localUser.actions.edit"
                                          :disabled="loading"
                                          @change="clearError('middleName')"
                            />
                            <span v-else>{{ localUser.middleName }}</span>
                        </td>
                    </tr>
                    <tr>
                        <th>Почта</th>
                        <td>
                            <div class="input-group w-100"
                                 v-if="localUser.actions.edit">
                                <input class="form-control"
                                       v-model="localUser.email"
                                       :disabled="loading">
                                <button class="btn btn-success"
                                        v-if="canGenerateEmail"
                                        @click="generateEmail"
                                        :disabled="loading">
                                    <i class="fa fa-retweet"></i>
                                </button>
                            </div>
                            <span v-else>{{ localUser.email }}</span>
                        </td>
                    </tr>
                    <tr>
                        <th>Телефон</th>
                        <td>
                            <custom-input v-model="localUser.phone"
                                          v-if="localUser.actions.edit"
                                          :disabled="loading"
                                          @change="clearError('phone')"
                            />
                            <span v-else>{{ localUser.phone }}</span>
                        </td>
                    </tr>
                    <template v-if="localUser.actions.edit">
                        <tr>
                            <th>
                                Участки
                            </th>
                            <td class="align-top">
                                <search-select v-model="accountIds"
                                               :disabled="loading"
                                               :multiple="true"
                                               :prop-class="'form-control'"
                                               :items="accounts"
                                               :placeholder="'Участок'"
                                />
                            </td>
                        </tr>
                        <template v-if="fractions && fractions.length && accounts && accounts.length">
                            <template v-for="(fraction) in fractions">
                                <tr>
                                    <th v-html="renderAccountLink(fraction.accountId)"></th>
                                    <td>
                                        <custom-input v-model="fraction.value"
                                                      :disabled="loading"
                                                      :label="'Доля владения (от 0 до 1)'"
                                                      :type="'number'"
                                                      :step="'0.1'"
                                                      :max="1"
                                                      :min="0"
                                        />
                                    </td>
                                </tr>
                            </template>
                        </template>
                    </template>
                    <template v-else>
                        <template v-if="fractions && fractions.length && accounts && accounts.length">
                            <template v-for="(fraction) in fractions">
                                <tr>
                                    <th>
                                        <span v-html="renderAccountLink(fraction.accountId)"></span>
                                    </th>
                                    <td>
                                        <i class="fa fa-user" :class="[fraction.value ? 'text-success' : 'text-light']"></i>&nbsp;{{ fraction.value ? (fraction.value * 100)+'%' : '' }}
                                    </td>
                                </tr>
                            </template>
                        </template>
                    </template>
                    <tr>
                        <th>Роль</th>
                        <td>
                            <search-select v-model="localUser.roleId"
                                           v-if="localUser.actions.edit"
                                           :disabled="loading"
                                           :prop-class="'form-control'"
                                           :items="roles"
                                           :placeholder="'Роль'"
                            />
                            <span v-else>{{ localUser.roleName }}</span>
                        </td>
                    </tr>
                    <tr>
                        <th colspan="2"
                            class="text-center table-light">Дополнительно
                        </th>
                    </tr>
                    <tr>
                        <th>Дата членства</th>
                        <td>
                            <custom-calendar v-model="localUser.ownershipDate"
                                             v-if="localUser.actions.edit"
                                             :disabled="loading"
                                             @change="clearError('ownershipDate')"
                            />
                            <span v-else>{{ $formatDate(localUser.ownershipDate) }}</span>
                        </td>
                    </tr>
                    <tr>
                        <th>Основание членства</th>
                        <td>
                            <custom-input v-model="localUser.ownershipDutyInfo"
                                          v-if="localUser.actions.edit"
                                          :disabled="loading"
                                          @change="clearError('phone')"
                            />
                            <span v-else>{{ localUser.ownershipDutyInfo }}</span>
                        </td>
                    </tr>
                    <tr>
                        <th>Дополнительный телефон</th>
                        <td>
                            <custom-input v-model="localUser.addPhone"
                                          v-if="localUser.actions.edit"
                                          :disabled="loading"
                            />
                            <span v-else>{{ localUser.addPhone }}</span>
                        </td>
                    </tr>
                    <tr>
                        <th>Адрес по прописке</th>
                        <td>
                            <custom-input v-model="localUser.legalAddress"
                                          v-if="localUser.actions.edit"
                                          :disabled="loading"
                            />
                            <span v-else>{{ localUser.legalAddress }}</span>
                        </td>
                    </tr>
                    <tr>
                        <th>Адрес почтовый</th>
                        <td>
                            <custom-input v-model="localUser.postAddress"
                                          v-if="localUser.actions.edit"
                                          :disabled="loading"
                            />
                            <span v-else>{{ localUser.postAddress }}</span>
                        </td>
                    </tr>
                    <tr>
                        <th>Примечание</th>
                        <td>
                            <custom-textarea v-model="localUser.additional"
                                             v-if="localUser.actions.edit"
                                             :height="100"
                                             :disabled="loading"
                            />
                            <span v-else>{{ localUser.additional }}</span>
                        </td>
                    </tr>
                    </tbody>
                </table>
            </div>
        </div>
        <div class="col-6 pt-2"
             v-if="localUser.id">
            <ul class="list-group"
                v-if="localUser.actions.edit">
                <li class="list-group-item list-group-item-action cursor-pointer"
                    @click="sendInvitePasswordEmail">
                    <i class="fa fa-envelope"></i>&nbsp;Выслать пригласительную ссылку для установки пароля
                </li>
                <li class="list-group-item list-group-item-action cursor-pointer"
                    @click="sendRestorePasswordEmail">
                    <i class="fa fa-wrench"></i>&nbsp;Выслать ссылку на восстановление пароля
                </li>
            </ul>
        </div>
    </div>
</template>

<script>
import ResponseError  from '../../../mixin/ResponseError.js';
import HistoryBtn     from '../../common/HistoryBtn.vue';
import Url            from '../../../utils/Url.js';
import CustomInput    from '../../common/form/CustomInput.vue';
import Pagination     from '../../common/pagination/Pagination.vue';
import SearchSelect   from '../../common/form/SearchSelect.vue';
import CustomCalendar from '../../common/form/CustomCalendar.vue';
import CustomTextarea from '../../common/form/CustomTextarea.vue';

export default {
    name      : 'UserItemView',
    components: {
        CustomTextarea,
        CustomCalendar,
        SearchSelect,
        Pagination,
        CustomInput,
        HistoryBtn,
    },
    mixins    : [
        ResponseError,
    ],
    props     : {
        user    : {
            type   : Object,
            default: {},
        },
        accounts: {
            type   : Array,
            default: [],
        },
        roles   : {
            type   : Array,
            default: [],
        },
    },
    created () {
        this.vueId = 'uuid' + this.$_uid;

        this.localUser = {
            ...this.user,
        };
        this.accountIds = Array.isArray(this.user.accountIds)
            ? this.user.accountIds.map(String)
            : (this.user.accountId ? [String(this.user.accountId)] : []);
        if (Array.isArray(this.user.accounts)) {
            for (const account of this.user.accounts) {
                this.fractions.push({
                    accountId: account.id,
                    value    : account.fraction,
                });
            }
        }
    },
    data () {
        return {
            localUser : {},
            accountIds: [],
            fractions : [],

            actions   : this.user.actions,
            historyUrl: this.user.historyUrl,

            loading   : null,
            routeState: 0,
        };
    },
    methods : {
        saveAction () {
            if (!this.actions.edit) {
                return;
            }
            this.loading = true;
            let form     = new FormData();
            form.append('id', this.localUser.id);
            form.append('first_name', this.localUser.firstName);
            form.append('last_name', this.localUser.lastName);
            form.append('middle_name', this.localUser.middleName);
            form.append('email', this.localUser.email);
            if (Array.isArray(this.fractions)) {
                for (const fraction of this.fractions) {
                    form.append('fractions['+fraction.accountId+']', fraction.value);
                }
            }
            form.append('role_id', this.localUser.roleId);
            form.append('phone', this.localUser.phone);
            form.append('ownershipDate', this.localUser.ownershipDate);
            form.append('ownershipDutyInfo', this.localUser.ownershipDutyInfo);
            form.append('add_phone', this.localUser.addPhone);
            form.append('legal_address', this.localUser.legalAddress);
            form.append('post_address', this.localUser.postAddress);
            form.append('additional', this.localUser.additional);

            this.clearResponseErrors();
            Url.RouteFunctions.adminUserSave({}, form).then((response) => {
                let text = this.localUser.id ? 'Пользователь обновлён' : 'Пользователь ' + response.data.id + ' создан';
                this.showInfo(text);

                this.localUser = response.data;

                let uri = Url.Generator.makeUri(Url.Routes.adminUserView, {
                    id: this.localUser.id,
                });
                window.history.pushState({ state: this.routeState++ }, '', uri);

                this.actions = response.data.actions;
            }).catch(response => {
                let text = response?.data?.message ?
                    response.data.message
                    : 'Не получилось ' + (this.id ? 'сохранить' : 'создать') + ' пользователя';
                this.showDanger(text);
                this.parseResponseErrors(response);
            }).then(() => {
                this.loading = false;
            });
        },
        generateEmail () {
            let form = new FormData();
            form.append('id', this.localUser.id);
            form.append('first_name', this.localUser.firstName);
            form.append('last_name', this.localUser.lastName);
            form.append('middle_name', this.localUser.middleName);

            this.clearResponseErrors();
            window.axios[Url.Routes.adminUserGenerateEmail.method](
                Url.Routes.adminUserGenerateEmail.uri,
                form,
            ).then((response) => {
                this.localUser.email = response.data;
            }).catch(response => {
                this.showDanger('Что-то пошло не так');
                this.parseResponseErrors(response);
            });
        },
        dropAction (id) {
            if (!this.actions.drop) {
                return;
            }
            if (!confirm('Удалить пользователя?')) {
                return;
            }

            let uri = Url.Generator.makeUri(Url.Routes.adminUserDelete, {
                id: id,
            });
            window.axios[Url.Routes.adminUserDelete.method](
                uri,
            ).then((response) => {
                this.dropped = response.data;
                if (response.data) {
                    this.showInfo('Пользователь удалён');
                    setTimeout(() => {
                        location.href = Url.Routes.adminUserIndex.uri;
                    }, 2000);
                }
                else {
                    this.showDanger('Пользователь не удалён');
                }
            }).catch(response => {
                this.parseResponseErrors(response);
            });
        },
        sendRestorePasswordEmail () {
            if (!confirm('Отправить письмо для сброса пароля?')) {
                return;
            }
            let form = new FormData();
            form.append('id', this.localUser.id);

            window.axios[Url.Routes.adminUserSendRestorePassword.method](
                Url.Routes.adminUserSendRestorePassword.uri,
                form,
            ).then((response) => {
                this.showInfo('Письмо отправлено');
            }).catch(response => {
                this.showDanger('Письмо не отправлено');
                this.parseResponseErrors(response);
            });
        },
        sendInvitePasswordEmail () {
            if (!confirm('Отправить пригласительное письмо для установки пароля?')) {
                return;
            }
            let form = new FormData();
            form.append('id', this.localUser.id);

            window.axios[Url.Routes.adminUserSendInviteWithPassword.method](
                Url.Routes.adminUserSendInviteWithPassword.uri,
                form,
            ).then((response) => {
                this.showInfo('Письмо отправлено');
            }).catch(response => {
                this.showDanger('Письмо не отправлено');
                this.parseResponseErrors(response);
            });
        },
        getCreateLink () {
            return Url.Generator.makeUri(Url.Routes.adminUserView, {
                id: null,
            });
        },
        renderAccountLink (accountId) {
            const uri = Url.Generator.makeUri(Url.Routes.adminAccountView, { accountId: accountId });

            return '<a href=' + uri + '>Участок ' + this.getAccountNumberById(accountId) + '</a>';
        },
        getAccountNumberById (accountId) {
            return this.accounts.find(account => String(account.key) === String(accountId))?.value;
        },
        getFractionByAccountId (accountId) {
            console.log(accountId,this.fractions);

            let result = null;
            this.fractions.forEach(fraction => {
                if (fraction.accountId === accountId) {
                    result = fraction;
                }
            });

            console.log(result);

            return result;
        },
    },
    computed: {
        canGenerateEmail () {
            return !this.localUser.id && this.localUser.lastName && this.localUser.firstName && this.localUser.middleName;
        },
        localUserAccounts () {
            let result = this.localUser.accounts;
            if (!result.length) {
                result = [{}];
            }

            return result;
        },
    },
    watch   : {
        accountIds: {
            handler (newAccountIds) {
                let fractions = [];
                newAccountIds.forEach(accountId => {
                    let value = 0;
                    this.fractions.forEach(fraction => {
                        if (fraction.accountId === accountId) {
                            value = fraction.value;
                        }
                    });
                    fractions.push({
                        accountId: accountId,
                        value    : value,
                    });
                });
            },
            deep: true,
        },
    },
};
</script>