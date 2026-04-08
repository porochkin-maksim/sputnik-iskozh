<template>
    <div class="row">
        <div class="col-lg-6 col-12">
            <div class="card mb-2">
                <div class="card-header bg-white">
                    <h5 class="m-0">Информация</h5>
                </div>
                <div class="card-body">
                    <template v-if="account.actions?.edit">
                        <div class="row">
                            <div class="col-6">
                                <custom-input v-model="formData.number"
                                              :required="true"
                                              :disabled="loading"
                                              :label="'Номер участка'"
                                />
                            </div>
                            <div class="col-6">
                                <custom-input v-model="formData.size"
                                              :required="true"
                                              :disabled="loading"
                                              :label="'Площадь (м²)'"
                                              :type="'number'"
                                              :min="0"
                                              :step="1"
                                />
                            </div>
                        </div>
                        <div>
                            <div class="mt-2">
                                <custom-checkbox v-model="formData.isInvoicing"
                                                 :disabled="loading"
                                                 :label="'Выставлять счета'"
                                                 switch-style
                                />
                            </div>
                            <div>
                                <custom-input v-model="formData.cadastreNumber"
                                              :disabled="loading"
                                              :label="'Кадастровый номер'"
                                />
                            </div>
                        </div>
                    </template>
                    <template v-else>
                        <h6>Данные участка</h6>
                        <account-info-list :account="account" />
                    </template>
                </div>
                <div class="card-footer bg-white">
                    <div class="d-flex align-items-center justify-content-between">
                        <div class="d-flex">
                            <button class="btn btn-success me-2"
                                    :disabled="!canSave || loading"
                                    @click="saveAction">
                                <i class="fa"
                                   :class="loading ? 'fa-spinner fa-spin' : 'fa-save'"></i>
                                Сохранить
                            </button>
                        </div>
                        <div class="d-flex">
                            <history-btn class="btn-link underline-none"
                                         :url="account.historyUrl" />
                        </div>
                    </div>
                </div>
            </div>
            <div class="mb-2">
                <users-block :account="account" :users="account.users" />
            </div>
        </div>
        <div class="col-lg-6 col-12 mb-2">
            <counters-block :account="account" />
        </div>
    </div>
    <div class="row">
        <div class="col-12">
            <invoices-block :account="account" />
        </div>
    </div>
</template>

<script setup>
import {
    ref,
    reactive,
    computed,
    onMounted,
    defineOptions,
}                              from 'vue';
import { useResponseError }    from '@composables/useResponseError';
import CustomInput             from '../../common/form/CustomInput.vue';
import CustomCheckbox          from '../../common/form/CustomCheckbox.vue';
import HistoryBtn              from '../../common/HistoryBtn.vue';
import CountersBlock           from './counters/CountersBlock.vue';
import AccountInfoList         from './AccountInfoList.vue';
import InvoicesBlock           from './invoices/InvoicesBlock.vue';
import UsersBlock              from './users/UsersBlock.vue';
import { ApiAdminAccountSave } from '@api';

defineOptions({
    name: 'AccountItemView',
});

const props = defineProps({
    modelValue: {
        type    : Object,
        required: true,
    },
});

const { parseResponseErrors, showInfo, showDanger } = useResponseError();

// Состояния
const loading = ref(false);
const account = ref({});

// Форма
const formData = reactive({
    id            : null,
    number        : '',
    size          : null,
    isInvoicing   : false,
    cadastreNumber: '',
});

// Инициализация данных
const initForm = () => {
    account.value           = props.modelValue;
    formData.id             = account.value.id;
    formData.number         = account.value.number || '';
    formData.size           = account.value.size || null;
    formData.isInvoicing    = account.value.isInvoicing || false;
    formData.cadastreNumber = account.value.cadastreNumber || '';
};

// Сохранение участка
const saveAction = async () => {
    loading.value = true;

    const form = new FormData();
    form.append('id', formData.id);
    form.append('number', formData.number);
    form.append('size', parseInt(formData.size ? formData.size : 0));
    form.append('is_invoicing', !!formData.isInvoicing);
    form.append('cadastreNumber', formData.cadastreNumber);

    try {
        const response = await ApiAdminAccountSave({}, form);

        // Обновляем данные участка
        account.value = {
            ...account.value,
            ...response.data,
        };

        showInfo('Участок обновлён');
    }
    catch (error) {
        const text = error?.response?.data?.message || 'Не получилось сохранить участок';
        showDanger(text);
        parseResponseErrors(error);
    }
    finally {
        loading.value = false;
    }
};

// Валидация формы
const canSave = computed(() => {
    return formData.number && formData.size !== null && formData.size >= 0;
});

onMounted(() => {
    initForm();
});
</script>