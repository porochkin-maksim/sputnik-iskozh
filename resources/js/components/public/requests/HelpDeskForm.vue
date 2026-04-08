<template>
    <div class="alert alert-success text-center" v-if="success">
        Ваша заявка под номером <b>{{ ticketNumber }}</b> принята и будет обработана.
    </div>

    <div v-else class="help-desk-form">
        <form @submit.prevent="submitForm">
            <custom-textarea
                v-model="form.description"
                :classes="'my-3'"
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
                class="my-3"
            />

            <custom-input
                v-model="form.name"
                :classes="'my-3'"
                :errors="errors.name"
                label="Ваше имя"
                required
                :disabled="loading || !!user"
                @change="clearError('name')"
            />
            <custom-input
                v-model="form.email"
                :classes="'my-3'"
                :errors="errors.email"
                label="Эл. почта"
                type="email"
                :disabled="loading || !!user"
                @change="clearError('email')"
            />
            <custom-input
                v-model="form.phone"
                :classes="'my-3'"
                :errors="errors.phone"
                label="Телефон"
                :disabled="loading || !!user"
                @change="clearError('phone')"
            />

            <div class="mb-3">
                <div v-for="(file, idx) in files" :key="idx"
                     class="d-flex justify-content-between align-items-center mb-2">
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
                     class="text-danger small mt-1">Общий размер файлов превышает 20 МБ
                </div>
            </div>

            <div class="d-flex justify-content-end mt-3">
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
    onMounted,
}                            from 'vue';
import { useResponseError }  from '@composables/useResponseError';
import CustomInput           from '@form/CustomInput.vue';
import CustomTextarea        from '@form/CustomTextarea.vue';
import AccountSearchSelect   from '@common/app/AccountSearchSelect.vue';
import { ApiHelpDeskTicket } from '@api';

const props = defineProps({
    type    : { type: String, required: true },
    category: { type: String, required: true },
    service : { type: String, required: true },
    user    : { type: Object, default: null },
    account : { type: Object, default: null },
});

const { errors, clearError, parseResponseErrors, showSuccess, showInfo } = useResponseError();

const loading       = ref(false);
const success       = ref(false);
const ticketNumber  = ref(null);
const formSubmitted = ref(false);
const files         = ref([]);
const fileInput     = ref(null);

const form = reactive({
    description: '',
    accountId  : null,
    name       : '',
    email      : '',
    phone      : '',
});

// Заполнение данными пользователя, если авторизован
if (props.user) {
    form.name  = (props.user?.lastName + ' ' + props.user?.firstName + ' ' + props.user?.middleName).replace(/null/g, '').trim() || '';
    form.email = props.user.email || '';
    form.phone = props.user.phone || '';
}

// Установка предварительного значения участка из пропа
if (props.account?.id) {
    form.accountId = props.account.id;
}

const totalFileSize = computed(() => files.value.reduce((sum, f) => sum + f.size, 0));

const canSubmit = computed(() => {
    return form.description.trim() !== '' &&
        form.name && form.accountId &&
        (form.email || form.phone) &&
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
    form.name        = props.user ? (props.user?.lastName + ' ' + props.user?.firstName + ' ' + props.user?.middleName).replace(/null/g, '').trim() || '' : '';
    form.email       = props.user?.email || '';
    form.phone       = props.user?.phone || '';
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

    files.value.forEach((file, idx) => {
        formData.append(`files[${idx}]`, file);
    });

    try {
        const response      = await ApiHelpDeskTicket(props.type, props.category, props.service, {}, formData);
        success.value       = response.data.success;
        ticketNumber.value  = response.data.number;
        formSubmitted.value = true;
        showSuccess(response.data.message);
        resetForm();
        setTimeout(() => {
            // можно редирект
        }, 3000);
    }
    catch (error) {
        parseResponseErrors(error, errors);
        formSubmitted.value = false;
    }
    finally {
        loading.value = false;
    }
};

onMounted(() => {
    // Если участок был передан как проп, он уже установлен в form.accountId выше
});
</script>