<template>
    <button class="btn"
            @click="open">
        <div :style="url ? '' : 'opacity: 0.2'">
            <i class="fa fa-file-text text-warning"></i>&nbsp;История
        </div>
    </button>
</template>
<script>
export default {
    props: {
        url: {
            required: true,
        },
    },
    data () {
        return {
            modal: null,
        };
    },
    methods: {
        open () {
            let options = [];

            options.push('width=800');
            options.push('height=300');
            options.push('left=50');
            options.push('top=50');
            options.push('scrollbars=yes');

            let w = window.open(this.url, 'history', options.join(','));

            w.focus();
        },
    },
};
</script>
