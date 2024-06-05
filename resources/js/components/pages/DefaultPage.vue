<template>
    <div class="layout-main">
        <div class="layout-left">
            <slot name="left"></slot>
        </div>
        <div class="layout-right">
            <slot name="right"></slot>
        </div>
    </div>
</template>

<script>
export default {
    name: 'DefaultPage',
};
</script>