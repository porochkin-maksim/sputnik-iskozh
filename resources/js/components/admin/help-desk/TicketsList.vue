<template>
    <div class="table-responsive">
        <table v-if="tickets.length" class="table table-sm table-striped table-bordered table-hover align-middle admin-table-firm">
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
            <ticket-row
                v-for="ticket in tickets"
                :key="ticket.id"
                :can-delete="canDelete"
                :ticket="ticket"
                @delete="$emit('delete', $event)"
            />
            </tbody>
        </table>
        <div v-else class="alert alert-info text-center my-3">
            <i class="fa fa-info-circle me-2"></i> Заявки не найдены
        </div>
    </div>
</template>

<script setup>
import TicketRow from './tickets-block/TicketsRow.vue';

const props = defineProps({
    tickets  : { type: Array, default: () => [] },
    sortField: { type: String, default: 'id' },
    sortOrder: { type: String, default: 'desc' },
    canDelete: { type: Boolean, default: false },
});

const emit = defineEmits(['sort', 'delete']);

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
</script>
