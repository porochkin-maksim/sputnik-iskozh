<template>
    <div class="card mb-3">
        <div class="card-header bg-white">
            <h5 class="mb-0">Файлы</h5>
        </div>
        <div class="card-body">
            <file-uploader
                ref="ticketFilesUploader"
                :existing-files="existingFiles"
                :editable="canEdit"
                button-text="Прикрепить файлы"
                existing-files-label="Файлы заявки"
                :max-files="10"
                :max-total-size="20 * 1024 * 1024"
                accept="image/*,application/pdf,.doc,.docx"
                @update:files="$emit('ticket-files-update', $event)"
                @delete-file="$emit('delete-ticket-file', $event)"
            />
        </div>
    </div>
</template>

<script setup>
import { ref }      from 'vue';
import FileUploader from '@common/files/FileUploader.vue';

defineProps({
    canEdit      : { type: Boolean, required: true },
    existingFiles: { type: Array, required: true },
});

defineEmits(['delete-ticket-file', 'ticket-files-update']);

const ticketFilesUploader = ref(null);

defineExpose({
    clearNewFiles: () => {
        ticketFilesUploader.value?.clearNewFiles();
    },
});
</script>
