<template>
    <div class="file">
        <!-- Режим редактирования -->
        <template v-if="modeEdit">
            <div class="form input-group input-group-sm">
                <button class="btn btn-success"
                        @click="save"
                        :disabled="loading"
                        title="Сохранить"
                        aria-label="Сохранить">
                    <i class="fa fa-save"></i>
                </button>
                <button class="btn btn-success"
                        @click="triggerFileUpload"
                        :disabled="loading"
                        title="Заменить файл"
                        aria-label="Заменить файл">
                    <i class="fa fa-upload"></i>
                </button>
                <button class="btn btn-light border"
                        @click="cancelEdit"
                        :disabled="loading"
                        title="Отмена"
                        aria-label="Отмена">
                    <i class="fa fa-window-close"></i>
                </button>
                <custom-input
                    v-model="baseName"
                    :errors="errors.name"
                    placeholder="Название"
                    :disabled="loading"
                    @change="clearError('name')"
                />
            </div>
            <input
                type="file"
                ref="fileInput"
                class="d-none"
                accept="*/*"
                @change="uploadReplacedFile"
            >
        </template>

        <!-- Режим просмотра -->
        <template v-else>
            <div class="d-flex align-items-center" style="min-width:0">
                <!-- Кнопки управления (если есть права на редактирование) -->
                <template v-if="edit">
                    <div class="btn-group btn-group-sm">
                        <button class="btn btn-success"
                                @click="enterEdit"
                                title="Редактировать"
                                aria-label="Редактировать">
                            <i class="fa fa-edit"></i>
                        </button>
                        <template v-if="showUpDownButtons">
                            <button
                                class="btn btn-light border"
                                :disabled="!useUpSort"
                                @click="sortUp(index)"
                                title="Переместить вверх"
                                aria-label="Переместить вверх"
                            >
                                <i class="fa fa-arrow-up"></i>
                            </button>
                            <button
                                class="btn btn-light border"
                                :disabled="!useDownSort"
                                @click="sortDown(index)"
                                title="Переместить вниз"
                                aria-label="Переместить вниз"
                            >
                                <i class="fa fa-arrow-down"></i>
                            </button>
                        </template>
                        <button class="btn btn-danger"
                                @click="deleteFile"
                                title="Удалить"
                                aria-label="Удалить">
                            <i class="fa fa-trash"></i>
                        </button>
                    </div>
                </template>

                <!-- Кнопка скачивания -->
                <a
                    class="btn btn-success btn-sm me-2"
                    :href="file.url"
                    :download="file.name"
                    title="Скачать"
                    aria-label="Скачать"
                >
                    <i :class="['fa', fileIcon]"></i>
                </a>

                <!-- Ссылка на файл -->
                <a
                    :href="file.url"
                    class="name text-truncate"
                    :data-lightbox="file.isImage ? file.name : null"
                    :data-title="file.isImage ? file.name : null"
                    target="_blank"
                    :title="file.name"
                >
                    {{ file.name }}
                </a>
            </div>
        </template>
    </div>
</template>

<script setup>
import {
    computed,
    ref,
    watch,
}                           from 'vue';
import CustomInput          from '@common/form/CustomInput.vue';
import {
    ApiFilesDown,
    ApiFilesReplace,
    ApiFilesUp,
    ApiNewsFileSave,
    ApiNewsFileDelete,
}                           from '@api';
import { useResponseError } from '@composables/useResponseError';

const emit  = defineEmits(['updated']);
const props = defineProps({
    file       : {
        type    : Object,
        required: true,
    },
    edit       : {
        type   : Boolean,
        default: false,
    },
    index      : {
        type   : Number,
        default: null,
    },
    useUpSort  : {
        type   : Boolean,
        default: false,
    },
    useDownSort: {
        type   : Boolean,
        default: false,
    },
});

const { errors, clearError, parseResponseErrors, showSuccess } = useResponseError();
const modeEdit                                                 = ref(false);
const loading                                                  = ref(false);
const baseName                                                 = ref('');
const fileInput                                                = ref(null);

const fileIcon = computed(() => {
    if (props.file.isImage) {
        return 'fa-file-image-o';
    }
    const ext = props.file.ext?.toLowerCase();
    if (ext === 'pdf') {
        return 'fa-file-pdf-o';
    }
    if (['doc', 'docx'].includes(ext)) {
        return 'fa-file-word-o';
    }
    if (['xls', 'xlsx'].includes(ext)) {
        return 'fa-file-excel-o';
    }
    if (['zip', 'rar', '7z'].includes(ext)) {
        return 'fa-file-archive-o';
    }
    return 'fa-file-o';
});

const showUpDownButtons = computed(() => props.useUpSort || props.useDownSort);

const getBaseName = (fullName) => fullName.replace(`.${props.file.ext}`, '');

watch(() => props.file, (newFile) => {
    baseName.value = getBaseName(newFile.name);
}, { deep: true, immediate: true });

const enterEdit = () => {
    modeEdit.value = true;
};

const cancelEdit = () => {
    modeEdit.value = false;
    baseName.value = getBaseName(props.file.name);
    clearError('name');
};

const save = async () => {
    loading.value = true;
    try {
        await ApiNewsFileSave({}, {
            id  : props.file.id,
            name: `${baseName.value}.${props.file.ext}`,
        });
        showSuccess('Файл сохранён');
        emit('updated', true);
        modeEdit.value = false;
    }
    catch (err) {
        parseResponseErrors(err);
    }
    finally {
        loading.value = false;
    }
};

const triggerFileUpload = () => {
    fileInput.value?.click();
};

const uploadReplacedFile = async (event) => {
    const file = event.target.files[0];
    if (!file) {
        return;
    }

    const formData = new FormData();
    formData.append('file', file);
    formData.append('id', props.file.id);

    loading.value = true;
    try {
        await ApiFilesReplace({}, formData);
        showSuccess('Файл заменён');
        emit('updated', true);
        fileInput.value.value = '';
    }
    catch (err) {
        parseResponseErrors(err);
    }
    finally {
        loading.value = false;
    }
};

const deleteFile = async () => {
    if (!confirm('Удалить файл?')) {
        return;
    }

    loading.value = true;
    try {
        await ApiNewsFileDelete(props.file.id);
        showSuccess('Файл удалён');
        emit('updated', true);
    }
    catch (err) {
        parseResponseErrors(err);
    }
    finally {
        loading.value = false;
    }
};

const sortUp = async (index) => {
    loading.value = true;
    try {
        await ApiFilesUp(props.file.id, {}, { index });
        showSuccess('Порядок изменён');
        emit('updated', true);
    }
    catch (err) {
        parseResponseErrors(err);
    }
    finally {
        loading.value = false;
    }
};

const sortDown = async (index) => {
    loading.value = true;
    try {
        await ApiFilesDown(props.file.id, {}, { index });
        showSuccess('Порядок изменён');
        emit('updated', true);
    }
    catch (err) {
        parseResponseErrors(err);
    }
    finally {
        loading.value = false;
    }
};
</script>
