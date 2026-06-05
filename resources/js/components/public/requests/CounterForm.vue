<template>
    <div class="alert alert-success"
         v-if="success">
        Спасибо большое! Сведения о показаниях приняты и будут обработаны.
    </div>
    <div class="public-form-card"
         v-if="!success">
        <custom-input v-model="account"
                      :classes="'public-form-field'"
                      @change="clearError('account')"
                      :required="true"
                      :errors="errors.account"
                      :label="'Номер дачи и номер участка (например: 999/1 )'"
                      :disabled="propAccount?.number"
                      @submit="sendForm"
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
                        @click="removeFile">
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
import SimpleSelect                  from '@common/form/SimpleSelect.vue';
import { ApiCounterCreate }          from '@api';
import { useResponseError }          from '@composables/useResponseError';
import { useRequestFormDefaults }    from './useRequestFormDefaults';
import { useRequestFormPersistence } from './useRequestFormPersistence';
import { routeUri }                  from '@utils/routeUri.js';

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

const email    = ref('');
const phone    = ref('');
const name     = ref('');
const account  = ref('');
const counter  = ref('');
const value    = ref('');
const file     = ref(null);
const success  = ref(null);
const pending  = ref(false);
const fileElem = ref(null);
const consent  = ref(false);

const { propUserName, storedRequestValue, resolveContactValue } = useRequestFormDefaults(props);

const hasCounters   = computed(() => props.propCounters.length > 0);
const disableSubmit = computed(() => !account.value || !value.value || !file.value || !consent.value || pending.value);
const privacyUrl    = routeUri('privacy');
const consentUrl    = routeUri('personalDataConsent');

const computedCounters = computed(() => {
    if (!hasCounters.value) {
        return [];
    }

    return props.propCounters.map(item => ({
        value: item.id,
        label: item.number,
    }));
});

account.value = resolveContactValue(props.propAccount?.number, 'requestAccount');
email.value   = resolveContactValue(props.propUser?.email, 'requestEmail');
phone.value   = resolveContactValue(props.propUser?.phone, 'requestPhone');
name.value    = propUserName.value ?? storedRequestValue('requestName');

if (hasCounters.value) {
    counter.value = props.propCounters[0]?.id ?? '';
    value.value   = props.propCounters[0]?.value ?? '';
}

useRequestFormPersistence({
    requestAccount: account,
    requestEmail  : email,
    requestPhone  : phone,
    requestName   : name,
});

function sendForm () {
    pending.value = true;
    clearResponseErrors();

    const form = new FormData();
    form.append('email', email.value ? email.value : null);
    form.append('phone', phone.value ? phone.value : null);
    form.append('name', name.value ? name.value : null);
    form.append('account', account.value ? account.value : null);
    form.append('counter', counter.value ? counter.value : null);
    form.append('value', value.value ? value.value : null);
    if (hasCounters.value) {
        form.append('counter_id', counter.value ? counter.value : null);
    }

    form.append('file', file.value);
    form.append('consent', consent.value ? '1' : '');

    ApiCounterCreate({}, form).then(() => {
        success.value = true;
        showSuccess('Показания приняты');

        setTimeout(() => {
            location.reload();
        }, 10000);
    }).catch(response => {
        parseResponseErrors(response);
    }).finally(() => {
        pending.value = false;
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
    props.propCounters.forEach(item => {
        if (parseInt(item.id) === parseInt(counter.value)) {
            value.value = item.value;
        }
    });
}
</script>
