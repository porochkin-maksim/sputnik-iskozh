<template>
    <div>
        <!-- Индикатор загрузки -->
        <loading-spinner
            v-if="loading && !localUser.id"
            size="lg"
            color="primary"
            text="Загрузка пользователя..."
            wrapper-class="py-5"
        />

        <template v-else>
            <!-- Верхняя панель с кнопками -->
            <div class="d-flex justify-content-between align-items-center mb-2">
                <div class="d-flex">
                    <a
                        v-if="actions.edit && localUser.id"
                        class="btn btn-outline-primary me-2"
                        :href="getCreateLink()"
                    >
                        <i class="fa fa-plus" aria-hidden="true"></i>
                        Добавить пользователя
                    </a>
                </div>
                <div class="d-flex">
                    <div class="me-2" v-if="actions.drop && localUser.id">
                        <button
                            class="btn btn-sm btn-danger"
                            v-if="!localUser.isDeleted"
                            @click="dropAction"
                            :disabled="saving"
                        >
                            <i class="fa fa-trash"></i> Удалить
                        </button>
                    </div>
                </div>
            </div>

            <div class="row">
                <!-- Левая колонка: основная информация -->
                <div class="col-6">
                    <!-- Блок удалённого пользователя -->
                    <div
                        v-if="localUser.isDeleted"
                        class="alert alert-danger text-center mb-0 d-flex justify-content-center align-items-center"
                    >
                        <div>Пользователь удалён</div>
                        <button
                            class="btn btn-sm btn-danger ms-2"
                            v-if="localUser.isDeleted && actions.drop"
                            @click="restoreAction"
                            :disabled="saving"
                        >
                            <i class="fa fa-rotate-left"></i> Восстановить
                        </button>
                    </div>

                    <!-- Основная карточка -->
                    <div class="card mb-2">
                        <div class="card-header bg-white">
                            <h5>Информация</h5>
                        </div>
                        <div class="card-body">
                            <!-- ФИО -->
                            <div class="row mb-2">
                                <div class="col-4 pe-1">
                                    <custom-input
                                        v-model="localUser.lastName"
                                        :disabled="saving"
                                        label="Фамилия"
                                        @update:modelValue="clearError('lastName')"
                                    />
                                </div>
                                <div class="col-4 px-1">
                                    <custom-input
                                        v-model="localUser.firstName"
                                        :disabled="saving"
                                        label="Имя"
                                        @update:modelValue="clearError('firstName')"
                                    />
                                </div>
                                <div class="col-4 ps-1">
                                    <custom-input
                                        v-model="localUser.middleName"
                                        :disabled="saving"
                                        label="Отчество"
                                        @update:modelValue="clearError('middleName')"
                                    />
                                </div>
                            </div>

                            <!-- Почта и телефон -->
                            <div class="row mb-2">
                                <div class="col-6 pe-1">
                                    <div class="input-group">
                                        <custom-input
                                            v-model="localUser.email"
                                            :disabled="saving"
                                            label="Почта"
                                            @update:modelValue="clearError('email')"
                                        />
                                        <button
                                            v-if="canGenerateEmail"
                                            class="btn btn-success"
                                            @click="generateEmail"
                                            :disabled="saving"
                                        >
                                            <i class="fa fa-retweet"></i>
                                        </button>
                                    </div>
                                </div>
                                <div class="col-6 ps-1">
                                    <custom-input
                                        v-model="localUser.phone"
                                        :disabled="saving"
                                        label="Телефон"
                                        @update:modelValue="clearError('phone')"
                                    />
                                </div>
                            </div>

                            <h5>Дополнительно</h5>

                            <!-- Роль и доп. телефон -->
                            <div class="row mb-2">
                                <div class="col-6 pe-1">
                                    <search-select
                                        v-if="localUser.actions?.edit"
                                        v-model="localUser.roleId"
                                        :disabled="saving"
                                        :items="roles"
                                        label="Роль"
                                    />
                                </div>
                                <div class="col-6 ps-1">
                                    <custom-input
                                        v-model="localUser.addPhone"
                                        :disabled="saving"
                                        label="Дополнительный телефон"
                                        @update:modelValue="clearError('addPhone')"
                                    />
                                </div>
                            </div>

                            <!-- Основание членства и дата -->
                            <div class="row mb-2">
                                <div class="col-6 pe-1">
                                    <custom-input
                                        v-model="localUser.membershipDutyInfo"
                                        :disabled="saving"
                                        label="Основание членства"
                                        @update:modelValue="clearError('membershipDutyInfo')"
                                    />
                                </div>
                                <div class="col-6 ps-1">
                                    <custom-calendar
                                        v-model="localUser.membershipDate"
                                        :disabled="saving"
                                        label="Дата членства"
                                        @update:modelValue="clearError('membershipDate')"
                                    />
                                </div>
                            </div>

                            <!-- Адреса -->
                            <div class="row mb-2">
                                <div class="col-6 pe-1">
                                    <custom-input
                                        v-model="localUser.legalAddress"
                                        :disabled="saving"
                                        label="Адрес по прописке"
                                        @update:modelValue="clearError('legalAddress')"
                                    />
                                </div>
                                <div class="col-6 ps-1">
                                    <custom-input
                                        v-model="localUser.postAddress"
                                        :disabled="saving"
                                        label="Почтовый адрес"
                                        @update:modelValue="clearError('postAddress')"
                                    />
                                </div>
                            </div>

                            <!-- Комментарий -->
                            <div class="row mb-2">
                                <div class="col-12">
                                    <custom-textarea
                                        v-model="localUser.additional"
                                        label="Комментарий"
                                        :rows="3"
                                        :disabled="saving"
                                    />
                                </div>
                            </div>

                            <!-- Кнопки сохранения и история -->
                            <div class="d-flex align-items-center justify-content-between mt-2">
                                <div class="d-flex">
                                    <button
                                        v-if="actions.edit"
                                        class="btn btn-success me-2"
                                        @click="saveAction"
                                        :disabled="saving"
                                    >
                                        <i class="fa" :class="saving ? 'fa-spinner fa-spin' : 'fa-save'"></i>
                                        {{ localUser.id ? 'Сохранить' : 'Создать' }}
                                    </button>
                                </div>
                                <div class="d-flex">
                                    <history-btn
                                        :disabled="!localUser.id"
                                        class="btn-link underline-none"
                                        :url="historyUrl"
                                    />
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Правая колонка: участки и уведомления -->
                <div class="col-6">
                    <!-- Блок участков -->
                    <div class="card mb-2">
                        <div class="card-body">
                            <div class="d-flex justify-content-between align-items-center mb-2">
                                <h5 class="m-0">Участки</h5>
                                <div class="w-75">
                                    <search-select
                                        v-model="accountIds"
                                        :disabled="saving"
                                        multiple
                                        :items="accounts"
                                        placeholder="Введите и выберите номера участков"
                                    />
                                </div>
                            </div>

                            <template v-if="fractions.length && accounts.length">
                                <div
                                    v-for="(fraction, index) in fractions"
                                    :key="fraction.accountId"
                                    class="row mb-2"
                                >
                                    <div class="col-3 pe-1 d-flex justify-content-center align-items-end pb-1">
                                        <h6 v-html="renderAccountLink(fraction.accountId)"></h6>
                                    </div>
                                    <div class="col-4 px-1">
                                        <custom-input
                                            v-model="fraction.value"
                                            :disabled="saving"
                                            label="Доля владения (от 0 до 1)"
                                            type="number"
                                            step="0.1"
                                            max="1"
                                            min="0"
                                        />
                                    </div>
                                    <div class="col-5 ps-1">
                                        <custom-calendar
                                            v-model="fraction.date"
                                            :disabled="saving"
                                            label="Дата права"
                                        />
                                    </div>
                                </div>
                            </template>
                        </div>
                    </div>

                    <!-- Блок уведомлений и QR -->
                    <div class="card mb-2" v-if="localUser.id">
                        <div class="card-body">
                            <h5>Уведомления</h5>
                            <ul class="list-group" v-if="localUser.actions?.edit">
                                <li
                                    class="list-group-item list-group-item-action cursor-pointer border-0"
                                    @click="sendInvitePasswordEmail"
                                    v-if="!localUser.isRealEmail"
                                >
                                    <i class="fa fa-envelope-o"></i>&nbsp;Выслать пригласительную ссылку для установки пароля
                                </li>
                                <li
                                    class="list-group-item list-group-item-action cursor-pointer border-0"
                                    @click="sendRestorePasswordEmail"
                                    v-if="localUser.isRealEmail"
                                >
                                    <i class="fa fa-wrench"></i>&nbsp;Выслать ссылку на восстановление пароля
                                </li>
                                <template v-if="qrViewLink">
                                    <li class="list-group-item list-group-item-action border-0">
                                        <div><b>Просмотреть QR-код</b></div>
                                        <a :href="qrViewLink" target="_blank">{{ qrViewLink }}</a>
                                        <div><b>Сгенерированная ссылка</b></div>
                                        <a
                                            :data-copy="tokenLink"
                                            @click.prevent="copyToClipboard(tokenLink)"
                                            class="cursor-pointer"
                                        >{{ tokenLink }}</a>
                                    </li>
                                </template>
                                <li
                                    v-else
                                    class="list-group-item list-group-item-action cursor-pointer border-0"
                                    @click="makeLoginQrCode"
                                >
                                    <i class="fa fa-external-link"></i>&nbsp;Получить постоянную ссылку для входа (QR-код)
                                </li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </template>
    </div>
</template>

<script setup>
import {
    ref,
    computed,
    watch,
    onMounted,
}                           from 'vue';
import { useResponseError } from '@composables/useResponseError';
import HistoryBtn           from '../../common/HistoryBtn.vue';
import CustomInput          from '../../common/form/CustomInput.vue';
import SearchSelect         from '../../common/form/SearchSelect.vue';
import CustomCalendar       from '../../common/form/CustomCalendar.vue';
import CustomTextarea       from '../../common/form/CustomTextarea.vue';
import LoadingSpinner       from '@common/LoadingSpinner.vue';
import {
    ApiAdminUserSave,
    ApiAdminUserDelete,
    ApiAdminUserRestore,
    ApiAdminUserGenerateEmail,
    ApiAdminLoginLink,
    ApiAdminUserSendRestorePassword,
    ApiAdminUserSendInviteWithPassword,
}                           from '@api';
import Url                  from '@utils/Url.js';

defineOptions({
    name: 'UserItemView',
});

const props = defineProps({
    user    : {
        type   : Object,
        default: () => ({}),
    },
    accounts: {
        type   : Array,
        default: () => [],
    },
    roles   : {
        type   : Array,
        default: () => [],
    },
});

const { clearError, parseResponseErrors, showInfo, showDanger } = useResponseError();

// Состояния
const loading    = ref(false);
const localUser  = ref({});
const accountIds = ref([]);
const fractions  = ref([]);
const actions    = ref({});
const historyUrl = ref(null);
const saving     = ref(false);
const routeState = ref(0);
const qrViewLink = ref(null);
const tokenLink  = ref(null);

// Инициализация
const initUser = () => {
    loading.value    = true;
    localUser.value  = { ...props.user };
    actions.value    = props.user.actions || {};
    historyUrl.value = props.user.historyUrl;
    initFractions();
    loading.value = false;
};

// Инициализация fractions
const initFractions = () => {
    if (Array.isArray(props.user.accountIds)) {
        accountIds.value = props.user.accountIds.map(String);
    }
    else if (props.user.accountId) {
        accountIds.value = [String(props.user.accountId)];
    }
    else {
        accountIds.value = [];
    }

    if (Array.isArray(props.user.accounts)) {
        fractions.value = props.user.accounts.map(account => ({
            accountId: account.id,
            value    : account.fraction,
            date     : account.ownerDate,
        }));
    }
    else {
        fractions.value = [];
    }
};

// Копирование в буфер обмена
const copyToClipboard = async (text) => {
    try {
        await navigator.clipboard.writeText(text);
        showInfo('Ссылка скопирована');
    }
    catch (err) {
        showDanger('Не удалось скопировать');
    }
};

// Вычисляемое свойство для проверки возможности генерации email
const canGenerateEmail = computed(() => {
    return (!localUser.value.id || !localUser.value.email) &&
        localUser.value.lastName &&
        localUser.value.firstName &&
        localUser.value.middleName;
});

// Сохранение пользователя
const saveAction = async () => {
    if (!actions.value.edit) {
        return;
    }

    saving.value   = true;
    const formData = new FormData();
    formData.append('id', localUser.value.id || '');
    formData.append('first_name', localUser.value.firstName || '');
    formData.append('last_name', localUser.value.lastName || '');
    formData.append('middle_name', localUser.value.middleName || '');
    formData.append('email', localUser.value.email || '');

    fractions.value.forEach(fraction => {
        formData.append(`fractions[${fraction.accountId}]`, fraction.value);
        formData.append(`ownerDates[${fraction.accountId}]`, fraction.date);
    });

    formData.append('role_id', localUser.value.roleId || '');
    formData.append('phone', localUser.value.phone || '');
    formData.append('membershipDate', localUser.value.membershipDate || '');
    formData.append('membershipDutyInfo', localUser.value.membershipDutyInfo || '');
    formData.append('add_phone', localUser.value.addPhone || '');
    formData.append('legal_address', localUser.value.legalAddress || '');
    formData.append('post_address', localUser.value.postAddress || '');
    formData.append('additional', localUser.value.additional || '');

    try {
        const response = await ApiAdminUserSave({}, formData);
        const message  = localUser.value.id ? 'Пользователь обновлён' : `Пользователь ${response.data.id} создан`;
        showInfo(message);

        localUser.value = response.data;
        actions.value   = response.data.actions || {};

        const uri = Url.Generator.makeUri(Url.Routes.adminUserView, { id: localUser.value.id });
        window.history.pushState({ state: routeState.value++ }, '', uri);
    }
    catch (error) {
        const message = error?.response?.data?.message || 'Не удалось сохранить пользователя';
        showDanger(message);
        parseResponseErrors(error);
    }
    finally {
        saving.value = false;
    }
};

// Генерация email
const generateEmail = async () => {
    const formData = new FormData();
    formData.append('id', localUser.value.id || '');
    formData.append('first_name', localUser.value.firstName || '');
    formData.append('last_name', localUser.value.lastName || '');
    formData.append('middle_name', localUser.value.middleName || '');

    try {
        const response        = await ApiAdminUserGenerateEmail({}, formData);
        localUser.value.email = response.data;
        showInfo('Email сгенерирован');
    }
    catch (error) {
        showDanger('Что-то пошло не так');
        parseResponseErrors(error);
    }
};

// Удаление пользователя
const dropAction = async () => {
    if (!actions.value.drop) {
        return;
    }
    if (!confirm('Удалить пользователя?')) {
        return;
    }

    try {
        const response = await ApiAdminUserDelete(localUser.value.id);
        if (response.data) {
            showInfo('Пользователь удалён');
            setTimeout(() => location.reload(), 2000);
        }
        else {
            showDanger('Пользователь не удалён');
        }
    }
    catch (error) {
        parseResponseErrors(error);
    }
};

// Восстановление пользователя
const restoreAction = async () => {
    if (!actions.value.drop) {
        return;
    }
    if (!confirm('Восстановить пользователя?')) {
        return;
    }

    try {
        const response = await ApiAdminUserRestore(localUser.value.id);
        if (response.data) {
            showInfo('Пользователь восстановлен');
            setTimeout(() => location.reload(), 2000);
        }
        else {
            showDanger('Пользователь не восстановлен');
        }
    }
    catch (error) {
        parseResponseErrors(error);
    }
};

// Создание QR-кода для входа
const makeLoginQrCode = async () => {
    const pin = prompt('Установите пароль для постоянной ссылки для входа');
    if (!pin) {
        return;
    }

    try {
        const response   = await ApiAdminLoginLink(localUser.value.id, pin);
        qrViewLink.value = response.data.qrLink;
        tokenLink.value  = response.data.tokenLink;
        showInfo('QR-код создан');
    }
    catch (error) {
        showDanger('Не получилось создать ссылку');
        parseResponseErrors(error);
    }
};

// Отправка письма для сброса пароля
const sendRestorePasswordEmail = async () => {
    if (!confirm('Отправить письмо для сброса пароля?')) {
        return;
    }

    const formData = new FormData();
    formData.append('id', localUser.value.id);

    try {
        await ApiAdminUserSendRestorePassword({}, formData);
        showInfo('Письмо отправлено');
    }
    catch (error) {
        showDanger('Письмо не отправлено');
        parseResponseErrors(error);
    }
};

// Отправка пригласительного письма
const sendInvitePasswordEmail = async () => {
    if (!confirm('Отправить пригласительное письмо для установки пароля?')) {
        return;
    }

    const formData = new FormData();
    formData.append('id', localUser.value.id);

    try {
        await ApiAdminUserSendInviteWithPassword({}, formData);
        showInfo('Письмо отправлено');
    }
    catch (error) {
        showDanger('Письмо не отправлено');
        parseResponseErrors(error);
    }
};

// Вспомогательные функции
const getCreateLink = () => {
    return Url.Generator.makeUri(Url.Routes.adminUserView, { id: null });
};

const getAccountNumberById = (accountId) => {
    const account = props.accounts.find(a => String(a.value) === String(accountId));
    return account?.label || '';
};

const renderAccountLink = (accountId) => {
    const uri   = Url.Generator.makeUri(Url.Routes.adminAccountView, { accountId });
    const label = getAccountNumberById(accountId);
    return `<a href="${uri}" class="text-decoration-none">${label}</a>`;
};

// Следим за изменениями accountIds
watch(accountIds, (newIds) => {
    const newFractions = [];
    newIds.forEach(id => {
        const existing = fractions.value.find(f => String(f.accountId) === String(id));
        newFractions.push({
            accountId: id,
            value    : existing?.value || 0,
            date     : existing?.date || null,
        });
    });
    fractions.value = newFractions;
}, { deep: true });

onMounted(() => {
    initUser();
});
</script>

<style scoped>
td {
    padding : 2px 0;
}

.cursor-pointer {
    cursor : pointer;
}
</style>