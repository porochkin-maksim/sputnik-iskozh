<template>
    <label class="form-control label-input">
        <span class="label-text">
            {{ label }}
        </span>
        <input :value="modelValue"
               :type="type"
               :required="required"
               :placeholder="placeholder"
               @change="onChange"
               @keyup="onChange"
               class="form-control"
               :class="[errors ? 'form-error' : '']"
        />
    </label>
    <ul v-if="errors"
        class="form-error"
    >
        <li v-for="error in errors">
            {{ error }}
        </li>
    </ul>
</template>

<script>
export default {
    props  : [
        'modelValue',
        'errors',
        'type',
        'label',
        'placeholder',
        'required',
    ],
    emits  : [
        'update:modelValue',
        'change',
    ],
    methods: {
        onChange (event) {
            this.$emit('update:modelValue', event.target.value);
            this.$emit('change', event.target.value);
        },
        togglePassword () {
            this.showPassword = !this.showPassword;
        },
    },
};
</script>
