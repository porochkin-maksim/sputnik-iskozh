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

            // Calculate window dimensions
            const screenWidth  = window.screen.width;
            const screenHeight = window.screen.height;
            const windowWidth  = Math.min(Math.max(screenWidth / 2, 1000), screenWidth);
            const windowHeight = screenHeight;
            const windowLeft   = (screenWidth - windowWidth) / 2;
            const windowTop    = 0;

            options.push(`width=${windowWidth}`);
            options.push(`height=${windowHeight}`);
            options.push(`left=${windowLeft}`);
            options.push(`top=${windowTop}`);
            options.push('scrollbars=yes');

            let w = window.open(this.url, 'history', options.join(','));

            w.focus();
        },
    },
};
</script>
