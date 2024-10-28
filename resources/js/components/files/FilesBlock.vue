<template>
    <page-template>
        <template v-slot:main>
            <template v-if="edit">
                <div class="d-flex justify-content-between mb-2">
                    <button class="btn btn-success"
                            @click="chooseFile">Загрузить файл
                    </button>
                    <input class="d-none"
                           type="file"
                           ref="fileElem"
                           @change="uploadFile">
                </div>
            </template>
            <file-list class="mt-3"
                       v-model:reloadList="reloadList"
                       v-model:canEdit="edit"
            />
        </template>
    </page-template>
</template>

<script>
import Url           from '../../utils/Url.js';
import ResponseError from '../../mixin/ResponseError.js';
import PageTemplate  from '../pages/SingleColumnPage.vue';
import FileList      from './FileList.vue';

export default {
    name      : 'FilesBlock',
    components: {
        PageTemplate,
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