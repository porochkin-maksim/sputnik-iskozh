<template>
    <div class="card">
        <div class="card-header bg-white">
            <div class="d-flex justify-content-between align-items-center">
                <h5>Услуги категории</h5>
                <button
                    class="btn btn-sm btn-primary"
                    :disabled="hasEditingService"
                    @click="createService"
                >
                    <i class="fa fa-plus"></i> Добавить услугу
                </button>
            </div>
        </div>

        <div class="card-body">
            <loading-spinner
                v-if="loadingServices"
                size="sm"
                color="secondary"
                text="Загрузка услуг..."
                wrapper-class="py-3"
            />

            <div
                v-else-if="services.length === 0"
                class="text-muted"
            >
                Услуг пока нет. Добавьте первую.
            </div>

            <ticket-service-list
                v-else
                :editing-service-id="editingServiceId"
                :editing-temp-id="editingTempId"
                :has-editing-service="hasEditingService"
                :saving="saving"
                :services="services"
                :form-data="formData"
                @cancel-edit="cancelEdit"
                @delete-service="deleteService"
                @edit-service="editService"
                @save-service="saveService"
            />
        </div>
    </div>
</template>

<script setup>
import { defineProps } from 'vue';

import LoadingSpinner            from '@common/LoadingSpinner.vue';
import { useTicketServiceBlock } from './ticket-service-block/useTicketServiceBlock';
import TicketServiceList         from './ticket-service-block/TicketServiceList.vue';

const props = defineProps({
    categoryId: {
        type    : Number,
        required: true,
    },
});

const {
          cancelEdit,
          createService,
          deleteService,
          editService,
          editingServiceId,
          editingTempId,
          formData,
          hasEditingService,
          loadingServices,
          saveService,
          services,
          saving,
      } = useTicketServiceBlock(props);
</script>
