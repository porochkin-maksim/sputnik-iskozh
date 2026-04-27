<template>
    <custom-select
        v-model="accountId"
        :options="options"
        :classes="'form-control-sm'"
        :required="false"
        @change="switchAccount"
    />
</template>

<script setup>
import {
    ref,
    computed,
    defineProps,
}                                  from 'vue';
import { useResponseError }        from '@composables/useResponseError';
import CustomSelect                from '@common/form/CustomSelect.vue';
import { ApiProfileAccountSwitch } from '@api';

const props = defineProps({
    accounts: {
        type   : Array,
        default: () => [],
    },
    selected: {
        type   : [String, Number],
        default: null,
    },
});

const { showInfo, showDanger } = useResponseError();

const accountId = ref(props.selected);

const options = computed(() => {
    return props.accounts.map(account => ({
        value: account.id,
        label: account.number,
    }));
});

const switchAccount = (value) => {
    ApiProfileAccountSwitch({}, { accountId: value })
        .then(response => {
            if (response.data) {
                showInfo('Сейчас страница будет перезагружена');
                location.reload();
            }
            else {
                showDanger('Что-то пошло не так');
            }
        })
        .catch(() => {
            showDanger('Ошибка при смене участка');
        });
};
</script>