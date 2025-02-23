<template>
    <div :class="[classes, label ? 'form-floating' : '']">
        <slot></slot>
        <label :for="id" v-if="label && id">
            <span>
                {{ label }}<span v-if="required" class="text-danger">*</span>
            </span>
        </label>
    </div>
</template>

<script>
export default {
    props: [
        'classes',
        'required',
        'label',
        'id',
    ],
};
</script>
