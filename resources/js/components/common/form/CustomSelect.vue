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
            :class="{ 'is-invalid': resolvedErrors }"
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
    <errors-list v-if="resolvedErrors" :errors="resolvedErrors" />
</template>

<script setup>
import { computed }       from 'vue';
import ErrorsList         from '@common/form/partial/ErrorsList.vue';
import ElementWrapper     from '@common/form/partial/ElementWrapper.vue';
import { useFormFieldId } from '@common/form/useFormFieldId';
import { useFieldError }  from '@common/form/useFieldError';

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

const selectId                                              = useFormFieldId('select');
const { clearResolvedError, resolvedError: resolvedErrors } = useFieldError(props);

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
    clearResolvedError();
};
</script>
