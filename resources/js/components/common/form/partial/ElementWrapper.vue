<template>
    <div :class="[classes, { 'form-floating': label }]">
        <slot />
        <label
            v-if="label && id"
            :for="id"
        >
            {{ label }}<span v-if="required"
                             class="text-danger">*</span>
        </label>
    </div>
</template>

<script setup>
defineProps({
    classes : String,
    required: Boolean,
    label   : String,
    id      : String,
});
</script>