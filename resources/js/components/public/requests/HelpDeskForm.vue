<template>
    <div class="alert alert-success text-center" v-if="success">
        Ваша заявка под номером <b>{{ ticketNumber }}</b> принята и будет обработана.
    </div>

    <div v-else class="help-desk-form public-form-card">
        <form @submit.prevent="submitForm" class="public-form-stack">
            <custom-textarea
                v-model="form.description"
                name="description"
                :classes="'public-form-field'"
                :errors="errors.description"
                label="Текст заявки"
                :required="true"
                :disabled="loading"
                @change="clearError('description')"
            />

            <!-- Блок выбора участка -->
            <account-search-select
                v-model="selectedAccountId"
                :label="'Участок'"
                :placeholder="'Начните вводить и выберите номер...'"
                :error="errors.accountId"
                :disabled="loading"
                :required="true"
                class="public-form-field"
                @select="onAccountSelect"
            />

            <custom-input
                v-model="form.name"
                name="name"
                :classes="'public-form-field'"
                :errors="errors.name"
                label="Ваше имя"
                required
                :disabled="loading || !!user"
                @change="clearError('name')"
            />
            <custom-input
                v-model="form.email"
                name="email"
                :classes="'public-form-field'"
                :errors="errors.email"
                label="Эл. почта"
                type="email"
                :disabled="loading || !!user"
                @change="clearError('email')"
            />
            <custom-input
                v-model="form.phone"
                name="phone"
                :classes="'public-form-field'"
                :errors="errors.phone"
                label="Телефон"
                :disabled="loading || !!user"
                @change="clearError('phone')"
            />

            <div class="public-form-field">
                <div v-for="(file, idx) in files" :key="idx"
                     class="d-flex justify-content-between align-items-center mb-2">
                    <span>{{ file.name }} ({{ (file.size / 1024).toFixed(0) }} КБ)</span>
                    <button type="button" class="btn btn-sm btn-danger" @click="removeFile(idx)" :disabled="loading" aria-label="Удалить файл">
                        <i class="fa fa-trash"></i>
                    </button>
                </div>
                <div>
                    <button type="button" class="btn btn-outline-secondary w-100" @click="chooseFiles"
                            :disabled="loading || files.length >= 5 || totalFileSize > 20 * 1024 * 1024">
                        <i class="fa fa-paperclip"></i> Выбрать файлы (не более 5, до 20 МБ суммарно)
                    </button>
                </div>
                <input type="file" ref="fileInput" class="d-none" multiple accept="image/*,application/pdf"
                       @change="handleFileSelect">
                <div v-if="totalFileSize > 20 * 1024 * 1024"
                     class="text-danger small">Общий размер файлов превышает 20 МБ
                </div>
            </div>
            <custom-checkbox
                v-model="form.consent"
                :errors="errors.consent"
                name="consent"
                classes="public-form-check"
                :label="'Я согласен(на) на обработку персональных данных'"
                @change="clearError('consent')"
            />
            <div class="small mt-1">
                <a :href="privacyUrl">Политика ПДн</a>
                и
                <a :href="consentUrl">согласие на обработку ПДн</a>.
            </div>

            <div class="public-form-actions d-flex justify-content-end">
                <button type="submit" class="btn btn-success" :disabled="!canSubmit || loading">
                    <i v-if="loading" class="fa fa-spinner fa-spin"></i>
                    {{ loading ? 'Отправка...' : 'Отправить заявку' }}
                </button>
            </div>
            <div v-if="isUploading" class="progress" style="height:6px;">
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
    reactive,
    computed,
}                                    from 'vue';
import { useResponseError }          from '@composables/useResponseError';
import { useUploadProgress }         from '@composables/useUploadProgress';
import CustomInput                   from '@common/form/CustomInput.vue';
import CustomTextarea                from '@common/form/CustomTextarea.vue';
import CustomCheckbox                from '@common/form/CustomCheckbox.vue';
import AccountSearchSelect           from '@components/shared/accounts/AccountSearchSelect.vue';
import { useRequestContactDefaults } from './useRequestContactDefaults';
import { useRequestFormPersistence } from './useRequestFormPersistence';
import { routeUri }                  from '@utils/routeUri.js';
import apiClient                     from '@api/client';
import { makeQuery }                 from '@api/helpers';

const props = defineProps({
    type    : { type: String, required: true },
    category: { type: String, required: true },
    service : { type: String, required: true },
    user    : { type: Object, default: null },
    account : { type: Object, default: null },
});

const { errors, clearError, clearResponseErrors, parseResponseErrors, showSuccess, showInfo } = useResponseError();
const { uploadProgress, isUploading, startUpload, onProgress, finishUpload } = useUploadProgress();

const loading      = ref(false);
const success      = ref(false);
const ticketNumber = ref(null);
const files        = ref([]);
const fileInput    = ref(null);

const { userName, storedRequestValue } = useRequestContactDefaults(props.user);

const selectedAccountId     = ref(null);
const selectedAccountNumber = ref('');

const form = reactive({
    description: '',
    name       : '',
    email      : '',
    phone      : '',
    consent    : false,
});
const privacyUrl = routeUri('privacy');
const consentUrl = routeUri('personalDataConsent');

form.name  = userName.value ?? storedRequestValue('requestName');
form.email = props.user?.email ?? storedRequestValue('requestEmail');
form.phone = props.user?.phone ?? storedRequestValue('requestPhone');

const savedId     = storedRequestValue('requestAccountId');
const savedNumber = storedRequestValue('requestAccountNumber');
if (savedId) {
    selectedAccountId.value     = parseInt(savedId);
    selectedAccountNumber.value = savedNumber;
}
else if (props.account?.id) {
    selectedAccountId.value     = props.account.id;
    selectedAccountNumber.value = props.account.number;
}

useRequestFormPersistence({
    requestName         : () => form.name,
    requestEmail        : () => form.email,
    requestPhone        : () => form.phone,
    requestAccountId    : selectedAccountId,
    requestAccountNumber: selectedAccountNumber,
});

const onAccountSelect = (item) => {
    if (item) {
        selectedAccountId.value     = item.key;
        selectedAccountNumber.value = item.value;
    }
    else {
        selectedAccountId.value     = null;
        selectedAccountNumber.value = '';
    }
    clearError('accountId');
};

const totalFileSize = computed(() => files.value.reduce((sum, f) => sum + f.size, 0));

const canSubmit = computed(() => {
    return form.description.trim() !== '' &&
        form.name && selectedAccountId.value &&
        (form.email || form.phone) &&
        form.consent &&
        totalFileSize.value <= 20 * 1024 * 1024 &&
        !loading.value;
});

const chooseFiles = () => {
    fileInput.value?.click();
};

const handleFileSelect = (event) => {
    const selected  = Array.from(event.target.files);
    const maxSize   = 20 * 1024 * 1024;
    let currentSize = totalFileSize.value;

    for (const file of selected) {
        if (files.value.length >= 5) {
            showInfo('Можно прикрепить не более 5 файлов');
            break;
        }
        if (currentSize + file.size > maxSize) {
            showInfo('Общий размер файлов не должен превышать 20 МБ');
            break;
        }
        files.value.push(file);
        currentSize += file.size;
    }
    fileInput.value.value = '';
};

const removeFile = (index) => {
    files.value.splice(index, 1);
};

const resetForm = () => {
    form.description = '';
    selectedAccountId.value     = props.account?.id || null;
    selectedAccountNumber.value = props.account?.number || '';
    form.name        = userName.value ?? storedRequestValue('requestName');
    form.email       = props.user?.email ?? storedRequestValue('requestEmail');
    form.phone       = props.user?.phone ?? storedRequestValue('requestPhone');
    form.consent     = false;
    files.value      = [];
    clearResponseErrors();
};

const submitForm = async () => {
    if (!canSubmit.value) {
        return;
    }

    loading.value = true;
    startUpload();
    clearResponseErrors();

    const formData = new FormData();
    formData.append('description', form.description);
    if (form.name) {
        formData.append('name', form.name);
    }
    if (form.email) {
        formData.append('email', form.email);
    }
    if (form.phone) {
        formData.append('phone', form.phone);
    }
    if (selectedAccountId.value) {
        formData.append('account_id', selectedAccountId.value);
    }
    formData.append('consent', form.consent ? '1' : '');

    files.value.forEach((file, idx) => {
        formData.append(`files[${idx}]`, file);
    });

    try {
        const response = await apiClient.post(
            makeQuery('/contacts/requests/help-desk/' + props.type + '/' + props.category + '/' + props.service, {}),
            formData,
            {
                onUploadProgress: onProgress,
                headers: { 'Content-Type': 'multipart/form-data' },
            }
        );
        success.value      = response.data.success;
        ticketNumber.value = response.data.number;
        showSuccess(response.data.message);
        resetForm();
    }
    catch (error) {
        parseResponseErrors(error, errors);
    }
    finally {
        loading.value = false;
        finishUpload();
    }
};
</script>
