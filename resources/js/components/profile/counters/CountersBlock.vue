<template>
    <div v-if="counters && counters.length">
        <div class="row">
            <div class="col-12 col-md-8 col-lg-6">
                <template v-for="item in counters" :key="item.id">
                    <div class="card mb-2">
                        <div class="card-body">
                            <div class="d-flex flex-sm-row flex-column justify-content-sm-between align-items-start">
                                <div>
                                    <a :href="item.viewUrl"
                                       class="text-decoration-none d-block">
                                        <h5 class="mb-2">Счётчик&nbsp;«{{ item.number }}»&nbsp;</h5>
                                    </a>
                                    <div v-if="item.expireAt">
                                        Поверен до {{ formatDate(item.expireAt) }}
                                    </div>
                                    <file-item :file="item.passport"
                                               v-if="item.passport"
                                               :name="'Паспорт'"
                                    />
                                </div>
                                <div
                                    class="text-end w-sm-25 d-flex flex-row flex-sm-column justify-content-between">
                                    <div>{{ item.value.toLocaleString('ru-RU') }}кВт</div>
                                    <div>от {{ formatDate(item.date) }}</div>
                                </div>
                            </div>
                        </div>
                    </div>
                </template>
            </div>
        </div>
    </div>
    <div v-if="loaded">
        <button class="btn btn-success mt-2"
                v-if="showAddCounterButton"
                @click="addCounter">
            Добавить счётчик
        </button>
    </div>
    <view-dialog v-model:show="showDialog"
                 v-model:hide="hideDialog"
                 @hidden="closeAction"
    >
        <template v-slot:title>Добавление счётчика</template>
        <template v-slot:body>
            <div class="container-fluid">
                <div>
                    <custom-input v-model="form.number"
                                  :errors="errors.number"
                                  type="text"
                                  label="Серийный номер устройства"
                                  :required="true"
                    />
                </div>
                <div class="mt-2">
                    <custom-input v-model="form.increment"
                                  :errors="errors.increment"
                                  type="number"
                                  :min="0"
                                  :step="1"
                                  label="Ежемесячное увеличение показаний на кВт"
                                  :required="true"
                                  @focusout="calculateIncrement"
                    />
                </div>
                <div class="mt-2">
                    <custom-input v-model="form.value"
                                  :errors="errors.value"
                                  type="number"
                                  label="Текущие показания на счётчике"
                                  :required="true"
                    />
                </div>
                <div class="mt-2">
                    <custom-calendar v-model="form.expire_at"
                                     :error="errors.expire_at"
                                     :required="true"
                                     label="Дата истечения поверки"
                    />
                </div>
                <div class="mt-2">
                    <div>
                        <template v-if="form.file">
                            <button class="btn btn-sm btn-danger"
                                    @click="removeFile">
                                <i class="fa fa-trash"></i>
                            </button>
                            &nbsp;
                            {{ form.file.name }}
                        </template>
                        <template v-else>
                            <button class="btn btn-outline-secondary w-100"
                                    @click="chooseFile"
                                    v-if="!form.file">
                                <i class="fa fa-paperclip"></i>&nbsp;Фото счётчика
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
                        <template v-if="form.passportFile">
                            <button class="btn btn-sm btn-danger"
                                    @click="removePassportFile">
                                <i class="fa fa-trash"></i>
                            </button>
                            &nbsp;
                            {{ form.passportFile.name }}
                        </template>
                        <template v-else>
                            <button class="btn btn-outline-secondary w-100"
                                    @click="choosePassportFile"
                                    v-if="!form.passportFile">
                                <i class="fa fa-paperclip"></i>&nbsp;Паспорт счётчика
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
            </div>
        </template>
        <template v-slot:footer>
            <button class="btn btn-success"
                    @click="createAction"
                    :disabled="!canSubmitAction || loading">
                Добавить
            </button>
        </template>
    </view-dialog>
</template>

<script setup>
import {
    ref,
    reactive,
    computed,
    onMounted,
}                           from 'vue';
import { useResponseError } from '@composables/useResponseError';
import { useFormat }        from '@composables/useFormat';
import CustomInput          from '@common/form/CustomInput.vue';
import ViewDialog           from '@common/ViewDialog.vue';
import FileItem             from '@common/files/FileItem.vue';
import CustomCalendar       from '@common/form/CustomCalendar.vue';
import {
    ApiProfileCounterCreate,
    ApiProfileCounterList,
}                           from '@api';

const { errors, parseResponseErrors } = useResponseError();
const { formatDate }                  = useFormat();

const loaded           = ref(false);
const showDialog       = ref(false);
const hideDialog       = ref(false);
const loading          = ref(false);
const counters         = ref([]);
const fileElem         = ref(null);
const filePassportElem = ref(null);

const form = reactive({
    number      : null,
    value       : null,
    expire_at   : null,
    increment   : 0,
    file        : null,
    passportFile: null,
});

onMounted(() => {
    listAction();
});

const listAction = () => {
    ApiProfileCounterList()
        .then(response => {
            counters.value = response.data.counters;
        })
        .catch(response => {
            parseResponseErrors(response);
        })
        .finally(() => {
            loaded.value = true;
        });
};

const createAction = () => {
    if (loading.value) {
        return;
    }

    if (!confirm('Номер счётчика невозможно будет изменить. Продолжить?')) {
        return;
    }

    loading.value = true;
    ApiProfileCounterCreate({}, {
        number      : form.number,
        value       : form.value,
        expireAt    : form.expire_at,
        file        : form.file,
        passportFile: form.passportFile,
        increment   : form.increment,
    }).then(() => {
        onSuccessSubmit();
    })
        .catch(response => {
            parseResponseErrors(response);
        })
        .finally(() => {
            loading.value = false;
        });
};

const onSuccessSubmit = () => {
    showDialog.value = false;
    hideDialog.value = true;
    Object.assign(form, {
        number      : null,
        value       : null,
        expire_at   : null,
        increment   : 0,
        file        : null,
        passportFile: null,
    });
    listAction();
};

const addCounter = () => {
    showDialog.value = true;
};

const closeAction = () => {
    showDialog.value = false;
};

const chooseFile = () => {
    fileElem.value?.click();
};

const appendFile = (event) => {
    form.file = event.target.files[0];
};

const removeFile = () => {
    form.file = null;
};

const choosePassportFile = () => {
    filePassportElem.value?.click();
};

const appendPassportFile = (event) => {
    form.passportFile = event.target.files[0];
};

const removePassportFile = () => {
    form.passportFile = null;
};

const calculateIncrement = () => {
    form.increment = form.increment < 0 ? form.increment * -1 : form.increment;
};

const showAddCounterButton = computed(() => {
    return !counters.value || !counters.value.length || counters.value.length < 1;
});

const canSubmitAction = computed(() => {
    return form.number && form.value && form.file && form.expire_at;
});
</script>