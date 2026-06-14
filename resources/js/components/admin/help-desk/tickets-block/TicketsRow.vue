<template>
    <tr class="text-center align-middle">
        <td class="table-thin-column text-center">
            <a :href="ticket.viewUrl" class="link-firm">
                {{ ticket.id }}
            </a>
        </td>
        <td>{{ ticket.type_name }}</td>
        <td>{{ ticket.category_name }}</td>
        <td>{{ ticket.service_name }}</td>
        <td>{{ ticket.status_name }}</td>
        <td>{{ ticket.priority_name }}</td>
        <td class="text-start">
            <div v-if="ticket.contact_name">{{ ticket.contact_name }}</div>
            <div v-if="ticket.contact_phone">{{ ticket.contact_phone }}</div>
            <div v-if="ticket.contact_email">{{ ticket.contact_email }}</div>
        </td>
        <td>
            <template v-if="ticket.account">
                <a :href="ticket.account.viewUrl" class="link-firm">
                    {{ ticket.account?.number }}
                </a>
            </template>
        </td>
        <td>
            <template v-if="ticket.user">
                <a :href="ticket.user.viewUrl" class="link-firm">
                    {{ ticket.user.fullName }}
                </a>
            </template>
        </td>
        <td>{{ formatDate(ticket.created_at) }}</td>
        <td class="table-thin-column text-center">
            <div class="btn-group btn-group-sm">
                <button v-if="canDeleteRow" class="btn btn-outline-danger" title="Удалить" @click="$emit('delete', ticket.id)">
                    <i class="fa fa-trash"></i>
                </button>
            </div>
        </td>
    </tr>
</template>

<script setup>
import { computed }         from 'vue';
import { useFormat }        from '@composables/useFormat';
import { TicketStatusEnum } from '@utils/enum.js';

const props = defineProps({
    canDelete: { type: Boolean, default: false },
    ticket   : { type: Object, required: true },
});

defineEmits(['delete']);

const { formatDate } = useFormat();

const canDeleteRow = computed(() =>
    props.canDelete &&
    ![TicketStatusEnum.CLOSED.value, TicketStatusEnum.REJECTED.value].includes(props.ticket.status),
);
</script>
