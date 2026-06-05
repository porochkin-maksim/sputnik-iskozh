<template>
    <div v-if="newFiles.length" class="mb-3">
        <label class="form-label fw-semibold">Новые файлы (ожидают загрузки)</label>
        <div class="border rounded p-2">
            <div
                v-for="(file, index) in newFiles"
                :key="`new-${index}`"
                class="d-flex justify-content-between align-items-center mb-2 p-2 border-bottom"
            >
                <div>
                    <i class="fa fa-file-o me-2"></i>llllll
                    {{ file.name }}
                    <span class="text-secondary small ms-2">({{ formatFileSize(file.size) }})</span>
                </div>
                <button
                    type="button"
                    class="btn btn-sm btn-outline-danger"
                    :disabled="!editable"
                    @click="$emit('remove', index)"
                >
                    <i class="fa fa-trash"></i>
                </button>
            </div>
        </div>
    </div>
</template>

<script setup>
defineProps({
    editable      : { type: Boolean, default: true },
    formatFileSize: { type: Function, required: true },
    newFiles      : { type: Array, required: true },
});

defineEmits(['remove']);
</script>
