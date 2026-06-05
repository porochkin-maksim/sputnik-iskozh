<template>
    <div class="public-files-list">
        <file-list-item v-for="file in files"
                        :file="file"
                        :edit="edit"
                        @updated="loadList"
        />
    </div>
</template>

<script setup>
import {
    onMounted,
    ref,
    watch,
}                           from 'vue';
import FileListItem         from '@common/files/FileListItem.vue';
import { ApiFilesList }     from '@api';
import { useResponseError } from '@composables/useResponseError';

const emit  = defineEmits(['update:canEdit', 'update:count', 'update:reloadList']);
const props = defineProps({
    reloadList: {
        type   : Boolean,
        default: false,
    },
    canEdit   : {
        type   : Boolean,
        default: false,
    },
    count     : {
        type   : Number,
        default: 0,
    },
    limit     : {
        type   : Number,
        default: null,
    },
});

const { parseResponseErrors } = useResponseError();
const files                   = ref([]);
const edit                    = ref(false);
const images                  = ref([]);

const loadList = () => {
    ApiFilesList({
        sort_desc: true,
        limit    : props.limit,
    }).then(response => {
        files.value  = response.data.files;
        images.value = [];
        files.value.forEach(file => {
            if (file.isImage) {
                images.value.push(file);
            }
        });
        edit.value = response.data.edit;
        emit('update:canEdit', edit.value);
        emit('update:count', files.value.length);
    }).catch(response => {
        parseResponseErrors(response);
    });
};

onMounted(loadList);

watch(() => props.reloadList, (value) => {
    if (value) {
        loadList();
        emit('update:reloadList', false);
    }
});
</script>
