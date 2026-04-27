<template>
    <element-wrapper
        :label="label"
        :required="required"
        :classes="classes"
        :id="inputId"
    >
        <input
            :id="inputId"
            :value="modelValue"
            :type="inputType"
            :required="required"
            :placeholder="placeholder"
            :disabled="disabled"
            :max="max"
            :min="min"
            :step="step"
            :name="name"
            class="form-control"
            :class="{ 'form-error': errors }"
            @input="onInput"
            @change="onChange"
            @keyup.enter="onEnter"
            @keyup="onKeyup"
            @focusout="$emit('focusout')"
            v-bind="$attrs"
        />
    </element-wrapper>
    <errors-list :errors="errors" />
</template>

<script setup>
import { computed }   from 'vue';
import { useId }      from 'vue';
import ErrorsList     from './partial/ErrorsList.vue';
import ElementWrapper from './partial/ElementWrapper.vue';

const props = defineProps({
    modelValue : [String, Number, File],
    errors     : [String, Array],
    type       : { type: String, default: 'text' },
    label      : String,
    name       : String,
    max        : [String, Number],
    min        : [String, Number],
    step       : [String, Number],
    placeholder: String,
    required   : Boolean,
    disabled   : Boolean,
    classes    : String,
});

const emit = defineEmits([
    'update:modelValue',
    'change',
    'submit',
    'keyup',
    'focusout',
]);

// Генерация уникального ID
const inputId = `input-${useId()}`;

// Преобразование type="money" в number
const inputType = computed(() => (props.type === 'money' ? 'number' : props.type));

const onInput = (event) => {
    const val = props.type === 'file' ? event.target.files[0] : event.target.value;
    emit('update:modelValue', val);
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