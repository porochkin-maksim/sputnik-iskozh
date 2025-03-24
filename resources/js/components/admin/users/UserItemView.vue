<template>
    <div class="d-flex justify-content-between align-items-center mb-2">
        <div class="d-flex">
            <button class="btn btn-success me-2"
                    v-if="actions.edit"
                    @click="saveAction"
            >{{ localUser.id ? 'Сохранить' : 'Создать' }}
            </button>
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
                                          :required="true"
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
                                          :required="true"
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
                                          :required="true"
                                          @change="clearError('middleName')"
                            />
                            <span v-else>{{ localUser.middleName }}</span>
                        </td>
                    </tr>
                    <tr>
                        <th>Почта</th>
                        <td>
                            <custom-input v-model="localUser.email"
                                          v-if="localUser.actions.edit"
                                          :required="true"
                                          @change="clearError('email')"
                            />
                            <span v-else>{{ localUser.email }}</span>
                        </td>
                    </tr>
                    <tr>
                        <th>Телефон</th>
                        <td>
                            <custom-input v-model="localUser.phone"
                                          v-if="localUser.actions.edit"
                                          :required="true"
                                          @change="clearError('phone')"
                            />
                            <span v-else>{{ localUser.phone }}</span>
                        </td>
                    </tr>
                    <tr>
                        <th>Участок</th>
                        <td>
                            <search-select v-model="localUser.accountId"
                                           v-if="localUser.actions.edit"
                                           :prop-class="'form-control'"
                                           :items="accounts"
                                           :placeholder="'Участок'"
                            />
                            <span v-else>{{ localUser.accountName }}</span>
                        </td>
                    </tr>
                    <tr>
                        <th>Роль</th>
                        <td>
                            <search-select v-model="localUser.roleId"
                                           v-if="localUser.actions.edit"
                                           :prop-class="'form-control'"
                                           :items="roles"
                                           :placeholder="'Роль'"
                            />
                            <span v-else>{{ localUser.roleName }}</span>
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
import ResponseError from '../../../mixin/ResponseError.js';
import HistoryBtn    from '../../common/HistoryBtn.vue';
import Url           from '../../../utils/Url.js';
import CustomInput   from '../../common/form/CustomInput.vue';
import Pagination    from '../../common/pagination/Pagination.vue';
import SearchSelect  from '../../common/form/SearchSelect.vue';

export default {
    name      : 'UserItemView',
    components: {
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
        this.localUser = this.user;
        this.vueId     = 'uuid' + this.$_uid;
    },
    data () {
        return {
            localUser: {},

            actions   : this.user.actions,
            historyUrl: this.user.historyUrl,

            routeState: 0,
        };
    },
    methods: {
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
            form.append('account_id', this.localUser.accountId);
            form.append('role_id', this.localUser.roleId);
            form.append('phone', this.localUser.phone);

            this.clearResponseErrors();
            window.axios[Url.Routes.adminUserSave.method](
                Url.Routes.adminUserSave.uri,
                form,
            ).then((response) => {
                let text = this.localUser.id ? 'Пользователь обновлён' : 'Пользователь ' + response.data.id + ' создан';
                this.showInfo(text);

                this.localUser.id   = response.data.id;
                this.localUser.name = response.data.name;

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
    },
};
</script>