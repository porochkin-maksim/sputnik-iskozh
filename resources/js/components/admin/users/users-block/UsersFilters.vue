<template>
    <div class="mt-2 mb-3">
        <div class="d-flex align-items-center">
            <custom-checkbox
                v-model="isMemberModel"
                label="Члены СНТ"
                switch-style
                :disabled="isLoading"
            />
            <custom-checkbox
                v-model="isNotMemberModel"
                label="Не члены СНТ"
                switch-style
                class="ms-2"
                :disabled="isLoading"
            />
            <custom-checkbox
                v-model="isDeletedModel"
                label="Удалён"
                switch-style
                class="ms-2"
                :disabled="isLoading"
            />
            <custom-checkbox
                v-model="hasVerifiedEmailModel"
                label="Подтверждённая почта"
                switch-style
                class="ms-2"
                :disabled="isLoading"
            />
        </div>
    </div>
</template>

<script setup>
import { computed } from 'vue';

import CustomCheckbox from '@common/form/CustomCheckbox.vue';

const props = defineProps({
    isMember   : {
        type   : null,
        default: null,
    },
    isNotMember: {
        type   : null,
        default: null,
    },
    isDeleted        : {
        type   : null,
        default: null,
    },
    hasVerifiedEmail : {
        type   : null,
        default: null,
    },
    isLoading        : {
        type    : Boolean,
        required: true,
    },
});

const emit = defineEmits(['update:isMember', 'update:isNotMember', 'update:isDeleted', 'update:hasVerifiedEmail']);

const isMemberModel = computed({
    get: () => props.isMember,
    set: (value) => emit('update:isMember', value),
});

const isNotMemberModel = computed({
    get: () => props.isNotMember,
    set: (value) => emit('update:isNotMember', value),
});

const isDeletedModel = computed({
    get: () => props.isDeleted,
    set: (value) => emit('update:isDeleted', value),
});

const hasVerifiedEmailModel = computed({
    get: () => props.hasVerifiedEmail,
    set: (value) => emit('update:hasVerifiedEmail', value),
});
</script>
