<template>
    <div>
        <!-- Верхняя панель -->
        <div class="d-flex align-items-center justify-content-between mb-2">
            <button
                v-if="actions.edit"
                class="btn btn-success"
                @click="makeAction"
                :disabled="isLoading"
            >
                <i class="fa fa-plus" aria-hidden="true"></i>
                Добавить роль
            </button>
            <div v-else></div>
            <history-btn
                v-if="actions.view"
                class="btn-link underline-none"
                :url="historyUrl"
            />
        </div>

        <!-- Индикатор загрузки -->
        <loading-spinner
            v-if="isLoading"
            size="lg"
            color="primary"
            text="Загрузка ролей..."
            wrapper-class="py-5"
        />

        <!-- Ошибка загрузки -->
        <div v-else-if="error" class="alert alert-danger">
            {{ error }}
        </div>

        <template v-else>
            <div class="row">
                <div class="col-8">
                    <table v-if="actions.view" class="table table-sm">
                        <thead>
                        <tr>
                            <th>№</th>
                            <th>Название</th>
                            <th></th>
                        </tr>
                        </thead>
                        <tbody>
                        <template v-for="r in roles"
                                  :key="r.id">
                            <tr
                                class="align-middle"
                                v-if="r?.actions?.view"
                            >
                                <td>{{ r.id }}</td>
                                <td class="w-100">
                                    <a href="#" @click.prevent="editAction(r)">
                                        {{ r.name }}
                                    </a>
                                </td>
                                <td>
                                    <button
                                        class="btn"
                                        v-if="r?.actions?.drop"
                                        @click="dropAction(r.id)"
                                        :disabled="deleting === r.id"
                                    >
                                        <i
                                            v-if="deleting === r.id"
                                            class="fa fa-spinner fa-spin text-danger"
                                        ></i>
                                        <i v-else class="fa fa-trash text-danger"></i>
                                    </button>
                                </td>
                            </tr>
                        </template>
                        <tr v-if="roles.length === 0">
                            <td colspan="3" class="text-center text-muted">
                                Нет доступных ролей
                            </td>
                        </tr>
                        </tbody>
                    </table>
                </div>

                <!-- Правая панель с правами -->
                <div v-if="selectedRole" class="col-4">
                    <div class="input-group input-group-sm mb-2">
                        <template v-if="selectedRole?.actions?.edit">
                            <button
                                class="btn btn-success"
                                @click="saveAction"
                                :disabled="!canSave || saving"
                            >
                                <i
                                    class="fa"
                                    :class="saving ? 'fa-spinner fa-spin' : 'fa-save'"
                                ></i>
                            </button>
                            <input
                                type="text"
                                class="form-control name"
                                placeholder="Название"
                                v-model="selectedRole.name"
                                :disabled="saving"
                            />
                        </template>
                        <template v-else>
                            <div class="p-2 border w-100">{{ selectedRole.name }}</div>
                        </template>
                    </div>

                    <!-- Разрешения -->
                    <div v-for="(group, section) in permissions" :key="section">
                        <ul class="list-group list-unstyled">
                            <template v-if="selectedRole?.actions?.edit">
                                <!-- Секция (группа) -->
                                <li class="fw-bold mb-2">
                                    <input
                                        class="form-check-input cursor-pointer"
                                        type="checkbox"
                                        :id="vueId + section"
                                        :checked="isSectionChecked(section)"
                                        :disabled="saving"
                                        @change="onChangedSection(section)"
                                    />
                                    <label :for="vueId + section" class="cursor-pointer ms-2">
                                        {{ group[section] || section }}
                                    </label>
                                </li>
                                <!-- Остальные элементы группы -->
                                <li v-for="(label, code) in group" :key="code">
                                    <template v-if="code !== section">
                                        <input
                                            class="form-check-input cursor-pointer"
                                            type="checkbox"
                                            :id="vueId + code"
                                            :checked="isChecked(code)"
                                            :disabled="saving"
                                            @change="onChanged(code)"
                                        />
                                        <label :for="vueId + code" class="cursor-pointer ms-2">
                                            {{ label }}
                                        </label>
                                    </template>
                                </li>
                            </template>
                            <template v-else>
                                <!-- Режим просмотра -->
                                <li v-for="(label, code) in group" :key="code">
                                    <i
                                        class="fa"
                                        :class="isChecked(code) ? 'fa-check text-success' : 'fa-check text-light'"
                                    ></i>
                                    <span class="ms-2">{{ label }}</span>
                                </li>
                            </template>
                        </ul>
                        <hr />
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
    onMounted,
    defineProps,
    defineEmits,
    defineOptions,
}                           from 'vue';
import { useResponseError } from '@composables/useResponseError';
import HistoryBtn           from '../../common/HistoryBtn.vue';
import LoadingSpinner       from '../../common/LoadingSpinner.vue';
import {
    ApiAdminRoleList,
    ApiAdminRoleCreate,
    ApiAdminRoleSave,
    ApiAdminRoleDelete,
}                           from '@api';

defineOptions({
    name: 'RolesBlock',
});

const props = defineProps({
    permissions: {
        type   : Object,
        default: () => ({}),
    },
});

const emit = defineEmits(['update:checked']);

const { parseResponseErrors, showInfo, showDanger } = useResponseError();

const vueId = ref('role-' + Date.now() + '-' + Math.random().toString(36).substring(2, 9));

const isLoading = ref(false);
const saving    = ref(false);
const deleting  = ref(null);
const error     = ref(null);

const roles      = ref([]);
const actions    = ref({});
const historyUrl = ref(null);

const selectedRole = ref(null);
const checked      = ref([]);

// Загрузка списка ролей
const loadList = async () => {
    isLoading.value = true;
    error.value     = null;
    try {
        const response = await ApiAdminRoleList();

        actions.value = response.data.actions || {};

        // Проверяем, где именно лежат роли
        let fetchedRoles = response.data.roles;
        if (!fetchedRoles && response.data.list) {
            fetchedRoles = response.data.list;
        }
        else if (!fetchedRoles && Array.isArray(response.data)) {
            fetchedRoles = response.data;
        }
        else {
            fetchedRoles = fetchedRoles || [];
        }

        roles.value = fetchedRoles.map(role => ({
            ...role,
            actions: role.actions || { view: true, edit: false, drop: false },
        }));

        historyUrl.value = response.data.historyUrl;
    }
    catch (err) {
        console.error('Error loading roles:', err);
        error.value = 'Не удалось загрузить роли';
        parseResponseErrors(err);
    }
    finally {
        isLoading.value = false;
    }
};

// Создание новой роли
const makeAction = async () => {
    if (!actions.value.edit) {
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
    catch (error) {
        parseResponseErrors(error);
    }
};

// Редактирование роли
const editAction = (role) => {
    checked.value      = role.permissions || [];
    selectedRole.value = { ...role };
};

// Сохранение роли
const saveAction = async () => {
    if (!actions.value.edit || saving.value) {
        return;
    }

    saving.value = true;
    const data   = {
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
    catch (error) {
        const message = error?.response?.data?.message || 'Не удалось сохранить роль';
        showDanger(message);
        parseResponseErrors(error);
    }
    finally {
        saving.value = false;
    }
};

// Удаление роли
const dropAction = async (id) => {
    if (!actions.value.drop) {
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
    catch (error) {
        parseResponseErrors(error);
    }
    finally {
        deleting.value = null;
    }
};

// Проверка, выбрано ли разрешение
const isChecked = (code) => checked.value.includes(String(code));

// Проверка, выбраны ли все разрешения в секции
const isSectionChecked = (section) => {
    const items = props.permissions[section];
    if (!items) {
        return false;
    }
    const codes = Object.keys(items).filter(key => key !== section);
    return codes.length > 0 && codes.every(code => isChecked(code));
};

// Изменение одного разрешения
const onChanged = (code) => {
    const strCode = String(code);
    if (checked.value.includes(strCode)) {
        checked.value = checked.value.filter(item => item !== strCode);
    }
    else {
        checked.value.push(strCode);
    }
    emit('update:checked', checked.value);
};

// Изменение всей секции
const onChangedSection = (section) => {
    const items = props.permissions[section];
    if (!items) {
        return;
    }

    const codes       = Object.keys(items).filter(key => key !== section);
    const allSelected = codes.every(code => isChecked(code));

    if (allSelected) {
        checked.value = checked.value.filter(item => !codes.includes(item));
    }
    else {
        codes.forEach(code => {
            if (!checked.value.includes(code)) {
                checked.value.push(code);
            }
        });
    }
    emit('update:checked', checked.value);
};

// Возможность сохранения
const canSave = computed(() => {
    return selectedRole.value?.name && checked.value.length > 0;
});

onMounted(loadList);
</script>

<style scoped>
/* Стили можно оставить пустыми или добавить специфические */
</style>