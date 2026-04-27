<template>
    <div class="help-desk-ticket-view">
        <div v-if="ticketData" class="row">
            <!-- Левая колонка: основная информация -->
            <div class="col-6">
                <div class="card mb-3">
                    <div class="card-header bg-white d-flex justify-content-between align-items-center">
                        <h5 class="mb-0">Заявка №{{ ticketData.id }} от {{ formatDate(ticketData.created_at) }}</h5>
                        <button v-if="canDelete" class="btn btn-sm btn-outline-danger" @click="deleteTicket">
                            <i class="fa fa-trash"></i> Удалить
                        </button>
                    </div>
                    <form @submit.prevent="saveTicket">
                        <div class="card-body">
                            <div>
                                <custom-textarea
                                    v-model="editForm.description"
                                    label="Описание заявки"
                                    required
                                    :errors="errors.description"
                                    :disabled="!canEdit"
                                    @change="clearError('description')"
                                />
                            </div>
                        </div>
                        <div class="card-header bg-white border-top">
                            <h6 class="mb-0">Состояние</h6>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-6">
                                    <div>
                                        <custom-select
                                            v-model="editForm.status"
                                            :options="statusOptions"
                                            label="Статус"
                                            :errors="errors.status"
                                            :disabled="!canEdit"
                                            @change="clearError('status')"
                                        />
                                    </div>
                                    <div class="mt-2">
                                        <custom-select
                                            v-model="editForm.priority"
                                            :options="priorityOptions"
                                            label="Приоритет"
                                            :errors="errors.priority"
                                            :disabled="!canEdit"
                                            @change="clearError('priority')"
                                        />
                                    </div>
                                </div>
                                <div class="col-6">
                                    <div>
                                        <custom-select
                                            v-model="editForm.type"
                                            :options="typeOptions"
                                            label="Тип"
                                            :errors="errors.type"
                                            :disabled="!canEdit"
                                            @change="onTypeChange"
                                        />
                                    </div>
                                    <div class="mt-2">
                                        <custom-select
                                            v-model="editForm.category_id"
                                            :options="categoryOptions"
                                            label="Категория"
                                            :errors="errors.category_id"
                                            :disabled="!canEdit"
                                            @change="onCategoryChange"
                                        />
                                    </div>
                                    <div class="mt-2">
                                        <custom-select
                                            v-model="editForm.service_id"
                                            :options="serviceOptions"
                                            label="Услуга"
                                            :errors="errors.service_id"
                                            :disabled="!canEdit"
                                            @change="clearError('service_id')"
                                        />
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="card-header bg-white border-top">
                            <h6 class="mb-0">Результат работы</h6>
                        </div>
                        <div class="card-body">
                            <custom-textarea
                                v-model="editForm.result"
                                label="Ответ"
                                :errors="errors.result"
                                :disabled="!canEdit"
                            />

                            <div class="mt-3">
                                <file-uploader
                                    ref="resultFilesUploader"
                                    :existing-files="existingResultFiles"
                                    :editable="canEdit"
                                    button-text="Прикрепить файлы к ответу"
                                    existing-files-label="Файлы ответа"
                                    :max-files="5"
                                    :max-total-size="10 * 1024 * 1024"
                                    accept="image/*,application/pdf"
                                    @update:files="onResultFilesUpdate"
                                    @delete-file="deleteResultFile"
                                />
                            </div>
                        </div>
                        <div class="card-footer">
                            <div v-if="canEdit" class="d-flex gap-2">
                                <button type="submit" class="btn btn-primary" :disabled="saving">
                                    <i v-if="saving" class="fa fa-spinner fa-spin"></i>
                                    {{ saving ? 'Сохранение...' : 'Сохранить' }}
                                </button>
                            </div>
                        </div>
                    </form>
                </div>

                <!-- Блок комментариев (можно добавить позже) -->
                <div class="card">
                    <div class="card-header bg-white">
                        <h5 class="mb-0">Комментарии</h5>
                    </div>
                    <div class="card-body">
                        <p class="text-muted text-center">Функция в разработке</p>
                    </div>
                </div>
            </div>

            <!-- Правая колонка: файлы и другая информация -->
            <div class="col-6">
                <div class="card mb-3">
                    <div class="card-header bg-white">
                        <h5 class="mb-0">Контактные данные</h5>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-7">
                                <div>
                                    <custom-input
                                        v-model="editForm.contact_name"
                                        label="Имя"
                                        :errors="errors.contact_name"
                                        :disabled="!canEdit"
                                        @change="clearError('contact_name')"
                                    />
                                </div>
                                <div class="mt-3">
                                    <custom-input
                                        v-model="editForm.contact_phone"
                                        label="Телефон"
                                        :errors="errors.contact_phone"
                                        :disabled="!canEdit"
                                        @change="clearError('contact_phone')"
                                    />
                                </div>
                                <div class="mt-3">
                                    <custom-input
                                        v-model="editForm.contact_email"
                                        label="Email"
                                        type="email"
                                        :errors="errors.contact_email"
                                        :disabled="!canEdit"
                                        @change="clearError('contact_email')"
                                    />
                                </div>
                            </div>
                            <div class="col-5">
                                <div>
                                    <account-search-select
                                        v-model="editForm.account_id"
                                        label="Участок"
                                        :error="errors.account_id"
                                        :disabled="!canEdit"
                                        @change="clearError('account_id')"
                                    />
                                </div>
                                <div class="mt-3">
                                    <custom-select
                                        v-model="editForm.user_id"
                                        :options="userOptions"
                                        label="Пользователь"
                                        :errors="errors.user_id"
                                        :disabled="!canEdit"
                                        @change="clearError('user_id')"
                                    />
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="card mb-3">
                    <div class="card-header bg-white">
                        <h5 class="mb-0">Файлы</h5>
                    </div>
                    <div class="card-body">
                        <file-uploader
                            ref="ticketFilesUploader"
                            :existing-files="existingFiles"
                            :editable="canEdit"
                            button-text="Прикрепить файлы"
                            existing-files-label="Файлы заявки"
                            :max-files="10"
                            :max-total-size="20 * 1024 * 1024"
                            accept="image/*,application/pdf,.doc,.docx"
                            @update:files="onTicketFilesUpdate"
                            @delete-file="deleteTicketFile"
                        />
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script setup>
import {
    ref,
    computed,
    onMounted,
}                           from 'vue';
import { useResponseError } from '@composables/useResponseError';
import { usePermissions }   from '@composables/usePermissions.js';
import AccountSearchSelect  from '@common/app/AccountSearchSelect.vue';
import CustomInput          from '@form/CustomInput.vue';
import CustomTextarea       from '@form/CustomTextarea.vue';
import CustomSelect         from '@form/CustomSelect.vue';
import FileUploader         from '@common/files/FileUploader.vue';
import {
    ApiAdminHelpDeskTicketsSave,
    ApiAdminHelpDeskTicketsDelete,
    ApiFilesDelete,
}                           from '@api';
import { useFormat }        from '@composables/useFormat';
import Url                  from '@utils/Url.js';
import { TicketStatusEnum } from '@utils/enum.js';

const props = defineProps({
    ticket    : { type: Object, required: true },
    users     : { type: Array, default: () => [] },
    accounts  : { type: Array, default: () => [] },
    types     : { type: Array, default: () => [] },
    categories: { type: Array, default: () => [] },
    services  : { type: Array, default: () => [] },
    statuses  : { type: Array, default: () => [] },
    priorities: { type: Array, default: () => [] },
});

const { errors, parseResponseErrors, showSuccess, showDanger, clearError } = useResponseError();
const { formatDate }                                                       = useFormat();

const saving              = ref(false);
const ticketData          = ref(null);
const existingFiles       = ref([]);
const existingResultFiles = ref([]);
const actions             = ref({});
const newTicketFiles      = ref([]);
const newResultFiles      = ref([]);
const ticketFilesUploader = ref(null);
const resultFilesUploader = ref(null);

// Права доступа
const { has }   = usePermissions();
const canEdit   = computed(() =>
    has('help_desk', 'edit') &&
    ![TicketStatusEnum.CLOSED.value, TicketStatusEnum.REJECTED.value].includes(ticketData.value?.status),
);
const canDelete = computed(() => has('help_desk', 'drop'));

// Данные для редактирования
const editForm = ref({
    description  : '',
    result       : '',
    type         : null,
    category_id  : null,
    service_id   : null,
    priority     : null,
    status       : null,
    contact_name : '',
    contact_phone: '',
    contact_email: '',
    user_id      : null,
    account_id   : null,
});

// Опции для селектов
const categoryOptions = computed(() => [
    { value: null, label: 'Не выбрано' },
    ...props.categories.map(c => ({ value: c.id, label: c.name })),
]);

const serviceOptions = computed(() => {
    let list = props.services;
    if (editForm.value.category_id) {
        list = list.filter(s => s.category_id === editForm.value.category_id);
    }
    return [
        { value: null, label: 'Не выбрано' },
        ...list.map(s => ({ value: s.id, label: s.name })),
    ];
});

const typeOptions     = computed(() => props.types.map(s => ({ value: s.value, label: s.label })));
const statusOptions   = computed(() => props.statuses.map(s => ({ value: s.value, label: s.label })));
const priorityOptions = computed(() => props.priorities.map(p => ({ value: p.value, label: p.label })));
const userOptions     = computed(() => [
    { value: null, label: 'Не выбран' },
    ...props.users.map(u => ({ value: u.id, label: u.fullName })),
]);

// Инициализация из пропсов
const initData = () => {
    ticketData.value          = props.ticket;
    existingFiles.value       = props.ticket?.files || [];
    existingResultFiles.value = props.ticket?.result_files || [];
    actions.value             = props.ticket?.actions || {};
    editForm.value            = {
        description  : props.ticket.description || '',
        result       : props.ticket.result || '',
        type         : props.ticket.type,
        category_id  : props.ticket.category_id,
        service_id   : props.ticket.service_id,
        priority     : props.ticket.priority,
        status       : props.ticket.status,
        contact_name : props.ticket.contact_name || '',
        contact_phone: props.ticket.contact_phone || '',
        contact_email: props.ticket.contact_email || '',
        user_id      : props.ticket.user_id,
        account_id   : props.ticket.account_id,
    };
};

const onTypeChange = () => {
    editForm.value.category_id = null;
    editForm.value.service_id  = null;
    clearError('type');
};

const onCategoryChange = () => {
    editForm.value.service_id = null;
    clearError('service_id');
};

const onTicketFilesUpdate = (files) => {
    newTicketFiles.value = files;
};

const onResultFilesUpdate = (files) => {
    newResultFiles.value = files;
};

const deleteTicketFile = async (file) => {
    if (!confirm('Удалить файл?')) {
        return;
    }
    try {
        await ApiFilesDelete(file.id);
        existingFiles.value = existingFiles.value.filter(f => f.id !== file.id);
        showSuccess('Файл удалён');
    }
    catch (error) {
        parseResponseErrors(error);
    }
};

const deleteResultFile = async (file) => {
    if (!confirm('Удалить файл?')) {
        return;
    }
    try {
        await ApiFilesDelete(file.id);
        existingResultFiles.value = existingResultFiles.value.filter(f => f.id !== file.id);
        showSuccess('Файл удалён');
    }
    catch (error) {
        parseResponseErrors(error);
    }
};

const saveTicket = async () => {
    if (!canEdit.value) {
        return;
    }

    saving.value = true;
    errors.value = {};

    try {
        const formData = new FormData();
        formData.append('id', ticketData.value.id);
        formData.append('description', editForm.value.description || '');
        formData.append('result', editForm.value.result || '');
        formData.append('type', editForm.value.type);
        formData.append('category_id', editForm.value.category_id || '');
        formData.append('service_id', editForm.value.service_id || '');
        formData.append('priority', editForm.value.priority);
        formData.append('status', editForm.value.status);
        formData.append('contact_name', editForm.value.contact_name || '');
        formData.append('contact_phone', editForm.value.contact_phone || '');
        formData.append('contact_email', editForm.value.contact_email || '');
        formData.append('user_id', editForm.value.user_id || '');
        formData.append('account_id', editForm.value.account_id || '');

        // Добавляем новые файлы к заявке
        for (const file of newTicketFiles.value) {
            formData.append('files[]', file);
        }

        // Добавляем новые файлы к ответу
        for (const file of newResultFiles.value) {
            formData.append('result_files[]', file);
        }

        const response            = await ApiAdminHelpDeskTicketsSave({}, formData);
        ticketData.value          = response.data.ticket;
        existingFiles.value       = response.data.ticket?.files || [];
        existingResultFiles.value = response.data.ticket?.result_files || [];

        // Очищаем новые файлы
        newTicketFiles.value = [];
        newResultFiles.value = [];
        ticketFilesUploader.value?.clearNewFiles();
        resultFilesUploader.value?.clearNewFiles();

        showSuccess('Заявка сохранена');
    }
    catch (error) {
        parseResponseErrors(error);
    }
    finally {
        saving.value = false;
    }
};

const deleteTicket = async () => {
    if (!canDelete.value) {
        return;
    }
    if (!confirm('Удалить заявку?')) {
        return;
    }

    try {
        await ApiAdminHelpDeskTicketsDelete(ticketData.value.id);
        showSuccess('Заявка удалена');
        window.location.href = Url.Routes.adminHelpDeskIndex.uri;
    }
    catch (error) {
        showDanger('Ошибка удаления');
        parseResponseErrors(error);
    }
};

onMounted(() => {
    initData();
});
</script>