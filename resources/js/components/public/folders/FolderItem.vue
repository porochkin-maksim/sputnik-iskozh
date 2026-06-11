<template>
    <div class="folder-item public-folder-item">
        <a
            class="folder-image"
            :href="folderUrl"
            :style="{ backgroundImage: `url(${folderImage})` }"
            @click.prevent="$emit('open', folder)"
        ></a>
        <div class="public-folder-item__body">
            <a
                class="name public-folder-item__title"
                :href="folderUrl"
                @click.prevent="$emit('open', folder)"
            >
                {{ folder.name }}
            </a>
        </div>
        <div v-if="edit" class="public-folder-item__actions">
            <button
                type="button"
                class="btn btn-outline-success btn-sm public-folder-item__action"
                @click="$emit('rename', folder)"
                aria-label="Переименовать">
                <i class="fa fa-pencil"></i>
            </button>
            <button
                type="button"
                class="btn btn-outline-danger btn-sm public-folder-item__action"
                @click="$emit('delete', folder.id)"
                aria-label="Удалить">
                <i class="fa fa-trash"></i>
            </button>
        </div>
    </div>
</template>

<script setup>
defineProps({
    folder     : {
        type    : Object,
        required: true,
    },
    edit       : {
        type   : Boolean,
        default: false,
    },
    folderImage: {
        type    : String,
        required: true,
    },
    folderUrl  : {
        type    : String,
        required: true,
    },
});

defineEmits(['open', 'rename', 'delete']);
</script>
