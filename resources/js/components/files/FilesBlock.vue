<template>
    <default-page>
        <template v-slot:left
                  v-if="edit">
            <div class="d-flex justify-content-between">
                <button class="btn btn-primary"
                        @click="chooseFile">Загрузить файл
                </button>
                <input class="d-none"
                       type="file"
                       ref="fileElem"
                       @change="uploadFile">
            </div>
        </template>
        <template v-slot:right>
            <file-list class="mt-3"
                       v-model:reloadList="reloadList"
                       v-model:canEdit="edit"
            />
        </template>
    </default-page>
</template>

<script>
import ResponseError from '../../mixin/ResponseError.js';
import FileList      from './FileList.vue';
import DefaultPage   from '../pages/DefaultPage.vue';
import Url           from '../../utils/Url.js';

export default {
    name      : 'FilesBlock',
    components: {
        DefaultPage,
        FileList,
    },
    mixins    : [
        ResponseError,
    ],
    data () {
        return {
            reloadList: false,
            edit      : false,
            id        : null,
        };
    },
    methods: {
        chooseFile () {
            this.$refs.fileElem.click();
        },
        uploadFile (event) {
            let form = new FormData();
            form.append('file', event.target.files[0]);

            let uri = Url.Generator.makeUri(Url.Routes.filesStore);

            window.axios[Url.Routes.filesStore.method](
                uri,
                form,
            ).then(response => {
                this.reloadList = true;
            }).catch(response => {
                this.parseResponseErrors(response);
            });
        },
    },
};
</script>