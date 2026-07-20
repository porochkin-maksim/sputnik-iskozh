<template>
    <div class="alert alert-success"
         v-if="success">
        Спасибо большое! Сведения о показаниях приняты и будут обработаны.
    </div>
    <div class="public-form-card"
         v-if="!success">
        <account-search-select v-model="selectedAccountId"
                               classes="public-form-field"
                               :required="true"
                               :error="errors.account"
                               @select="onAccountSelect"
        />
        <custom-input v-model="value"
                      :classes="'public-form-field'"
                      @change="clearError('value')"
                      :required="true"
                      :errors="errors.value"
                      :label="'Показания счётчика'"
                      @submit="sendForm"
        />
        <template v-if="hasCounters">
            <label class="small text-secondary">Счётчик</label>
            <simple-select v-model="counter"
                           class="period"
                           :options="computedCounters"
                           @change="onCounterChange"
            />
        </template>
        <template v-else>
            <custom-input v-model="counter"
                          :classes="'public-form-field'"
                          @change="clearError('counter')"
                          :errors="errors.counter"
                          :label="'Номер счётчика'"
                          @submit="sendForm"
            />
        </template>
        <custom-input v-model="name"
                      :classes="'public-form-field'"
                      @change="clearError('name')"
                      :errors="errors.name"
                      :label="'Ваше имя (по желанию)'"
                      :disabled="propUserName"
                      @submit="sendForm"
        />
        <custom-input v-model="email"
                      :classes="'public-form-field'"
                      @change="clearError('email')"
                      :errors="errors.email"
                      :label="'Эл.почта (по желанию)'"
                      :disabled="propUser?.email"
                      @submit="sendForm"
        />
        <custom-input v-model="phone"
                      :classes="'public-form-field'"
                      @change="clearError('phone')"
                      :errors="errors.phone"
                      :label="'Телефон (по желанию)'"
                      :disabled="propUser?.phone"
                      @submit="sendForm"
        />
        <div class="public-form-field">
            <div v-if="file">
                <button class="btn btn-sm btn-danger"
                        @click="removeFile"
                        aria-label="Удалить файл">
                    <i class="fa fa-trash"></i>
                </button>
                &nbsp;
                {{ file.name }}
            </div>
            <template v-else>
                <button class="btn btn-outline-secondary"
                        @click="chooseFile"
                        v-if="!file">
                    <i class="fa fa-paperclip "></i>&nbsp;Фото счётчика
                </button>
                <input class="d-none"
                       type="file"
                       ref="fileElem"
                       accept="image/*"
                       @change="appendFile"
                />
            </template>
        </div>
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
                    :disabled="disableSubmit"
                    v-if="!pending"
                    @click="sendForm"
                    class="btn btn-success">Отправить
            </button>
            <button class="btn border-0" disabled v-else>
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
import AccountSearchSelect           from '@components/shared/accounts/AccountSearchSelect.vue';
import CustomInput                   from '@common/form/CustomInput.vue';
import CustomCheckbox                from '@common/form/CustomCheckbox.vue';
import SimpleSelect                  from '@common/form/SimpleSelect.vue';
import { useResponseError }          from '@composables/useResponseError';
import { useUploadProgress }         from '@composables/useUploadProgress';
import { useRequestFormDefaults }    from './useRequestFormDefaults';
import { useRequestFormPersistence } from './useRequestFormPersistence';
import { routeUri }                  from '@utils/routeUri.js';
import apiClient                     from '@api/client';
import { makeQuery }                 from '@api/helpers';

const props = defineProps({
    propAccount : {
        type   : Object,
        default: null,
    },
    propUser    : {
        type   : Object,
        default: null,
    },
    propCounters: {
        type   : Array,
        default: () => [],
    },
});

const { errors, clearError, parseResponseErrors, clearResponseErrors, showSuccess } = useResponseError();
const { uploadProgress, isUploading, startUpload, onProgress, finishUpload }        = useUploadProgress();

const email                 = ref('');
const phone                 = ref('');
const name                  = ref('');
const selectedAccountId     = ref(null);
const selectedAccountNumber = ref('');
const counter               = ref('');
const value                 = ref('');
const file                  = ref(null);
const success               = ref(null);
const pending               = ref(false);
const fileElem              = ref(null);
const consent               = ref(false);
const counters              = ref([]);
const countersLoading       = ref(false);

const { propUserName, storedRequestValue, resolveContactValue } = useRequestFormDefaults(props);

const hasCounters   = computed(() => counters.value.length > 0);
const disableSubmit = computed(() => !selectedAccountNumber.value || !value.value || !file.value || !consent.value || pending.value);
const privacyUrl    = routeUri('privacy');
const consentUrl    = routeUri('personalDataConsent');

const computedCounters = computed(() => {
    if (!hasCounters.value) {
        return [];
    }

    return counters.value.map(item => {
        if ('value' in item && 'label' in item) {
            return item;
        }
        return {
            value: item.id,
            label: item.number,
        };
    });
});

const loadCounters = (accountId) => {
    if (!accountId) {
        counters.value = [];
        return;
    }

    countersLoading.value = true;
    apiClient.get(makeQuery('/ajax/selects/counters/' + accountId, {}))
        .then(response => {
            counters.value = response.data ?? [];
            if (counters.value.length) {
                counter.value = counters.value[0]?.id ?? counters.value[0]?.value ?? '';
            } else {
                counter.value = '';
                value.value   = '';
            }
        })
        .catch(() => {
            counters.value = [];
        })
        .finally(() => {
            countersLoading.value = false;
        });
};

if (props.propAccount?.number) {
    selectedAccountNumber.value = props.propAccount.number;
    selectedAccountId.value     = props.propAccount.id ?? null;
    counters.value              = props.propCounters ?? [];
    if (counters.value.length) {
        counter.value = counters.value[0]?.id ?? counters.value[0]?.value ?? '';
        value.value   = counters.value[0]?.value ?? '';
    }
}
else {
    const savedId     = storedRequestValue('requestAccountId');
    const savedNumber = storedRequestValue('requestAccountNumber');
    if (savedId) {
        selectedAccountId.value     = parseInt(savedId);
        selectedAccountNumber.value = savedNumber;
        loadCounters(selectedAccountId.value);
    }
}
email.value = resolveContactValue(props.propUser?.email, 'requestEmail');
phone.value = resolveContactValue(props.propUser?.phone, 'requestPhone');
name.value  = propUserName.value ?? storedRequestValue('requestName');

useRequestFormPersistence({
    requestAccountId    : selectedAccountId,
    requestAccountNumber: selectedAccountNumber,
    requestEmail        : email,
    requestPhone        : phone,
    requestName         : name,
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
    clearError('account');
};

watch(selectedAccountId, (newId) => {
    if (newId && (!props.propAccount || newId !== props.propAccount.id)) {
        loadCounters(newId);
    }
});

function sendForm () {
    pending.value = true;
    startUpload();
    clearResponseErrors();

    const form = new FormData();
    form.append('email', email.value ? email.value : null);
    form.append('phone', phone.value ? phone.value : null);
    form.append('name', name.value ? name.value : null);
    form.append('account', selectedAccountNumber.value ? selectedAccountNumber.value : null);
    form.append('counter', counter.value ? counter.value : null);
    form.append('value', value.value ? value.value : null);
    if (hasCounters.value) {
        form.append('counter_id', counter.value ? counter.value : null);
    }
    else {
        form.append('counter_id', '');
    }

    form.append('file', file.value);
    form.append('consent', consent.value ? '1' : '');

    apiClient.post(makeQuery('/contacts/requests/counter', {}), form, {
        onUploadProgress: onProgress,
        headers         : { 'Content-Type': 'multipart/form-data' },
    }).then(() => {
        success.value = true;
        showSuccess('Показания приняты');
    }).catch(response => {
        parseResponseErrors(response);
    }).finally(() => {
        pending.value = false;
        finishUpload();
    });
}

function chooseFile () {
    fileElem.value.click();
}

function appendFile (event) {
    file.value = event.target.files[0];
}

function removeFile () {
    file.value = null;
}

function onCounterChange () {
    counters.value.forEach(item => {
        const itemId = item.id ?? item.value;
        if (parseInt(itemId) === parseInt(counter.value)) {
            value.value = item.value ?? '';
        }
    });
}
</script>
