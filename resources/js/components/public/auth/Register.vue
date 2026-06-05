<template>
    <div v-if="registerSuccessMessage" class="alert alert-success" v-html="registerSuccessMessage" />
    <form v-else @submit.prevent="registerAction" class="auth-form-stack">
        <custom-input v-model="login" :errors="errors.login" type="email" placeholder="Эл.почта" :required="true" :classes="'auth-form-field'" />
        <div class="toggle-parent">
            <custom-input v-model="password" @change="clearError('password')" :errors="errors.password"
                          :type="showPassword ? 'text' : 'password'" placeholder="Пароль" :required="true" :classes="'auth-form-field'" />
            <span class="toggle fa" :class="showPassword ? 'fa-eye' : 'fa-eye-slash'" @click="togglePassword"></span>
        </div>
        <div>
            <custom-input v-model="passwordConfirm" @change="clearError('password')"
                          :type="showPassword ? 'text' : 'password'" placeholder="Повторите пароль" :required="true" :classes="'auth-form-field'" />
        </div>
        <div class="d-grid">
            <button type="submit" class="btn btn-success">Зарегистрироваться</button>
        </div>
    </form>
</template>

<script setup>
import { ref }              from 'vue';
import { useStore }         from 'vuex';
import CustomInput          from '@common/form/CustomInput.vue';
import { ApiRegister }      from '@api/public-auth';
import { useResponseError } from '@composables/useResponseError';

const store                                       = useStore();
const { errors, clearError, parseResponseErrors } = useResponseError();

const registerSuccessMessage = ref(null);
const showPassword           = ref(false);
const login                  = ref('');
const password               = ref('');
const passwordConfirm        = ref('');

const togglePassword = () => {
    showPassword.value = !showPassword.value;
};

const registerAction = () => {
    ApiRegister({
        email                : login.value,
        password             : password.value,
        password_confirmation: passwordConfirm.value,
    }).then(response => {
        registerSuccessMessage.value = response.data;
        store.dispatch('auth/closeModal');
        setTimeout(() => location.reload(), 3000);
    }).catch(response => {
        parseResponseErrors(response);
    });
};
</script>
