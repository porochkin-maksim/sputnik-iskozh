<template>
    <div>
        <h5>Услуги</h5>
        <div>
            <button
                v-if="invoice.actions.claims.edit"
                class="btn btn-success mb-2"
                @click="makeAction"
            >
                <i class="fa fa-plus" aria-hidden="true"></i>
                Добавить услугу
            </button>
        </div>

        <claims-list
            :invoice-id="invoice.id"
            v-model:selected-id="selectedId"
            v-model:reload="reloadList"
            v-model:count="claimCount"
            @update:count="onUpdatedCount"
        />

        <claim-editor
            :claim="claim"
            :can-save="canSave"
            :errors="errors"
            :hide-dialog="hideDialog"
            :loading="loading"
            :selected-id="selectedId"
            :services-select="servicesSelect"
            :show-dialog="showDialog"
            @clear-error="clearError"
            @cost-changed="onCostChanged"
            @hidden="closeAction"
            @save="saveAction"
            @service-id-changed="onServiceIdChanged"
            @tariff-changed="onTariffChanged"
        />
    </div>
</template>

<script setup>
import {
    defineOptions,
    defineProps,
}                        from 'vue';
import ClaimsList        from './ClaimsList.vue';
import ClaimEditor       from './claim-block/ClaimEditor.vue';
import { useClaimBlock } from './claim-block/useClaimBlock';

const props = defineProps({
    invoice: {
        type    : Object,
        required: true,
    },
    reload : {
        type   : Boolean,
        default: false,
    },
    count  : {
        type   : Number,
        default: 0,
    },
});

const emit = defineEmits(['update:count', 'update:reload']);

const {
          canSave,
          claim,
          claimCount,
          clearError,
          closeAction,
          errors,
          hideDialog,
          loading,
          makeAction,
          onCostChanged,
          onServiceIdChanged,
          onTariffChanged,
          onUpdatedCount,
          reloadList,
          saveAction,
          selectedId,
          servicesSelect,
          showDialog,
      } = useClaimBlock(props, emit);
</script>
