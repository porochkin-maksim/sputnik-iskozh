<template>
    <div class="file">
        <div class="d-inline-flex align-items-center">
            <a class="btn btn-success btn-sm me-2"
               v-if="showDownload"
               :href="file.url"
               :download="fileName">
                <i class="fa fa-download"></i>
            </a>
            <template v-if="file.isImage">
                <a :href="file.url"
                   class="name text-decoration-none"
                   :data-lightbox="fileName"
                   :data-title="fileName"
                   target="_blank">{{ fileName }}</a>
            </template>
            <template v-else>
                <a :href="file.url"
                   class="name text-nowrap"
                   target="_blank">{{ fileName }}</a>
            </template>
        </div>
    </div>
</template>

<script>

export default {
    emits   : ['updated'],
    props   : {
        file        : {
            type    : Object,
            required: true,
        },
        name        : {
            type   : String,
            default: null,
        },
        showDownload: {
            type   : Boolean,
            default: true,
        },
    },
    computed: {
        fileName () {
            return this.name ? this.name + '.' + this.file.ext : this.file.name;
        },
    },
};
</script>