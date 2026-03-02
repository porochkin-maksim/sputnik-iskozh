<template>
    <div v-if="successMessage" class="alert alert-success" v-html="successMessage" />
    <form v-else @submit.prevent="restoreAction">
        <custom-input
            v-model="login"
            :errors="errors.email"
            @change="clearError('email')"
            type="email"
            label="Эл.почта"
            :required="true"
            autocomplete="email"
        />
        <div class="d-grid mt-3">
            <button type="submit" class="btn btn-success btn-block text-uppercase mb-2 rounded-pill shadow-sm">
                Восстановить
            </button>
        </div>
    </form>
</template>

<script setup>
import { ref }              from 'vue';
import CustomInput          from '@common/form/CustomInput.vue';
import { useResponseError } from '@composables/useResponseError';
import Url                  from '@utils/Url.js';

const { errors, clearError, parseResponseErrors } = useResponseError();

const successMessage = ref(null);
const login          = ref('');

const restoreAction = () => {
    Url.RouteFunctions.passwordEmail({}, {
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