<template>
    <div class="row custom-list list files-list">
        <file-list-item v-for="file in files"
                        :file="file"
                        :edit="edit"
                        @updated="loadList"
        />
    </div>
</template>

<script>
import Url           from '../../utils/Url.js';
import ResponseError from '../../mixin/ResponseError.js';
import FileListItem  from './FileListItem.vue';

export default {
    components: {
        FileListItem,
    },
    mixins    : [
        ResponseError,
    ],
    props     : [
        'reloadList',
        'canEdit',
        'count',
        'limit',
    ],
    data () {
        return {
            showForm: false,

            files : [],
            edit  : false,
            images: [],
        };
    },
    mounted () {
        this.loadList();
    },
    methods: {
        loadList () {
            window.axios[Url.Routes.filesList.method](Url.Routes.filesList.uri, {
                params: {
                    limit: this.limit,
                },
            }).then(response => {
                this.files  = response.data.files;
                this.images = [];
                this.files.forEach(file => {
                    if (file.isImage) {
                        this.images.push(file);
                    }
                });
                this.edit = response.data.edit;
                this.$emit('update:canEdit', this.edit);
                this.$emit('update:count', this.files.length);
            }).catch(response => {
                this.parseResponseErrors(response);
            });
        },
    },
    watch  : {
        reloadList () {
            if (this.reloadList) {
                this.loadList();
                this.$emit('update:reloadList', false);
            }
        },
    },
};
</script>