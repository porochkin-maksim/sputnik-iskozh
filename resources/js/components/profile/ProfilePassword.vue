<template>
    <div class="form">
        <table class="table table-responsive align-middle password">
            <tbody>
            <tr>
                <td class="toggle-parent">
                    <custom-input
                        v-model="password"
                        @change="clearError('password')"
                        :errors="errors.password"
                        :type="showPassword ? 'text' : 'password'"
                        label="Пароль"
                        :required="true"
                    />
                    <span
                        class="toggle fa"
                        :class="showPassword ? 'fa-eye' : 'fa-eye-slash'"
                        @click="togglePassword"
                    ></span>
                </td>
            </tr>
            <tr>
                <td>
                    <custom-input
                        v-model="passwordConfirm"
                        @change="clearError('password')"
                        :type="showPassword ? 'text' : 'password'"
                        label="Повторите пароль"
                        :required="true"
                    />
                </td>
            </tr>
            </tbody>
        </table>
    </div>
    <div class="d-flex justify-content-center mb-3">
        <button class="btn btn-success" @click="updatePassword">
            <i class="fa fa-save"></i> Сменить пароль
        </button>
    </div>
</template>

<script setup>
import {
    ref,
    defineProps,
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

const { errors, clearError, parseResponseErrors, showSuccess } = useResponseError();

const password        = ref(props.user.password || null);
const passwordConfirm = ref(null);
const showPassword    = ref(false);

const togglePassword = () => {
    showPassword.value = !showPassword.value;
};

const updatePassword = () => {
    ApiProfileSavePassword({}, {
        password             : password.value,
        password_confirmation: passwordConfirm.value,
    })
        .then(() => {
            password.value        = null;
            passwordConfirm.value = null;
            showPassword.value    = false;
            showSuccess('Пароль изменён');
        })
        .catch(response => {
            parseResponseErrors(response);
        });
};
</script>

<style scoped>
.table tr th {
    text-align : right;
    width      : 50px;
}
</style>