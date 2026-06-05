<template>
    <div>
        <div class="d-flex justify-content-between align-items-center mb-2">
            <h5>Категории</h5>
            <button
                class="btn btn-sm btn-primary"
                @click="$emit('open-type-dialog')"
            >
                <i class="fa fa-plus"></i> Добавить
            </button>
        </div>

        <div class="list-group">
            <template
                v-for="cat in categories"
                :key="cat.id"
            >
                <button
                    v-if="cat.id"
                    class="list-group-item list-group-item-action d-flex justify-content-between align-items-center"
                    :class="{ active: selectedCategory && selectedCategory.id === cat.id }"
                    @click="$emit('select-category', cat)"
                >
                    <span>
                        <strong>{{ cat.name }}</strong>
                        &nbsp;<small class="text-muted">{{ cat.type_name }}</small>
                    </span>
                    <span
                        v-if="!cat.is_active"
                        class="badge bg-secondary"
                    >Неактивна</span>
                </button>
            </template>
        </div>

        <div
            v-if="categories.length === 0"
            class="text-muted mt-3"
        >
            Категории не найдены
        </div>
    </div>
</template>

<script setup>
defineProps({
    categories      : {
        type    : Array,
        required: true,
    },
    selectedCategory: {
        type   : Object,
        default: null,
    },
});

defineEmits(['open-type-dialog', 'select-category']);
</script>
