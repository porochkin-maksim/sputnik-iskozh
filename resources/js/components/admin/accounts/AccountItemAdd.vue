<template>
    <div class="account-item-add">
        <account-add-dialog
            :can-save="canSave"
            :form-data="formData"
            :hide-dialog="hideDialog"
            :loading="loading"
            :show-dialog="showDialog"
            @close="onCloseDialog"
            @save="saveAction"
            @update:cadastreNumber="formData.cadastreNumber = $event"
            @update:isInvoicing="formData.isInvoicing = $event"
            @update:number="formData.number = $event"
            @update:size="formData.size = $event"
        />
    </div>
</template>

<script setup>
import AccountAddDialog      from './account-item-add/AccountAddDialog.vue';
import { useAccountItemAdd } from './account-item-add/useAccountItemAdd';

const props = defineProps({
    modelValue: {
        type   : Object,
        default: null,
    },
});

const emit = defineEmits(['updated']);

const {
          canSave,
          formData,
          hideDialog,
          loading,
          onCloseDialog,
          saveAction,
          showDialog,
      } = useAccountItemAdd(props, emit);
</script>
