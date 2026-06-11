<template>
    <form @submit.prevent="setAction" class="password auth-form-stack">
        <input type="hidden" name="token" :value="token" />
        <input type="hidden" name="email" :value="email" />

        <div class="toggle-parent">
            <custom-input
                v-model="password"
                @change="clearError('password')"
                :errors="errors.password"
                :type="showPassword ? 'text' : 'password'"
                label="Пароль"
                name="password"
                :required="true"
                autocomplete="new-password"
            />
            <span
                class="toggle fa"
                :class="showPassword ? 'fa-eye-slash' : 'fa-eye'"
                @click="togglePassword"
                :aria-label="showPassword ? 'Скрыть пароль' : 'Показать пароль'"
            ></span>
        </div>
        <div class="toggle-parent">
            <custom-input
                v-model="passwordConfirm"
                @change="clearError('password')"
                :errors="errors.password"
                :type="showPassword ? 'text' : 'password'"
                label="Подтвердите пароль"
                name="password_confirmation"
                :required="true"
                autocomplete="new-password"
            />
        </div>
        <div class="mb-3"
             :class="{ 'text-success': canSubmitPassword, 'text-secondary': !canSubmitPassword }">
            {{ passwordHint }}
        </div>
        <div class="d-grid">
            <button type="submit" class="btn btn-success" :disabled="!canSubmitPassword || loading">
                <i class="fa" :class="loading ? 'fa-spinner fa-spin' : 'fa-save'"></i>
                Установить пароль
            </button>
        </div>
    </form>
</template>

<script setup>
import { ref }                   from 'vue';
import CustomInput               from '@common/form/CustomInput.vue';
import { useResponseError }      from '@composables/useResponseError';
import { usePasswordValidation } from '@composables/usePasswordValidation';
import { ApiPasswordSave }       from '@api';

const props = defineProps({
    token: { type: String, required: true },
    email: { type: String, default: '' },
});

const { errors, clearError, parseResponseErrors } = useResponseError();
const {
          showPassword,
          password,
          passwordConfirm,
          canSubmitPassword,
          passwordHint,
          togglePassword,
      }                                           = usePasswordValidation();

const loading = ref(false);

const setAction = () => {
    if (!canSubmitPassword.value) {
        return;
    }

    loading.value = true;
    ApiPasswordSave({}, {
        email                : props.email,
        token                : props.token,
        password             : password.value,
        password_confirmation: passwordConfirm.value,
    })
        .then(() => {
            window.location.href = '/';
        })
        .catch(response => {
            parseResponseErrors(response);
        })
        .finally(() => {
            loading.value = false;
        });
};
</script>