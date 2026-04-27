<template>
    <element-wrapper
        :label="label"
        :required="required"
        :classes="classes"
        :id="textareaId"
    >
    <textarea
        :id="textareaId"
        :value="modelValue"
        :required="required"
        :placeholder="placeholder"
        :disabled="disabled"
        :name="name"
        :rows="rows"
        class="form-control"
        :class="{ 'form-error': errors }"
        @input="onInput"
        @change="onChange"
        @keyup="onKeyup"
        v-bind="$attrs"
    />
    </element-wrapper>
    <errors-list :errors="errors" />
</template>

<script setup>
import { useId }      from 'vue';
import ErrorsList     from './partial/ErrorsList.vue';
import ElementWrapper from './partial/ElementWrapper.vue';

const props = defineProps({
    modelValue : [String, Number],
    errors     : [String, Array],
    label      : String,
    name       : String,
    placeholder: String,
    required   : Boolean,
    disabled   : Boolean,
    classes    : String,
    rows       : { type: [Number, String], default: 4 },
});

const emit = defineEmits(['update:modelValue', 'change', 'keyup']);

const textareaId = `textarea-${useId()}`;

const onInput = (event) => {
    emit('update:modelValue', event.target.value);
};

const onChange = (event) => {
    emit('change', event);
};

const onKeyup = (event) => {
    emit('keyup', event);
};
</script>