<template>
    <div class="folder-item public-folder-item public-folder-item--file">
        <a
            class="folder-image"
            target="_blank"
            :style="{ backgroundImage: `url(${fileImage})` }"
            :href="file.url"
            :data-title="file.name"
        ></a>
        <div class="public-folder-item__body">
            <template v-if="edit">
                <div class="public-folder-item__actions public-folder-item__actions--wrap">
                    <button
                        type="button"
                        class="btn btn-outline-success btn-sm public-folder-item__action"
                        @click="$emit('rename', file)"
                        aria-label="Переименовать"
                    >
                        <i class="fa fa-pencil"></i>
                    </button>
                    <button
                        type="button"
                        class="btn btn-outline-success btn-sm public-folder-item__action"
                        @click="$emit('copy', file.id)"
                        aria-label="Копировать"
                    >
                        <i class="fa fa-copy"></i>
                    </button>
                    <button
                        type="button"
                        class="btn btn-outline-success btn-sm public-folder-item__action"
                        @click="$emit('replace', file.id)"
                        aria-label="Заменить"
                    >
                        <i class="fa fa-refresh"></i>
                    </button>
                    <button
                        type="button"
                        class="btn btn-outline-success btn-sm public-folder-item__action"
                        @click="$emit('cut', file.id)"
                        aria-label="Вырезать"
                    >
                        <i class="fa fa-cut"></i>
                    </button>
                    <button
                        type="button"
                        class="btn btn-outline-danger btn-sm public-folder-item__action"
                        @click="$emit('delete', file.id)"
                        aria-label="Удалить"
                    >
                        <i class="fa fa-trash"></i>
                    </button>
                </div>
            </template>
            <a
                class="name public-folder-item__title"
                target="_blank"
                :href="file.url"
                :data-lightbox="file.isImage ? 'files' : null"
                :data-title="file.name"
            >
                {{ file.name }}
            </a>
        </div>
        <a class="btn btn-outline-success btn-sm public-folder-item__download"
           :href="file.url"
           :download="file.name"
           aria-label="Скачать">
            <i class="fa fa-download"></i>
        </a>
    </div>
</template>

<script setup>
defineProps({
    file     : {
        type    : Object,
        required: true,
    },
    edit     : {
        type   : Boolean,
        default: false,
    },
    fileImage: {
        type    : String,
        required: true,
    },
});

defineEmits(['rename', 'delete', 'copy', 'replace', 'cut']);
</script>
