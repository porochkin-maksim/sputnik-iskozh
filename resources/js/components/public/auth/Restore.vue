<template>
    <div v-if="successMessage" class="alert alert-success" v-html="successMessage" />
    <form v-else @submit.prevent="restoreAction" class="auth-form-stack">
        <custom-input
            v-model="login"
            :errors="errors.email"
            @change="clearError('email')"
            type="email"
            label="Эл.почта"
            :required="true"
            autocomplete="email"
        />
        <div class="d-grid">
            <button type="submit" class="btn btn-success btn-block text-uppercase rounded-pill shadow-sm">
                Восстановить
            </button>
        </div>
    </form>
</template>

<script setup>
import { ref }              from 'vue';
import CustomInput          from '@common/form/CustomInput.vue';
import { useResponseError } from '@composables/useResponseError';
import { ApiPasswordEmail } from '@api';

const { errors, clearError, parseResponseErrors } = useResponseError();

const successMessage = ref(null);
const login          = ref('');

const restoreAction = () => {
    ApiPasswordEmail({}, {
        email: login.value,
    })
        .then(response => {
            successMessage.value = response.data.message;
        })
        .catch(response => {
            parseResponseErrors(response);
        });
};
</script>
