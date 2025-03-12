<template>
    <div class="d-flex justify-content-between align-items-center mb-2">
        <div class="d-flex">
            <button class="btn btn-success me-2"
                    v-if="(!localUser.id && actions.create) || (localUser.id && actions.edit)"
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
                <table class="table table-responsive align-middle table-borderless">
                    <tbody>
                    <tr>
                        <th>Фамилия</th>
                        <td>
                            <custom-input v-model="localUser.lastName"
                                          :errors="errors.lastName"
                                          :required="true"
                                          @change="clearError('lastName')"
                            />
                        </td>
                    </tr>
                    <tr>
                        <th>Имя</th>
                        <td>
                            <custom-input v-model="localUser.firstName"
                                          :errors="errors.firstName"
                                          :required="true"
                                          @change="clearError('firstName')"
                            />
                        </td>
                    </tr>
                    <tr>
                        <th>Отчество</th>
                        <td>
                            <custom-input v-model="localUser.middleName"
                                          :errors="errors.middleName"
                                          :required="true"
                                          @change="clearError('middleName')"
                            />
                        </td>
                    </tr>
                    <tr>
                        <th>Почта</th>
                        <td>
                            <custom-input v-model="localUser.email"
                                          :errors="errors.email"
                                          :required="true"
                                          @change="clearError('email')"
                            />
                        </td>
                    </tr>
                    <tr>
                        <th>Участок</th>
                        <td>
                            <search-select v-model="localUser.accountId"
                                           :prop-class="'form-control'"
                                           :items="accounts"
                                           :placeholder="'Участок'"
                            />
                        </td>
                    </tr>
                    <tr>
                        <th>Роль</th>
                        <td>
                            <search-select v-model="localUser.roleId"
                                           :prop-class="'form-control'"
                                           :items="roles"
                                           :placeholder="'Роль'"
                            />
                        </td>
                    </tr>
                    </tbody>
                </table>
            </div>
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

            this.clearResponseErrors();
            window.axios[Url.Routes.adminUserSave.method](
                Url.Routes.adminUserSave.uri,
                form,
            ).then((response) => {
                let text = this.localUser.id ? 'Пользователь обновлён' : 'Пользователь ' + response.data.id + ' создан';
                this.showInfo(text);

                this.localUser.id   = response.data.id;
                this.localUser.name = response.data.name;

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
    },
};
</script>