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
                ></span>
                <button class="profile-password-copy btn btn-sm btn-outline-success"
                        type="button"
                        :disabled="!password"
                        @click="copyPassword">
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
import {
    ref,
    computed,
    defineProps,
    defineEmits,
}                                 from 'vue';
import { useResponseError }       from '@composables/useResponseError';
import CustomInput                from '@common/form/CustomInput.vue';
import { ApiProfileSavePassword } from '@api';

const props = defineProps({
    user: {
        type    : Object,
        required: true,
    },
});

const emit = defineEmits(['update:password']);

const { errors, clearError, parseResponseErrors, showSuccess } = useResponseError();

const password        = ref(props.user.password || null);
const passwordConfirm = ref(null);
const showPassword    = ref(false);
const loading         = ref(false);

const canSubmitPassword = computed(() => {
    if (!password.value || !passwordConfirm.value) {
        return false;
    }

    if (password.value !== passwordConfirm.value) {
        return false;
    }

    if (password.value.length < 8) {
        return false;
    }

    if (!/[a-z]/.test(password.value) || !/[A-Z]/.test(password.value)) {
        return false;
    }

    if (!/\d/.test(password.value)) {
        return false;
    }

    return true;
});

const passwordHint = computed(() => {
    if (!password.value && !passwordConfirm.value) {
        return 'Введите пароль и повторите его, чтобы сохранить изменения.';
    }

    if (!password.value || !passwordConfirm.value) {
        return 'Заполните оба поля пароля.';
    }

    if (password.value !== passwordConfirm.value) {
        return 'Пароли должны совпадать.';
    }

    if (password.value.length < 8) {
        return 'Минимум 8 символов.';
    }

    if (!/[a-z]/.test(password.value) || !/[A-Z]/.test(password.value)) {
        return 'Нужны строчные и заглавные буквы.';
    }

    if (!/\d/.test(password.value)) {
        return 'Нужна хотя бы одна цифра.';
    }

    return 'Пароль готов к сохранению.';
});

const togglePassword = () => {
    showPassword.value = !showPassword.value;
};

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
