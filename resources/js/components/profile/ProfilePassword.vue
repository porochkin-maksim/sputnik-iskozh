<template>
    <div class="form profile-password-form">
        <div class="profile-password-form__field">
            <div class="profile-password-input-wrap">
                <custom-input
                    v-model="password"
                    @change="clearError('password')"
                    :errors="errors.password"
                    :type="showPassword ? 'text' : 'password'"
                    label="Пароль"
                    :required="true"
                    :classes="'profile-password-field'"
                />
                <span
                    class="toggle fa"
                    :class="showPassword ? 'fa-eye' : 'fa-eye-slash'"
                    @click="togglePassword"
                    :aria-label="showPassword ? 'Скрыть пароль' : 'Показать пароль'"
                ></span>
                <button class="profile-password-copy btn btn-sm btn-outline-success"
                        type="button"
                        :disabled="!password"
                        @click="copyPassword"
                        aria-label="Скопировать пароль">
                    <i class="fa fa-copy"></i>
                </button>
            </div>
        </div>
        <div class="profile-password-form__field mt-2">
            <custom-input
                v-model="passwordConfirm"
                @change="clearError('password')"
                :type="showPassword ? 'text' : 'password'"
                label="Повторите пароль"
                :required="true"
                :classes="'profile-password-field'"
            />
        </div>
        <div class="profile-password-form__hint mt-2"
             :class="{ 'text-success': canSubmitPassword, 'text-secondary': !canSubmitPassword }">
            {{ passwordHint }}
        </div>
    </div>
    <div class="d-flex justify-content-center pt-3">
        <button class="btn btn-success px-4"
                :disabled="!canSubmitPassword || loading"
                @click="updatePassword">
            <i class="fa fa-save"></i> Сменить пароль
        </button>
    </div>
</template>

<script setup>
import { ref, defineProps, defineEmits } from 'vue';
import { useResponseError }            from '@composables/useResponseError';
import { usePasswordValidation }       from '@composables/usePasswordValidation';
import CustomInput                     from '@common/form/CustomInput.vue';
import { ApiProfileSavePassword }      from '@api';

const props = defineProps({
    user: {
        type    : Object,
        required: true,
    },
});

const emit = defineEmits(['update:password']);

const { errors, clearError, parseResponseErrors, showSuccess } = useResponseError();

const loading = ref(false);

const {
    showPassword,
    password,
    passwordConfirm,
    canSubmitPassword,
    passwordHint,
    togglePassword,
} = usePasswordValidation();

const copyPassword = async () => {
    if (!password.value) {
        return;
    }

    await navigator.clipboard.writeText(password.value);
};

const updatePassword = () => {
    if (!canSubmitPassword.value || loading.value) {
        return;
    }

    loading.value = true;
    ApiProfileSavePassword({}, {
        password             : password.value,
        password_confirmation: passwordConfirm.value,
    })
        .then(() => {
            password.value        = null;
            passwordConfirm.value = null;
            showPassword.value    = false;
            showSuccess('Пароль изменён');
            emit('update:password');
        })
        .catch(response => {
            parseResponseErrors(response);
        })
        .finally(() => {
            loading.value = false;
        });
};
</script>
