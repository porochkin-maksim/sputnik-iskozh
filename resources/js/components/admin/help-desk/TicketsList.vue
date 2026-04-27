<template>
    <div class="table-responsive">
        <table v-if="tickets.length" class="table table-sm table-striped table-bordered">
            <thead>
            <tr class="text-center">
                <th class="cursor-pointer" @click="sort('id')">
                    №
                    <i :class="sortIcon('id')"></i>
                </th>
                <th class="cursor-pointer" @click="sort('type')">
                    Тип
                    <i :class="sortIcon('type')"></i>
                </th>
                <th>Категория</th>
                <th>Вид</th>
                <th class="cursor-pointer" @click="sort('status')">
                    Статус
                    <i :class="sortIcon('status')"></i>
                </th>
                <th class="cursor-pointer" @click="sort('priority')">
                    Приоритет
                    <i :class="sortIcon('priority')"></i>
                </th>
                <th>Контакты</th>
                <th>Участок</th>
                <th>Пользователь</th>
                <th class="cursor-pointer" @click="sort('created_at')">
                    Создана
                    <i :class="sortIcon('created_at')"></i>
                </th>
                <th class="text-center" v-if="canDelete"></th>
            </tr>
            </thead>
            <tbody>
            <tr v-for="ticket in tickets" :key="ticket.id" class="text-center">
                <td class="text-end">
                    <a :href="ticket.viewUrl" class="text-decoration-none">
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
                        <a :href="ticket.account.viewUrl" class="text-decoration-none">
                            {{ ticket.account?.number }}
                        </a>
                    </template></td>
                <td>
                    <template v-if="ticket.user">
                        <a :href="ticket.user.viewUrl" class="text-decoration-none">
                            {{ ticket.user.fullName }}
                        </a>
                    </template>
                </td>
                <td>{{ formatDate(ticket.created_at) }}</td>
                <td class="text-center" v-if="canDelete">
                    <div class="btn-group btn-group-sm">
                        <button class="btn btn-outline-danger" @click="$emit('delete', ticket.id)" title="Удалить">
                            <i class="fa fa-trash"></i>
                        </button>
                    </div>
                </td>
            </tr>
            </tbody>
        </table>
        <div v-else class="alert alert-info text-center my-3">
            <i class="fa fa-info-circle me-2"></i> Заявки не найдены
        </div>
    </div>
</template>

<script setup>
import { useFormat } from '@composables/useFormat';
import { usePermissions } from '@composables/usePermissions.js';
import { computed } from 'vue';

// Права доступа
const { has } = usePermissions();
const canView   = computed(() => has('help_desk', 'view'));
const canDelete = false;//computed(() => has('help_desk', 'drop'));

const props = defineProps({
    tickets  : { type: Array, default: () => [] },
    sortField: { type: String, default: 'id' },
    sortOrder: { type: String, default: 'desc' },
});

const emit = defineEmits(['sort', 'delete']);

const { formatDate } = useFormat();

const sort = (field) => {
    let order = 'asc';
    if (props.sortField === field) {
        order = props.sortOrder === 'asc' ? 'desc' : 'asc';
    }
    else {
        order = 'asc';
    }
    emit('sort', { field, order });
};

const sortIcon = (field) => {
    if (field !== props.sortField) {
        return 'fa fa-sort';
    }
    return props.sortOrder === 'asc' ? 'fa fa-sort-asc' : 'fa fa-sort-desc';
};

const statusBadgeClass = (status) => {
    const map = {
        new        : 'badge bg-primary',
        in_progress: 'badge bg-warning text-dark',
        resolved   : 'badge bg-info',
        closed     : 'badge bg-secondary',
    };
    return map[status] || 'badge bg-light';
};

const priorityBadgeClass = (priority) => {
    const map = {
        low     : 'badge bg-success',
        medium  : 'badge bg-primary',
        high    : 'badge bg-warning',
        critical: 'badge bg-danger',
    };
    return map[priority] || 'badge bg-light';
};
</script>