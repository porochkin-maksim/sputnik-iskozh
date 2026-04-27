<template>
    <div class="file-uploader">
        <!-- Список существующих файлов -->
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

        <!-- Список новых файлов (ожидают загрузки) -->
        <div v-if="newFiles.length" class="mb-3">
            <label class="form-label fw-semibold">Новые файлы (ожидают загрузки)</label>
            <div class="border rounded p-2">
                <div
                    v-for="(file, index) in newFiles"
                    :key="`new-${index}`"
                    class="d-flex justify-content-between align-items-center mb-2 p-2 border-bottom"
                >
                    <div>
                        <i class="fa fa-file-o me-2"></i>
                        {{ file.name }}
                        <span class="text-secondary small ms-2">
                            ({{ formatFileSize(file.size) }})
                        </span>
                    </div>
                    <button
                        type="button"
                        class="btn btn-sm btn-outline-danger"
                        @click="removeNewFile(index)"
                        :disabled="!editable"
                    >
                        <i class="fa fa-trash"></i>
                    </button>
                </div>
            </div>
        </div>

        <!-- Кнопка выбора файлов -->
        <div v-if="editable">
            <button
                type="button"
                class="btn btn-outline-secondary w-100"
                @click="triggerFileSelect"
                :disabled="isMaxFilesReached"
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
import {
    ref,
    computed,
}                   from 'vue';
import FileListItem from './FileListItem.vue';

const props = defineProps({
    // Список уже загруженных файлов (из БД)
    existingFiles: {
        type   : Array,
        default: () => [],
    },
    // Можно ли редактировать (добавлять/удалять файлы)
    editable: {
        type   : Boolean,
        default: true,
    },
    // Текст на кнопке
    buttonText: {
        type   : String,
        default: 'Выбрать файлы',
    },
    // Подпись для блока существующих файлов
    existingFilesLabel: {
        type   : String,
        default: 'Прикреплённые файлы',
    },
    // Разрешенные типы файлов (accept атрибут)
    accept: {
        type   : String,
        default: 'image/*,application/pdf,.doc,.docx,.xls,.xlsx',
    },
    // Максимальное количество файлов
    maxFiles: {
        type   : Number,
        default: 10,
    },
    // Максимальный общий размер файлов (в байтах)
    maxTotalSize: {
        type   : Number,
        default: 20 * 1024 * 1024, // 20 MB
    },
    // Максимальный размер одного файла (в байтах)
    maxFileSize: {
        type   : Number,
        default: 20 * 1024 * 1024, // 5 MB
    },
    // Можно ли выбирать несколько файлов
    multiple: {
        type   : Boolean,
        default: true,
    },
});

const emit = defineEmits(['update:files', 'delete-file']);

const fileInput = ref(null);
const newFiles  = ref([]);

// Вычисляем общий размер новых файлов
const newFilesTotalSize = computed(() => {
    return newFiles.value.reduce((sum, file) => sum + file.size, 0);
});

// Проверка на превышение лимита файлов
const isMaxFilesReached = computed(() => {
    const totalCount = props.existingFiles.length + newFiles.value.length;
    return totalCount >= props.maxFiles;
});

// Проверка на превышение общего размера
const totalSizeExceeded = computed(() => {
    return newFilesTotalSize.value > props.maxTotalSize;
});

// Форматирование размера файла
const formatFileSize = (bytes) => {
    if (bytes === 0) {
        return '0 B';
    }
    const k     = 1024;
    const sizes = ['B', 'KB', 'MB', 'GB'];
    const i     = Math.floor(Math.log(bytes) / Math.log(k));
    return parseFloat((bytes / Math.pow(k, i)).toFixed(2)) + ' ' + sizes[i];
};

// Проверка отдельного файла
const validateFile = (file) => {
    if (file.size > props.maxFileSize) {
        alert(`Файл "${file.name}" превышает максимальный размер (${formatFileSize(props.maxFileSize)})`);
        return false;
    }
    return true;
};

// Выбор файлов
const triggerFileSelect = () => {
    if (isMaxFilesReached.value) {
        alert(`Достигнут лимит файлов (максимум ${props.maxFiles})`);
        return;
    }
    fileInput.value?.click();
};

// Обработка выбора файлов
const handleFileSelect = (event) => {
    const selected   = Array.from(event.target.files);
    const validFiles = [];

    for (const file of selected) {
        if (!validateFile(file)) {
            continue;
        }

        const totalCount = props.existingFiles.length + newFiles.value.length + validFiles.length;
        if (totalCount > props.maxFiles) {
            alert(`Нельзя добавить больше ${props.maxFiles} файлов`);
            break;
        }

        validFiles.push(file);
    }

    newFiles.value.push(...validFiles);
    emitFilesUpdate();
    fileInput.value.value = '';
};

// Удаление нового файла
const removeNewFile = (index) => {
    newFiles.value.splice(index, 1);
    emitFilesUpdate();
};

// Очистка всех новых файлов (используется после успешной отправки)
const clearNewFiles = () => {
    newFiles.value = [];
    emitFilesUpdate();
};

// Эмит события при изменении списка новых файлов
const emitFilesUpdate = () => {
    emit('update:files', newFiles.value);
};

// Доступ к новым файлам из родителя
const getNewFiles = () => {
    return newFiles.value;
};

// Набор методов для родительского компонента
defineExpose({
    clearNewFiles,
    getNewFiles,
    newFiles,
});
</script>