<template>
    <div class="form">
        <table class="table table-responsive align-middle password">
            <tbody>
            <tr>
                <th>Пароль</th>
                <td class="toggle-parent">
                    <custom-input v-model="password"
                                  @change="clearError('password')"
                                  :errors="errors.password"
                                  :type="[showPassword ? 'text' : 'password']"
                                  :placeholder="'Пароль'"
                                  :required="true"
                    />
                    <span class="toggle fa"
                          :class="[showPassword ? 'fa-eye' : 'fa-eye-slash']"
                          @click="togglePassword"
                    ></span>
                </td>
            </tr>
            <tr>
                <th>Повторите пароль</th>
                <td>
                    <custom-input v-model="passwordConfirm"
                                  @change="clearError('password')"
                                  :type="[showPassword ? 'text' : 'password']"
                                  :placeholder="'Повторите пароль'"
                                  :required="true"
                    />
                </td>
            </tr>
            </tbody>
        </table>
    </div>
    <div class="d-flex justify-content-center mb-3">
        <button class="btn btn-primary"
                @click="updatePassword">
            <i class="fa fa-save"></i> Обновить
        </button>
    </div>
</template>

<script>
import Url           from '../../utils/Url.js';
import ResponseError from '../../mixin/ResponseError.js';
import CustomInput   from '../common/form/CustomInput.vue';

export default {
    components: {
        CustomInput,
    },
    mixins    : [
        ResponseError,
    ],
    props     : [
        'user',
    ],
    data () {
        return {
            password       : null,
            passwordConfirm: null,

            showPassword   : false,
        };
    },
    created () {
        this.password = this.user.password;
    },
    methods: {
        updatePassword () {
            window.axios[Url.Routes.profileSavePassword.method](Url.Routes.profileSavePassword.uri, {
                password             : this.password,
                password_confirmation: this.passwordConfirm,
            }).then(response => {
                location.reload();
            }).catch(response => {
                this.parseResponseErrors(response);
            });
        },
        togglePassword () {
            this.showPassword = !this.showPassword;
        },
    },
};
</script>

<style scoped>
.table {
    tr {
        th {
            text-align : right;
            width      : 50px;
        }
    }
}
</style>