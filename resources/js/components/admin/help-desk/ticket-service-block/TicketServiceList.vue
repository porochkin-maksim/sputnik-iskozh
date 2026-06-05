<template>
    <div v-for="(service, index) in services" :key="service.tempId || service.id">
        <template v-if="editingServiceId !== (service.id || service.tempId)">
            <ticket-service-row
                :has-editing-service="hasEditingService"
                :service="service"
                :is-last="index === services.length - 1"
                @delete-service="$emit('delete-service', service)"
                @edit-service="$emit('edit-service', service)"
            />
        </template>

        <template v-else>
            <ticket-service-editor
                :form-data="formData"
                :saving="saving"
                @cancel-edit="$emit('cancel-edit')"
                @save-service="$emit('save-service')"
            />
        </template>
    </div>
</template>

<script setup>
import TicketServiceEditor from './TicketServiceEditor.vue';
import TicketServiceRow    from './TicketServiceRow.vue';

defineProps({
    editingServiceId : {
        type   : [Number, String, null],
        default: null,
    },
    editingTempId    : {
        type   : [Number, String, null],
        default: null,
    },
    formData         : {
        type    : Object,
        required: true,
    },
    hasEditingService: {
        type    : Boolean,
        required: true,
    },
    saving           : {
        type    : Boolean,
        required: true,
    },
    services         : {
        type    : Array,
        required: true,
    },
});

defineEmits(['cancel-edit', 'delete-service', 'edit-service', 'save-service']);
</script>
