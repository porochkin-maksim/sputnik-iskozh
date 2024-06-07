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
import FileListItem      from './FileListItem.vue';

export default {
    name      : 'List',
    components: {
        FileListItem,
    },
    mixins    : [
        ResponseError,
    ],
    props     : [
        'reloadList',
        'canEdit',
    ],
    data () {
        return {
            showForm: false,

            files: [],
            edit : false,
        };
    },
    mounted () {
        this.loadList();
    },
    methods: {
        loadList () {
            window.axios[Url.Routes.filesList.method](Url.Routes.filesList.uri).then(response => {
                this.files = response.data.files;
                this.edit  = response.data.edit;
                this.$emit('update:canEdit', this.edit);
            }).catch(response => {
                this.parseResponseErrors(response);
            });
        },
    },
    watch  : {
        reloadList () {
            if (this.reloadList) {
                this.loadList();
                this.$emit('reloadList', false);
            }
        },
    },
};
</script>