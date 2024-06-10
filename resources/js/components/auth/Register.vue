<template>
    <div v-if="registerSuccessMessage"
         class="alert alert-success"
         v-html="registerSuccessMessage" />
    <form @submit.prevent="registerAction"
          v-else>
        <div>
            <custom-input v-model="login"
                          :errors="errors.login"
                          :type="'email'"
                          :placeholder="'Эл.почта'"
                          :required="true"
            />
        </div>

        <div class="mt-3 toggle-parent">
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
        </div>

        <div class="mt-3">
            <custom-input v-model="passwordConfirm"
                          @change="clearError('password')"
                          :type="[showPassword ? 'text' : 'password']"
                          :placeholder="'Повторите пароль'"
                          :required="true"
            />
        </div>

        <div class="d-grid my-3">
            <button type="submit"
                    class="btn btn-primary">Зарегистрироваться
            </button>
        </div>
    </form>
</template>

<script>
import CustomInput   from '../common/form/CustomInput.vue';
import ResponseError from '../../mixin/ResponseError.js';

export default {
    name      : 'Register',
    components: {
        CustomInput,
    },
    mixins    : [
        ResponseError,
    ],
    data () {
        return {
            registerSuccessMessage: null,
            showPassword          : false,

            login          : null,
            password       : null,
            passwordConfirm: null,
        };
    },
    methods : {
        togglePassword () {
            this.showPassword = !this.showPassword;
        },
        registerAction (e) {
            window.axios.post('/register', {
                login                : this.login,
                password             : this.password,
                password_confirmation: this.passwordConfirm,
            }).then(response => {
                this.registerSuccessMessage = response.data;
                setTimeout(() => location.reload(), 3000);
            }).catch(response => {
                this.parseResponseErrors(response);
            });
        },
    },
    computed: {
        confirmedStatus () {
            switch (true) {
                case !this.password || !this.passwordConfirm:
                    return null;
                case this.password === this.passwordConfirm:
                    return true;
                default:
                    return false;
            }
        },
    },
};
</script>
