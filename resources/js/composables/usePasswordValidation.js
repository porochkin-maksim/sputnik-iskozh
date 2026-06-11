import {
    ref,
    computed,
} from 'vue';
import {
    PASSWORD_MIN_LENGTH,
    PASSWORD_LOWERCASE_PATTERN,
    PASSWORD_UPPERCASE_PATTERN,
    PASSWORD_DIGIT_PATTERN,
} from '@utils/passwordPolicy';

export function usePasswordValidation () {
    const showPassword    = ref(false);
    const password        = ref('');
    const passwordConfirm = ref('');

    const canSubmitPassword = computed(() => {
        if (!password.value || !passwordConfirm.value) {
            return false;
        }

        if (password.value !== passwordConfirm.value) {
            return false;
        }

        if (password.value.length < PASSWORD_MIN_LENGTH) {
            return false;
        }

        if (!PASSWORD_LOWERCASE_PATTERN.test(password.value) || !PASSWORD_UPPERCASE_PATTERN.test(password.value)) {
            return false;
        }

        if (!PASSWORD_DIGIT_PATTERN.test(password.value)) {
            return false;
        }

        return true;
    });

    const passwordHint = computed(() => {
        if (!password.value && !passwordConfirm.value) {
            return 'Введите пароль и повторите его.';
        }

        if (!password.value || !passwordConfirm.value) {
            return 'Заполните оба поля пароля.';
        }

        if (password.value !== passwordConfirm.value) {
            return 'Пароли должны совпадать.';
        }

        if (password.value.length < PASSWORD_MIN_LENGTH) {
            return 'Минимум 8 символов.';
        }

        if (!PASSWORD_LOWERCASE_PATTERN.test(password.value) || !PASSWORD_UPPERCASE_PATTERN.test(password.value)) {
            return 'Нужны строчные и заглавные буквы.';
        }

        if (!PASSWORD_DIGIT_PATTERN.test(password.value)) {
            return 'Нужна хотя бы одна цифра.';
        }

        return 'Пароль подходит.';
    });

    const togglePassword = () => {
        showPassword.value = !showPassword.value;
    };

    return {
        showPassword,
        password,
        passwordConfirm,
        canSubmitPassword,
        passwordHint,
        togglePassword,
    };
}