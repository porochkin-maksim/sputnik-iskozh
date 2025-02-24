<template>
    <div class="bg-wrapper" @click="$emit('close')">
    </div>
    <div class="wrapper-container" :class="containerClass">
        <slot></slot>
    </div>
</template>

<script>
export default {
    name      : 'Wrapper',
    props     : [
        'containerClass',
    ],
    emits: ['close'],
}
</script>