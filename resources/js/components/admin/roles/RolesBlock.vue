<template>
    <div>
        <div class="d-flex align-items-center justify-content-between mb-3">
            <button
                v-if="has('roles', 'edit')"
                class="btn btn-success"
                @click="makeAction"
                :disabled="isLoading"
            >
                <i class="fa fa-plus me-1" aria-hidden="true"></i>
                Добавить роль
            </button>
            <history-btn
                v-if="has('roles', 'view')"
                class="btn-link underline-none"
                :url="historyUrl"
            />
        </div>

        <loading-spinner
            v-if="isLoading"
            size="lg"
            color="primary"
            text="Загрузка ролей..."
            wrapper-class="py-5"
        />

        <div v-else-if="error" class="alert alert-danger">
            {{ error }}
        </div>

        <div v-else class="row g-3">
            <RolesList
                :roles="roles"
                :selected-role="selectedRole"
                :deleting="deleting"
                :has="has"
                @edit="editAction"
                @drop="dropAction"
            />
            <RolesEditor
                :selected-role="selectedRole"
                :permissions="permissions"
                :vue-id="vueId"
                :has="has"
                :can-save="canSave"
                :saving="saving"
                :is-checked="isChecked"
                :is-section-checked="isSectionChecked"
                @save="saveAction"
                @change="onChanged"
                @change-section="onChangedSection"
            />
        </div>
    </div>
</template>

<script setup>
import HistoryBtn        from '@common/HistoryBtn.vue';
import LoadingSpinner    from '@common/LoadingSpinner.vue';
import { useRolesBlock } from './roles-block/useRolesBlock';
import RolesEditor       from './roles-block/RolesEditor.vue';
import RolesList         from './roles-block/RolesList.vue';

const props = defineProps({
    permissions: {
        type   : Object,
        default: () => ({}),
    },
});

const emit = defineEmits(['update:checked']);

const {
          canSave,
          checked,
          deleting,
          editAction,
          error,
          historyUrl,
          isChecked,
          isLoading,
          isSectionChecked,
          makeAction,
          onChanged,
          onChangedSection,
          roles,
          saveAction,
          selectedRole,
          saving,
          vueId,
          dropAction,
          has,
      } = useRolesBlock(props, emit);
</script>
