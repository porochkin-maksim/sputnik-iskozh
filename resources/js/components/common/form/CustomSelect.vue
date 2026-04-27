<template>
    <element-wrapper
        :label="label"
        :required="required"
        :classes="classes"
        :id="selectId"
    >
        <select
            :id="selectId"
            :value="modelValue"
            :required="required"
            :disabled="disabled"
            :name="name"
            class="form-control"
            :class="{ 'form-error': errors }"
            @change="onChange"
            v-bind="$attrs"
        >
            <option
                v-for="option in normalizedOptions"
                :key="option.value"
                :value="option.value"
            >
                {{ option.label }}
            </option>
        </select>
    </element-wrapper>
    <errors-list :errors="errors" />
</template>

<script setup>
import {
    computed,
    useId,
}                     from 'vue';
import ErrorsList     from './partial/ErrorsList.vue';
import ElementWrapper from './partial/ElementWrapper.vue';

const props = defineProps({
    modelValue: [String, Number],
    errors    : [String, Array],
    label     : String,
    name      : String,
    required  : Boolean,
    disabled  : Boolean,
    classes   : String,
    // Принимаем options в любом формате: массив строк или массив объектов { value, label }
    options: {
        type    : Array,
        required: true,
    },
});

const emit = defineEmits(['update:modelValue', 'change']);

const selectId = `select-${useId()}`;

// Нормализуем options к единому формату { value, label }
const normalizedOptions = computed(() => {
    return props.options.map(opt => {
        if (typeof opt === 'object' && opt !== null) {
            return {
                value: opt.value ?? opt.key ?? opt,
                label: opt.label ?? opt.value ?? opt,
            };
        }
        return { value: opt, label: opt };
    });
});

const onChange = (event) => {
    const value = event.target.value;
    emit('update:modelValue', value);
    emit('change', value);
};
</script>