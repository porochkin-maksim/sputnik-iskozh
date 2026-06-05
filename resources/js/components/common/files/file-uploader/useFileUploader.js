import {
    computed,
    ref,
} from 'vue';

export function useFileUploader (props, emit) {
    const fileInput = ref(null);
    const newFiles  = ref([]);

    const newFilesTotalSize = computed(() => newFiles.value.reduce((sum, file) => sum + file.size, 0));

    const isMaxFilesReached = computed(() => {
        const totalCount = props.existingFiles.length + newFiles.value.length;
        return totalCount >= props.maxFiles;
    });

    const totalSizeExceeded = computed(() => newFilesTotalSize.value > props.maxTotalSize);

    const formatFileSize = (bytes) => {
        if (bytes === 0) {
            return '0 B';
        }

        const k     = 1024;
        const sizes = ['B', 'KB', 'MB', 'GB'];
        const i     = Math.floor(Math.log(bytes) / Math.log(k));
        return parseFloat((bytes / Math.pow(k, i)).toFixed(2)) + ' ' + sizes[i];
    };

    const validateFile = (file) => {
        if (file.size > props.maxFileSize) {
            alert(`Файл "${file.name}" превышает максимальный размер (${formatFileSize(props.maxFileSize)})`);
            return false;
        }

        return true;
    };

    const emitFilesUpdate = () => {
        emit('update:files', newFiles.value);
    };

    const triggerFileSelect = () => {
        if (isMaxFilesReached.value) {
            alert(`Достигнут лимит файлов (максимум ${props.maxFiles})`);
            return;
        }

        fileInput.value?.click();
    };

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

    const removeNewFile = (index) => {
        newFiles.value.splice(index, 1);
        emitFilesUpdate();
    };

    const clearNewFiles = () => {
        newFiles.value = [];
        emitFilesUpdate();
    };

    const getNewFiles = () => newFiles.value;

    return {
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
    };
}
