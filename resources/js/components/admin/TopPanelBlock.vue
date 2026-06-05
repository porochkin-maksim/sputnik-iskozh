<template>
    <div class="mb-2 pb-2 border-bottom panel-block d-flex justify-content-between" v-if="canActions">
        <loading-spinner
            v-if="loading && !canActions"
            size="sm"
            color="primary"
            text="Загрузка..."
            wrapper-class="py-1"
        />

        <template v-else>
            <div class="d-flex">
                        <div class="search-block" v-if="actions.accounts">
                            <i class="fa fa-home prefix"></i>
                            <inline-input v-model="account"
                                  placeholder="Участок"
                                  :grouped="true"
                                  :input-class="'search-block-input'"
                                  @keyup="onAccountSearch"
                                  @submit="searchAction" />
                    <i class="fa fa-search postfix"></i>
                </div>
                        <div class="search-block ms-2" v-if="actions.users">
                            <i class="fa fa-user prefix"></i>
                            <inline-input v-model="user"
                                  placeholder="Пользователь"
                                  :grouped="true"
                                  :input-class="'search-block-input'"
                                  @keyup="onUserSearch"
                                  @submit="searchAction" />
                    <i class="fa fa-search postfix"></i>
                </div>
            </div>
        </template>
    </div>
</template>

<script setup>
import {
    ref,
    onMounted,
}                           from 'vue';
import InlineInput          from '@common/form/InlineInput.vue';
import LoadingSpinner       from '@common/LoadingSpinner.vue';
import { useResponseError } from '@composables/useResponseError';
import {
    ApiAdminTopPanelIndex,
    ApiAdminTopPanelSearch,
}                           from '@api';

const { parseResponseErrors } = useResponseError();

// Состояния
const loading    = ref(false);
const actions    = ref(null);
const canActions = ref(false);
const account    = ref(null);
const user       = ref(null);
const payments   = ref(0);

// Загрузка данных
const loadData = async () => {
    loading.value = true;
    try {
        const response   = await ApiAdminTopPanelIndex();
        actions.value    = response.data.actions;
        canActions.value = response.data.canActions;
        payments.value   = response.data.payments;

        const paymentsElement = document.getElementById('new-payments-count');
        if (paymentsElement) {
            paymentsElement.innerHTML = ' (' + payments.value + ')';
        }
    }
    catch (error) {
        parseResponseErrors(error);
    }
    finally {
        loading.value = false;
    }
};

// Поиск
const searchAction = async () => {
    try {
        const response = await ApiAdminTopPanelSearch({}, {
            account: account.value,
            user   : user.value,
        });
        if (response.data) {
            location.href = response.data;
        }
    }
    catch (error) {
        parseResponseErrors(error);
    }
};

// Очистка при поиске по участку
const onAccountSearch = () => {
    user.value = null;
};

// Очистка при поиске по пользователю
const onUserSearch = () => {
    account.value = null;
};

onMounted(() => {
    loadData();
});
</script>
