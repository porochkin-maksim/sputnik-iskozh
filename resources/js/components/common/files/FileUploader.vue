<template>
    <div class="file-uploader">
        <div v-if="existingFiles.length" class="mb-3">
            <label class="form-label fw-semibold">{{ existingFilesLabel }}</label>
            <div>
                <file-list-item
                    v-for="(file, index) in existingFiles"
                    :key="file.id"
                    :file="file"
                    :edit="editable"
                    :class="index === existingFiles.length - 1 ? '' : 'mb-2'"
                    @delete="$emit('delete-file', file)"
                />
            </div>
        </div>

        <file-uploader-pending-list
            :editable="editable"
            :format-file-size="formatFileSize"
            :new-files="newFiles"
            @remove="removeNewFile"
        />

        <div v-if="editable">
            <file-uploader-select-button
                :button-text="buttonText"
                :format-file-size="formatFileSize"
                :is-max-files-reached="isMaxFilesReached"
                :max-files="maxFiles"
                :max-total-size="maxTotalSize"
                :total-size-exceeded="totalSizeExceeded"
                @trigger="triggerFileSelect"
            />
            <input
                type="file"
                ref="fileInput"
                class="d-none"
                :accept="accept"
                :multiple="multiple"
                @change="handleFileSelect"
            />
        </div>
    </div>
</template>

<script setup>
import FileListItem             from './FileListItem.vue';
import FileUploaderPendingList  from './file-uploader/FileUploaderPendingList.vue';
import FileUploaderSelectButton from './file-uploader/FileUploaderSelectButton.vue';
import { useFileUploader }      from './file-uploader/useFileUploader.js';

const props = defineProps({
    existingFiles     : {
        type   : Array,
        default: () => [],
    },
    editable          : {
        type   : Boolean,
        default: true,
    },
    buttonText        : {
        type   : String,
        default: 'Выбрать файлы',
    },
    existingFilesLabel: {
        type   : String,
        default: 'Прикреплённые файлы',
    },
    accept            : {
        type   : String,
        default: 'image/*,application/pdf,.doc,.docx,.xls,.xlsx',
    },
    maxFiles          : {
        type   : Number,
        default: 10,
    },
    maxTotalSize      : {
        type   : Number,
        default: 20 * 1024 * 1024, // 20 MB
    },
    maxFileSize       : {
        type   : Number,
        default: 20 * 1024 * 1024, // 5 MB
    },
    multiple          : {
        type   : Boolean,
        default: true,
    },
});

const emit = defineEmits(['update:files', 'delete-file']);
const {
          clearNewFiles,
          fileInput,
          formatFileSize,
          getNewFiles,
          handleFileSelect,
          isMaxFilesReached,
          newFiles,
          removeNewFile,
          totalSizeExceeded,
          triggerFileSelect,
      }    = useFileUploader(props, emit);

defineExpose({
    clearNewFiles,
    getNewFiles,
    newFiles,
});
</script>
