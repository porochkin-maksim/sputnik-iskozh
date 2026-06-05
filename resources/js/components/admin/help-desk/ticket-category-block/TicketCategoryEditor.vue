<template>
    <div class="card">
        <div class="card-header bg-white">
            <h5 class="m-0">{{ formTitle }}</h5>
        </div>
        <form @submit.prevent="$emit('save-category')">
            <div class="card-body">
                <div class="mb-2">
                    <simple-select
                        v-model="formData.type"
                        :options="types"
                        label="Тип заявки"
                        :clearable="false"
                    />
                </div>

                <div class="mb-2">
                    <custom-input
                        v-model="formData.name"
                        label="Название категории"
                        :disabled="saving"
                        required
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

                <div class="mb-2">
                    <custom-checkbox
                        v-model="formData.is_active"
                        label="Активна"
                        :disabled="saving"
                        switch-style
                    />
                </div>
            </div>
            <div class="card-footer bg-white d-flex gap-2">
                <button
                    type="submit"
                    class="btn btn-primary"
                    :disabled="saving"
                >
                    <i
                        v-if="saving"
                        class="fa fa-spinner fa-spin"
                    ></i>
                    {{ saving ? 'Сохранение...' : 'Сохранить' }}
                </button>
                <button
                    v-if="selectedCategory.id"
                    type="button"
                    class="btn btn-outline-danger"
                    :disabled="saving"
                    @click="$emit('delete-category')"
                >
                    Удалить
                </button>
                <button
                    type="button"
                    class="btn btn-outline-secondary"
                    @click="$emit('reset-form')"
                >
                    Отмена
                </button>
            </div>
        </form>
    </div>
</template>

<script setup>
import CustomCheckbox from '@common/form/CustomCheckbox.vue';
import CustomInput    from '@common/form/CustomInput.vue';
import SimpleSelect   from '@common/form/SimpleSelect.vue';

defineProps({
    formData        : {
        type    : Object,
        required: true,
    },
    formTitle       : {
        type    : String,
        required: true,
    },
    saving          : {
        type    : Boolean,
        required: true,
    },
    selectedCategory: {
        type   : Object,
        default: null,
    },
    types           : {
        type    : Array,
        required: true,
    },
});

defineEmits(['delete-category', 'reset-form', 'save-category']);
</script>
