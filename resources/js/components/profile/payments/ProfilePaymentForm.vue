<template>
    <div class="profile-payment-form">
        <button
            v-if="!showForm"
            type="button"
            class="btn btn-outline-success w-100 d-flex align-items-center justify-content-center gap-2"
            @click="showForm = true"
        >
            <i class="fa fa-plus"></i> Сообщить об оплате
        </button>

        <form v-else @submit.prevent="submitForm">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <strong class="text-dark">Сообщить об оплате</strong>
                <button type="button" class="btn-close" @click="closeForm" aria-label="Закрыть"></button>
            </div>

            <div class="mb-3">
                <custom-input
                    v-model="cost"
                    label="Сумма платежа, ₽"
                    type="number"
                    step="0.01"
                    min="0"
                    :classes="'public-form-field'"
                    :errors="errors.cost"
                    placeholder="0.00"
                    :disabled="loading"
                />
            </div>

            <div class="mb-3">
                <custom-input
                    v-model="name"
                    label="Название платежа"
                    :classes="'public-form-field'"
                    placeholder="Пополнение счёта"
                    :disabled="loading"
                />
            </div>

            <div class="mb-3">
                <div v-if="file"
                     class="d-flex justify-content-between align-items-center mb-2">
                    <span>{{ file.name }} ({{ (file.size / 1024).toFixed(0) }} КБ)</span>
                    <button type="button"
                            class="btn btn-sm btn-danger"
                            @click="file = null"
                            :disabled="loading">
                        <i class="fa fa-trash"></i>
                    </button>
                </div>
                <button type="button"
                        class="btn btn-outline-secondary w-100"
                        @click="chooseFile"
                        :disabled="loading || !!file">
                    <i class="fa fa-paperclip"></i> Прикрепить чек/квитанцию
                </button>
                <input type="file"
                       ref="fileInput"
                       class="d-none"
                       accept="image/*,application/pdf"
                       @change="onFileSelect" />
            </div>

            <div class="d-flex justify-content-end gap-2">
                <button type="button" class="btn btn-outline-secondary" @click="closeForm" :disabled="loading">
                    Отмена
                </button>
                <button type="submit" class="btn btn-success" :disabled="!canSubmit || loading">
                    <i v-if="loading" class="fa fa-spinner fa-spin me-1"></i>
                    {{ loading ? 'Отправка...' : 'Отправить' }}
                </button>
            </div>
            <div v-if="isUploading" class="progress mt-2" style="height:6px;">
                <div class="progress-bar progress-bar-striped progress-bar-animated bg-success"
                     role="progressbar"
                     :style="{ width: uploadProgress + '%' }"
                     :aria-valuenow="uploadProgress"
                     aria-valuemin="0"
                     aria-valuemax="100">
                </div>
            </div>
        </form>
    </div>
</template>

<script setup>
import {
    ref,
    computed,
}                            from 'vue';
import { useResponseError }  from '@composables/useResponseError';
import { useUploadProgress } from '@composables/useUploadProgress';
import CustomInput             from '@common/form/CustomInput.vue';
import apiClient             from '@api/client';

const { errors, clearResponseErrors, parseResponseErrors, showSuccess }      = useResponseError();
const { uploadProgress, isUploading, startUpload, onProgress, finishUpload } = useUploadProgress();

const showForm   = ref(false);
const loading    = ref(false);
const cost       = ref('');
const name       = ref('');
const file       = ref(null);
const fileInput  = ref(null);

const canSubmit  = computed(() => cost.value > 0 && file.value && !loading.value);

const closeForm  = () => {
    showForm.value = false;
    cost.value     = '';
    name.value     = '';
    file.value     = null;
    clearResponseErrors();
};

const chooseFile = () => {
    fileInput.value?.click();
};

const onFileSelect = (event) => {
    file.value = event.target.files[0];
};

const submitForm = async () => {
    if (!canSubmit.value) return;

    loading.value = true;
    startUpload();
    clearResponseErrors();

    const formData = new FormData();
    formData.append('cost', cost.value);
    if (name.value) {
        formData.append('name', name.value);
    }
    formData.append('files[0]', file.value);

    try {
        const response = await apiClient.post('/home/payments/json/send', formData, {
            onUploadProgress: onProgress,
            headers: { 'Content-Type': 'multipart/form-data' },
        });
        showSuccess(response.data.message);
        closeForm();
    }
    catch (error) {
        parseResponseErrors(error);
    }
    finally {
        loading.value = false;
        finishUpload();
    }
};
</script>
