<template>
    <form @submit.prevent="loginAction" class="auth-form-stack">
        <custom-input
            v-model="email"
            @change="clearError('email')"
            :errors="errors.email"
            type="email"
            name="email"
            label="Эл.почта"
            :required="true"
            autocomplete="username"
            :classes="'auth-form-field'"
        />
        <div class="toggle-parent">
            <custom-input
                v-model="password"
                @change="clearError('password')"
                :errors="errors.password"
                :type="showPassword ? 'text' : 'password'"
                label="Пароль"
                name="password"
                :required="true"
                autocomplete="current-password"
                :classes="'auth-form-field'"
            />
            <span
                class="toggle fa"
                :class="showPassword ? 'fa-eye-slash' : 'fa-eye'"
                @click="togglePassword"
                :aria-label="showPassword ? 'Скрыть пароль' : 'Показать пароль'"
            ></span>
        </div>
        <div class="d-flex justify-content-between align-items-center">
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
        <div class="d-grid">
            <button type="submit" class="btn btn-success">Войти</button>
        </div>
    </form>
</template>

<script setup>
import { ref }              from 'vue';
import { useStore }         from 'vuex';
import CustomInput          from '@common/form/CustomInput.vue';
import { useResponseError } from '@composables/useResponseError';
import { ApiLogin }         from '@api';

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
    ApiLogin({}, {
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
