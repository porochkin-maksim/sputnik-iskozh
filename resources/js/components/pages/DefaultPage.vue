<template>
    <div class="layout-page">
        <div class="block-sub" v-if="slots.sub">
            <slot name="sub"></slot>
        </div>
        <div class="block-main" :class="slots.sub ? '' : 'no-sub'">
            <slot name="main"></slot>
        </div>
    </div>
</template>

<script setup>
import { useSlots } from 'vue'
const slots = useSlots()
</script>

<script>

export default {
    name: 'DefaultPage',
};
</script>