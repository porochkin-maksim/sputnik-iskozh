<template>
    <div v-if="registerSuccessMessage" class="alert alert-success" v-html="registerSuccessMessage" />
    <form v-else @submit.prevent="registerAction">
        <custom-input v-model="login" :errors="errors.login" type="email" placeholder="Эл.почта" :required="true" />
        <div class="mt-3 toggle-parent">
            <custom-input v-model="password" @change="clearError('password')" :errors="errors.password"
                          :type="showPassword ? 'text' : 'password'" placeholder="Пароль" :required="true" />
            <span class="toggle fa" :class="showPassword ? 'fa-eye' : 'fa-eye-slash'" @click="togglePassword"></span>
        </div>
        <div class="mt-3">
            <custom-input v-model="passwordConfirm" @change="clearError('password')"
                          :type="showPassword ? 'text' : 'password'" placeholder="Повторите пароль" :required="true" />
        </div>
        <div class="d-grid my-3">
            <button type="submit" class="btn btn-success">Зарегистрироваться</button>
        </div>
    </form>
</template>

<script setup>
import { ref }              from 'vue';
import { useStore }         from 'vuex';
import CustomInput          from '@common/form/CustomInput.vue';
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
    window.axios.post('/register', {
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