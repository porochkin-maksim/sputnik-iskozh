<template>
    <div class="d-flex justify-content-between align-items-center mb-2">
        <div class="d-flex">
            <a class="btn btn-outline-primary me-2"
               v-if="actions.edit && localUser.id"
               :href="getCreateLink()">Добавить пользователя
            </a>
        </div>
        <div class="d-flex">
            <div class="me-2" v-if="actions.drop && localUser.id">
                <button class="btn btn-sm btn-danger" v-if="!localUser.isDeleted" @click="dropAction">
                    <i class="fa fa-trash"></i> Удалить
                </button>
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-6">
            <div v-if="localUser.isDeleted" class="alert alert-danger text-center mb-0 d-flex justify-content-center align-items-center">
                <div>Пользователь удалён</div>
                <button class="btn btn-sm btn-danger ms-2" v-if="localUser.isDeleted && actions.drop" @click="restoreAction">
                    <i class="fa fa-rotate-left"></i> Восстановить
                </button>
            </div>
            <div class="card mb-2">
                <div class="card-body">
                    <h5>Информация</h5>
                    <div class="row mb-2">
                        <div class="col-4 pe-1">
                            <custom-input v-model="localUser.lastName"
                                          :disabled="loading"
                                          :label="'Фамилия'"
                                          @change="clearError('lastName')"
                            />
                        </div>
                        <div class="col-4 px-1">
                            <custom-input v-model="localUser.firstName"
                                          :disabled="loading"
                                          :label="'Имя'"
                                          @change="clearError('firstName')"
                            />
                        </div>
                        <div class="col-4 ps-1">
                            <custom-input v-model="localUser.middleName"
                                          :disabled="loading"
                                          :label="'Отчество'"
                                          @change="clearError('middleName')"
                            />
                        </div>
                    </div>
                    <div class="row mb-2">
                        <div class="col-6 pe-1">
                            <div class="w-100" :class="[canGenerateEmail ? 'input-group' : '']">
                                <custom-input v-model="localUser.email"
                                              :disabled="loading"
                                              :label="'Почта'"
                                              @change="clearError('email')"
                                />
                                <button class="btn btn-success"
                                        v-if="canGenerateEmail"
                                        @click="generateEmail"
                                        :disabled="loading">
                                    <i class="fa fa-retweet"></i>
                                </button>
                            </div>
                        </div>
                        <div class="col-6 ps-1">
                            <div class="w-100" :class="[canGenerateEmail ? 'input-group' : '']">
                                <custom-input v-model="localUser.phone"
                                              :disabled="loading"
                                              :label="'Телефон'"
                                              @change="clearError('phone')"
                                />
                                <button class="btn btn-success"
                                        v-if="canGenerateEmail"
                                        @click="generateEmail"
                                        :disabled="loading">
                                    <i class="fa fa-retweet"></i>
                                </button>
                            </div>
                        </div>
                    </div>
                    <h5>Дополнительно</h5>
                    <div class="row mb-2">
                        <div class="col-6 pe-1">
                            <search-select v-model="localUser.roleId"
                                           v-if="localUser.actions.edit"
                                           :disabled="loading"
                                           :prop-class="'form-control'"
                                           :items="roles"
                                           :label="'Роль'"
                                           :placeholder="'Роль'"
                            />
                        </div>
                        <div class="col-6 ps-1">
                            <custom-input v-model="localUser.addPhone"
                                          :disabled="loading"
                                          :label="'Дополнительный телефон'"
                                          @change="clearError('addPhone')"
                            />
                        </div>
                    </div>
                    <div class="row mb-2">
                        <div class="col-6 pe-1">
                            <custom-input v-model="localUser.membershipDutyInfo"
                                          :disabled="loading"
                                          :label="'Основание членства'"
                                          @change="clearError('membershipDutyInfo')"
                            />
                        </div>
                        <div class="col-6 ps-1">
                            <custom-calendar v-model="localUser.membershipDate"
                                             :disabled="loading"
                                             :label="'Дата членства'"
                                             @change="clearError('membershipDate')"
                            />
                        </div>
                    </div>
                    <div class="row mb-2">
                        <div class="col-6 pe-1">
                            <custom-input v-model="localUser.legalAddress"
                                          :disabled="loading"
                                          :label="'Адрес по прописке'"
                                          @change="clearError('legalAddress')"
                            />
                        </div>
                        <div class="col-6 ps-1">
                            <custom-input v-model="localUser.postAddress"
                                             :disabled="loading"
                                             :label="'Почтовый адрес'"
                                             @change="clearError('postAddress')"
                            />
                        </div>
                    </div>
                    <div class="row mb-2">
                        <div class="col-12">
                            <custom-textarea v-model="localUser.additional"
                                             :label="'Комментарий'"
                                             :rows="3"
                                             :disabled="loading"
                            />
                        </div>
                    </div>
                    <div class="d-flex align-items-center justify-content-between mt-2">
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
                        </div>
                        <div class="d-flex">
                            <history-btn
                                :disabled="!localUser.id"
                                class="btn-link underline-none"
                                :url="historyUrl" />
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-6">
            <div class="card mb-2">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center mb-2">
                        <h5 class="m-0">Участки</h5>
                        <div class="w-75">
                            <search-select v-model="accountIds"
                                           :disabled="loading"
                                           :multiple="true"
                                           :prop-class="'form-control w-100'"
                                           :items="accounts"
                                           :placeholder="'Введите и выберите номера участков'"
                            />
                        </div>
                    </div>
                    <template v-if="fractions && fractions.length && accounts && accounts.length">
                        <div v-for="(fraction) in fractions" class="row mb-2">
                            <div class="col-3 pe-1 d-flex justify-content-center align-items-end pb-1">
                                <h6 v-html="renderAccountLink(fraction.accountId)"></h6>
                            </div>
                            <div class="col-4 px-1">
                                <custom-input v-model="fraction.value"
                                              :disabled="loading"
                                              :label="'Доля владения (от 0 до 1)'"
                                              :type="'number'"
                                              :step="'0.1'"
                                              :max="1"
                                              :min="0"
                                />
                            </div>
                            <div class="col-5 ps-1">
                                <custom-calendar v-model="fraction.date"
                                                 :disabled="loading"
                                                 :label="'Дата права'"
                                />
                            </div>
                        </div>
                    </template>
                </div>
            </div>
            <div class="card mb-2" v-if="localUser.id">
                <div class="card-body">
                    <h5>Уведомления</h5>
                    <ul class="list-group" v-if="localUser.actions.edit">
                        <li class="list-group-item list-group-item-action cursor-pointer border-0" @click="sendInvitePasswordEmail" v-if="!localUser.isRealEmail">
                            <i class="fa fa-envelope-o"></i>&nbsp;Выслать пригласительную ссылку для установки пароля
                        </li>
                        <li class="list-group-item list-group-item-action cursor-pointer border-0" @click="sendRestorePasswordEmail" v-if="localUser.isRealEmail">
                            <i class="fa fa-wrench"></i>&nbsp;Выслать ссылку на восстановление пароля
                        </li>
                        <template v-if="qrViewLink">
                            <li class="list-group-item list-group-item-action border-0">
                                <div><b>Просмотреть QR-код</b></div>
                                <a :href="qrViewLink" target="_blank">
                                    {{ qrViewLink }}
                                </a>
                                <div><b>Сгенерированная ссылка</b></div>
                                <a :data-copy="tokenLink" @click.prevent="showInfo('Скопировано')" class="cursor-pointer">
                                    {{ tokenLink }}
                                </a>
                            </li>
                        </template>
                        <template v-else>
                            <li class="list-group-item list-group-item-action cursor-pointer border-0" @click="makeLoginQrCode">
                                <i class="fa fa-external-link"></i>&nbsp;Получить постоянную ссылку для входа (QR-код)
                            </li>
                        </template>
                    </ul>
                </div>
            </div>
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

        this.localUser  = {
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
                    date     : account.ownerDate,
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

            qrViewLink: null,
            tokenLink : null,
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
                    form.append('fractions[' + fraction.accountId + ']', fraction.value);
                    form.append('ownerDates[' + fraction.accountId + ']', fraction.date);
                }
            }
            form.append('role_id', this.localUser.roleId);
            form.append('phone', this.localUser.phone);
            form.append('membershipDate', this.localUser.membershipDate);
            form.append('membershipDutyInfo', this.localUser.membershipDutyInfo);
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
        dropAction () {
            if (!this.actions.drop) {
                return;
            }
            if (!confirm('Удалить пользователя?')) {
                return;
            }

            Url.RouteFunctions.adminUserDelete(this.localUser.id).then((response) => {
                this.dropped = response.data;
                if (response.data) {
                    this.showInfo('Пользователь удалён');
                    setTimeout(() => {
                        location.reload();
                    }, 2000);
                }
                else {
                    this.showDanger('Пользователь не удалён');
                }
            }).catch(response => {
                this.parseResponseErrors(response);
            });
        },
        restoreAction () {
            if (!this.actions.drop) {
                return;
            }
            if (!confirm('Восстановить пользователя?')) {
                return;
            }

            Url.RouteFunctions.adminUserRestore(this.localUser.id).then((response) => {
                this.dropped = response.data;
                if (response.data) {
                    this.showInfo('Пользователь восстановлен');
                    setTimeout(() => {
                        location.reload();
                    }, 2000);
                }
                else {
                    this.showDanger('Пользователь не восстановлен');
                }
            }).catch(response => {
                this.parseResponseErrors(response);
            });
        },
        makeLoginQrCode () {
            let pin = prompt('Установите пароль для постоянной ссылки для входа')
            if (!pin) {
                return;
            }

            Url.RouteFunctions.adminLoginLink(this.localUser.id, pin).then((response) => {
                this.qrViewLink = response.data.qrLink;
                this.tokenLink  = response.data.tokenLink;
            }).catch(response => {
                this.showDanger('Не получилось создать ссылку');
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

            return '<a href=' + uri + ' class="text-decoration-none">' + this.getAccountNumberById(accountId) + '</a>';
        },
        getAccountNumberById (accountId) {
            return this.accounts.find(account => String(account.value) === String(accountId))?.label;
        },
    },
    computed: {
        canGenerateEmail () {
            return (!this.localUser.id || !this.localUser.email) && this.localUser.lastName && this.localUser.firstName && this.localUser.middleName;
        },
    },
    watch   : {
        accountIds: {
            handler (newAccountIds) {
                let fractions = [];
                newAccountIds.forEach(accountId => {
                    let value = 0;
                    let date  = null;
                    this.fractions.forEach(fraction => {
                        if (String(fraction.accountId) === String(accountId)) {
                            value = fraction.value;
                            date  = fraction.date;
                        }
                    });
                    fractions.push({
                        accountId: accountId,
                        value    : value,
                        date     : date,
                    });
                });
                this.fractions = fractions;
            },
            deep: true,
        },
    },
};
</script>

<style scoped>
td {
    padding : 2px 0;
}
</style>