<template>
    <view-dialog v-model:show="showDialog"
                 v-model:hide="hideDialog"
                 @hidden="closeAction"
    >
        <template v-slot:title>{{ localCounter?.id ? 'Редактирование счётчика' : 'Добавление счётчика' }}</template>
        <template v-slot:body>
            <div class="container-fluid">
                <div>
                    <custom-input v-model="localCounter.number"
                                  :errors="errors.number"
                                  :type="'text'"
                                  :label="'Серийный номер устройства'"
                                  :required="true"
                    />
                </div>
                <div class="mt-2">
                    <custom-checkbox v-model="localCounter.isInvoicing"
                                     :label="'Выставлять счета'"
                                     switch-style
                    />
                </div>
                <div class="mt-2"
                     v-if="!localCounter?.id">
                    <custom-input v-model="localCounter.value"
                                  :errors="errors.value"
                                  :type="'number'"
                                  :label="'Текущие показания на счётчике'"
                                  :required="true"
                    />
                </div>
                <div class="mt-2">
                    <custom-calendar v-model="localCounter.expireAt"
                                     :error="errors.expireAt"
                                     :required="true"
                                     :label="'Дата истечения поверки'"
                    />
                </div>
                <div class="mt-2">
                    <custom-input v-model="localCounter.increment"
                                  :errors="errors.increment"
                                  :type="'number'"
                                  :min="0"
                                  :step="1"
                                  :label="'Ежемесячное увеличение показаний на кВт'"
                                  :required="true"
                                  @focusout="calculateIncrement"
                    />
                </div>
                <div class="mt-2"
                     v-if="!localCounter?.id">
                    <div v-if="file">
                        <button class="btn btn-sm btn-danger"
                                @click="removeFile">
                            <i class="fa fa-trash"></i>
                        </button>
                        &nbsp;
                        {{ file.name }}
                    </div>
                    <template v-else>
                        <button class="btn btn-outline-secondary w-100"
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
                <div class="mt-2">
                    <template v-if="passportFile">
                        <button class="btn btn-sm btn-danger"
                                @click="removePassportFile">
                            <i class="fa fa-trash"></i>
                        </button>
                        &nbsp;
                        {{ passportFile.name }}
                    </template>
                    <template v-else>
                        <button class="btn btn-outline-secondary w-100"
                                @click="choosePassportFile"
                                v-if="!passportFile">
                            <i class="fa fa-paperclip "></i>&nbsp;Паспорт счётчика
                        </button>
                        <input class="d-none"
                               type="file"
                               ref="filePassportElem"
                               accept="image/*, application/pdf"
                               @change="appendPassportFile"
                        />
                    </template>
                </div>
            </div>
        </template>
        <template v-slot:footer>
            <button class="btn btn-success"
                    @click="saveAction"
                    :disabled="!canSubmitAction">
                {{ localCounter?.id ? 'Сохранить' : 'Добавить' }}
            </button>
        </template>
    </view-dialog>
</template>

<script setup>
import {
    ref,
    reactive,
    computed,
    watch,
}                              from 'vue';
import CustomInput             from '../../../common/form/CustomInput.vue';
import CustomCheckbox          from '../../../common/form/CustomCheckbox.vue';
import ViewDialog              from '../../../common/ViewDialog.vue';
import CustomCalendar          from '../../../common/form/CustomCalendar.vue';
import { useResponseError }    from '@composables/useResponseError';
import { ApiAdminCounterSave } from '@api';

const props = defineProps({
    account : {
        type   : Object,
        default: null,
    },
    counter : {
        type   : Object,
        default: null,
    },
    showForm: {
        type   : Boolean,
        default: false,
    },
});

const emit = defineEmits(['counterUpdated']);

const { parseResponseErrors, showInfo } = useResponseError();

// Состояния
const loading          = ref(false);
const showDialog       = ref(false);
const hideDialog       = ref(false);
const file             = ref(null);
const passportFile     = ref(null);
const fileElem         = ref(null);
const filePassportElem = ref(null);

// Локальный объект счётчика
const localCounter = reactive({
    id         : null,
    number     : null,
    isInvoicing: false,
    increment  : 0,
    value      : null,
    expireAt   : null,
});

// Ошибки валидации
const errors = reactive({
    number   : null,
    increment: null,
    value    : null,
    expireAt : null,
});

// Инициализация формы
const initForm = () => {
    if (props.counter) {
        Object.assign(localCounter, props.counter);
    }
    else {
        localCounter.id          = null;
        localCounter.number      = null;
        localCounter.isInvoicing = false;
        localCounter.increment   = 0;
        localCounter.value       = null;
        localCounter.expireAt    = null;
    }
    showDialog.value = props.showForm;
};

// Валидация формы
const canSubmitAction = computed(() => {
    return localCounter.number && (localCounter.id || localCounter.value);
});

// Сохранение счётчика
const saveAction = async () => {
    loading.value    = true;
    // Сброс ошибок
    errors.number    = null;
    errors.increment = null;
    errors.value     = null;
    errors.expireAt  = null;

    const form = new FormData();
    form.append('id', localCounter.id);
    form.append('number', localCounter.number);
    form.append('isInvoicing', localCounter.isInvoicing);
    if (localCounter.value !== undefined && localCounter.value !== null) {
        form.append('value', localCounter.value);
    }
    form.append('expireAt', localCounter.expireAt);
    form.append('increment', localCounter.increment);
    if (file.value) {
        form.append('file', file.value);
    }
    if (passportFile.value) {
        form.append('passportFile', passportFile.value);
    }

    try {
        await ApiAdminCounterSave(props.account.id, {}, form);
        onSuccessSubmit();
        const message = localCounter.id ? 'Счётчик обновлён' : 'Счётчик добавлен';
        showInfo(message);
    }
    catch (error) {
        parseResponseErrors(error);
    }
    finally {
        loading.value = false;
    }
};

const onSuccessSubmit = () => {
    showDialog.value   = false;
    hideDialog.value   = true;
    file.value         = null;
    passportFile.value = null;
    emit('counterUpdated');
};

const closeAction = () => {
    showDialog.value = false;
    emit('counterUpdated');
};

// Работа с файлами (фото)
const chooseFile = () => {
    fileElem.value?.click();
};

const appendFile = (event) => {
    file.value = event.target.files[0];
};

const removeFile = () => {
    file.value = null;
};

// Работа с паспортом
const choosePassportFile = () => {
    filePassportElem.value?.click();
};

const appendPassportFile = (event) => {
    passportFile.value = event.target.files[0];
};

const removePassportFile = () => {
    passportFile.value = null;
};

// Корректировка инкремента
const calculateIncrement = () => {
    if (localCounter.increment < 0) {
        localCounter.increment = -localCounter.increment;
    }
};

// Следим за showForm из props
watch(() => props.showForm, (newVal) => {
    if (newVal) {
        initForm();
    }
});

// Инициализация
initForm();
</script>