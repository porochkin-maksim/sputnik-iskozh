<template>
    <form @submit.prevent="$emit('save-service')">
        <div class="card">
            <div class="card-body">
                <div class="mb-2">
                    <custom-input
                        v-model="formData.name"
                        label="Название услуги"
                        required
                        :disabled="saving"
                    />
                </div>
                <div class="mb-2">
                    <custom-input
                        v-model="formData.sort_order"
                        label="Порядок сортировки"
                        type="number"
                        :disabled="saving"
                    />
                </div>
                <div class="mb-2 d-flex align-items-center">
                    <custom-checkbox
                        v-model="formData.is_active"
                        label="Активна"
                        switch-style
                        :disabled="saving"
                    />
                </div>
            </div>
            <div class="card-footer bg-white">
                <div class="d-flex gap-2">
                    <button
                        type="submit"
                        class="btn btn-sm btn-primary"
                        :disabled="saving"
                    >
                        <i
                            v-if="saving"
                            class="fa fa-spinner fa-spin"
                        ></i>
                        {{ saving ? 'Сохранение...' : 'Сохранить' }}
                    </button>
                    <button
                        type="button"
                        class="btn btn-sm btn-outline-secondary"
                        @click="$emit('cancel-edit')"
                    >
                        Отмена
                    </button>
                </div>
            </div>
        </div>
    </form>
</template>

<script setup>
import CustomCheckbox from '@common/form/CustomCheckbox.vue';
import CustomInput    from '@common/form/CustomInput.vue';

defineProps({
    formData: {
        type    : Object,
        required: true,
    },
    saving  : {
        type    : Boolean,
        required: true,
    },
});

defineEmits(['cancel-edit', 'save-service']);
</script>
