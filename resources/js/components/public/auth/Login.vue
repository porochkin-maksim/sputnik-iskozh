<template>
    <form @submit.prevent="loginAction">
        <custom-input
            v-model="email"
            @change="clearError('email')"
            :errors="errors.email"
            type="email"
            name="email"
            label="Эл.почта"
            :required="true"
            autocomplete="username"
        />
        <div class="mt-3 toggle-parent">
            <custom-input
                v-model="password"
                @change="clearError('password')"
                :errors="errors.password"
                :type="showPassword ? 'text' : 'password'"
                label="Пароль"
                name="password"
                :required="true"
                autocomplete="current-password"
            />
            <span
                class="toggle fa"
                :class="showPassword ? 'fa-eye-slash' : 'fa-eye'"
                @click="togglePassword"
            ></span>
        </div>
        <div class="mt-3 d-flex justify-content-between align-items-center">
            <div class="form-check">
                <input
                    v-model="remember"
                    type="checkbox"
                    class="form-check-input"
                    id="remember"
                />
                <label for="remember" class="form-check-label">Запомнить</label>
            </div>
            <slot name="restore"></slot>
        </div>
        <div class="d-grid my-3">
            <button type="submit" class="btn btn-success">Войти</button>
        </div>
    </form>
</template>

<script setup>
import { ref }              from 'vue';
import { useStore }         from 'vuex';
import Url                  from '@utils/Url.js';
import CustomInput          from '@common/form/CustomInput.vue';
import { useResponseError } from '@composables/useResponseError';

const store                                       = useStore();
const { errors, clearError, parseResponseErrors } = useResponseError();

const showPassword = ref(false);
const email        = ref('');
const password     = ref('');
const remember     = ref(true);

const togglePassword = () => {
    showPassword.value = !showPassword.value;
};

const loginAction = () => {
    Url.RouteFunctions.login({}, {
        email   : email.value,
        password: password.value,
        remember: remember.value,
    })
        .then(() => {
            store.dispatch('auth/closeModal');
            location.reload();
        })
        .catch(response => {
            parseResponseErrors(response);
        });
};
</script>