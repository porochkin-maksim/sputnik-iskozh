<template>
    <label class="form-control label-select">
        <span class="label-text">
            {{ label }}
        </span>
        <select :required="required"
                @change="onChange"
                @keyup="onChange"
                class="form-control"
                :class="[errors ? 'form-error' : '']"
        >
            <option v-for="item in items"
                    :key="item.key !== undefined ? item.key : item"
                    :value="item.key !== undefined ? item.key : item"
                    :selected="(item.key !== undefined ? item.key : item) === modelValue"
            >
                {{ item.value !== undefined ? item.value : item }}
            </option>
        </select>
    </label>
    <ul v-if="errors"
        class="form-error">
        <li v-for="error in errors">{{ error }}</li>
    </ul>
</template>

<script>
export default {
    props  : [
        'modelValue',
        'errors',
        'required',
        'items',
        'label',
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
    },
};
</script>
