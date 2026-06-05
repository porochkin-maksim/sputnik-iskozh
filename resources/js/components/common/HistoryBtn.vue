<template>
    <button class="btn py-0"
            @click="open">
        <span :style="url ? '' : 'opacity: 0.2'" class="text-nowrap">
            <i class="fa fa-file-text text-warning"></i>&nbsp;История
        </span>
    </button>
</template>
<script setup>
const props = defineProps({
    url: {
        required: true,
    },
});

const open = () => {
    const screenWidth  = window.screen.width;
    const screenHeight = window.screen.height;
    const windowWidth  = Math.min(Math.max(screenWidth / 2, 1000), screenWidth);
    const windowLeft   = (screenWidth - windowWidth) / 2;
    const options      = [
        `width=${windowWidth}`,
        `height=${screenHeight}`,
        `left=${windowLeft}`,
        'top=0',
        'scrollbars=yes',
    ];

    const openedWindow = window.open(props.url, 'history', options.join(','));
    openedWindow?.focus();
};
</script>
