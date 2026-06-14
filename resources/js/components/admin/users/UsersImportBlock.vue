<template>
    <div>
        <users-import-controls
            :loading="loading"
            :submitting="submitting"
            :can-upload="canUpload"
            :can-submit="canSubmit"
            :file="file"
            @upload="uploadFiles"
            @submit="submitUsers"
            @file-selected="file = $event"
        />

        <div v-if="error" class="alert alert-danger mt-3">
            {{ error }}
        </div>

        <users-import-preview
            v-if="items.length"
            :items="items"
            :total="total"
            :changes="changes"
        />

        <div v-else-if="parsed && total > 0" class="alert alert-success mt-3 d-flex align-items-center gap-2">
            <i class="fa fa-check-circle fa-lg" aria-hidden="true"></i>
            <div>
                <p class="mb-0">Всего пользователей в файле: <strong>{{ total }}</strong>. Различий с текущими данными нет.</p>
            </div>
        </div>

        <div v-if="submitted" class="alert alert-success mt-3 d-flex align-items-center gap-2">
            <i class="fa fa-check-circle fa-lg" aria-hidden="true"></i>
            <div>
                <p class="mb-0">Пользователи будут сохранены в фоне.</p>
            </div>
        </div>
    </div>
</template>

<script setup>
import { useUsersImportBlock } from './users-import/useUsersImportBlock';
import UsersImportControls     from './users-import/UsersImportControls.vue';
import UsersImportPreview      from './users-import/UsersImportPreview.vue';

const {
          canSubmit,
          canUpload,
          changes,
          error,
          file,
          items,
          loading,
          parsed,
          submitted,
          submitting,
          total,
          uploadFiles,
          submitUsers,
      } = useUsersImportBlock();
</script>
