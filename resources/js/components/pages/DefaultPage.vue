<template>
    <div class="layout-main">
        <div class="layout-left" v-if="slots.left">
            <slot name="left"></slot>
        </div>
        <div class="layout-right" :class="slots.left ? '' : 'no-left'">
            <slot name="right"></slot>
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
    methods: {
        hasLeftSlot() {
            return !!!this.$slots.left;
        }
    }
};
</script>