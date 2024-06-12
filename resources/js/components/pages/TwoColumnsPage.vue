<template>
    <div class="layout-page">
        <div class="block-sub"
             v-if="slots.sub">
            <div class="w-100">
                <slot name="sub"></slot>
            </div>
        </div>
        <div class="block-main"
             :class="slots.sub ? '' : 'no-sub'">
            <div class="w-100">
                <slot name="main"></slot>
            </div>
        </div>
    </div>
</template>

<script setup>
import { useSlots } from 'vue';

const slots = useSlots();
</script>

<script>
export default {
    name: 'PageTemplate',
};
</script>