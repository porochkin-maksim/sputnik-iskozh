import { describe, it, expect } from 'vitest';
import { usePasswordValidation } from '../composables/usePasswordValidation';

describe('usePasswordValidation', () => {
    it('starts with empty passwords', () => {
        const { password, passwordConfirm } = usePasswordValidation();

        expect(password.value).toBe('');
        expect(passwordConfirm.value).toBe('');
    });

    it('starts with showPassword false', () => {
        const { showPassword, togglePassword } = usePasswordValidation();

        expect(showPassword.value).toBe(false);
    });

    it('toggles showPassword', () => {
        const { showPassword, togglePassword } = usePasswordValidation();

        togglePassword();
        expect(showPassword.value).toBe(true);

        togglePassword();
        expect(showPassword.value).toBe(false);
    });

    it('returns canSubmitPassword false when both passwords empty', () => {
        const { canSubmitPassword } = usePasswordValidation();

        expect(canSubmitPassword.value).toBe(false);
    });

    it('returns canSubmitPassword false when only password is filled', () => {
        const { password, passwordConfirm, canSubmitPassword } = usePasswordValidation();

        password.value = 'SomePass1';
        expect(canSubmitPassword.value).toBe(false);
    });

    it('returns canSubmitPassword false when passwords do not match', () => {
        const { password, passwordConfirm, canSubmitPassword } = usePasswordValidation();

        password.value        = 'SomePass1';
        passwordConfirm.value = 'SomePass2';
        expect(canSubmitPassword.value).toBe(false);
    });

    it('returns canSubmitPassword false when password is too short', () => {
        const { password, passwordConfirm, canSubmitPassword } = usePasswordValidation();

        password.value        = 'Sh0rt';
        passwordConfirm.value = 'Sh0rt';
        expect(canSubmitPassword.value).toBe(false);
    });

    it('returns canSubmitPassword false when no uppercase letter', () => {
        const { password, passwordConfirm, canSubmitPassword } = usePasswordValidation();

        password.value        = 'lowercase1';
        passwordConfirm.value = 'lowercase1';
        expect(canSubmitPassword.value).toBe(false);
    });

    it('returns canSubmitPassword false when no lowercase letter', () => {
        const { password, passwordConfirm, canSubmitPassword } = usePasswordValidation();

        password.value        = 'UPPERCASE1';
        passwordConfirm.value = 'UPPERCASE1';
        expect(canSubmitPassword.value).toBe(false);
    });

    it('returns canSubmitPassword false when no digit', () => {
        const { password, passwordConfirm, canSubmitPassword } = usePasswordValidation();

        password.value        = 'NoDigitsA';
        passwordConfirm.value = 'NoDigitsA';
        expect(canSubmitPassword.value).toBe(false);
    });

    it('returns canSubmitPassword true for valid password', () => {
        const { password, passwordConfirm, canSubmitPassword } = usePasswordValidation();

        password.value        = 'ValidPass1';
        passwordConfirm.value = 'ValidPass1';
        expect(canSubmitPassword.value).toBe(true);
    });

    describe('passwordHint', () => {
        it('shows initial hint when both fields are empty', () => {
            const { passwordHint } = usePasswordValidation();

            expect(passwordHint.value).toBe('Введите пароль и повторите его.');
        });

        it('shows hint to fill both fields when one is filled', () => {
            const { password, passwordHint } = usePasswordValidation();

            password.value = 'something';
            expect(passwordHint.value).toBe('Заполните оба поля пароля.');
        });

        it('shows mismatch hint when passwords differ', () => {
            const { password, passwordConfirm, passwordHint } = usePasswordValidation();

            password.value        = 'Same1Same';
            passwordConfirm.value = 'Differen';
            expect(passwordHint.value).toBe('Пароли должны совпадать.');
        });

        it('shows length hint when password is too short', () => {
            const { password, passwordConfirm, passwordHint } = usePasswordValidation();

            password.value        = 'Ab1';
            passwordConfirm.value = 'Ab1';
            expect(passwordHint.value).toBe('Минимум 8 символов.');
        });

        it('shows case hint when no uppercase', () => {
            const { password, passwordConfirm, passwordHint } = usePasswordValidation();

            password.value        = 'lowercase1';
            passwordConfirm.value = 'lowercase1';
            expect(passwordHint.value).toBe('Нужны строчные и заглавные буквы.');
        });

        it('shows case hint when no lowercase', () => {
            const { password, passwordConfirm, passwordHint } = usePasswordValidation();

            password.value        = 'UPPERCASE1';
            passwordConfirm.value = 'UPPERCASE1';
            expect(passwordHint.value).toBe('Нужны строчные и заглавные буквы.');
        });

        it('shows digit hint when no digit', () => {
            const { password, passwordConfirm, passwordHint } = usePasswordValidation();

            password.value        = 'NoDigitsA';
            passwordConfirm.value = 'NoDigitsA';
            expect(passwordHint.value).toBe('Нужна хотя бы одна цифра.');
        });

        it('shows ready hint for valid password', () => {
            const { password, passwordConfirm, passwordHint } = usePasswordValidation();

            password.value        = 'ValidPass1';
            passwordConfirm.value = 'ValidPass1';
            expect(passwordHint.value).toBe('Пароль подходит.');
        });
    });
});