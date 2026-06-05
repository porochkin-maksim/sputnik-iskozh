<template>
    <div class="help-desk-ticket-view">
        <div v-if="ticketData" class="row">
            <!-- Левая колонка: основная информация -->
            <div class="col-6">
                <ticket-main-panel
                    ref="resultFilesPanel"
                    :can-delete="canDelete"
                    :can-edit="canEdit"
                    :category-options="categoryOptions"
                    :edit-form="editForm"
                    :errors="errors"
                    :existing-result-files="existingResultFiles"
                    :format-date="formatDate"
                    :priority-options="priorityOptions"
                    :saving="saving"
                    :service-options="serviceOptions"
                    :status-options="statusOptions"
                    :ticket-id="ticketData.id"
                    :type-options="typeOptions"
                    @category-change="onCategoryChange"
                    @clear-error="clearError"
                    @delete-result-file="deleteResultFile"
                    @delete-ticket="deleteTicket"
                    @result-files-update="onResultFilesUpdate"
                    @save-ticket="saveTicket"
                    @type-change="onTypeChange"
                />

                <ticket-comments-panel class="mt-3" />
            </div>

            <!-- Правая колонка: файлы и другая информация -->
            <div class="col-6">
                <ticket-contact-panel
                    :can-edit="canEdit"
                    :edit-form="editForm"
                    :errors="errors"
                    :user-options="userOptions"
                    @clear-error="clearError"
                />

                <ticket-files-panel
                    ref="ticketFilesPanel"
                    class="mb-3"
                    :can-edit="canEdit"
                    :existing-files="existingFiles"
                    @delete-ticket-file="deleteTicketFile"
                    @ticket-files-update="onTicketFilesUpdate"
                />
            </div>
        </div>
    </div>
</template>

<script setup>
import TicketCommentsPanel from './tickets-view/TicketCommentsPanel.vue';
import TicketContactPanel  from './tickets-view/TicketContactPanel.vue';
import TicketFilesPanel    from './tickets-view/TicketFilesPanel.vue';
import TicketMainPanel     from './tickets-view/TicketMainPanel.vue';
import { useTicketsView }  from './tickets-view/useTicketsView';

const props = defineProps({
    ticket    : { type: Object, required: true },
    users     : { type: Array, default: () => [] },
    accounts  : { type: Array, default: () => [] },
    types     : { type: Array, default: () => [] },
    categories: { type: Array, default: () => [] },
    services  : { type: Array, default: () => [] },
    statuses  : { type: Array, default: () => [] },
    priorities: { type: Array, default: () => [] },
});

const {
          canDelete,
          canEdit,
          categoryOptions,
          clearError,
          deleteResultFile,
          deleteTicket,
          deleteTicketFile,
          editForm,
          errors,
          existingFiles,
          existingResultFiles,
          formatDate,
          newResultFiles,
          onCategoryChange,
          onResultFilesUpdate,
          onTicketFilesUpdate,
          onTypeChange,
          priorityOptions,
          resultFilesPanel,
          saveTicket,
          saving,
          serviceOptions,
          statusOptions,
          ticketData,
          ticketFilesPanel,
          typeOptions,
          userOptions,
      } = useTicketsView(props);
</script>
