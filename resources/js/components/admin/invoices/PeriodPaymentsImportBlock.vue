<template>
    <div class="period-payments-import-block">
        <period-payments-import-controls
            ref="controlsRef"
            v-model:mode="mode"
            :import-name="importName"
            :columns="columns"
            :files="files"
            :loading="loading"
            :submitting="submitting"
            :is-columns-valid="isColumnsValid"
            :can-upload="canUpload"
            :can-submit="canSubmit"
            :loading-text="loadingText"
            @file-selected="onFileSelected"
            @upload="uploadFiles"
            @submit="submitPayments"
            @update:import-name="importName = $event"
        />

        <period-payments-import-preview
            :import-data="importData"
            :active-tab="activeTab"
            :submitting="submitting"
            :auto-fill-strategy="autoFillStrategy"
            :fill-strategies="fillStrategies"
            :edited-amounts="editedAmounts"
            :get-key="getKey"
            @update:activeTab="activeTab = $event"
            @apply-auto-fill="applyAutoFill"
            @apply-auto-fill-all="applyAutoFillAll"
            @set-auto-fill-all="setAutoFillForAll"
            @validate-amount="validateAmount"
        />

        <div v-if="error" class="alert alert-danger mt-3">
            {{ error }}
        </div>
    </div>
</template>

<script setup>
import { ref }                          from 'vue';
import PeriodPaymentsImportControls     from './period-payments-import/PeriodPaymentsImportControls.vue';
import PeriodPaymentsImportPreview      from './period-payments-import/PeriodPaymentsImportPreview.vue';
import { usePeriodPaymentsImportBlock } from './period-payments-import/usePeriodPaymentsImportBlock.js';

const props = defineProps({
    periodId: {
        type    : Number,
        required: true,
    },
});

const controlsRef = ref(null);
const {
          activeTab,
          applyAutoFill,
          applyAutoFillAll,
          setAutoFillForAll,
          autoFillStrategy,
          canSubmit,
          canUpload,
          columns,
          editedAmounts,
          error,
          fillStrategies,
          files,
          getKey,
          importData,
          isColumnsValid,
          loading,
          loadingText,
          mode,
          onFileSelected,
          submitPayments: submitPaymentsOriginal,
          submitting,
          uploadFiles,
          validateAmount,
      }           = usePeriodPaymentsImportBlock(props, controlsRef);

const importName = ref('');

const submitPayments = () => {
    submitPaymentsOriginal(importName.value || undefined);
};
</script>
