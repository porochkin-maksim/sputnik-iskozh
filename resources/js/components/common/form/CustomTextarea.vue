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
            :class="{ 'is-invalid': resolvedErrors }"
            @input="onInput"
            @change="onChange"
            @keyup="onKeyup"
            v-bind="$attrs"
        />
    </element-wrapper>
    <errors-list v-if="resolvedErrors" :errors="resolvedErrors" />
</template>

<script setup>
import ErrorsList         from '@common/form/partial/ErrorsList.vue';
import ElementWrapper     from '@common/form/partial/ElementWrapper.vue';
import { useFormFieldId } from '@common/form/useFormFieldId';
import { useFieldError }  from '@common/form/useFieldError';

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

const textareaId                                            = useFormFieldId('textarea');
const { clearResolvedError, resolvedError: resolvedErrors } = useFieldError(props);

const onInput = (event) => {
    emit('update:modelValue', event.target.value);
    clearResolvedError();
};

const onChange = (event) => {
    emit('change', event);
    clearResolvedError();
};

const onKeyup = (event) => {
    emit('keyup', event);
    clearResolvedError();
};
</script>
