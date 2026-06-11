<template>
    <div class="alert alert-success"
         v-if="success">
        Спасибо большое! Сведения о платеже приняты и будут обработаны.
    </div>
    <div class="public-form-card"
         v-if="!success">
        <custom-input v-model="account"
                      :classes="'public-form-field'"
                      @change="clearError('account')"
                      :required="true"
                      :errors="errors.account"
                      :label="'Номер дачи и номер участка (например: 999/1 )'"
                      :disabled="loading || propAccount?.number || propInvoice?.id"
                      @submit="sendForm"
        />
        <custom-input v-model="cost"
                      :classes="'public-form-field'"
                      @change="clearError('cost')"
                      :required="true"
                      :errors="errors.cost"
                      :label="'Сумма платежа'"
                      :disabled="loading"
                      @submit="sendForm"
        />
        <custom-textarea v-model="text"
                         @change="clearError('text')"
                         :required="true"
                         :errors="errors.text"
                         :label="'Комментарий о платеже - когда и за что платили'"
                         :rows="2"
                         :disabled="loading || propInvoice?.id"
                         :classes="'public-form-field'"
        />
        <div class="d-flex justify-content-end small public-form-field"
             v-if="text && text.length">
            <span class="text-secondary">Символов: {{ text.length }}</span>
        </div>
        <custom-input v-model="name"
                      :classes="'public-form-field'"
                      @change="clearError('name')"
                      :errors="errors.name"
                      :label="'Ваше имя (по желанию)'"
                      :disabled="loading || propUser?.email"
                      @submit="sendForm"
        />
        <custom-input v-model="email"
                      :classes="'public-form-field'"
                      @change="clearError('email')"
                      :errors="errors.email"
                      :label="'Эл.почта (по желанию)'"
                      :disabled="loading || propUser?.email"
                      @submit="sendForm"
        />
        <custom-input v-model="phone"
                      :classes="'public-form-field'"
                      @change="clearError('phone')"
                      :errors="errors.phone"
                      :label="'Телефон (по желанию)'"
                      :disabled="loading || propUser?.phone"
                      @submit="sendForm"
        />
        <template v-if="files && files.length">
            <ul class="list-unstyled public-file-queue">
                <li v-for="(file, index) in files"
                    class="d-flex justify-content-between">
                    <div>
                        <button class="btn btn-sm btn-danger"
                                :disabled="loading"
                                @click="removeFile(index)"
                                aria-label="Удалить файл">
                            <i class="fa fa-trash"></i>
                        </button>
                        &nbsp;
                        {{ index + 1 }}. {{ file.name }}
                    </div>
                    <span class="text-secondary">
                        {{ (file.size / (1024 * 1024)).toFixed(2) }}MB
                    </span>
                </li>
            </ul>
            <div class="d-flex justify-content-end small public-form-field">
                <span
                    :class="[fileSizeExceed ? 'text-danger' : 'text-secondary']">Размер файлов: {{ filesSize }}MB</span>
            </div>
        </template>
        <button class="btn btn-outline-secondary"
                @click="chooseFiles"
                :disabled="loading"
                v-if="!fileCountExceed">
            <i class="fa fa-paperclip "></i>&nbsp;Файлы подтверждающие оплату
        </button>
        <input class="d-none"
               type="file"
               ref="fileElem"
               accept="image/*,application/pdf"
               @change="appendFiles"
               multiple>
        <custom-checkbox v-model="consent"
                         :errors="errors.consent"
                         name="consent"
                         classes="public-form-check"
                         @change="clearError('consent')"
                         :label="'Я согласен(на) на обработку персональных данных'" />
        <div class="small mt-1">
            <a :href="privacyUrl">Политика ПДн</a>
            и
            <a :href="consentUrl">согласие на обработку ПДн</a>.
        </div>
        <div class="public-form-actions d-flex justify-content-end">
            <button type="submit"
                    :disabled="isSubmitDisable"
                    v-if="!loading"
                    @click="sendForm"
                    class="btn btn-success">Отправить
            </button>
            <button class="btn border-0"
                    disabled
                    v-else>
                <i class="fa fa-spinner fa-spin"></i> Отправка
            </button>
            <div v-if="isUploading" class="progress mt-2" style="height:6px;">
                <div class="progress-bar progress-bar-striped progress-bar-animated bg-success"
                     role="progressbar"
                     :style="{ width: uploadProgress + '%' }"
                     :aria-valuenow="uploadProgress"
                     aria-valuemin="0"
                     aria-valuemax="100">
                </div>
            </div>
        </div>
    </div>
</template>

<script setup>
import {
    computed,
    ref,
    watch,
}                                    from 'vue';
import CustomInput                   from '@common/form/CustomInput.vue';
import CustomCheckbox                from '@common/form/CustomCheckbox.vue';
import CustomTextarea                from '@common/form/CustomTextarea.vue';
import { useResponseError }          from '@composables/useResponseError';
import { useUploadProgress }         from '@composables/useUploadProgress';
import { useRequestFormDefaults }    from './useRequestFormDefaults';
import { useRequestFormPersistence } from './useRequestFormPersistence';
import { routeUri }                  from '@utils/routeUri.js';
import apiClient                     from '@api/client';
import { makeQuery }                 from '@api/helpers';

const props = defineProps({
    propAccount: {
        type   : Object,
        default: null,
    },
    propUser   : {
        type   : Object,
        default: null,
    },
    propInvoice: {
        type   : Object,
        default: null,
    },
});

const { errors, clearError, parseResponseErrors, clearResponseErrors, showSuccess } = useResponseError();
const { uploadProgress, isUploading, startUpload, onProgress, finishUpload }        = useUploadProgress();

const loading  = ref(false);
const account  = ref('');
const email    = ref('');
const phone    = ref('');
const name     = ref('');
const text     = ref('');
const cost     = ref('');
const files    = ref([]);
const success  = ref(null);
const fileElem = ref(null);
const consent  = ref(false);

const { propUserName, storedRequestValue, resolveContactValue } = useRequestFormDefaults(props);

const filesSize = computed(() => {
    let result = 0;
    files.value.forEach(file => {
        result += file.size;
    });
    return (result / (1024 * 1024)).toFixed(2);
});

const fileSizeExceed  = computed(() => filesSize.value > 20);
const fileCountExceed = computed(() => files.value.length > 4);
const isSubmitDisable = computed(() => !files.value.length || !account.value || !cost.value || !text.value || !consent.value || loading.value || fileSizeExceed.value);
const privacyUrl      = routeUri('privacy');
const consentUrl      = routeUri('personalDataConsent');

account.value = props.propInvoice?.id
    ? props.propInvoice?.account?.number ?? ''
    : resolveContactValue(props.propAccount?.number, 'requestAccount');
email.value   = resolveContactValue(props.propUser?.email, 'requestEmail');
phone.value   = resolveContactValue(props.propUser?.phone, 'requestPhone');
name.value    = propUserName.value ?? storedRequestValue('requestName');
cost.value    = props.propInvoice?.delta ?? storedRequestValue('requestCost');
text.value    = props.propInvoice?.id
    ? 'Оплата по счёту №' + props.propInvoice.id + ' за период "' + (props.propInvoice.period?.name ?? '') + '" за участок ' + (props.propInvoice.account?.number ?? '')
    : storedRequestValue('requestPaymentText');

useRequestFormPersistence({
    requestAccount    : account,
    requestEmail      : email,
    requestPhone      : phone,
    requestName       : name,
    requestPaymentText: text,
    requestCost       : cost,
});

function sendForm () {
    loading.value = true;
    startUpload();
    clearResponseErrors();
    const form = new FormData();
    form.append('email', email.value ? email.value : null);
    form.append('phone', phone.value ? phone.value : null);
    form.append('name', name.value ? name.value : null);
    form.append('account', account.value ? account.value : null);
    form.append('text', text.value ? text.value : null);
    form.append('cost', cost.value ? cost.value : null);
    form.append('invoice', props.propInvoice?.id ? props.propInvoice?.id : null);
    form.append('consent', consent.value ? '1' : '');

    files.value.forEach((file, index) => {
        form.append('file' + index, file);
    });

    apiClient.post(makeQuery('/contacts/requests/payment', {}), form, {
        onUploadProgress: onProgress,
        headers         : { 'Content-Type': 'multipart/form-data' },
    }).then(() => {
        localStorage.removeItem('requestPaymentText');
        success.value = true;
        showSuccess('Платёж принят');
    }).catch(response => {
        parseResponseErrors(response);
    }).finally(() => {
        loading.value = false;
        finishUpload();
    });
}

function chooseFiles () {
    fileElem.value.click();
}

function appendFiles (event) {
    for (let i = 0; i < event.target.files.length; i++) {
        if (!fileCountExceed.value) {
            files.value.push(event.target.files[i]);
        }
    }
}

function removeFile (index) {
    files.value = files.value.filter((_, i) => i !== index);
}
</script>
