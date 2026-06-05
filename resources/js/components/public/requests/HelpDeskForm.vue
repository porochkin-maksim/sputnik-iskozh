<template>
    <div class="alert alert-success text-center" v-if="success">
        Ваша заявка под номером <b>{{ ticketNumber }}</b> принята и будет обработана.
    </div>

    <div v-else class="help-desk-form public-form-card">
        <form @submit.prevent="submitForm" class="public-form-stack">
            <custom-textarea
                v-model="form.description"
                :classes="'public-form-field'"
                :errors="errors.description"
                label="Текст заявки"
                :required="true"
                :disabled="loading"
                @change="clearError('description')"
            />

            <!-- Блок выбора участка -->
            <account-search-select
                v-model="form.accountId"
                :label="'Участок'"
                :placeholder="'Начните вводить и выберите номер...'"
                :error="errors.accountId"
                :disabled="loading"
                :required="true"
                class="public-form-field"
            />

            <custom-input
                v-model="form.name"
                :classes="'public-form-field'"
                :errors="errors.name"
                label="Ваше имя"
                required
                :disabled="loading || !!user"
                @change="clearError('name')"
            />
            <custom-input
                v-model="form.email"
                :classes="'public-form-field'"
                :errors="errors.email"
                label="Эл. почта"
                type="email"
                :disabled="loading || !!user"
                @change="clearError('email')"
            />
            <custom-input
                v-model="form.phone"
                :classes="'public-form-field'"
                :errors="errors.phone"
                label="Телефон"
                :disabled="loading || !!user"
                @change="clearError('phone')"
            />

            <div class="public-form-field">
                <div v-for="(file, idx) in files" :key="idx"
                     class="d-flex justify-content-between align-items-center">
                    <span>{{ file.name }} ({{ (file.size / 1024).toFixed(0) }} КБ)</span>
                    <button type="button" class="btn btn-sm btn-danger" @click="removeFile(idx)" :disabled="loading">
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
import CustomInput                   from '@common/form/CustomInput.vue';
import CustomTextarea                from '@common/form/CustomTextarea.vue';
import CustomCheckbox                from '@common/form/CustomCheckbox.vue';
import AccountSearchSelect           from '@components/shared/accounts/AccountSearchSelect.vue';
import { ApiHelpDeskTicket }         from '@api';
import { useRequestContactDefaults } from './useRequestContactDefaults';
import { useRequestFormPersistence } from './useRequestFormPersistence';
import { routeUri }                  from '@utils/routeUri.js';

const props = defineProps({
    type    : { type: String, required: true },
    category: { type: String, required: true },
    service : { type: String, required: true },
    user    : { type: Object, default: null },
    account : { type: Object, default: null },
});

const { errors, clearError, parseResponseErrors, showSuccess, showInfo } = useResponseError();

const loading      = ref(false);
const success      = ref(false);
const ticketNumber = ref(null);
const files        = ref([]);
const fileInput    = ref(null);

const { userName, storedRequestValue } = useRequestContactDefaults(props.user);

const form = reactive({
    description: '',
    accountId  : null,
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

useRequestFormPersistence({
    requestName : () => form.name,
    requestEmail: () => form.email,
    requestPhone: () => form.phone,
});

// Установка предварительного значения участка из пропа
if (props.account?.id) {
    form.accountId = props.account.id;
}

const totalFileSize = computed(() => files.value.reduce((sum, f) => sum + f.size, 0));

const canSubmit = computed(() => {
    return form.description.trim() !== '' &&
        form.name && form.accountId &&
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
    form.accountId   = props.account?.id || null;
    form.name        = userName.value ?? storedRequestValue('requestName');
    form.email       = props.user?.email ?? storedRequestValue('requestEmail');
    form.phone       = props.user?.phone ?? storedRequestValue('requestPhone');
    form.consent     = false;
    files.value      = [];
    Object.keys(errors).forEach(key => delete errors[key]);
};

const submitForm = async () => {
    if (!canSubmit.value) {
        return;
    }

    loading.value = true;
    Object.keys(errors).forEach(key => delete errors[key]);

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
    if (form.accountId) {
        formData.append('account_id', form.accountId);
    }
    formData.append('consent', form.consent ? '1' : '');

    files.value.forEach((file, idx) => {
        formData.append(`files[${idx}]`, file);
    });

    try {
        const response     = await ApiHelpDeskTicket(props.type, props.category, props.service, {}, formData);
        success.value      = response.data.success;
        ticketNumber.value = response.data.number;
        showSuccess(response.data.message);
        resetForm();
        setTimeout(() => {
            // можно редирект
        }, 3000);
    }
    catch (error) {
        parseResponseErrors(error, errors);
    }
    finally {
        loading.value = false;
    }
};
</script>
