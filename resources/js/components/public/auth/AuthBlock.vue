<template>
    <a class="nav-link" @click="openModal">
        <i class="fa fa-sign-in"></i>&nbsp;Вход
    </a>
    <view-dialog
        v-if="hasModal"
        v-model:show="modalVisible"
        @hidden="closeModal"
    >
        <template #title>{{ title }}</template>
        <template #body>
            <div class="container-fluid auth">
                <div class="login d-flex align-items-center py-4">
                    <div class="container">
                        <div class="w-100 mx-auto form">
                            <login v-if="isLogin">
                                <template #restore>
                                    <div @click="state = STATES.RESTORE">
                                        <button type="button" class="btn btn-link">Забыли пароль?</button>
                                    </div>
                                </template>
                            </login>
                            <restore v-else-if="isRestore" />

                            <div v-if="isRestore"
                                 class="d-flex justify-content-center align-items-center text-dark-emphasis mt-3"
                                 @click="state = STATES.LOGIN">
                                <span>У вас уже есть аккаунт?</span>
                                <button class="btn btn-link">Войти</button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </template>
    </view-dialog>
</template>

<script setup>
import {
    ref,
    computed,
    defineOptions,
}                   from 'vue';
import { useStore } from 'vuex';
import Login        from './Login.vue';
import Restore      from './Restore.vue';
import ViewDialog   from '../../common/ViewDialog.vue';

defineOptions({
    name: 'AuthBlock', // или любое другое имя, под которым вы регистрируете компонент
});

const props = defineProps({
    hasModal: {
        type   : Boolean,
        default: false,
    },
});

const store = useStore();

const STATES = {
    LOGIN   : 'Login',
    REGISTER: 'Register',
    RESTORE : 'Restore',
};

const state = ref(STATES.LOGIN);

const modalVisible = computed({
    get: () => store.state.auth.modalVisible,
    set: (value) => {
        if (value) {
            store.dispatch('auth/openModal');
        }
        else {
            store.dispatch('auth/closeModal');
        }
    },
});

const openModal = () => {
    state.value = STATES.LOGIN; // сброс на логин
    store.dispatch('auth/openModal');
};

const closeModal = () => {
    store.dispatch('auth/closeModal');
};

const title = computed(() => {
    switch (state.value) {
        case STATES.LOGIN:
            return 'Вход';
        case STATES.REGISTER:
            return 'Регистрация';
        case STATES.RESTORE:
            return 'Восстановление пароля';
        default:
            return '';
    }
});

const isLogin    = computed(() => state.value === STATES.LOGIN);
const isRegistry = computed(() => state.value === STATES.REGISTER);
const isRestore  = computed(() => state.value === STATES.RESTORE);
</script>