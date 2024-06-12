<template>
    <div class="layout-page single-column-page">
        <div class="block-main">
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
    name: 'SingleColumnPage',
};
</script>