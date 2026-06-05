<template>
    <div
        :class="[isLast ? '' : 'border-bottom pb-2 mb-2']"
    >
        <div class="d-flex justify-content-between align-items-start">
            <div>
                <strong>{{ service.name }}</strong>
                <span
                    v-if="!service.is_active"
                    class="badge bg-secondary ms-2"
                >Неактивна</span>
                <br>
                <small class="text-muted">Код: {{ service.code }}</small>
                <small class="text-muted ms-2">Порядок: {{ service.sort_order }}</small>
            </div>
            <div class="btn-group btn-group-sm">
                <button
                    class="btn btn-outline-primary"
                    :disabled="hasEditingService"
                    @click="$emit('edit-service', service)"
                >
                    <i class="fa fa-pencil"></i>
                </button>
                <button
                    class="btn btn-outline-danger"
                    :disabled="hasEditingService"
                    @click="$emit('delete-service', service)"
                >
                    <i class="fa fa-trash"></i>
                </button>
            </div>
        </div>
    </div>
</template>

<script setup>
defineProps({
    hasEditingService: {
        type    : Boolean,
        required: true,
    },
    isLast           : {
        type    : Boolean,
        required: true,
    },
    service          : {
        type    : Object,
        required: true,
    },
});

defineEmits(['delete-service', 'edit-service']);
</script>
