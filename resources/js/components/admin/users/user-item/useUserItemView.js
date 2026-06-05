import {
    computed,
    ref,
    watch,
}                           from 'vue';
import { usePermissions }   from '@composables/usePermissions.js';
import { useResponseError } from '@composables/useResponseError';
import { getSelects }       from '@composables/getSelects.js';
import { routeUri }         from '@utils/routeUri.js';
import {
    ApiAdminUserGet,
    ApiAdminUserSave,
    ApiAdminUserDelete,
    ApiAdminUserRestore,
    ApiAdminUserGenerateEmail,
    ApiAdminLoginLink,
    ApiAdminUserSendRestorePassword,
    ApiAdminUserSendInviteWithPassword,
}                           from '@api';

export function useUserItemView (props, emit = null) {
    const { parseResponseErrors, showInfo, showDanger } = useResponseError();
    const { has }                                       = usePermissions();
    const {
              getAccounts,
              getRoles,
          }                                             = getSelects();

    const loading    = ref(false);
    const localUser  = ref({});
    const accountIds = ref([]);
    const fractions  = ref([]);
    const accounts   = ref([]);
    const roles      = ref([]);
    const historyUrl = ref(null);
    const saving     = ref(false);
    const routeState = ref(0);
    const qrViewLink = ref(null);
    const tokenLink  = ref(null);

    const initFractions = (user = localUser.value) => {
        if (Array.isArray(user.accountIds)) {
            accountIds.value = user.accountIds.map(String);
        }
        else if (user.accountId) {
            accountIds.value = [String(user.accountId)];
        }
        else {
            accountIds.value = [];
        }

        if (Array.isArray(user.accounts)) {
            fractions.value = user.accounts.map(account => ({
                accountId: account.id,
                value    : account.fraction,
                date     : account.ownerDate,
            }));
        }
        else {
            fractions.value = [];
        }
    };

    const loadUser = async () => {
        loading.value = true;

        try {
            const params    = {};
            const urlParams = new URLSearchParams(window.location.search);
            const userId    = props.id || null;
            const accountId = urlParams.get('accountId') || props.accountId || null;

            if (accountId) {
                params.accountId = accountId;
            }

            const response = await ApiAdminUserGet(userId ? userId : '', params);
            const user     = response.data?.data || response.data;

            localUser.value  = { ...user };
            historyUrl.value = user.historyUrl;
            initFractions(user);
        }
        catch (error) {
            parseResponseErrors(error);
            showDanger('Не удалось загрузить пользователя');
        }
        finally {
            loading.value = false;
        }
    };

    const initSelects = async () => {
        roles.value    = await getRoles();
        accounts.value = await getAccounts();
    };

    const copyToClipboard = async (text) => {
        try {
            await navigator.clipboard.writeText(text);
            showInfo('Ссылка скопирована');
        }
        catch (err) {
            showDanger('Не удалось скопировать');
        }
    };

    const canGenerateEmail = computed(() => {
        return Boolean((!localUser.value.id || !localUser.value.email) &&
            localUser.value.lastName &&
            localUser.value.firstName &&
            localUser.value.middleName,
        );
    });

    const canEdit = computed(() => has('users', 'edit'));
    const canDrop = computed(() => has('users', 'drop'));

    const saveAction = async () => {
        if (!canEdit.value) {
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
            const uri       = routeUri('adminUserView', { id: localUser.value.id });
            window.history.pushState({ state: routeState.value++ }, '', uri);
            emit?.('updated', response.data);
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

    const dropAction = async () => {
        if (!canDrop.value) {
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

    const restoreAction = async () => {
        if (!canDrop.value) {
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

    const getAccountNumberById = (accountId) => {
        const account = accounts.value.find(a => String(a.value) === String(accountId));
        return account?.label || '';
    };

    const renderAccountLink = (accountId) => {
        const uri   = routeUri('adminAccountView', { accountId });
        const label = getAccountNumberById(accountId);
        return `<a href="${uri}" class="link-firm">${label}</a>`;
    };

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

    return {
        canEdit,
        canDrop,
        loading,
        localUser,
        accountIds,
        fractions,
        accounts,
        roles,
        historyUrl,
        saving,
        qrViewLink,
        tokenLink,
        canGenerateEmail,
        copyToClipboard,
        saveAction,
        generateEmail,
        dropAction,
        restoreAction,
        makeLoginQrCode,
        sendRestorePasswordEmail,
        sendInvitePasswordEmail,
        renderAccountLink,
        loadUser,
        initSelects,
    };
}
