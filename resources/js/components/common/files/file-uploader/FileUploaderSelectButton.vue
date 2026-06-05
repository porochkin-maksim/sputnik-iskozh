<template>
    <button
        type="button"
        class="btn btn-outline-secondary w-100"
        :disabled="isMaxFilesReached"
        @click="$emit('trigger')"
    >
        <i class="fa fa-paperclip me-2"></i>
        <template v-if="totalSizeExceeded">
            Общий размер файлов превышает {{ formatFileSize(maxTotalSize) }}
        </template>
        <template v-else-if="isMaxFilesReached">
            Достигнут лимит файлов (максимум {{ maxFiles }})
        </template>
        <template v-else>
            {{ buttonText }}
        </template>
    </button>
</template>

<script setup>
defineProps({
    buttonText       : { type: String, required: true },
    formatFileSize   : { type: Function, required: true },
    isMaxFilesReached: { type: Boolean, required: true },
    maxFiles         : { type: Number, required: true },
    maxTotalSize     : { type: Number, required: true },
    totalSizeExceeded: { type: Boolean, required: true },
});

defineEmits(['trigger']);
</script>
