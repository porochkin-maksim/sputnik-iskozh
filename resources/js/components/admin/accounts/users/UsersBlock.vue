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
                    <table class="table align-middle m-0 text-center"
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
                        <tr v-for="user in users" :key="user.id">
                            <td class="text-start">
                                <a :href="user.viewUrl">{{ user.fullName }}</a>
                            </td>
                            <td class="text-start">
                            <span :data-copy="user.email"
                                  class="text-primary cursor-pointer"
                                  @click="copyToClipboard(user.email)">
                                {{ user.email }}
                            </span>
                            </td>
                            <td class="text-center">
                                <i class="fa fa-user"
                                   :class="[user.fractionPercent ? 'text-success' : 'text-light']"></i>
                                &nbsp;<span>{{ user.fractionPercent }}</span>
                            </td>
                            <td class="text-center">{{ formatDate(user.ownerDate) }}</td>
                        </tr>
                        </tbody>
                    </table>

                    <div v-else-if="!loading && (!users || users.length === 0)"
                         class="text-center text-muted py-3">
                        Нет пользователей для отображения
                    </div>
                </template>
            </div>
        </div>
        <div class="card-footer bg-white">
            <div class="d-flex align-items-center justify-content-between">
                <div class="d-flex">
                    <a class="btn btn-success me-2"
                       :href="createUserPageLink">
                        Добавить пользователя
                    </a>
                </div>
            </div>
        </div>
    </div>
</template>

<script setup>
import {
    ref,
    computed,
}                           from 'vue';
import LoadingSpinner       from '@common/LoadingSpinner.vue';
import Url                  from '../../../../utils/Url.js';
import { useResponseError } from '@composables/useResponseError';
import { useFormat }        from '@composables/useFormat.js';

const props = defineProps({
    account: {
        type    : Object,
        required: true,
    },
    users  : {
        type   : Array,
        default: () => [],
    },
});

const { showInfo, showDanger } = useResponseError();
const { formatDate }           = useFormat();

// Состояния
const loading = ref(false);

// Копирование email в буфер обмена
const copyToClipboard = async (email) => {
    try {
        await navigator.clipboard.writeText(email);
        showInfo('Email скопирован в буфер обмена');
    }
    catch (err) {
        showDanger('Не удалось скопировать email');
        console.error('Failed to copy: ', err);
    }
};

// Ссылка на создание пользователя
const createUserPageLink = computed(() => {
    if (!props.account?.id) {
        return '#';
    }
    return Url.Generator.makeUri(
        Url.Routes.adminUserView,
        { id: null },
        { accountId: props.account.id },
    );
});
</script>