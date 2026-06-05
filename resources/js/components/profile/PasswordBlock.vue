<template>
    <div class="profile-password-card overflow-hidden">
        <button
            class="profile-password-card__head d-flex justify-content-between align-items-center cursor-pointer profile-password-toggle"
            type="button"
            @click="togglePasswordBlock">
            <div class="profile-password-toggle__title">
                Смена пароля
            </div>
            <span class="btn btn-sm btn-outline-success profile-password-toggle__btn">
                <i class="fa fa-chevron-down"
                   v-if="showPasswordBlock"></i>
                <i class="fa fa-chevron-left"
                   v-else></i>
            </span>
        </button>
        <div class="profile-password-card__body"
             v-if="showPasswordBlock">
            <profile-password :user="user"
                              @update:password="showPasswordBlock=false"
            />
        </div>
    </div>
</template>

<script setup>
import {
    ref,
    defineProps,
}                      from 'vue';
import ProfilePassword from './ProfilePassword.vue';

defineProps({
    user: {
        type    : Object,
        required: true,
    },
});

const showPasswordBlock = ref(false);

const togglePasswordBlock = () => {
    showPasswordBlock.value = !showPasswordBlock.value;
};
</script>
