<template>
    <search-select
        v-model="selectedAccount"
        :items="accountOptions"
        :label="label"
        :placeholder="placeholder"
        :disabled="disabled"
        :required="required"
        :error="error"
        :classes="classes"
        @select="onSelect"
    />
</template>

<script setup>
import {
    ref,
    onMounted,
    watch,
}                                 from 'vue';
import SearchSelect               from '@form/SearchSelect.vue';
import { ApiAjaxSelectsAccounts } from '@api';

const props = defineProps({
    modelValue : {
        type   : [String, Number, null],
        default: null,
    },
    label      : {
        type   : String,
        default: 'Участок',
    },
    placeholder: {
        type   : String,
        default: 'Поиск участка...',
    },
    disabled   : Boolean,
    required   : Boolean,
    error      : [String, Array],
    classes    : String,
});

const emit = defineEmits(['update:modelValue', 'select']);

const selectedAccount = ref(props.modelValue);
const accountOptions  = ref([]);

const loadAccounts = async () => {
    try {
        const response       = await ApiAjaxSelectsAccounts();
        // Ожидаем массив { value, label } от API
        accountOptions.value = response.data || [];
    }
    catch (error) {
        console.error('Ошибка загрузки участков', error);
        accountOptions.value = [];
    }
};

// Двусторонняя синхронизация
watch(selectedAccount, (newVal) => {
    emit('update:modelValue', newVal);
});

watch(() => props.modelValue, (newVal) => {
    if (newVal !== selectedAccount.value) {
        selectedAccount.value = newVal;
    }
});

const onSelect = (item) => {
    emit('select', item);
};

onMounted(() => {
    loadAccounts();
});
</script>