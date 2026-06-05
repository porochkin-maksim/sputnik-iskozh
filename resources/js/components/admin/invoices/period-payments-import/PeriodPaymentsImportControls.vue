<template>
    <div>
        <div class="mb-3 row g-2 admin-toolbar">
            <div class="col-md-4">
                <custom-input
                    v-model="columns.accrued"
                    label="Колонка Начислено"
                    placeholder="например: E или 5"
                    required
                />
            </div>
            <div class="col-md-4">
                <custom-input
                    v-model="columns.paid"
                    label="Колонка Оплачено"
                    placeholder="например: T или 20"
                    required
                />
            </div>
            <div class="col-md-4">
                <custom-input
                    v-model="columns.debt"
                    label="Колонка Долг"
                    placeholder="например: AI или 35"
                    required
                />
            </div>
        </div>

        <div class="mb-3">
            <div class="btn-group" role="group">
                <button
                    type="button"
                    class="btn btn-outline-primary"
                    :class="{ active: mode === 'single' }"
                    @click="$emit('update:mode', 'single')"
                >
                    Один файл
                </button>
                <button
                    type="button"
                    class="btn btn-outline-primary"
                    :class="{ active: mode === 'diff' }"
                    @click="$emit('update:mode', 'diff')"
                >
                    Сравнить два файла
                </button>
            </div>
        </div>

        <div class="mb-3">
            <div class="row g-2">
                <div class="col-md-6">
                    <div class="border rounded p-2">
                        <label class="form-label">Основной файл (последний)</label>
                        <div class="input-group">
                            <button
                                class="btn btn-primary"
                                @click="clickFileInput('main')"
                                :disabled="loading || !isColumnsValid"
                            >
                                <i class="fa fa-file-excel-o me-2"></i>
                                Выбрать файл
                            </button>
                            <span class="form-control bg-light" v-if="files.main">
                                {{ files.main.name }}
                            </span>
                        </div>
                        <input
                            ref="mainFileInput"
                            type="file"
                            class="d-none"
                            accept=".xlsx, .xls, .csv"
                            @change="$emit('file-selected', 'main', $event)"
                        />
                    </div>
                </div>
                <div class="col-md-6" v-if="mode === 'diff'">
                    <div class="border rounded p-2">
                        <label class="form-label">Предыдущий файл (для сравнения)</label>
                        <div class="input-group">
                            <button
                                class="btn btn-secondary"
                                @click="clickFileInput('prev')"
                                :disabled="loading || !isColumnsValid"
                            >
                                <i class="fa fa-file-excel-o me-2"></i>
                                Выбрать файл
                            </button>
                            <span class="form-control bg-light" v-if="files.prev">
                                {{ files.prev.name }}
                            </span>
                        </div>
                        <input
                            ref="prevFileInput"
                            type="file"
                            class="d-none"
                            accept=".xlsx, .xls, .csv"
                            @change="$emit('file-selected', 'prev', $event)"
                        />
                    </div>
                </div>
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
                    {{ submitting ? loadingText : 'Сохранить платежи' }}
                </button>
            </div>
        </div>

        <loading-spinner
            v-if="loading"
            size="md"
            color="primary"
            :text="loadingText"
            wrapper-class="py-4"
        />
    </div>
</template>

<script setup>
import CustomInput    from '@common/form/CustomInput.vue';
import LoadingSpinner from '@common/LoadingSpinner.vue';

import { ref } from 'vue';

const props = defineProps({
    columns       : {
        type    : Object,
        required: true,
    },
    mode          : {
        type    : String,
        required: true,
    },
    files         : {
        type    : Object,
        required: true,
    },
    loading       : {
        type    : Boolean,
        required: true,
    },
    submitting    : {
        type    : Boolean,
        required: true,
    },
    isColumnsValid: {
        type    : Boolean,
        required: true,
    },
    canUpload     : {
        type    : Boolean,
        required: true,
    },
    canSubmit     : {
        type    : Boolean,
        required: true,
    },
    loadingText   : {
        type    : String,
        required: true,
    },
});

const emit = defineEmits(['update:mode', 'file-selected', 'upload', 'submit']);

const mainFileInput = ref(null);
const prevFileInput = ref(null);

const clickFileInput = (type) => {
    if (type === 'main') {
        mainFileInput.value?.click();
    }
    else {
        prevFileInput.value?.click();
    }
};

const resetFileInputs = () => {
    if (mainFileInput.value) {
        mainFileInput.value.value = '';
    }
    if (prevFileInput.value) {
        prevFileInput.value.value = '';
    }
};

defineExpose({
    clickFileInput,
    resetFileInputs,
});
</script>
