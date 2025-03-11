<template>
    <div class="d-flex align-items-center justify-content-between mb-2">
        <button class="btn btn-success"
                :disabled="!actions.create"
                v-on:click="makeAction">Добавить роль
        </button>
        <history-btn
            class="btn-link underline-none"
            :disabled="!actions.view"
            :url="historyUrl" />
    </div>
    <div class="row">
        <div class="col-8">
            <table class="table table-sm"
                   v-if="actions.view">
                <thead>
                <tr>
                    <th>№</th>
                    <th>Название</th>
                    <th></th>
                </tr>
                </thead>
                <tbody>
                <tr class="align-middle"
                    v-for="(role, index) in roles">
                    <td>{{ role.id }}</td>
                    <td class="w-100">
                        <a href=""
                           @click.prevent="editAction(role)">
                            {{ role.name }}
                        </a>
                    </td>
                    <td>
                        <button class="btn"
                                @click="dropAction(role.id)"
                                :disabled="role.actions?.drop === false || loading">
                            <i class="fa fa-trash text-danger"></i>
                        </button>
                    </td>
                </tr>
                </tbody>
            </table>
        </div>
        <div class="col-4"
             v-if="role">
            <div class="input-group input-group-sm mb-2">
                <button class="btn btn-success"
                        @click="saveAction"
                        :disabled="!canSave || role.actions?.edit === false || loading">
                    <i class="fa"
                       :class="loading ? 'fa-spinner fa-spin' : 'fa-save'"></i>
                </button>
                <input type="text"
                       class="form-control name"
                       placeholder="Название"
                       v-model="role.name">
            </div>
            <div v-for="(group, section) in permissions">
                <ul v-for="(item, code) in group"
                    class="list-group list-unstyled">
                    <template v-if="code === section">
                        <li class="fw-bold mb-2">
                            <input class="form-check-input cursor-pointer"
                                   type="checkbox"
                                   :id="vueId + code"
                                   :checked="isChecked(code)"
                                   :disabled="role.actions?.edit === false || loading"
                                   @change="onChangedSection(code)">
                            &nbsp;
                            <label :for="vueId + code"
                                   class="cursor-pointer">{{ item }}</label>
                        </li>
                    </template>
                    <template v-else>
                        <li>
                            <input class="form-check-input cursor-pointer"
                                   type="checkbox"
                                   :id="vueId + code"
                                   :checked="isChecked(code)"
                                   :disabled="role.actions?.edit === false || loading"
                                   @change="onChanged(code)">
                            &nbsp;
                            <label :for="vueId + code"
                                   class="cursor-pointer">{{ item }}</label>
                        </li>
                    </template>
                </ul>
                <hr>
            </div>
        </div>
    </div>
</template>

<script>
import ResponseError from '../../../mixin/ResponseError.js';
import Url           from '../../../utils/Url.js';
import HistoryBtn    from '../../common/HistoryBtn.vue';

export default {
    name      : 'RolesBlock',
    components: { HistoryBtn },
    emits     : ['update:checked'],
    mixins    : [
        ResponseError,
    ],
    props     : {
        permissions: {
            type   : Object,
            default: {},
        },
    },
    data () {
        return {
            vueId  : null,
            loading: false,

            role      : null,
            checked   : [],
            roles     : [],
            historyUrl: null,
            actions   : {},
        };
    },
    created () {
        this.vueId = 'uuid' + this.$_uid;
        this.listAction();
    },
    methods : {
        makeAction () {
            if (!this.actions.create) {
                return;
            }
            window.axios[Url.Routes.adminRoleCreate.method](Url.Routes.adminRoleCreate.uri).then(response => {
                this.role    = response.data;
                this.checked = [];
            }).catch(response => {
                this.parseResponseErrors(response);
            });
        },
        listAction () {
            this.loading = true;
            window.axios[Url.Routes.adminRoleList.method](Url.Routes.adminRoleList.uri).then(response => {
                this.actions    = response.data.actions;
                this.roles      = response.data.roles;
                this.historyUrl = response.data.historyUrl;
            }).catch(response => {
                this.parseResponseErrors(response);
            }).then(() => {
                this.loading = false;
            });
        },
        editAction (role) {
            this.checked = role.permissions;
            this.role    = role;
        },
        saveAction () {
            if (!this.actions.edit) {
                return;
            }
            this.loading = true;
            let form     = new FormData();
            form.append('id', this.role.id);
            form.append('name', this.role.name);
            this.checked.forEach(item => {
                form.append('permissions[]', item);
            });

            this.clearResponseErrors();
            window.axios[Url.Routes.adminRoleSave.method](
                Url.Routes.adminRoleSave.uri,
                form,
            ).then((response) => {
                let text = this.role.id ? 'Роль обновлена' : 'Роль ' + response.data.id + ' создана';
                this.showInfo(text);

                this.role.id   = response.data.id;
                this.role.name = response.data.name;

                this.actions = response.data.actions;
            }).catch(response => {
                let text = response?.data?.message ?
                    response.data.message
                    : 'Не получилось ' + (this.id ? 'сохранить' : 'создать') + ' роль';
                this.showDanger(text);
                this.parseResponseErrors(response);
            }).then(() => {
                this.listAction();
            });
        },
        dropAction (id) {
            if (!this.actions.drop) {
                return;
            }
            if (!confirm('Удалить роль?')) {
                return;
            }

            let uri = Url.Generator.makeUri(Url.Routes.adminRoleDelete, {
                id: id,
            });
            window.axios[Url.Routes.adminRoleDelete.method](
                uri,
            ).then((response) => {
                this.dropped = response.data;
                if (response.data) {
                    this.showInfo('Роль удалена');
                }
                else {
                    this.showDanger('Роль не удалена');
                }
            }).catch(response => {
                this.parseResponseErrors(response);
            }).then(() => {
                if (this.role.id === id) {
                    this.role = null;
                }
                this.listAction();
            });
        },
        isChecked (code) {
            return this.checked.includes(String(code));
        },
        onChanged (code) {
            if (this.checked.includes(String(code))) {
                this.checked = this.checked.filter(item => String(item) !== String(code));
            }
            else {
                this.checked.push(String(code));
            }
            this.$emit('update:checked', this.checked);
        },
        onChangedSection (code) {
            let items = this.permissions[String(code)];

            if (this.checked.includes(String(code))) {
                Object.keys(items).forEach(permission => {
                    this.checked = this.checked.filter(item => String(item) !== String(permission));
                });
            }
            else {
                Object.keys(items).forEach(permission => {
                    this.checked.push(String(permission));
                });
            }
            this.$emit('update:checked', this.checked);
        },
    },
    computed: {
        canSave () {
            return this.role.name && this.checked && this.checked.length;
        },
    },
};
</script>
