<template>
    <div class="col-4">
        <table v-if="has('roles', 'view')" class="table table-sm">
            <thead>
            <tr>
                <th>№</th>
                <th>Название</th>
                <th></th>
            </tr>
            </thead>
            <tbody>
            <template v-for="role in roles" :key="role.id">
                <tr class="align-middle">
                    <td class="text-nowrap">{{ role.name }}</td>
                    <td class="w-100">
                        <a href="#" @click.prevent="$emit('edit', role)">
                            {{ role.name }}
                        </a>
                    </td>
                    <td>
                        <button
                            v-if="has('roles', 'drop')"
                            class="btn"
                            @click="$emit('drop', role.id)"
                            :disabled="deleting === role.id"
                        >
                            <i
                                v-if="deleting === role.id"
                                class="fa fa-spinner fa-spin text-danger"
                            ></i>
                            <i v-else class="fa fa-trash text-danger"></i>
                        </button>
                    </td>
                </tr>
            </template>
            <tr v-if="roles.length === 0">
                <td colspan="3" class="text-center text-muted">
                    Нет доступных ролей
                </td>
            </tr>
            </tbody>
        </table>
    </div>
</template>

<script setup>
defineProps({
    roles   : {
        type    : Array,
        required: true,
    },
    deleting: {
        type    : [Number, String, null],
        required: true,
    },
    has     : {
        type    : Function,
        required: true,
    },
});

defineEmits(['edit', 'drop']);
</script>
