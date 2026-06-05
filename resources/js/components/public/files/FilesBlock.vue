<template>
    <page-template>
        <template v-slot:main>
            <div class="files-block public-files-block" :class="{ 'public-files-block--readonly': !edit }">
                <div class="public-files-block__head">
                    <div class="public-files-block__title-wrap">
                        <h3 class="public-files-block__title">Документы</h3>
                        <div class="public-files-block__count">{{ count }}</div>
                    </div>
                    <div class="public-files-block__actions" v-if="edit">
                        <button class="btn btn-outline-success btn-sm public-files-block__action"
                                @click="chooseFile">
                            <i class="fa fa-upload"></i>&nbsp;Загрузить файл
                        </button>
                        <input class="d-none"
                               type="file"
                               ref="fileElem"
                               accept="*/*"
                               @change="uploadFile">
                    </div>
                </div>
                <file-list class="public-files-block__list"
                           v-model:reloadList="reloadList"
                           v-model:canEdit="edit"
                           v-model:count="count"
                />
            </div>
        </template>
    </page-template>
</template>

<script setup>
import { ref }              from 'vue';
import PageTemplate         from '@components/public/pages/SingleColumnPage.vue';
import FileList             from '@components/public/files/FileList.vue';
import { ApiFilesStore }    from '@api';
import { useResponseError } from '@composables/useResponseError';

const reloadList              = ref(false);
const edit                    = ref(false);
const fileElem                = ref(null);
const count                   = ref(0);
const { parseResponseErrors } = useResponseError();

const chooseFile = () => {
    fileElem.value?.click();
};

const uploadFile = (event) => {
    const form = new FormData();
    form.append('file', event.target.files[0]);

    ApiFilesStore({}, form).then(() => {
        reloadList.value = true;
    }).catch(response => {
        parseResponseErrors(response);
    });
};
</script>
