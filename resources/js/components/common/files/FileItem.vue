<template>
    <div class="file">
        <div class="d-inline-flex align-items-center">
            <a class="btn btn-success btn-sm me-2"
               :href="file.url"
               :download="file.name">
                <i class="fa fa-download"></i>
            </a>
            <template v-if="file.isImage">
                <a :href="file.url"
                   class="name text-decoration-none"
                   :data-lightbox="file.name"
                   :data-title="file.name"
                   target="_blank">{{ file.name }}</a>
            </template>
            <template v-else>
                <a :href="file.url"
                   class="name text-nowrap"
                   target="_blank">{{ file.name }}</a>
            </template>
        </div>
    </div>
</template>

<script>

export default {
    emits: ['updated'],
    props: [
        'file',
    ],
};
</script>