<template>
    <div class="card">
        <div class="card-header bg-white">
            <h5 class="m-0">Пользователи</h5>
        </div>

        <div class="card-body">
            <div>
                <loading-spinner
                    v-if="loading && (!users || users.length === 0)"
                    size="lg"
                    color="primary"
                    text="Загрузка пользователей..."
                    wrapper-class="py-5"
                />

                <template v-else>
                    <table class="table table-sm table-bordered table-hover align-middle admin-table-firm mb-0"
                           v-if="users && users.length">
                        <thead>
                        <tr class="text-center">
                            <th>ФИО</th>
                            <th>Почта</th>
                            <th>Доля</th>
                            <th>Дата</th>
                        </tr>
                        </thead>
                        <tbody>
                        <tr v-for="user in users" :key="user.id" class="align-middle">
                            <td>
                                <a v-if="canView" :href="user.viewUrl" class="link-firm">
                                    {{ user.fullName }}
                                </a>
                                <span v-else>
                                    {{ user.fullName }}
                                </span>
                            </td>
                            <td>
                            <span :data-copy="user.email"
                                  class="link-firm cursor-pointer"
                                  @click="copyToClipboard(user.email)">
                                {{ user.email }}
                            </span>
                            </td>
                            <td>
                                <div class="text-center d-flex flex-nowrap align-items-center">
                                    <i class="fa fa-user pe-1"
                                       :class="[user.fractionPercent ? 'text-success' : 'text-light']"></i>
                                    <span>{{ user.fractionPercent }}</span>
                                </div>
                            </td>
                            <td class="text-center">{{ formatDate(user.ownerDate) }}</td>
                        </tr>
                        </tbody>
                    </table>

                    <div v-else
                         class="text-center text-muted py-3">
                        Нет пользователей для отображения
                    </div>
                </template>
            </div>
        </div>
        <div class="card-footer bg-white" v-if="canEdit">
            <div class="d-flex align-items-center justify-content-between">
                <div class="d-flex">
                    <a class="btn btn-success me-2"
                       :href="createUserPageLink"
                       @click="onCreateUserClick"
                    >
                        <i class="fa fa-plus"></i>&nbsp;Добавить пользователя
                    </a>
                </div>
            </div>
        </div>
    </div>

    <view-dialog
        v-model:show="showUserDialog"
        modal-class="modal-xl"
        :ask-before-close="true"
        @hidden="onUserDialogHidden"
    >
        <template #title>
            Создать пользователя
        </template>

        <template #body>
            <user-item-view
                :key="dialogKey"
                :id="null"
                @updated="onUserUpdated"
            />
        </template>
    </view-dialog>
</template>

<script setup>
import {
    ref,
    computed,
    watch,
}                             from 'vue';
import LoadingSpinner         from '@common/LoadingSpinner.vue';
import ViewDialog             from '@common/ViewDialog.vue';
import UserItemView           from '@components/admin/users/UserItemView.vue';
import { ApiAdminAccountGet } from '@api';
import { useResponseError }   from '@composables/useResponseError';
import { useFormat }          from '@composables/useFormat.js';
import { usePermissions }     from '@composables/usePermissions.js';
import { routeUri }           from '@utils/routeUri.js';

const props = defineProps({
    account: {
        type    : Object,
        required: true,
    },
});

const { showInfo, showDanger } = useResponseError();
const { formatDate }           = useFormat();
const { has }                  = usePermissions();

const canView = computed(() => has('users', 'view'));
const canEdit = computed(() => has('users', 'edit'));

// Состояния
const loading        = ref(false);
const showUserDialog = ref(false);
const dialogKey      = ref(0);
const returnUrl      = ref(null);
const users          = ref(props.account?.users || []);

const createUserPageLink = computed(() => {
    if (!props.account?.id) {
        return '#';
    }
    return routeUri('adminUserView', { id: null }, { accountId: props.account.id });
});

// Копирование email в буфер обмена
const copyToClipboard = async (email) => {
    try {
        await navigator.clipboard.writeText(email);
        showInfo('Email скопирован в буфер обмена');
    }
    catch (err) {
        showDanger('Не удалось скопировать email');
    }
};

const openCreateUserDialog = () => {
    returnUrl.value = window.location.href;
    const url       = new URL(createUserPageLink.value, window.location.origin);
    window.history.pushState({}, '', url);
    dialogKey.value++;
    showUserDialog.value = true;
};

const onUserDialogHidden = () => {
    showUserDialog.value = false;
    if (returnUrl.value) {
        window.history.pushState({}, '', returnUrl.value);
        returnUrl.value = null;
    }
};

const loadUsers = async () => {
    if (!props.account?.id) {
        users.value = [];
        return;
    }

    loading.value = true;
    try {
        const response = await ApiAdminAccountGet(props.account.id);
        const account  = response.data?.data || response.data?.account || response.data;
        users.value    = account?.users || [];
    }
    catch (error) {
        showDanger('Не удалось загрузить пользователей');
    }
    finally {
        loading.value = false;
    }
};

const onUserUpdated = async () => {
    showUserDialog.value = false;
    await loadUsers();
};

const onCreateUserClick = (event) => {
    if (event.button !== 0 || event.metaKey || event.ctrlKey || event.shiftKey || event.altKey) {
        return;
    }

    event.preventDefault();
    openCreateUserDialog();
};

watch(() => props.account?.id, (newId, oldId) => {
    if (newId && newId !== oldId) {
        loadUsers();
    }
}, { immediate: true });
</script>
