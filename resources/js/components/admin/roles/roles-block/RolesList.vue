<template>
    <div class="col-12 col-md-4 mb-2 mb-md-0">
        <div v-if="has('roles', 'view')" class="list-group">
            <div
                v-for="role in roles"
                :key="role.id"
                class="list-group-item list-group-item-action d-flex align-items-center justify-content-between"
                :class="{ active: selectedRole?.id === role.id }"
                @click="$emit('edit', role)"
            >
                <span>{{ role.name }}</span>
                <button
                    v-if="has('roles', 'drop')"
                    class="btn btn-sm p-1"
                    @click.stop="$emit('drop', role.id)"
                    :disabled="deleting === role.id"
                >
                    <i
                        v-if="deleting === role.id"
                        class="fa fa-spinner fa-spin text-danger"
                    ></i>
                    <i v-else class="fa fa-trash text-danger"></i>
                </button>
            </div>
            <div v-if="roles.length === 0" class="list-group-item text-center text-muted">
                Нет доступных ролей
            </div>
        </div>
    </div>
</template>

<script setup>
defineProps({
    roles        : {
        type    : Array,
        required: true,
    },
    selectedRole: {
        type   : Object,
        default: null,
    },
    deleting     : {
        type    : [Number, String, null],
        required: true,
    },
    has          : {
        type    : Function,
        required: true,
    },
});

defineEmits(['edit', 'drop']);
</script>
