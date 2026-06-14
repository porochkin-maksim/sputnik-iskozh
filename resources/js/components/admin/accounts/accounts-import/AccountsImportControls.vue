<template>
    <div>
        <div class="mb-3">
            <div class="border rounded p-2">
                <label class="form-label">Файл Excel с участками</label>
                <div class="input-group">
                    <button
                        class="btn btn-primary"
                        @click="clickFileInput"
                        :disabled="loading"
                    >
                        <i class="fa fa-file-excel-o me-2"></i>
                        Выбрать файл
                    </button>
                    <span class="form-control bg-light" v-if="fileName">
                        {{ fileName }}
                    </span>
                </div>
                <input
                    ref="fileInput"
                    type="file"
                    class="d-none"
                    accept=".xlsx, .xls, .csv"
                    @change="onFileChange"
                />
            </div>
        </div>

        <div class="mb-3">
            <div class="btn-group">
                <button
                    class="btn btn-success"
                    @click="$emit('upload')"
                    :disabled="loading || !canUpload"
                >
                    <i class="fa fa-upload me-2"></i>
                    {{ loading ? 'Обработка...' : 'Загрузить и обработать' }}
                </button>
                <button
                    class="btn"
                    :class="submitting || !canSubmit ? 'btn-success' : 'btn-danger'"
                    @click="$emit('submit')"
                    :disabled="submitting || !canSubmit"
                >
                    <i class="fa" :class="submitting ? 'fa-spinner fa-spin' : 'fa-save'"></i>
                    {{ submitting ? 'Сохранение...' : 'Сохранить участки' }}
                </button>
            </div>
        </div>

        <loading-spinner
            v-if="loading"
            size="md"
            color="primary"
            text="Обработка файла..."
            wrapper-class="py-4"
        />
    </div>
</template>

<script setup>
import LoadingSpinner from '@common/LoadingSpinner.vue';

import {
    ref,
    watch,
} from 'vue';

const props = defineProps({
    loading: { type: Boolean, required: true },
    submitting: { type: Boolean, required: true },
    canUpload: { type: Boolean, required: true },
    canSubmit: { type: Boolean, required: true },
    file: { type: Object, default: null },
});

const emit = defineEmits(['upload', 'submit', 'file-selected']);

const fileInput = ref(null);
const fileName  = ref(null);

const clickFileInput = () => {
    fileInput.value?.click();
};

const onFileChange = (event) => {
    const file = event.target.files[0];
    if (!file) {
        return;
    }
    fileName.value = file.name;
    emit('file-selected', file);
    event.target.value = '';
};

watch(() => props.file, (newFile) => {
    if (!newFile) {
        fileName.value = null;
        if (fileInput.value) {
            fileInput.value.value = '';
        }
    }
});
</script>
