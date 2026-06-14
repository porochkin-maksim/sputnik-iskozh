<template>
    <div class="card mb-3">
        <div class="card-header bg-white d-flex justify-content-between align-items-center">
            <h5 class="mb-0">Заявка №{{ ticketId }} от {{ formatDate(createdAt) }}</h5>
            <button v-if="canDelete" class="btn btn-sm btn-outline-danger" @click="$emit('delete-ticket')">
                <i class="fa fa-trash"></i> Удалить
            </button>
        </div>
        <form @submit.prevent="$emit('save-ticket')">
            <div class="card-body">
                <custom-textarea
                    v-model="editForm.description"
                    label="Описание заявки"
                    required
                    :errors="errors.description"
                    :disabled="!canEdit"
                    @change="$emit('clear-error', 'description')"
                />
            </div>
            <div class="card-header bg-white border-top">
                <h6 class="mb-0">Состояние</h6>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-6">
                        <custom-select
                            v-model="editForm.status"
                            :options="statusOptions"
                            label="Статус"
                            :errors="errors.status"
                            :disabled="!canEdit"
                            @change="$emit('clear-error', 'status')"
                        />
                        <div class="mt-2">
                            <custom-select
                                v-model="editForm.priority"
                                :options="priorityOptions"
                                label="Приоритет"
                                :errors="errors.priority"
                                :disabled="!canEdit"
                                @change="$emit('clear-error', 'priority')"
                            />
                        </div>
                    </div>
                    <div class="col-6">
                        <custom-select
                            v-model="editForm.type"
                            :options="typeOptions"
                            label="Тип"
                            :errors="errors.type"
                            :disabled="!canEdit"
                            @change="$emit('type-change')"
                        />
                        <div class="mt-2">
                            <custom-select
                                v-model="editForm.category_id"
                                :options="categoryOptions"
                                label="Категория"
                                :errors="errors.category_id"
                                :disabled="!canEdit"
                                @change="$emit('category-change')"
                            />
                        </div>
                        <div class="mt-2">
                            <custom-select
                                v-model="editForm.service_id"
                                :options="serviceOptions"
                                label="Услуга"
                                :errors="errors.service_id"
                                :disabled="!canEdit"
                                @change="$emit('clear-error', 'service_id')"
                            />
                        </div>
                    </div>
                </div>
            </div>
            <div class="card-header bg-white border-top">
                <h6 class="mb-0">Результат работы</h6>
            </div>
            <div class="card-body">
                <custom-textarea
                    v-model="editForm.result"
                    label="Ответ"
                    :errors="errors.result"
                    :disabled="!canEdit"
                />

                <div class="mt-3">
                    <file-uploader
                        ref="resultFilesUploader"
                        :existing-files="existingResultFiles"
                        :editable="canEdit"
                        button-text="Прикрепить файлы к ответу"
                        existing-files-label="Файлы ответа"
                        :max-files="5"
                        :max-total-size="10 * 1024 * 1024"
                        accept="image/*,application/pdf"
                        @update:files="$emit('result-files-update', $event)"
                        @delete-file="$emit('delete-result-file', $event)"
                    />
                </div>
            </div>
            <div class="card-footer">
                <div v-if="canEdit" class="d-flex gap-2">
                    <button type="submit" class="btn btn-primary" :disabled="saving">
                        <i v-if="saving" class="fa fa-spinner fa-spin"></i>
                        {{ saving ? 'Сохранение...' : 'Сохранить' }}
                    </button>
                </div>
            </div>
        </form>
    </div>
</template>

<script setup>
import { ref }        from 'vue';
import CustomTextarea from '@common/form/CustomTextarea.vue';
import CustomSelect   from '@common/form/CustomSelect.vue';
import FileUploader   from '@common/files/FileUploader.vue';

defineProps({
    categoryOptions    : { type: Array, required: true },
    canDelete          : { type: Boolean, required: true },
    createdAt          : { type: String, default: null },
    canEdit            : { type: Boolean, required: true },
    editForm           : { type: Object, required: true },
    errors             : { type: Object, required: true },
    existingResultFiles: { type: Array, required: true },
    formatDate         : { type: Function, required: true },
    priorityOptions    : { type: Array, required: true },
    saving             : { type: Boolean, required: true },
    serviceOptions     : { type: Array, required: true },
    statusOptions      : { type: Array, required: true },
    ticketId           : { type: [String, Number], required: true },
    typeOptions        : { type: Array, required: true },
});

defineEmits([
    'category-change',
    'clear-error',
    'delete-result-file',
    'delete-ticket',
    'result-files-update',
    'save-ticket',
    'type-change',
]);

const resultFilesUploader = ref(null);

defineExpose({
    clearNewFiles: () => {
        resultFilesUploader.value?.clearNewFiles();
    },
});
</script>
