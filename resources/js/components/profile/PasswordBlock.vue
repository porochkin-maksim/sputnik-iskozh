<template>
    <div class="card">
        <div class="card-body d-flex justify-content-between align-items-center cursor-pointer"
             @click="togglePasswordBlock"
             :class="showPasswordBlock ? '' : 'border-bottom-0'">
            <a class="">
                Смена пароля
            </a>
            <button class="btn py-0 btn-sm">
                <i class="fa fa-chevron-down"
                   v-if="showPasswordBlock"></i>
                <i class="fa fa-chevron-left"
                   v-else></i>
            </button>
        </div>
        <div class="card-body pb-0"
             v-if="showPasswordBlock">
            <profile-password :user="user" />
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