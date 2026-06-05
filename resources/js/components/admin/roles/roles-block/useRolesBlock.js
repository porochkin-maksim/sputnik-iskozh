import {
    computed,
    onMounted,
    ref,
} from 'vue';

import { usePermissions }   from '@composables/usePermissions';
import { useResponseError } from '@composables/useResponseError';
import {
    ApiAdminRoleList,
    ApiAdminRoleCreate,
    ApiAdminRoleSave,
    ApiAdminRoleDelete,
}                           from '@api';

export function useRolesBlock (props, emit) {
    const { has }                                       = usePermissions();
    const { parseResponseErrors, showInfo, showDanger } = useResponseError();

    const isLoading = ref(false);
    const saving    = ref(false);
    const deleting  = ref(null);
    const error     = ref(null);

    const roles      = ref([]);
    const historyUrl = ref(null);

    const selectedRole = ref(null);
    const checked      = ref([]);
    const vueId        = ref('role-' + Date.now() + '-' + Math.random().toString(36).substring(2, 9));

    const normalizeRoles = (responseData) => {
        let fetchedRoles = responseData.roles;

        if (!fetchedRoles && responseData.list) {
            fetchedRoles = responseData.list;
        }
        else if (!fetchedRoles && Array.isArray(responseData)) {
            fetchedRoles = responseData;
        }
        else {
            fetchedRoles = fetchedRoles || [];
        }

        return fetchedRoles.map((role) => ({
            ...role,
            actions: role.actions || { view: true, edit: false, drop: false },
        }));
    };

    const loadList = async () => {
        isLoading.value = true;
        error.value     = null;

        try {
            const response   = await ApiAdminRoleList();
            roles.value      = normalizeRoles(response.data);
            historyUrl.value = response.data.historyUrl;
        }
        catch (err) {
            error.value = 'Не удалось загрузить роли';
            parseResponseErrors(err);
        }
        finally {
            isLoading.value = false;
        }
    };

    const makeAction = async () => {
        if (!has('roles', 'edit')) {
            return;
        }

        try {
            const response = await ApiAdminRoleCreate();
            const newRole  = response.data;

            if (!newRole.actions) {
                newRole.actions = { edit: true, view: true, drop: false };
            }

            selectedRole.value = newRole;
            checked.value      = [];
        }
        catch (err) {
            parseResponseErrors(err);
        }
    };

    const editAction = (role) => {
        checked.value      = role.permissions || [];
        selectedRole.value = { ...role };
    };

    const saveAction = async () => {
        if (!has('roles', 'edit') || saving.value || !selectedRole.value) {
            return;
        }

        saving.value = true;

        const data = {
            id         : selectedRole.value.id,
            name       : selectedRole.value.name,
            permissions: checked.value,
        };

        try {
            const response = await ApiAdminRoleSave({}, data);
            const message  = selectedRole.value.id ? 'Роль обновлена' : `Роль ${response.data.id} создана`;
            showInfo(message);
            await loadList();
            selectedRole.value = null;
            checked.value      = [];
        }
        catch (err) {
            const message = err?.response?.data?.message || 'Не удалось сохранить роль';
            showDanger(message);
            parseResponseErrors(err);
        }
        finally {
            saving.value = false;
        }
    };

    const dropAction = async (id) => {
        if (!has('roles', 'drop')) {
            return;
        }
        if (!confirm('Удалить роль?')) {
            return;
        }

        deleting.value = id;

        try {
            const response = await ApiAdminRoleDelete(id);
            if (response.data) {
                showInfo('Роль удалена');
                if (selectedRole.value?.id === id) {
                    selectedRole.value = null;
                    checked.value      = [];
                }
                await loadList();
            }
            else {
                showDanger('Роль не удалена');
            }
        }
        catch (err) {
            parseResponseErrors(err);
        }
        finally {
            deleting.value = null;
        }
    };

    const isChecked = (code) => checked.value.includes(String(code));

    const isSectionChecked = (section) => {
        const items = props.permissions[section];

        if (!items) {
            return false;
        }

        const codes = Object.keys(items).filter((key) => key !== section);
        return codes.length > 0 && codes.every((code) => isChecked(code));
    };

    const onChanged = (code) => {
        const strCode = String(code);

        if (checked.value.includes(strCode)) {
            checked.value = checked.value.filter((item) => item !== strCode);
        }
        else {
            checked.value.push(strCode);
        }

        emit('update:checked', checked.value);
    };

    const onChangedSection = (section) => {
        const items = props.permissions[section];

        if (!items) {
            return;
        }

        const codes       = Object.keys(items).filter((key) => key !== section);
        const allSelected = codes.every((code) => isChecked(code));

        if (allSelected) {
            checked.value = checked.value.filter((item) => !codes.includes(item));
        }
        else {
            codes.forEach((code) => {
                if (!checked.value.includes(code)) {
                    checked.value.push(code);
                }
            });
        }

        emit('update:checked', checked.value);
    };

    const canSave = computed(() => selectedRole.value?.name && checked.value.length > 0);

    onMounted(loadList);

    return {
        canSave,
        checked,
        deleting,
        editAction,
        error,
        historyUrl,
        isChecked,
        isLoading,
        isSectionChecked,
        makeAction,
        onChanged,
        onChangedSection,
        roles,
        saveAction,
        selectedRole,
        saving,
        vueId,
        dropAction,
        has,
    };
}
