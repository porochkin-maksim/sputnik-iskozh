<template>
    <div class="layout-page" :class="layoutClass">
        <div class="block-sub"
             :class="subClass"
             v-if="slots.sub">
            <div class="w-100">
                <slot name="sub"></slot>
            </div>
        </div>
        <div class="block-main"
             :class="[slots.sub ? '' : 'no-sub', mainClass]">
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
    props: [
        'layoutClass',
        'subClass',
        'mainClass',
    ],
};
</script>