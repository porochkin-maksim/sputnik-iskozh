<template>
    <div v-if="!grouped" :class="wrapperClass">
        <input
            :id="id"
            :value="modelValue"
            :type="type"
            :required="required"
            :placeholder="placeholder"
            :disabled="disabled"
            :name="name"
            class="form-control"
            :class="[inputClass, { 'is-invalid': errors }]"
            @input="onInput"
            @change="onChange"
            @keyup.enter="onEnter"
            @keyup="onKeyup"
            v-bind="$attrs"
        >
        <errors-list :errors="errors" />
    </div>
    <input
        v-else
        :id="id"
        :value="modelValue"
        :type="type"
        :required="required"
        :placeholder="placeholder"
        :disabled="disabled"
        :name="name"
        class="form-control"
        :class="[inputClass, { 'is-invalid': errors }]"
        @input="onInput"
        @change="onChange"
        @keyup.enter="onEnter"
        @keyup="onKeyup"
        v-bind="$attrs"
    >
</template>

<script setup>
import ErrorsList from '@common/form/partial/ErrorsList.vue';

defineProps({
    modelValue  : [String, Number],
    errors      : [String, Array],
    type        : { type: String, default: 'text' },
    name        : String,
    id          : String,
    placeholder : String,
    required    : Boolean,
    disabled    : Boolean,
    wrapperClass: String,
    inputClass  : [String, Array, Object],
    grouped     : Boolean,
});

const emit = defineEmits([
    'update:modelValue',
    'change',
    'submit',
    'keyup',
]);

const onInput = (event) => {
    emit('update:modelValue', event.target.value);
};

const onChange = (event) => {
    emit('change', event);
};

const onEnter = (event) => {
    emit('submit', event);
};

const onKeyup = (event) => {
    emit('keyup', event);
};
</script>
